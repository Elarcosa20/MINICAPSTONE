<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Attendance Assistant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(90deg, #1A73E8, #003366);
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .login-card {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            text-decoration: none;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #1A73E8;
            border-color: #1A73E8;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
.social-buttons a{
    text-decoration: none;
    color: white;
}
        .social-buttons button {
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 16px;
            transition: background-color 0.3s;
            color: white;
        }

        .social-buttons .btn-email {
            background-color: #6c757d;
        }

        .social-buttons .btn-facebook {
            background-color: #4267B2;
        }

        .social-buttons .btn-twitter {
            background-color: #1DA1F2;
        }

        .social-buttons button i {
            margin-right: 10px;
        }

        .social-buttons button:hover {
            opacity: 0.9;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
        }

        .divider span {
            padding: 0 10px;
            background-color: white;
            color: #666;
        }

        .divider:before,
        .divider:after {
            content: '';
            display: inline-block;
            width: 45%;
            height: 1px;
            background-color: #ddd;
            vertical-align: middle;
        }
        .modal-content {
            border-radius: 10px;
        }
        
        .modal-header {
            background-color: #1A73E8;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
    </style>
</head>

<body>
<?php
    require_once 'vendor/autoload.php';
    
    $clientID = '648737561092-cpdbt3ipvc2mc930io3n4jr17grdj5s5.apps.googleusercontent.com';
    $clientSecret = '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';
    $redirectUri = 'http://localhost:3000/email.php';
    
   
    $client = new Google_Client();
    $client->setClientId($clientID);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri($redirectUri);
    $client->addScope("email");
    $client->addScope("profile");
    
 
    if (isset($_GET['code'])) {
      $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
      $client->setAccessToken($token['access_token']);
    
   
      $google_oauth = new Google_Service_Oauth2($client);
      $google_account_info = $google_oauth->userinfo->get();
      $email =  $google_account_info->email;
      $name =  $google_account_info->name;

      
      header("Location: /login.php");
      exit();
    } else {
    ?>
    <div class="login-card">
        <h2 class="text-center mb-4">Login</h2>
     
        <div class="social-buttons">
    <a href="<?php echo $client->createAuthUrl() ?>">
        <button class="btn btn-email" data-bs-toggle="modal" data-bs-target="#emailModal">
            <img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" alt="Email Logo" style="width: 20px; height: 20px; margin-right: 10px;"> Login with Gmail
        </button>
    </a>
                    </div>
                </div>
            </div>
    <?php } ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
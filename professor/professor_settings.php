<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professor') {
    header("Location: index.html");
    exit();
}

$professor_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT password FROM professors WHERE professor_id = :professor_id");
$stmt->bindParam(':professor_id', $professor_id);
$stmt->execute();
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $reenter_password = $_POST['reenter_password'];

    $errors = [];

 
    if (!password_verify($current_password, $professor['password'])) {
        $errors[] = "Current password is incorrect.";
    }

   
    if ($new_password !== $reenter_password) {
        $errors[] = "New passwords do not match.";
    }

    if (strlen($new_password) < 8) {
        $errors[] = "New password must be at least 8 characters long.";
    }

   
    if (empty($errors)) {
        $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);


        $stmt = $conn->prepare("UPDATE professors SET password = :password WHERE professor_id = :professor_id");
        $stmt->bindParam(':password', $hashed_new_password);
        $stmt->bindParam(':professor_id', $professor_id);

        if ($stmt->execute()) {
            $success_message = "Password changed successfully.";
        } else {
            $errors[] = "Failed to update the password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
        }
        .navbar {
            background-color: #000051;
        }

        .navbar-nav {
            margin-left: auto;
        }

        .navbar-nav .nav-item .nav-link {
            color: white;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .settings-form {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .btn-primary {
            background-color: #000080;
        }
        .btn-primary:hover {
            background-color: #000070;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a href="professor_dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="professor_view_profile.php" class="nav-link"><i class="fas fa-user-circle"></i> Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="professor_settings.php"><i class="fas fa-cogs"></i> Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="settings-form">
        <h3>Change Your Password</h3>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($success_message)): ?>
            <div class="alert alert-success">
                <p><?php echo htmlspecialchars($success_message); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="reenter_password" class="form-label">Re-enter New Password</label>
                <input type="password" class="form-control" id="reenter_password" name="reenter_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Change Password</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

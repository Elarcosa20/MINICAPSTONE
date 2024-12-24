<?php
session_start();
require_once 'db/db.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = trim($_POST['user_id']);
    $password = trim($_POST['password']);
    

    $roles = [
        'admin' => ['table' => 'admins', 'id_column' => 'admin_id', 'dashboard' => '/admin/admin_dashboard.php'],
        'professor' => ['table' => 'professors', 'id_column' => 'professor_id', 'dashboard' => '/professor/professor_dashboard.php'],
        'student' => ['table' => 'students', 'id_column' => 'student_id', 'dashboard' => '/student/student_dashboard.php']
    ];
    
    try {
        foreach ($roles as $role => $data) {
            $sql = "SELECT * FROM {$data['table']} WHERE {$data['id_column']} = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
           
                if (password_verify($password, $user['password']) || md5($password) === $user['password']) {
                    $_SESSION['user_id'] = $user[$data['id_column']];
                    $_SESSION['role'] = $role;
                    header("Location: " . $data['dashboard']);
                    exit();
                }
            }
        }
      
        $error = "1"; 
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $error = "server";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000051;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .school-logo {
            max-width: 200px;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background-color: #000051;
            border-color: #000051;
        }
        .btn-primary:hover {
            background-color: #000033;
            border-color: #000033;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="/images/logo.png" alt="School Logo" class="school-logo mx-auto d-block">
        <h2 class="text-center mb-4">Login</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" name="user_id" placeholder="ID" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <?php if (isset($error)): ?>
            <div class="mt-3 text-danger text-center">
                <?php 
                    if ($error === "1") {
                        echo "Invalid ID or password. Please try again.";
                    } elseif ($error === "server") {
                        echo "Server error, please try again later.";
                    }
                ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

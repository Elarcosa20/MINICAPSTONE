<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $student_id = $_POST['student_id'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = password_hash($_POST['password'], PASSWORD_ARGON2I); 

    try {
         // Call the stored procedure
    $stmt = $conn->prepare("CALL AddStudent(:name, :student_id, :birthday, :gender, :email, :contact, :password)");
    
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':birthday', $birthday);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contact', $contact);
    $stmt->bindParam(':password', $password);
    
    if ($stmt->execute()) {
        $success = "Student added successfully!";
    } else {
        $error = "Error adding student.";
    }
} catch (PDOException $e) {
    $error = "Error adding student: " . $e->getMessage();
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000051;
            color: white;
        }
        .card-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            border: none;
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
        }
        .card-header {
            background: linear-gradient(90deg, #4b6cb7, #182848);
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }
        .card-header h2 {
            margin: 0;
        }
        .card-body {
            padding: 20px;
        }
        .form-control {
            margin-bottom: 15px;
        }
        .btn-primary {
            background: linear-gradient(90deg, #4b6cb7, #182848);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #3333a1, #000051);
            border: none;
            color: white;
        }
        .btn-secondary {
            color: #000051;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card-container">
            <div class="card">
                <div class="card-header">
                    <h2>Add New Student</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="Student's Name" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="student_id" placeholder="Student's ID" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="date" class="form-control" name="birthday" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" class="form-control" name="contact" placeholder="Contact number" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Add</button>
                            <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

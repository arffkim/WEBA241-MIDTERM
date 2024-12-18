<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - My Food Menu</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --soft-blue: #e6f2ff;
            --primary-color: #4a90e2;
            --text-color: #333;
            --light-text: #6c757d;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--soft-blue);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .logo {
            max-height: 50px;
            margin-right: 15px;
        }

        .register-section {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .register-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 2.5rem;
            max-width: 500px;
            width: 100%;
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h3 {
            color: var(--primary-color);
            font-weight: 600;
        }

        .form-label {
            color: var(--light-text);
            font-weight: 500;
        }

        .form-control {
            padding: 0.75rem;
            border-color: #e0e0e0;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74,144,226,0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary:hover {
            background-color: #3a7bd5;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .register-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .register-footer a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .register-footer a:hover {
            color: #3a7bd5;
            text-decoration: underline;
        }

        footer {
            background-color: white;
            padding: 1rem 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }

        @media (max-width: 576px) {
            .register-container {
                padding: 1.5rem;
                margin: 0 1rem;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="logo.png" alt="My Food Menu Logo" class="logo">
                <span class="fw-bold">My Food Menu</span>
            </a>
        </div>
    </nav>

    <!-- Register Section -->
    <main class="register-section">
        <div class="register-container">
            <div class="register-header">
                <h3>Register for My Food Menu</h3>
            </div>
            <form action="register.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="repassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="repassword" name="repassword" required>
                </div>
                <div class="mb-3">
                    <label for="no_phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="no_phone" name="no_phone" required>
                </div>
                <div class="mb-3">
                    <label for="user_image" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="user_image" name="user_image" accept="image/*" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>
            <div class="register-footer">
                <p>Already have an account? <a href="login.php">Log In here</a></p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p class="m-0 text-muted">&copy; 2024 My Food Menu. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $repassword = $conn->real_escape_string($_POST['repassword']);
    $no_phone = $conn->real_escape_string($_POST['no_phone']);

    if ($password !== $repassword) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Passwords do not match!'
                });
              </script>";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["user_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image is valid
    if (!getimagesize($_FILES["user_image"]["tmp_name"])) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'File is not an image!'
                });
              </script>";
        exit;
    }

    // Check if upload is successful
    if (!move_uploaded_file($_FILES["user_image"]["tmp_name"], $target_file)) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'There was an error uploading your file.'
                });
              </script>";
        exit;
    }

    $sql = "INSERT INTO USER (EMAIL, NAME, PASSWORD, NO_PHONE, PICTURE) VALUES ('$email', '$username', '$hashed_password', '$no_phone', '$target_file')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful',
                    text: 'You can now log in!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = 'login.php';
                    }
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '" . $conn->error . "'
                });
              </script>";
    }

    $conn->close();
}
?>
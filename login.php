<?php
include('dbconnect.php');
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle auto-login if the "Remember Me" cookie is set
if (isset($_COOKIE['user_email'])) {
    $email = $conn->real_escape_string($_COOKIE['user_email']);
    $sql = "SELECT * FROM USER WHERE EMAIL = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_email'] = $email;

        // Auto-login success message
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      <script>
      document.addEventListener('DOMContentLoaded', () => {
          Swal.fire({
              icon: 'success',
              title: 'Welcome Back',
              text: 'You are automatically logged in!'
          }).then(() => {
              window.location = 'mainpage.php';
          });
      });
      </script>";
        exit();
    } else {
        // Expire invalid cookies
        setcookie('user_email', '', time() - 3600, "/");
    }
}

// Handle POST form submission
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM USER WHERE EMAIL = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['PASSWORD'])) {
            if (isset($_POST['remember'])) {
                setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 days
            }
            $_SESSION['user_email'] = $email;

            // Success message
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful',
                            text: 'Welcome back, {$user['NAME']}!'
                        }).then(() => {
                            window.location = 'mainpage.php';
                        });
                    });
                  </script>";
            exit();
        } else {
            $login_error = 'Invalid email or password.';
        }
    } else {
        $login_error = 'Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My Food Menu</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .logo {
            max-height: 50px;
            margin-right: 15px;
        }

        .login-section {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .login-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            max-width: 500px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h3 {
            color: var(--primary-color);
            font-weight: 600;
        }

        .form-label {
            color: var(--light-text);
            font-weight: 500;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-primary:hover {
            background-color: #3a7bd5;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer {
            background-color: white;
            padding: 1rem 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
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

    <!-- Login Section -->
    <main class="login-section">
        <div class="login-container">
            <div class="login-header">
                <h3>Login to My Food Menu</h3>
            </div>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label for="remember" class="form-check-label">Remember Me</label>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
            <div class="login-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <p><a href="index.php">Landing page</a></p>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 for Errors -->
    <?php if (!empty($login_error)): ?>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: '<?php echo $login_error; ?>'
                });
            });
        </script>
    <?php endif; ?>
</body>

</html>
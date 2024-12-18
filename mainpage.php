<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('Please log in first.');</script>";
    echo "<script>window.location.replace('login.php');</script>";
    exit;
}

include('dbconnect.php');

$user_email = $_SESSION['user_email'];
$sql = "SELECT NAME, PICTURE FROM USER WHERE EMAIL = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $username = $user['NAME'];
    $profile_picture = $user['PICTURE'];
} else {
    echo "<script>alert('User not found.');</script>";
    echo "<script>window.location.replace('login.php');</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page - My Food Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
            overflow-y: auto;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #ffffff;
            display: block;
        }
        .sidebar a:hover {
            background-color: #575d63;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .content {
                margin-left: 0;
            }
        }

        @media (max-width: 576px) {
            .sidebar a {
                font-size: 16px;
            }
        }
    </style>
    <script>
        function loadPage(page) {
            document.getElementById('content-frame').src = page;
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <div class="text-center mb-4">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="rounded-circle" width="100">
            <h4 class="text-white mt-2">Welcome, <?php echo htmlspecialchars($username); ?></h4>
        </div>
        <a href="javascript:void(0)" onclick="loadPage('profile.php')">Profile</a>
        <a href="javascript:void(0)" onclick="loadPage('menu.php')">Menu</a>
        
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <iframe id="content-frame" src="menu.php" style="width: 100%; height: 100vh; border: none;"></iframe>
    </div>
</body>
</html>

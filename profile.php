<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('Please log in first.');</script>";
    echo "<script>window.location.replace('login.php');</script>";
    exit;
}

include('dbconnect.php');

$user_email = $_SESSION['user_email'];
$sql = "SELECT * FROM USER WHERE EMAIL = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $username = $user['NAME'];
    $phone = $user['NO_PHONE'];
    $profile_picture = $user['PICTURE'];
} else {
    echo "<script>alert('User not found.');</script>";
    echo "<script>window.location.replace('login.php');</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $new_email = $_POST['email'];
        $new_username = $_POST['username'];
        $new_phone = $_POST['phone'];
        $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
        $new_picture = $_FILES['profile_picture']['name'] ? "uploads/" . basename($_FILES['profile_picture']['name']) : $profile_picture;

        if ($_FILES['profile_picture']['tmp_name']) {
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $new_picture);
        }

        $update_sql = "UPDATE USER SET EMAIL = ?, NAME = ?, NO_PHONE = ?, PICTURE = ?";
        if ($new_password) {
            $update_sql .= ", PASSWORD = ?";
        }
        $update_sql .= " WHERE EMAIL = ?";

        $update_stmt = $conn->prepare($update_sql);
        if ($new_password) {
            $update_stmt->bind_param('ssssss', $new_email, $new_username, $new_phone, $new_picture, $new_password, $user_email);
        } else {
            $update_stmt->bind_param('sssss', $new_email, $new_username, $new_phone, $new_picture, $user_email);
        }

        if ($update_stmt->execute()) {
            $_SESSION['user_email'] = $new_email;
            echo "<script>alert('Profile updated successfully!'); window.location.href = 'profile.php';</script>";
        } else {
            echo "<script>alert('Failed to update profile.');</script>";
        }
    }

    if (isset($_POST['delete'])) {
        $delete_sql = "DELETE FROM USER WHERE EMAIL = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param('s', $user_email);

        if ($delete_stmt->execute()) {
            echo "<script>alert('Account deleted successfully.');</script>";
            echo "<script>window.location.replace('logout.php');</script>";
        } else {
            echo "<script>alert('Failed to delete account.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Profile</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3 text-center">
                            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="rounded-circle" width="100">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                    <form method="POST" class="mt-3">
                        <div class="d-grid">
                            <button type="submit" name="delete" class="btn btn-danger">Delete Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

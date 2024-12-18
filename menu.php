<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('Please log in first.');</script>";
    echo "<script>window.location.replace('login.php');</script>";
    exit;
}

include('dbconnect.php');

// Pagination settings
$items_per_page = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Fetch paginated food data
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search === '') {
    $sql = "SELECT * FROM makanan LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $offset, $items_per_page);
} else {
    $sql = "SELECT * FROM makanan WHERE NAME LIKE ? LIMIT ?, ?";
    $search_param = "%$search%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $search_param, $offset, $items_per_page);
}
$stmt->execute();
$result = $stmt->get_result();

// Count total records for pagination
if ($search === '') {
    $count_sql = "SELECT COUNT(*) as total FROM makanan";
    $count_result = $conn->query($count_sql);
} else {
    $count_sql = "SELECT COUNT(*) as total FROM makanan WHERE NAME LIKE ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("s", $search_param);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
}
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

// Handle form submission for adding new food
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_food'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $details = $_POST['details'];
    $quantity = $_POST['quantity'];

    // Handle file upload
    $picture = 'uploads/' . basename($_FILES['picture']['name']);
    if (move_uploaded_file($_FILES['picture']['tmp_name'], $picture)) {
        $insert_sql = "INSERT INTO makanan (NAME, PRICE, PICTURE, DETAILS, QUANTITY) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sdssi", $name, $price, $picture, $details, $quantity);
        if ($stmt->execute()) {
            $_SESSION['msg'] = 'Food item has been added successfully!';
            $_SESSION['msg_type'] = 'success';
        } else {
            $_SESSION['msg'] = 'Failed to add food item.';
            $_SESSION['msg_type'] = 'error';
        }
    } else {
        $_SESSION['msg'] = 'Failed to upload picture.';
        $_SESSION['msg_type'] = 'error';
    }
    header("Location: menu.php?page=$page&search=$search");
    exit;
}

// Handle form submission for delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_food'])) {
    $food_id = $_POST['food_id'];
    $delete_sql = "DELETE FROM makanan WHERE ID = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $food_id);
    if ($stmt->execute()) {
        $_SESSION['msg'] = 'Food item has been deleted successfully!';
        $_SESSION['msg_type'] = 'success';
    } else {
        $_SESSION['msg'] = 'Failed to delete food item.';
        $_SESSION['msg_type'] = 'error';
    }
    header("Location: menu.php?page=$page&search=$search");
    exit;
}

// Handle form submission for edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_food'])) {
    $food_id = $_POST['food_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $details = $_POST['details'];
    $quantity = $_POST['quantity'];

    // Handle optional image upload
    $picture = null;
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $picture = 'uploads/' . basename($_FILES['picture']['name']);
        if (!move_uploaded_file($_FILES['picture']['tmp_name'], $picture)) {
            $_SESSION['msg'] = 'Failed to upload new picture.';
            $_SESSION['msg_type'] = 'error';
            header("Location: menu.php?page=$page&search=$search");
            exit;
        }
    }

    if ($picture) {
        // Update query to include picture
        $update_sql = "UPDATE makanan SET NAME = ?, PRICE = ?, DETAILS = ?, QUANTITY = ?, PICTURE = ? WHERE ID = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sdsssi", $name, $price, $details, $quantity, $picture, $food_id);
    } else {
        // Update query without changing picture
        $update_sql = "UPDATE makanan SET NAME = ?, PRICE = ?, DETAILS = ?, QUANTITY = ? WHERE ID = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sdssi", $name, $price, $details, $quantity, $food_id);
    }

    if ($stmt->execute()) {
        $_SESSION['msg'] = 'Food item has been updated successfully!';
        $_SESSION['msg_type'] = 'success';
    } else {
        $_SESSION['msg'] = 'Failed to update food item.';
        $_SESSION['msg_type'] = 'error';
    }
    header("Location: menu.php?page=$page&search=$search");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Food Menu</h2>

        <!-- Search and Add Food Button -->
        <div class="d-flex justify-content-between mb-3">
            <form class="d-flex" method="GET">
                <input class="form-control me-2" type="search" name="search" placeholder="Search by name"
                    value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFoodModal">Add Food</button>
        </div>

        <!-- Food Cards -->
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($row['PICTURE']); ?>" class="card-img-top"
                                alt="<?php echo htmlspecialchars($row['NAME']); ?>"
                                style="width: 100%; height: auto; object-fit: cover;">

                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['NAME']); ?></h5>
                                <p class="card-text"><strong>Price:</strong> RM <?php echo number_format($row['PRICE'], 2); ?>
                                </p>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['DETAILS'])); ?></p>
                                <p class="card-text"><strong>Quantity:</strong>
                                    <?php echo htmlspecialchars($row['QUANTITY']); ?></p>
                                <!-- Edit and Delete Buttons -->
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="food_id" value="<?php echo $row['ID']; ?>">
                                    <button type="submit" name="delete_food" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editFoodModal<?php echo $row['ID']; ?>">Edit</button>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Food Modal -->
                    <div class="modal fade" id="editFoodModal<?php echo $row['ID']; ?>" tabindex="-1"
                        aria-labelledby="editFoodModalLabel<?php echo $row['ID']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editFoodModalLabel<?php echo $row['ID']; ?>">Edit Food</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <input type="hidden" name="food_id" value="<?php echo $row['ID']; ?>">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name"
                                                value="<?php echo htmlspecialchars($row['NAME']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price</label>
                                            <input type="number" step="0.01" class="form-control" name="price"
                                                value="<?php echo $row['PRICE']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="details" class="form-label">Details</label>
                                            <textarea class="form-control" name="details" rows="3"
                                                required><?php echo htmlspecialchars($row['DETAILS']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">Quantity</label>
                                            <input type="number" class="form-control" name="quantity"
                                                value="<?php echo $row['QUANTITY']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="picture" class="form-label">Update Picture (Optional)</label>
                                            <input type="file" class="form-control" name="picture" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="edit_food" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">No food items available.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                        <a class="page-link"
                            href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- Add Food Modal -->
    <div class="modal fade" id="addFoodModal" tabindex="-1" aria-labelledby="addFoodModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFoodModalLabel">Add Food</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="picture" class="form-label">Picture</label>
                            <input type="file" class="form-control" id="picture" name="picture" accept="image/*"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="details" class="form-label">Details</label>
                            <textarea class="form-control" id="details" name="details" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_food" class="btn btn-primary">Add Food</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($_SESSION['msg'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    icon: '<?php echo $_SESSION['msg_type']; ?>',
                    title: '<?php echo $_SESSION['msg_type'] === 'success' ? 'Success' : 'Error'; ?>',
                    text: '<?php echo $_SESSION['msg']; ?>',
                    confirmButtonText: 'OK'
                });
            });
        </script>
        <?php unset($_SESSION['msg'], $_SESSION['msg_type']); endif; ?>
</body>

</html>
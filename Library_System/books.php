<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = "";
$error   = "";

// ADD BOOK
if (isset($_POST['add_book'])) {
    $title    = trim($_POST['title']);
    $book_no  = trim($_POST['book_no']);
    $genre    = trim($_POST['genre']);
    $author   = trim($_POST['author']);
    $quantity = (int)$_POST['quantity'];

    // Check if book number already exists
    $check = mysqli_prepare($conn, "SELECT id FROM books WHERE book_no = ?");
    mysqli_stmt_bind_param($check, "s", $book_no);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $error = "Book number already exists!";
    } else {
        $sql  = "INSERT INTO books (title, book_no, genre, author, quantity, available) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssii", $title, $book_no, $genre, $author, $quantity, $quantity);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Book added successfully!";
        } else {
            $error = "Failed to add book. Make sure your database table is updated!";
        }
    }
}

// DELETE BOOK
if (isset($_GET['delete'])) {
    $id   = (int)$_GET['delete'];
    $sql  = "DELETE FROM books WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: books.php?deleted=1");
    exit();
}

// EDIT BOOK
$edit_book = null;
if (isset($_GET['edit'])) {
    $id   = (int)$_GET['edit'];
    $sql  = "SELECT * FROM books WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit_book = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// UPDATE BOOK
if (isset($_POST['update_book'])) {
    $id       = (int)$_POST['id'];
    $title    = trim($_POST['title']);
    $book_no  = trim($_POST['book_no']);
    $genre    = trim($_POST['genre']);
    $author   = trim($_POST['author']);
    $quantity = (int)$_POST['quantity'];

    // Get current book to calculate borrowed count
    $current  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT quantity, available FROM books WHERE id=$id"));
    $borrowed = $current['quantity'] - $current['available'];
    if ($borrowed < 0) $borrowed = 0;

    // New available = new quantity minus currently borrowed
    $new_available = $quantity - $borrowed;
    if ($new_available < 0) $new_available = 0;
    if ($new_available > $quantity) $new_available = $quantity;

    $sql  = "UPDATE books SET title=?, book_no=?, genre=?, author=?, quantity=?, available=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssii", $title, $book_no, $genre, $author, $quantity, $new_available, $id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: books.php?updated=1");
        exit();
    } else {
        $error = "Failed to update book.";
    }
}

// SEARCH / GET ALL BOOKS
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search) {
    $sql   = "SELECT * FROM books WHERE title LIKE ? OR book_no LIKE ? OR genre LIKE ? OR author LIKE ? ORDER BY id DESC";
    $stmt  = mysqli_prepare($conn, $sql);
    $like  = "%$search%";
    mysqli_stmt_bind_param($stmt, "ssss", $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $books = mysqli_stmt_get_result($stmt);
} else {
    $books = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books — Library System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <span style="font-weight:600; font-size:18px;">📚 Library System</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="members.php">Members</a>
        <a href="borrow.php">Borrow</a>
        <a href="return.php">Return</a>
        <a href="logout.php" style="color:#ff6b6b;">Logout</a>
    </div>
</nav>

<div class="container">
    <h2 style="margin-bottom:20px;">Manage Books</h2>

    <?php if ($success): ?>
        <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div style="background:#fdecea; color:#c0392b; border:1px solid #f5c6cb; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            Book deleted successfully.
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            Book updated successfully.
        </div>
    <?php endif; ?>

    <!-- ADD / EDIT FORM -->
    <div class="table-box" style="margin-bottom:24px;">
        <h3 style="margin-bottom:16px;"><?= $edit_book ? 'Edit Book' : 'Add New Book' ?></h3>
        <form method="POST">
            <?php if ($edit_book): ?>
                <input type="hidden" name="id" value="<?= $edit_book['id'] ?>">
            <?php endif; ?>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label>Book Title</label>
                    <input type="text" name="title" required placeholder="Enter book title"
                        value="<?= $edit_book ? htmlspecialchars($edit_book['title']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Book No.</label>
                    <input type="text" name="book_no" required placeholder="e.g. BK-001"
                        value="<?= $edit_book ? htmlspecialchars($edit_book['book_no']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <select name="genre" required style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                        <option value="">-- Select Genre --</option>
                        <?php
                        $genres = ['Fiction', 'Non-Fiction', 'Science', 'Technology', 'History',
                                   'Biography', 'Mystery', 'Romance', 'Fantasy', 'Self-Help',
                                   'Education', 'Religion', 'Children', 'Comics', 'Other'];
                        foreach ($genres as $g):
                            $selected = ($edit_book && $edit_book['genre'] === $g) ? 'selected' : '';
                        ?>
                            <option value="<?= $g ?>" <?= $selected ?>><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Author</label>
                    <input type="text" name="author" required placeholder="Author name"
                        value="<?= $edit_book ? htmlspecialchars($edit_book['author']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantity" min="1" required placeholder="Number of copies"
                        value="<?= $edit_book ? $edit_book['quantity'] : '' ?>">
                </div>
            </div>
            <?php if ($edit_book): ?>
                <button type="submit" name="update_book" class="btn-primary" style="width:auto; padding:10px 24px;">Update Book</button>
                <a href="books.php" style="margin-left:12px; color:#888;">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_book" class="btn-primary" style="width:auto; padding:10px 24px;">Add Book</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- SEARCH -->
    <div style="margin-bottom:16px;">
        <form method="GET" style="display:flex; gap:10px;">
            <input type="text" name="search" placeholder="Search by title, book no, genre, author..."
                value="<?= htmlspecialchars($search) ?>"
                style="padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; width:350px;">
            <button type="submit" class="btn-primary" style="width:auto; padding:10px 20px;">Search</button>
            <?php if ($search): ?>
                <a href="books.php" style="padding:10px 16px; color:#888; text-decoration:none;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- BOOKS TABLE -->
    <div class="table-box">
        <h3 style="margin-bottom:16px;">All Books (<?= mysqli_num_rows($books) ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>Book No.</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Quantity</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($books) > 0): ?>
                    <?php while ($book = mysqli_fetch_assoc($books)): ?>
                    <tr>
                        <td><?= htmlspecialchars($book['book_no']) ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= htmlspecialchars($book['genre']) ?></td>
                        <td><?= $book['quantity'] ?></td>
                        <td>
                            <span style="
                                padding:3px 10px;
                                border-radius:20px;
                                font-size:12px;
                                background:<?= $book['available'] > 0 ? '#e8f5e9' : '#fdecea' ?>;
                                color:<?= $book['available'] > 0 ? '#2e7d32' : '#c0392b' ?>;">
                                <?= $book['available'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="books.php?edit=<?= $book['id'] ?>" class="btn-sm btn-edit">Edit</a>
                            <a href="books.php?delete=<?= $book['id'] ?>"
                               class="btn-sm btn-delete"
                               onclick="return confirm('Delete this book?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#888;">No books found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

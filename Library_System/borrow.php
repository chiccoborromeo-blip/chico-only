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

// ISSUE BOOK
if (isset($_POST['borrow_book'])) {
    $user_id     = (int)$_POST['user_id'];
    $book_id     = (int)$_POST['book_id'];
    $borrow_date = $_POST['borrow_date'];
    $due_date    = $_POST['due_date'];

    // Check if book is available
    $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE id = $book_id"));

    if ($book['available'] <= 0) {
        $error = "This book is not available!";
    } else {
        // Check if member already borrowed this book
        $check = mysqli_prepare($conn, "SELECT id FROM borrow_records WHERE user_id=? AND book_id=? AND status='borrowed'");
        mysqli_stmt_bind_param($check, "ii", $user_id, $book_id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "This member already borrowed this book!";
        } else {
            // Insert borrow record
            $sql  = "INSERT INTO borrow_records (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, 'borrowed')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiss", $user_id, $book_id, $borrow_date, $due_date);

            if (mysqli_stmt_execute($stmt)) {
                // Decrease available count
                mysqli_query($conn, "UPDATE books SET available = available - 1 WHERE id = $book_id");
                $success = "Book issued successfully!";
            } else {
                $error = "Failed to issue book.";
            }
        }
    }
}

// GET ALL MEMBERS
$members = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='user' ORDER BY name");

// GET ALL AVAILABLE BOOKS
$books = mysqli_query($conn, "SELECT * FROM books WHERE available > 0 ORDER BY title");

// GET BORROW RECORDS
$records = mysqli_query($conn, "
    SELECT br.id, u.name AS member, u.email, b.title AS book, 
           br.borrow_date, br.due_date, br.status
    FROM borrow_records br
    JOIN users u ON br.user_id = u.id
    JOIN books b ON br.book_id = b.id
    WHERE br.status = 'borrowed'
    ORDER BY br.borrow_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow — Library System</title>
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
    <h2 style="margin-bottom:20px;">Borrow Books</h2>

    <?php if ($success): ?>
        <div class="alert" style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <!-- BORROW FORM -->
    <div class="table-box" style="margin-bottom:24px;">
        <h3 style="margin-bottom:16px;">Issue a Book</h3>
        <form method="POST">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label>Select Member</label>
                    <select name="user_id" required style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                        <option value="">-- Select Member --</option>
                        <?php while ($member = mysqli_fetch_assoc($members)): ?>
                            <option value="<?= $member['id'] ?>">
                                <?= htmlspecialchars($member['name']) ?> (<?= htmlspecialchars($member['email']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Book</label>
                    <select name="book_id" required style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                        <option value="">-- Select Book --</option>
                        <?php while ($book = mysqli_fetch_assoc($books)): ?>
                            <option value="<?= $book['id'] ?>">
                                <?= htmlspecialchars($book['title']) ?> by <?= htmlspecialchars($book['author']) ?> (<?= $book['available'] ?> available)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Borrow Date</label>
                    <input type="date" name="borrow_date" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" name="due_date" required value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
                </div>
            </div>
            <button type="submit" name="borrow_book" class="btn-primary" style="width:auto; padding:10px 24px;">Issue Book</button>
        </form>
    </div>

    <!-- CURRENTLY BORROWED -->
    <div class="table-box">
        <h3 style="margin-bottom:16px;">Currently Borrowed Books</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>Email</th>
                    <th>Book</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($records) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($records)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['member']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['book']) ?></td>
                        <td><?= $row['borrow_date'] ?></td>
                        <td><?= $row['due_date'] ?></td>
                        <td>
                            <?php
                            $due = strtotime($row['due_date']);
                            $today = strtotime(date('Y-m-d'));
                            $overdue = $today > $due;
                            ?>
                            <span style="
                                padding:3px 10px;
                                border-radius:20px;
                                font-size:12px;
                                background:<?= $overdue ? '#fdecea' : '#fff3e0' ?>;
                                color:<?= $overdue ? '#c0392b' : '#e65100' ?>;">
                                <?= $overdue ? 'Overdue' : 'Borrowed' ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#888;">No borrowed books yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
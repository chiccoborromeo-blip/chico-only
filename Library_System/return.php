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

// PROCESS RETURN
if (isset($_POST['return_book'])) {
    $record_id  = (int)$_POST['record_id'];
    $return_date = $_POST['return_date'];

    // Get the record
    $record = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM borrow_records WHERE id = $record_id"));

    if ($record) {
        // Update borrow record
        $sql  = "UPDATE borrow_records SET status='returned', return_date=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $return_date, $record_id);

        if (mysqli_stmt_execute($stmt)) {
            // Increase available count
            mysqli_query($conn, "UPDATE books SET available = available + 1 WHERE id = " . $record['book_id']);
            $success = "Book returned successfully!";
        } else {
            $error = "Failed to process return.";
        }
    } else {
        $error = "Record not found!";
    }
}

// SEARCH BORROWED BOOKS
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search) {
    $sql  = "
        SELECT br.id, u.name AS member, u.email, b.title AS book,
               br.borrow_date, br.due_date, br.status
        FROM borrow_records br
        JOIN users u ON br.user_id = u.id
        JOIN books b ON br.book_id = b.id
        WHERE br.status = 'borrowed'
        AND (u.name LIKE ? OR b.title LIKE ? OR u.email LIKE ?)
        ORDER BY br.due_date ASC
    ";
    $stmt = mysqli_prepare($conn, $sql);
    $like = "%$search%";
    mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $records = mysqli_stmt_get_result($stmt);
} else {
    $records = mysqli_query($conn, "
        SELECT br.id, u.name AS member, u.email, b.title AS book,
               br.borrow_date, br.due_date, br.status
        FROM borrow_records br
        JOIN users u ON br.user_id = u.id
        JOIN books b ON br.book_id = b.id
        WHERE br.status = 'borrowed'
        ORDER BY br.due_date ASC
    ");
}

// GET RETURN HISTORY
$history = mysqli_query($conn, "
    SELECT br.id, u.name AS member, b.title AS book,
           br.borrow_date, br.due_date, br.return_date
    FROM borrow_records br
    JOIN users u ON br.user_id = u.id
    JOIN books b ON br.book_id = b.id
    WHERE br.status = 'returned'
    ORDER BY br.return_date DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return — Library System</title>
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
    <h2 style="margin-bottom:20px;">Return Books</h2>

    <?php if ($success): ?>
        <div class="alert" style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <!-- SEARCH -->
    <div style="margin-bottom:16px;">
        <form method="GET" style="display:flex; gap:10px;">
            <input type="text" name="search" placeholder="Search by member name, email or book title..."
                value="<?= htmlspecialchars($search) ?>"
                style="padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; width:350px;">
            <button type="submit" class="btn-primary" style="width:auto; padding:10px 20px;">Search</button>
            <?php if ($search): ?>
                <a href="return.php" style="padding:10px 16px; color:#888; text-decoration:none;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- BORROWED BOOKS TABLE -->
    <div class="table-box" style="margin-bottom:24px;">
        <h3 style="margin-bottom:16px;">Currently Borrowed Books</h3>
        <form method="POST">
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
                        <th>Return Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($records) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($records)): ?>
                        <?php
                            $due     = strtotime($row['due_date']);
                            $today   = strtotime(date('Y-m-d'));
                            $overdue = $today > $due;
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['member']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['book']) ?></td>
                            <td><?= $row['borrow_date'] ?></td>
                            <td><?= $row['due_date'] ?></td>
                            <td>
                                <span style="
                                    padding:3px 10px;
                                    border-radius:20px;
                                    font-size:12px;
                                    background:<?= $overdue ? '#fdecea' : '#fff3e0' ?>;
                                    color:<?= $overdue ? '#c0392b' : '#e65100' ?>;">
                                    <?= $overdue ? 'Overdue' : 'Borrowed' ?>
                                </span>
                            </td>
                            <td>
                                <input type="date" name="return_date" value="<?= date('Y-m-d') ?>"
                                    style="padding:6px 10px; border:1px solid #ddd; border-radius:6px; font-size:13px;">
                            </td>
                            <td>
                                <button type="submit" name="return_book" value="1"
                                    onclick="document.querySelector('input[name=record_id]').value=<?= $row['id'] ?>"
                                    class="btn-sm btn-green">
                                    Return
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center; color:#888;">No borrowed books found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <input type="hidden" name="record_id" value="">
        </form>
    </div>

    <!-- RETURN HISTORY -->
    <div class="table-box">
        <h3 style="margin-bottom:16px;">Return History (Last 10)</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($history) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($history)): ?>
                    <?php
                        $due        = strtotime($row['due_date']);
                        $returned   = strtotime($row['return_date']);
                        $late       = $returned > $due;
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['member']) ?></td>
                        <td><?= htmlspecialchars($row['book']) ?></td>
                        <td><?= $row['borrow_date'] ?></td>
                        <td><?= $row['due_date'] ?></td>
                        <td><?= $row['return_date'] ?></td>
                        <td>
                            <span style="
                                padding:3px 10px;
                                border-radius:20px;
                                font-size:12px;
                                background:<?= $late ? '#fdecea' : '#e8f5e9' ?>;
                                color:<?= $late ? '#c0392b' : '#2e7d32' ?>;">
                                <?= $late ? 'Returned Late' : 'On Time' ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#888;">No return history yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
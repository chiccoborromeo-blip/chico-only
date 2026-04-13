<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

/* =========================
   DASHBOARD STATISTICS
========================= */
$total_books    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM books"))['total'];
$total_members  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'"))['total'];
$total_borrow   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_records WHERE status='borrowed'"))['total'];
$total_return   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_records WHERE status='returned'"))['total'];
$total_pending  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE status='pending'"))['total'];

/* ⭐ NEW STAT: Pending Return Requests */
$total_return_pending = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM return_requests WHERE status='pending'")
)['total'];


/* =========================
   RECENT BORROW RECORDS
========================= */
$recent = mysqli_query($conn, "
    SELECT br.id, u.name AS member, b.title AS book, br.borrow_date, br.due_date, br.status
    FROM borrow_records br
    JOIN users u ON br.user_id = u.id
    JOIN books b ON br.book_id = b.id
    ORDER BY br.borrow_date DESC
    LIMIT 10
");

/* =========================
   RECENT BORROW REQUESTS
========================= */
$pending = mysqli_query($conn, "
    SELECT br.id, u.name AS member, b.title AS book, br.purpose, br.requested_at
    FROM borrow_requests br
    JOIN users u ON br.user_id = u.id
    JOIN books b ON br.book_id = b.id
    WHERE br.status = 'pending'
    ORDER BY br.requested_at ASC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — Library System</title>
<link rel="stylesheet" href="css/style.css">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
    <span style="font-weight:600; font-size:18px;">📚 Library System</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="members.php">Members</a>

        <a href="borrow_requests.php" style="color:#ffd54f;">
            Requests
            <?php if ($total_pending > 0): ?>
                <span style="background:#ff5252;color:white;border-radius:50%;padding:1px 6px;font-size:11px;margin-left:4px;">
                    <?= $total_pending ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- ⭐ NEW NAV BADGE FOR RETURN REQUESTS -->
        <a href="return_requests.php" style="color:#64b5f6;">
            Return Requests
            <?php if ($total_return_pending > 0): ?>
                <span style="background:#2196f3;color:white;border-radius:50%;padding:1px 6px;font-size:11px;margin-left:4px;">
                    <?= $total_return_pending ?>
                </span>
            <?php endif; ?>
        </a>

        <a href="borrow.php">Borrow</a>
        <a href="return.php">Return</a>
        <a href="logout.php" style="color:#ff6b6b;">Logout</a>
    </div>
</nav>


<div class="container">
    <h2 style="margin-bottom:4px;">Admin Dashboard</h2>
    <p style="color:#888; margin-bottom:24px;">
        Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!
    </p>

    <!-- =========================
         STAT CARDS
    ========================= -->
    <div class="cards">
        <div class="card">
            <h3><?= $total_books ?></h3>
            <p>Total Books</p>
        </div>

        <div class="card">
            <h3><?= $total_members ?></h3>
            <p>Total Members</p>
        </div>

        <div class="card">
            <h3><?= $total_borrow ?></h3>
            <p>Currently Borrowed</p>
        </div>

        <div class="card">
            <h3><?= $total_return ?></h3>
            <p>Returned Books</p>
        </div>

        <div class="card" style="border-top:4px solid #ff9800;">
            <h3 style="color:#ff9800;"><?= $total_pending ?></h3>
            <p>Pending Borrow Requests</p>
            <?php if ($total_pending > 0): ?>
                <a href="borrow_requests.php" style="font-size:12px;color:#ff9800;">View all →</a>
            <?php endif; ?>
        </div>

        <!-- ⭐ NEW CARD -->
        <div class="card" style="border-top:4px solid #1D9E75;">
            <h3 style="color:#1D9E75;"><?= $total_return_pending ?></h3>
            <p>Pending Returns</p>
            <?php if ($total_return_pending > 0): ?>
                <a href="return_requests.php" style="font-size:12px;color:#1D9E75;">View all →</a>
            <?php endif; ?>
        </div>
    </div>


    <!-- =========================
         PENDING BORROW REQUESTS
    ========================= -->
    <?php if ($total_pending > 0): ?>
    <div class="table-box" style="margin-bottom:24px;border-left:4px solid #ff9800;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3>⏳ Pending Borrow Requests</h3>
            <a href="borrow_requests.php" class="btn-primary" style="padding:8px 16px;font-size:13px;">View All</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Purpose</th>
                    <th>Requested</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($pending)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['member']) ?></td>
                    <td><?= htmlspecialchars($row['book']) ?></td>
                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                    <td><?= date('M d, Y', strtotime($row['requested_at'])) ?></td>
                    <td><a href="borrow_requests.php" class="btn-sm btn-green">Review</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>


    <!-- =========================
         RECENT BORROW RECORDS
    ========================= -->
    <div class="table-box">
        <h3 style="margin-bottom:16px;">Recent Borrow Records</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead
            <tbody>
                <?php if (mysqli_num_rows($recent) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($recent)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['member']) ?></td>
                        <td><?= htmlspecialchars($row['book']) ?></td>
                        <td><?= $row['borrow_date'] ?></td>
                        <td><?= $row['due_date'] ?></td>
                        <td><?= ucfirst($row['status']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;color:#888;">No records yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// SUBMIT RETURN REQUEST
if (isset($_POST['request_return'])) {
    $borrow_record_id = (int)$_POST['borrow_record_id'];
    $book_id          = (int)$_POST['book_id'];
    $reason           = trim($_POST['reason']);

    // Check if already has pending return request
    $check = mysqli_prepare($conn, "SELECT id FROM return_requests WHERE borrow_record_id=? AND status='pending'");
    mysqli_stmt_bind_param($check, "i", $borrow_record_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $error = "You already have a pending return request for this book!";
    } else {
        $sql  = "INSERT INTO return_requests (user_id, book_id, borrow_record_id, reason) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiis", $user_id, $book_id, $borrow_record_id, $reason);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Return request submitted! Please wait for admin approval.";
        } else {
            $error = "Failed to submit return request.";
        }
    }
}

// GET ALL BORROW RECORDS FOR THIS USER
$records = mysqli_query($conn, "
    SELECT br.id, b.id as book_id, b.title, b.author, b.category,
           br.borrow_date, br.due_date, br.return_date, br.status
    FROM borrow_records br
    JOIN books b ON br.book_id = b.id
    WHERE br.user_id = $user_id
    ORDER BY br.borrow_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowed Books — Library System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: white;
            border-radius: 16px;
            padding: 32px;
            width: 100%;
            max-width: 480px;
            position: relative;
        }
        .modal h3 { margin-bottom: 16px; color: #1a1a2e; }
        .modal-close {
            position: absolute;
            top: 16px; right: 20px;
            font-size: 22px;
            cursor: pointer;
            color: #888;
            background: none;
            border: none;
        }
        .book-info-card {
            background: #f8f9ff;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 16px;
            border-left: 4px solid #1D9E75;
        }
        .book-info-card p { font-size: 12px; color: #888; margin-bottom: 2px; }
        .book-info-card h4 { font-size: 16px; color: #1a1a2e; }
    </style>
</head>
<body>

<nav class="navbar">
    <span style="font-weight:600; font-size:18px;">📚 Library System</span>
    <div>
        <a href="user_dashboard.php">Home</a>
        <a href="brows_books.php">Browse Books</a>
        <a href="borrow_request.php">My Requests</a>
        <a href="my_borrowed.php">My Books</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php" style="color:#ff6b6b;">Logout</a>
    </div>
</nav>

<div class="container">
    <h2 style="margin-bottom:20px;">My Borrowed Books</h2>

    <?php if ($success): ?>
        <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($records) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($records)): ?>
                    <?php
                        $due     = strtotime($row['due_date']);
                        $today   = strtotime(date('Y-m-d'));
                        $overdue = $today > $due && $row['status'] === 'borrowed';

                        if ($row['status'] === 'returned') {
                            $bg = '#e8f5e9'; $color = '#2e7d32'; $label = 'Returned';
                        } elseif ($overdue) {
                            $bg = '#fdecea'; $color = '#c0392b'; $label = 'Overdue';
                        } else {
                            $bg = '#fff3e0'; $color = '#e65100'; $label = 'Borrowed';
                        }

                        // Check if already has pending return request
                        $chk = mysqli_fetch_assoc(mysqli_query($conn,
                            "SELECT id FROM return_requests WHERE borrow_record_id={$row['id']} AND status='pending'"));
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td><?= $row['borrow_date'] ?></td>
                        <td><?= $row['due_date'] ?></td>
                        <td><?= $row['return_date'] ?? '—' ?></td>
                        <td>
                            <span style="padding:3px 10px; border-radius:20px; font-size:12px; background:<?= $bg ?>; color:<?= $color ?>;">
                                <?= $label ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['status'] === 'borrowed'): ?>
                                <?php if ($chk): ?>
                                    <span style="font-size:12px; color:#ff9800;">⏳ Return Pending</span>
                                <?php else: ?>
                                    <button class="btn-sm btn-green"
                                        onclick="openReturnModal(
                                            <?= $row['id'] ?>,
                                            <?= $row['book_id'] ?>,
                                            '<?= addslashes($row['title']) ?>'
                                        )">
                                        Request Return
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="font-size:12px; color:#888;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center; color:#888;">
                            You have not borrowed any books yet.
                            <a href="brows_books.php">Browse books</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- RETURN REQUEST MODAL -->
<div class="modal-overlay" id="returnModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal()">✕</button>
        <h3>📤 Request Return</h3>

        <div class="book-info-card">
            <p>Book to Return</p>
            <h4 id="modal-title"></h4>
        </div>

        <form method="POST">
            <input type="hidden" name="borrow_record_id" id="modal-record-id">
            <input type="hidden" name="book_id" id="modal-book-id">

            <div class="form-group">
                <label>Reason / Condition Note</label>
                <input type="text" name="reason" required
                    placeholder="e.g. Book is in good condition, finished reading">
            </div>

            <div style="background:#fff3e0; border:1px solid #ffe0b2; border-radius:8px; padding:12px; margin-bottom:16px; font-size:13px; color:#e65100;">
                ⚠️ Your return request will be reviewed by the admin before it is processed.
            </div>

            <div style="display:flex; gap:12px;">
                <button type="submit" name="request_return" class="btn-primary" style="width:auto; padding:10px 24px;">
                    Submit Return Request
                </button>
                <button type="button" onclick="closeModal()"
                    style="padding:10px 20px; background:#f5f5f5; border:none; border-radius:8px; cursor:pointer; color:#555;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openReturnModal(recordId, bookId, title) {
    document.getElementById('modal-title').textContent    = title;
    document.getElementById('modal-record-id').value      = recordId;
    document.getElementById('modal-book-id').value        = bookId;
    document.getElementById('returnModal').classList.add('active');
}
function closeModal() {
    document.getElementById('returnModal').classList.remove('active');
}
document.getElementById('returnModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

</body>
</html>
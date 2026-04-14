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

// SUBMIT REQUEST
if (isset($_POST['submit_request'])) {
    $book_id     = (int)$_POST['book_id'];
    $book_no     = trim($_POST['book_no']);
    $purpose     = trim($_POST['purpose']);
    $borrow_date = $_POST['borrow_date'];
    $return_date = $_POST['return_date'];
    $agreed      = isset($_POST['agreed']) ? 1 : 0;

    if (!$agreed) {
        $error = "You must agree to the borrower's agreement!";
    } elseif (empty($purpose)) {
        $error = "Please state your purpose for borrowing.";
    } elseif ($return_date <= $borrow_date) {
        $error = "Return date must be after borrow date.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM borrow_requests WHERE user_id=? AND book_id=? AND status='pending'");
        mysqli_stmt_bind_param($check, "ii", $user_id, $book_id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "You already have a pending request for this book!";
        } else {
            $check2 = mysqli_prepare($conn, "SELECT id FROM borrow_records WHERE user_id=? AND book_id=? AND status='borrowed'");
            mysqli_stmt_bind_param($check2, "ii", $user_id, $book_id);
            mysqli_stmt_execute($check2);
            mysqli_stmt_store_result($check2);

            if (mysqli_stmt_num_rows($check2) > 0) {
                $error = "You already have this book borrowed!";
            } else {
                $sql  = "INSERT INTO borrow_requests (user_id, book_id, book_no, purpose, borrow_date, return_date, agreed) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iissssi", $user_id, $book_id, $book_no, $purpose, $borrow_date, $return_date, $agreed);
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Borrow request submitted successfully! Please wait for admin approval.";
                } else {
                    $error = "Failed to submit request.";
                }
            }
        }
    }
}

// GET ALL AVAILABLE BOOKS
$books = mysqli_query($conn, "SELECT * FROM books WHERE available > 0 ORDER BY title");

// GET USER'S REQUESTS
$requests = mysqli_query($conn, "
    SELECT br.id, b.title, b.book_no, b.author, br.purpose,
           br.borrow_date, br.return_date, br.status, br.admin_note, br.requested_at
    FROM borrow_requests br
    JOIN books b ON br.book_id = b.id
    WHERE br.user_id = $user_id
    ORDER BY br.requested_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Request — Library System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .agreement-box {
            background: #f8f9ff;
            border: 1px solid #c5cae9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.8;
            color: #444;
        }
        .agreement-box h4 {
            color: #3f51b5;
            margin-bottom: 10px;
            font-size: 15px;
        }
        .agreement-box ol {
            padding-left: 20px;
        }
        .agreement-box ol li {
            margin-bottom: 6px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
        }
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .status-pending  { background:#fff3e0; color:#e65100; padding:3px 10px; border-radius:20px; font-size:12px; }
        .status-approved { background:#e8f5e9; color:#2e7d32; padding:3px 10px; border-radius:20px; font-size:12px; }
        .status-rejected { background:#fdecea; color:#c0392b; padding:3px 10px; border-radius:20px; font-size:12px; }
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
        <a href="#" onclick="confirmLogout(); return false;" style="color:#ff6b6b;">Logout</a>
    </div>
</nav>

<div class="container">
    <h2 style="margin-bottom:20px;">Book Borrow Request</h2>

    <?php if ($success): ?>
        <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <!-- BORROW REQUEST FORM -->
    <div class="table-box" style="margin-bottom:24px;">
        <h3 style="margin-bottom:16px;">Fill Out Borrow Request Form</h3>
        <form method="POST">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

                <div class="form-group">
                    <label>Select Book</label>
                    <select name="book_id" id="book_select" required
                        style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;"
                        onchange="fillBookNo(this)">
                        <option value="">-- Select a Book --</option>
                        <?php while ($book = mysqli_fetch_assoc($books)): ?>
                            <option value="<?= $book['id'] ?>"
                                data-bookno="<?= htmlspecialchars($book['book_no'] ?? '') ?>"
                                data-author="<?= htmlspecialchars($book['author']) ?>">
                                <?= htmlspecialchars($book['title']) ?> — <?= htmlspecialchars($book['author']) ?>
                                (<?= $book['available'] ?> available)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Book No.</label>
                    <input type="text" name="book_no" id="book_no" required
                        placeholder="Auto-filled when book is selected"
                        style="background:#f5f5f5;" readonly>
                </div>

                <div class="form-group">
                    <label>Borrower Name</label>
                    <input type="text" value="<?= htmlspecialchars($_SESSION['user_name']) ?>"
                        disabled style="background:#f5f5f5; color:#888;">
                </div>

                <div class="form-group">
                    <label>Purpose of Borrowing</label>
                    <input type="text" name="purpose" required
                        placeholder="e.g. Research, Study, Personal reading"
                        value="<?= isset($_POST['purpose']) ? htmlspecialchars($_POST['purpose']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>Preferred Borrow Date</label>
                    <input type="date" name="borrow_date" required
                        value="<?= date('Y-m-d') ?>"
                        min="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label>Preferred Return Date</label>
                    <input type="date" name="return_date" required
                        value="<?= date('Y-m-d', strtotime('+14 days')) ?>"
                        min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>

            </div>

            <!-- BORROWER'S AGREEMENT -->
            <div class="agreement-box">
                <h4>📋 Borrower's Agreement</h4>
                <ol>
                    <li>I agree to take full responsibility for the borrowed book and return it in good condition.</li>
                    <li>I agree to return the book on or before the agreed return date.</li>
                    <li>I understand that failure to return the book on time may result in penalties or restrictions.</li>
                    <li>I agree not to lend the borrowed book to other persons.</li>
                    <li>I understand that lost or damaged books must be replaced or paid for.</li>
                    <li>I agree that the library reserves the right to recall the book at any time.</li>
                    <li>I confirm that all information I provided is true and correct.</li>
                </ol>
                <div class="checkbox-group">
                    <input type="checkbox" name="agreed" id="agreed" value="1">
                    <label for="agreed" style="cursor:pointer; font-weight:500; color:#3f51b5;">
                        I have read and agree to the Borrower's Agreement above.
                    </label>
                </div>
            </div>

            <button type="submit" name="submit_request" class="btn-primary"
                style="width:auto; padding:10px 28px;">
                Submit Borrow Request
            </button>
        </form>
    </div>

    <!-- MY REQUESTS -->
    <div class="table-box">
        <h3 style="margin-bottom:16px;">My Borrow Requests</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Book Title</th>
                    <th>Book No.</th>
                    <th>Purpose</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Requested</th>
                    <th>Status</th>
                    <th>Admin Note</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($requests) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($requests)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['book_no'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                        <td><?= $row['borrow_date'] ?></td>
                        <td><?= $row['return_date'] ?></td>
                        <td><?= date('M d, Y', strtotime($row['requested_at'])) ?></td>
                        <td>
                            <span class="status-<?= $row['status'] ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td style="color:#888; font-size:13px;">
                            <?= $row['admin_note'] ? htmlspecialchars($row['admin_note']) : '—' ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center; color:#888;">No requests yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function fillBookNo(select) {
    const option = select.options[select.selectedIndex];
    document.getElementById('book_no').value = option.dataset.bookno || '';
}
</script>

<?php require 'includes/toast.php'; ?>
</body>
</html>

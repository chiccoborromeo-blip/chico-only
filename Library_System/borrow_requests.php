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

// APPROVE REQUEST
if (isset($_POST['approve'])) {
    $request_id = (int)$_POST['request_id'];
    $admin_note = trim($_POST['admin_note']);

    // Get request details
    $req = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM borrow_requests WHERE id = $request_id"));

    if ($req) {
        // Check book availability
        $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE id = " . $req['book_id']));

        if ($book['available'] <= 0) {
            $error = "Book is no longer available!";
        } else {
            // Insert into borrow_records
            $sql  = "INSERT INTO borrow_records (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, 'borrowed')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiss", $req['user_id'], $req['book_id'], $req['borrow_date'], $req['return_date']);

            if (mysqli_stmt_execute($stmt)) {
                // Decrease available count
                mysqli_query($conn, "UPDATE books SET available = available - 1 WHERE id = " . $req['book_id']);

                // Update request status
                $upd  = "UPDATE borrow_requests SET status='approved', admin_note=? WHERE id=?";
                $stmt2 = mysqli_prepare($conn, $upd);
                mysqli_stmt_bind_param($stmt2, "si", $admin_note, $request_id);
                mysqli_stmt_execute($stmt2);

                $success = "Request approved and book issued successfully!";
            }
        }
    }
}

// REJECT REQUEST
if (isset($_POST['reject'])) {
    $request_id = (int)$_POST['request_id'];
    $admin_note = trim($_POST['admin_note']);

    $sql  = "UPDATE borrow_requests SET status='rejected', admin_note=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $admin_note, $request_id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Request rejected.";
    }
}

// GET PENDING REQUESTS
$pending = mysqli_query($conn, "
    SELECT br.id, u.name AS member, u.email, b.title AS book,
           b.book_no, b.author, br.purpose,
           br.borrow_date, br.return_date, br.requested_at
    FROM borrow_requests br
    JOIN users u ON br.user_id = u.id
    JOIN books b ON br.book_id = b.id
    WHERE br.status = 'pending'
    ORDER BY br.requested_at ASC
");

// GET ALL REQUESTS HISTORY
$history = mysqli_query($conn, "
    SELECT br.id, u.name AS member, b.title AS book,
           br.purpose, br.borrow_date, br.return_date,
           br.status, br.admin_note, br.requested_at
    FROM borrow_requests br
    JOIN users u ON br.user_id = u.id
    JOIN books b ON br.book_id = b.id
    WHERE br.status != 'pending'
    ORDER BY br.requested_at DESC
    LIMIT 20
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Requests — Library System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .status-pending  { background:#fff3e0; color:#e65100; }
        .status-approved { background:#e8f5e9; color:#2e7d32; }
        .status-rejected { background:#fdecea; color:#c0392b; }
        .request-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid #ff9800;
        }
        .request-card h4 {
            margin-bottom: 8px;
            color: #1a1a2e;
        }
        .request-info {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }
        .request-info-item p:first-child {
            font-size: 12px;
            color: #888;
            margin-bottom: 2px;
        }
        .request-info-item p:last-child {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }
        .action-form {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .action-form input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 13px;
            flex: 1;
            min-width: 200px;
        }
        .btn-approve {
            padding: 8px 20px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-approve:hover { background: #1b5e20; }
        .btn-reject {
            padding: 8px 20px;
            background: #c0392b;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-reject:hover { background: #96281b; }
    </style>
</head>
<body>

<nav class="navbar">
    <span style="font-weight:600; font-size:18px;">📚 Library System</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="members.php">Members</a>
        <a href="borrow_requests.php" style="color:#ffd54f;">Requests</a>
        <a href="borrow.php">Borrow</a>
        <a href="return.php">Return</a>
        <a href="logout.php" style="color:#ff6b6b;">Logout</a>
    </div>
</nav>

<div class="container">
    <h2 style="margin-bottom:20px;">Borrow Requests</h2>

    <?php if ($success): ?>
        <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; padding:10px 14px; border-radius:8px; margin-bottom:16px;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <!-- PENDING REQUESTS -->
    <h3 style="margin-bottom:16px;">
        Pending Requests
        <span style="background:#ff9800; color:white; padding:2px 10px; border-radius:20px; font-size:13px; margin-left:8px;">
            <?= mysqli_num_rows($pending) ?>
        </span>
    </h3>

    <?php if (mysqli_num_rows($pending) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($pending)): ?>
        <div class="request-card">
            <h4><?= htmlspecialchars($row['book']) ?> <span style="font-weight:400; color:#888; font-size:13px;">(<?= htmlspecialchars($row['book_no']) ?>)</span></h4>

            <div class="request-info">
                <div class="request-info-item">
                    <p>Member</p>
                    <p><?= htmlspecialchars($row['member']) ?></p>
                </div>
                <div class="request-info-item">
                    <p>Email</p>
                    <p><?= htmlspecialchars($row['email']) ?></p>
                </div>
                <div class="request-info-item">
                    <p>Author</p>
                    <p><?= htmlspecialchars($row['author']) ?></p>
                </div>
                <div class="request-info-item">
                    <p>Purpose</p>
                    <p><?= htmlspecialchars($row['purpose']) ?></p>
                </div>
                <div class="request-info-item">
                    <p>Borrow Date</p>
                    <p><?= $row['borrow_date'] ?></p>
                </div>
                <div class="request-info-item">
                    <p>Return Date</p>
                    <p><?= $row['return_date'] ?></p>
                </div>
                <div class="request-info-item">
                    <p>Requested On</p>
                    <p><?= date('M d, Y h:i A', strtotime($row['requested_at'])) ?></p>
                </div>
            </div>

            <form method="POST" class="action-form">
                <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                <input type="text" name="admin_note" placeholder="Add a note (optional)">
                <button type="submit" name="approve" class="btn-approve"
                    onclick="return confirm('Approve this request?')">
                    ✓ Approve
                </button>
                <button type="submit" name="reject" class="btn-reject"
                    onclick="return confirm('Reject this request?')">
                    ✗ Reject
                </button>
            </form>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="table-box" style="margin-bottom:24px;">
            <p style="text-align:center; color:#888; padding:20px;">No pending requests.</p>
        </div>
    <?php endif; ?>

    <!-- HISTORY -->
    <div class="table-box" style="margin-top:30px;">
        <h3 style="margin-bottom:16px;">Request History</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Purpose</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Admin Note</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($history) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($history)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['member']) ?></td>
                        <td><?= htmlspecialchars($row['book']) ?></td>
                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                        <td><?= $row['borrow_date'] ?></td>
                        <td><?= $row['return_date'] ?></td>
                        <td>
                            <span style="padding:3px 10px; border-radius:20px; font-size:12px;"
                                class="status-<?= $row['status'] ?>">
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
                        <td colspan="8" style="text-align:center; color:#888;">No history yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

// SEARCH
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search) {
    $sql  = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR genre LIKE ? ORDER BY title";
    $stmt = mysqli_prepare($conn, $sql);
    $like = "%$search%";
    mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $books = mysqli_stmt_get_result($stmt);
} else {
    $books = mysqli_query($conn, "SELECT * FROM books ORDER BY title");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books — Library System</title>
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
        .modal-overlay.active {
            display: flex;
        }
        .modal {
            background: white;
            border-radius: 16px;
            padding: 32px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }
        .modal h3 {
            margin-bottom: 20px;
            color: #1a1a2e;
            font-size: 20px;
        }
        .modal-close {
            position: absolute;
            top: 16px; right: 20px;
            font-size: 22px;
            cursor: pointer;
            color: #888;
            background: none;
            border: none;
        }
        .modal-close:hover { color: #333; }
        .book-info-card {
            background: #f8f9ff;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            border-left: 4px solid #5c6bc0;
        }
        .book-info-card p {
            font-size: 13px;
            color: #888;
            margin-bottom: 2px;
        }
        .book-info-card h4 {
            font-size: 18px;
            color: #1a1a2e;
            margin-bottom: 8px;
        }
        .book-info-card span {
            font-size: 13px;
            color: #555;
        }
        .agreement-box {
            background: #f8f9ff;
            border: 1px solid #c5cae9;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            font-size: 13px;
            line-height: 1.8;
            color: #444;
            max-height: 200px;
            overflow-y: auto;
        }
        .agreement-box h4 {
            color: #3f51b5;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .agreement-box ol {
            padding-left: 18px;
        }
        .agreement-box ol li {
            margin-bottom: 4px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
            margin-bottom: 16px;
        }
        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
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
    <h2 style="margin-bottom:20px;">Browse Books</h2>

    <!-- SEARCH -->
    <div style="margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:10px;">
            <input type="text" name="search" placeholder="Search by title, author, genre..."
                value="<?= htmlspecialchars($search) ?>"
                style="padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; width:350px;">
            <button type="submit" class="btn-primary" style="width:auto; padding:10px 20px;">Search</button>
            <?php if ($search): ?>
                <a href="brows_books.php" style="padding:10px 16px; color:#888; text-decoration:none;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- BOOKS TABLE -->
    <div class="table-box">
        <h3 style="margin-bottom:16px;">All Books (<?= mysqli_num_rows($books) ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Book No.</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Availability</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($books) > 0): ?>
                    <?php while ($book = mysqli_fetch_assoc($books)): ?>
                    <tr>
                        <td><?= $book['id'] ?></td>
                        <td><?= htmlspecialchars($book['book_no'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= htmlspecialchars($book['genre'] ?? '—') ?></td>
                        <td>
                            <span style="
                                padding:3px 10px;
                                border-radius:20px;
                                font-size:12px;
                                background:<?= $book['available'] > 0 ? '#e8f5e9' : '#fdecea' ?>;
                                color:<?= $book['available'] > 0 ? '#2e7d32' : '#c0392b' ?>;">
                                <?= $book['available'] > 0 ? $book['available'] . ' Available' : 'Not Available' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($book['available'] > 0): ?>
                                <button class="btn-sm btn-green"
                                    onclick="openBorrowModal(
                                        <?= $book['id'] ?>,
                                        '<?= addslashes($book['title']) ?>',
                                        '<?= addslashes($book['author']) ?>',
                                        '<?= addslashes($book['book_no'] ?? '') ?>',
                                        '<?= addslashes($book['genre'] ?? '') ?>',
                                        <?= $book['available'] ?>
                                    )">
                                    Borrow
                                </button>
                            <?php else: ?>
                                <button class="btn-sm" style="background:#eee; color:#aaa;" disabled>
                                    Not Available
                                </button>
                            <?php endif; ?>
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

<!-- BORROW MODAL -->
<div class="modal-overlay" id="borrowModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal()">✕</button>
        <h3>📋 Borrow Request Form</h3>

        <!-- Book Info -->
        <div class="book-info-card">
            <p>Selected Book</p>
            <h4 id="modal-title"></h4>
            <span>Author: <strong id="modal-author"></strong></span><br>
            <span>Book No: <strong id="modal-bookno"></strong></span><br>
            <span>Genre: <strong id="modal-genre"></strong></span><br>
            <span>Available: <strong id="modal-available"></strong> copies</span>
        </div>

        <form method="POST" action="borrow_request.php">
            <input type="hidden" name="book_id" id="modal-book-id">
            <input type="hidden" name="book_no" id="modal-book-no">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label>Borrower Name</label>
                    <input type="text" value="<?= htmlspecialchars($_SESSION['user_name']) ?>"
                        disabled style="background:#f5f5f5; color:#888;">
                </div>
                <div class="form-group">
                    <label>Purpose of Borrowing</label>
                    <input type="text" name="purpose" required
                        placeholder="e.g. Research, Study, Personal reading">
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

            <!-- Borrower's Agreement -->
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
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="agreed" id="agreed" value="1">
                <label for="agreed" style="cursor:pointer; font-weight:500; color:#3f51b5; font-size:14px;">
                    I have read and agree to the Borrower's Agreement above.
                </label>
            </div>

            <div style="display:flex; gap:12px;">
                <button type="submit" name="submit_request" class="btn-primary" style="width:auto; padding:10px 28px;">
                    Submit Request
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
function openBorrowModal(id, title, author, bookno, genre, available) {
    document.getElementById('modal-title').textContent     = title;
    document.getElementById('modal-author').textContent    = author;
    document.getElementById('modal-bookno').textContent    = bookno || '—';
    document.getElementById('modal-genre').textContent     = genre || '—';
    document.getElementById('modal-available').textContent = available;
    document.getElementById('modal-book-id').value         = id;
    document.getElementById('modal-book-no').value         = bookno || '';
    document.getElementById('borrowModal').classList.add('active');
    document.getElementById('agreed').checked = false;
}

function closeModal() {
    document.getElementById('borrowModal').classList.remove('active');
}

// Close modal when clicking outside
document.getElementById('borrowModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

</body>
</html>

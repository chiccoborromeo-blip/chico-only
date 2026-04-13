<?php
ini_set('session.save_path', '/Applications/XAMPP/xamppfiles/temp/');
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Count stats
$total_borrowed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_records WHERE user_id=$user_id"))['total'];
$total_returned = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_records WHERE user_id=$user_id AND status='returned'"))['total'];
$total_active   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_records WHERE user_id=$user_id AND status='borrowed'"))['total'];
$total_pending  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrow_requests WHERE user_id=$user_id AND status='pending'"))['total'];

// Currently borrowed
$records = mysqli_query($conn, "
    SELECT br.id, b.title, b.author, b.book_no, br.borrow_date, br.due_date
    FROM borrow_records br
    JOIN books b ON br.book_id = b.id
    WHERE br.user_id = $user_id AND br.status = 'borrowed'
    ORDER BY br.due_date ASC
");

// TRENDING BOOKS — most borrowed
$trending = mysqli_query($conn, "
    SELECT b.id, b.title, b.author, b.genre, b.available,
           COUNT(br.id) as borrow_count
    FROM books b
    LEFT JOIN borrow_records br ON b.id = br.book_id
    GROUP BY b.id
    ORDER BY borrow_count DESC, b.available DESC
    LIMIT 6
");

// USER'S FAVORITE GENRE — based on their borrow history
$fav_genre = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT b.genre, COUNT(*) as cnt
    FROM borrow_records br
    JOIN books b ON br.book_id = b.id
    WHERE br.user_id = $user_id AND b.genre IS NOT NULL AND b.genre != ''
    GROUP BY b.genre
    ORDER BY cnt DESC
    LIMIT 1
"));

// RECOMMENDED — books in same genre not yet borrowed by user
if ($fav_genre) {
    $genre = $fav_genre['genre'];
    $recommended = mysqli_query($conn, "
        SELECT b.id, b.title, b.author, b.genre, b.available
        FROM books b
        WHERE b.genre = '$genre'
        AND b.available > 0
        AND b.id NOT IN (
            SELECT book_id FROM borrow_records WHERE user_id = $user_id
        )
        LIMIT 6
    ");
} else {
    // No history yet — show available books
    $recommended = mysqli_query($conn, "
        SELECT b.id, b.title, b.author, b.genre, b.available
        FROM books b
        WHERE b.available > 0
        ORDER BY b.id DESC
        LIMIT 6
    ");
}

// Genre colors
function genreColor($genre) {
    $colors = [
        'Fiction'     => ['#e8eaf6', '#3f51b5'],
        'Non-Fiction' => ['#e0f2f1', '#00796b'],
        'Science'     => ['#e3f2fd', '#1565c0'],
        'Technology'  => ['#f3e5f5', '#7b1fa2'],
        'History'     => ['#fff3e0', '#e65100'],
        'Biography'   => ['#fce4ec', '#c2185b'],
        'Mystery'     => ['#efebe9', '#4e342e'],
        'Romance'     => ['#fce4ec', '#e91e63'],
        'Fantasy'     => ['#ede7f6', '#512da8'],
        'Self-Help'   => ['#e8f5e9', '#2e7d32'],
        'Education'   => ['#e3f2fd', '#0d47a1'],
        'Religion'    => ['#fff8e1', '#f57f17'],
        'Children'    => ['#f9fbe7', '#558b2f'],
        'Comics'      => ['#fbe9e7', '#bf360c'],
        'Other'       => ['#f5f5f5', '#616161'],
    ];
    return isset($colors[$genre]) ? $colors[$genre] : ['#f5f5f5', '#616161'];
}

function genreIcon($genre) {
    $icons = [
        'Fiction'     => '📖',
        'Non-Fiction' => '📰',
        'Science'     => '🔬',
        'Technology'  => '💻',
        'History'     => '🏛️',
        'Biography'   => '👤',
        'Mystery'     => '🔍',
        'Romance'     => '💕',
        'Fantasy'     => '🧙',
        'Self-Help'   => '💡',
        'Education'   => '🎓',
        'Religion'    => '🕌',
        'Children'    => '🧸',
        'Comics'      => '🦸',
        'Other'       => '📚',
    ];
    return isset($icons[$genre]) ? $icons[$genre] : '📚';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard — Library System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .book-card {
            border-radius: 12px;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 200px;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-decoration: none;
        }
        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .book-card .genre-icon {
            font-size: 32px;
            margin-bottom: 12px;
        }
        .book-card .book-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
            line-height: 1.4;
        }
        .book-card .book-author {
            font-size: 12px;
            opacity: 0.75;
            margin-bottom: 10px;
        }
        .book-card .book-genre-tag {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-block;
            font-weight: 500;
            opacity: 0.85;
        }
        .book-card .borrow-count {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 6px;
        }
        .book-card .availability {
            font-size: 11px;
            margin-top: 8px;
            font-weight: 500;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }
        .section-header h3 {
            font-size: 18px;
            color: #1a1a2e;
        }
        .section-header a {
            font-size: 13px;
            color: #5c6bc0;
            text-decoration: none;
        }
        .section-header a:hover { text-decoration: underline; }
        .section-subtitle {
            font-size: 13px;
            color: #888;
            margin-bottom: 4px;
        }
        .trend-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fff3e0;
            color: #e65100;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
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
    <h1 style="margin-bottom:4px;">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>! 👋</h1>
    <p style="color:#888; margin-bottom:24px;">Discover books and manage your library account.</p>

    <!-- Stats -->
    <div class="cards">
        <div class="card">
            <h3><?= $total_borrowed ?></h3>
            <p>Total Borrowed</p>
        </div>
        <div class="card">
            <h3><?= $total_active ?></h3>
            <p>Currently Borrowed</p>
        </div>
        <div class="card">
            <h3><?= $total_returned ?></h3>
            <p>Returned Books</p>
        </div>
        <div class="card" style="border-top:4px solid #ff9800;">
            <h3 style="color:#ff9800;"><?= $total_pending ?></h3>
            <p>Pending Requests</p>
            <?php if ($total_pending > 0): ?>
                <a href="borrow_request.php" style="font-size:12px; color:#ff9800;">View →</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- TRENDING BOOKS -->
    <div style="margin-bottom:36px;">
        <div class="section-header">
            <h3>🔥 Trending Books</h3>
            <a href="brows_books.php">See all books →</a>
        </div>
        <p class="section-subtitle">Most borrowed books in the library</p>

        <div class="book-grid">
            <?php
            $trend_count = 0;
            while ($book = mysqli_fetch_assoc($trending)):
                $colors = genreColor($book['genre']);
                $icon   = genreIcon($book['genre']);
                $trend_count++;
            ?>
            <a href="brows_books.php" class="book-card"
                style="background:<?= $colors[0] ?>; color:<?= $colors[1] ?>;">
                <div>
                    <div class="genre-icon"><?= $icon ?></div>
                    <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
                    <div class="book-author"><?= htmlspecialchars($book['author']) ?></div>
                </div>
                <div>
                    <?php if ($book['genre']): ?>
                        <span class="book-genre-tag"
                            style="background:<?= $colors[1] ?>22; color:<?= $colors[1] ?>;">
                            <?= htmlspecialchars($book['genre']) ?>
                        </span>
                    <?php endif; ?>
                    <div class="borrow-count">
                        🔥 <?= $book['borrow_count'] ?> times borrowed
                    </div>
                    <div class="availability" style="color:<?= $book['available'] > 0 ? '#2e7d32' : '#c0392b' ?>">
                        <?= $book['available'] > 0 ? '✓ ' . $book['available'] . ' available' : '✗ Not available' ?>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>

            <?php if ($trend_count === 0): ?>
                <p style="color:#888; grid-column:1/-1;">No books available yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- RECOMMENDED FOR YOU -->
    <div style="margin-bottom:36px;">
        <div class="section-header">
            <h3>⭐ Recommended For You</h3>
            <a href="brows_books.php">Browse more →</a>
        </div>
        <?php if ($fav_genre): ?>
            <p class="section-subtitle">
                Based on your interest in
                <strong><?= htmlspecialchars($fav_genre['genre']) ?></strong> books
            </p>
        <?php else: ?>
            <p class="section-subtitle">Explore our latest available books</p>
        <?php endif; ?>

        <div class="book-grid">
            <?php
            $rec_count = 0;
            while ($book = mysqli_fetch_assoc($recommended)):
                $colors = genreColor($book['genre']);
                $icon   = genreIcon($book['genre']);
                $rec_count++;
            ?>
            <a href="brows_books.php" class="book-card"
                style="background:<?= $colors[0] ?>; color:<?= $colors[1] ?>; border:2px solid <?= $colors[1] ?>22;">
                <div>
                    <div class="genre-icon"><?= $icon ?></div>
                    <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
                    <div class="book-author"><?= htmlspecialchars($book['author']) ?></div>
                </div>
                <div>
                    <?php if ($book['genre']): ?>
                        <span class="book-genre-tag"
                            style="background:<?= $colors[1] ?>22; color:<?= $colors[1] ?>;">
                            <?= htmlspecialchars($book['genre']) ?>
                        </span>
                    <?php endif; ?>
                    <div class="availability" style="color:<?= $book['available'] > 0 ? '#2e7d32' : '#c0392b' ?>; margin-top:8px;">
                        <?= $book['available'] > 0 ? '✓ ' . $book['available'] . ' available' : '✗ Not available' ?>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>

            <?php if ($rec_count === 0): ?>
                <p style="color:#888; grid-column:1/-1;">
                    No recommendations yet.
                    <a href="brows_books.php" style="color:#5c6bc0;">Browse books to get started!</a>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- CURRENTLY BORROWED -->
    <?php if ($total_active > 0): ?>
    <div class="table-box" style="margin-bottom:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3>📋 Currently Borrowed</h3>
            <a href="my_borrowed.php" style="font-size:13px; color:#5c6bc0; text-decoration:none;">View all →</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Book No.</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($records)): ?>
                <?php
                    $due     = strtotime($row['due_date']);
                    $today   = strtotime(date('Y-m-d'));
                    $overdue = $today > $due;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['book_no'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['author']) ?></td>
                    <td><?= $row['borrow_date'] ?></td>
                    <td><?= $row['due_date'] ?></td>
                    <td>
                        <span style="
                            padding:3px 10px;
                            border-radius:20px;
                            font-size:12px;
                            background:<?= $overdue ? '#fdecea' : '#fff3e0' ?>;
                            color:<?= $overdue ? '#c0392b' : '#e65100' ?>;">
                            <?= $overdue ? '⚠️ Overdue' : 'Borrowed' ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

</body>
</html>
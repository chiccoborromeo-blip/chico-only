<?php if (!empty($due_warnings)): ?>
<div style="margin-bottom: 24px;">
    <?php foreach ($due_warnings as $w): ?>

        <?php if ($w['level'] === 'overdue'): ?>
        <div style="background:#fdecea; border-left:5px solid #c0392b; border-radius:10px; padding:18px 24px; margin-bottom:12px; display:flex; gap:16px; align-items:flex-start;">
            <div style="font-size:28px;">🚨</div>
            <div style="flex:1;">
                <strong style="font-family:'Playfair Display',serif; color:#c0392b; font-size:15px; display:block; margin-bottom:4px;">Overdue Book — <?= $w['days_label'] ?>!</strong>
                <p style="font-family:'Crimson Pro',serif; font-size:14px; color:#4a3f35; margin-bottom:8px;">
                    <strong>"<?= htmlspecialchars($w['book_title']) ?>"</strong> was due on <strong><?= date('F d, Y', strtotime($w['due_date'])) ?></strong>. Please return it immediately.
                </p>
                <div style="background:#c0392b; color:#fff; border-radius:8px; padding:10px 14px; margin-top:8px; font-family:'Crimson Pro',serif; font-size:13px;">
                    🔒 <strong>Your borrowing privilege is currently suspended.</strong><br>
                    Return this book to the library to restore your account access.
                </div>
                <p style="font-family:'Crimson Pro',serif; font-size:12px; color:#c0392b; font-weight:600; margin-top:10px;">
                    ⏰  Penalty: ₱50 fine + 1 hour cleaning duty.
                </p>
            </div>
        </div>

        <?php elseif (!isset($_SESSION['is_banned']) || $_SESSION['is_banned'] != 1): ?>

            <?php if ($w['level'] === 'today'): ?>
            <div style="background:#fff3e0; border-left:5px solid #e65100; border-radius:10px; padding:18px 24px; margin-bottom:12px; display:flex; gap:16px; align-items:flex-start;">
                <div style="font-size:28px;">🔔</div>
                <div style="flex:1;">
                    <strong style="font-family:'Playfair Display',serif; color:#e65100; font-size:15px; display:block; margin-bottom:4px;">Book Due Today!</strong>
                    <p style="font-family:'Crimson Pro',serif; font-size:14px; color:#4a3f35; margin-bottom:8px;">
                        <strong>"<?= htmlspecialchars($w['book_title']) ?>"</strong> must be returned <strong>today</strong>. Please bring it before closing time.
                    </p>
                    <a href="my_borrowed.php" style="font-family:'Crimson Pro',serif; font-style:italic; font-size:13px; color:#e65100;">View my books →</a>
                </div>
            </div>

            <?php elseif ($w['level'] === 'tomorrow'): ?>
            <div style="background:#fff8e1; border-left:5px solid #ffa000; border-radius:10px; padding:18px 24px; margin-bottom:12px; display:flex; gap:16px; align-items:flex-start;">
                <div style="font-size:28px;">📅</div>
                <div style="flex:1;">
                    <strong style="font-family:'Playfair Display',serif; color:#ffa000; font-size:15px; display:block; margin-bottom:4px;">Reminder: Book Due Tomorrow</strong>
                    <p style="font-family:'Crimson Pro',serif; font-size:14px; color:#4a3f35; margin-bottom:8px;">
                        <strong>"<?= htmlspecialchars($w['book_title']) ?>"</strong> is due <strong>tomorrow, <?= date('F d, Y', strtotime($w['due_date'])) ?></strong>.
                    </p>
                    <a href="my_borrowed.php" style="font-family:'Crimson Pro',serif; font-style:italic; font-size:13px; color:#ffa000;">View my books →</a>
                </div>
            </div>

            <?php else: ?>
            <div style="background:#f5ede0; border-left:5px solid #a8863a; border-radius:10px; padding:18px 24px; margin-bottom:12px; display:flex; gap:16px; align-items:flex-start;">
                <div style="font-size:28px;">📌</div>
                <div style="flex:1;">
                    <strong style="font-family:'Playfair Display',serif; color:#a8863a; font-size:15px; display:block; margin-bottom:4px;">Upcoming Due Date — <?= $w['days_label'] ?></strong>
                    <p style="font-family:'Crimson Pro',serif; font-size:14px; color:#4a3f35; margin-bottom:8px;">
                        <strong>"<?= htmlspecialchars($w['book_title']) ?>"</strong> is due on <strong><?= date('F d, Y', strtotime($w['due_date'])) ?></strong>. Plan your return ahead of time.
                    </p>
                    <a href="my_borrowed.php" style="font-family:'Crimson Pro',serif; font-style:italic; font-size:13px; color:#a8863a;">View my books →</a>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

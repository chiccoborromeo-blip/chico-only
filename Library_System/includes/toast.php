<?php
// Show toast from session
if (isset($_SESSION['toast'])):
    $toast = $_SESSION['toast'];
    unset($_SESSION['toast']);
?>
<div class="toast-container" id="toastContainer">
    <div class="toast <?= $toast['type'] ?>" id="mainToast">
        <span class="toast-icon">
            <?php
            $icons = [
                'success' => '✅',
                'error'   => '❌',
                'warning' => '⚠️',
                'info'    => 'ℹ️',
            ];
            echo $icons[$toast['type']] ?? 'ℹ️';
            ?>
        </span>
        <span class="toast-msg"><?= htmlspecialchars($toast['message']) ?></span>
        <button class="toast-close" onclick="closeToast()">✕</button>
    </div>
</div>
<script>
    setTimeout(() => closeToast(), 3500);
    function closeToast() {
        const t = document.getElementById('mainToast');
        if (t) {
            t.classList.add('hide');
            setTimeout(() => t.remove(), 300);
        }
    }
</script>
<?php endif; ?>

<!-- Logout Confirmation Modal -->
<div class="confirm-overlay" id="logoutConfirm">
    <div class="confirm-box">
        <div class="confirm-icon">👋</div>
        <h3>Logging out?</h3>
        <p>Are you sure you want to logout from Library System?</p>
        <div class="confirm-buttons">
            <button class="btn-confirm-cancel" onclick="cancelLogout()">Cancel</button>
            <a href="logout.php" class="btn-confirm-logout">Yes, Logout</a>
        </div>
    </div>
</div>

<script>
function confirmLogout() {
    document.getElementById('logoutConfirm').classList.add('active');
}
function cancelLogout() {
    document.getElementById('logoutConfirm').classList.remove('active');
}
// Close when clicking outside
document.getElementById('logoutConfirm').addEventListener('click', function(e) {
    if (e.target === this) cancelLogout();
});
</script>
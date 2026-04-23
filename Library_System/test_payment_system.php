<?php
/**
 * test_payment_system.php
 * 
 * Test script to simulate overdue payments and test the punishment system
 * Run this after enabling the payment system via create_payments_table.php
 */

session_start();
require 'includes/db_connect.php';

// Check if payment tables exist
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'payments'");
$column_check = mysqli_query($conn, "SHOW COLUMNS FROM borrow_records LIKE 'fine_amount'");

if (mysqli_num_rows($table_check) == 0 || mysqli_num_rows($column_check) == 0) {
    die("<h2>Payment system not enabled!</h2><p>Please run <a href='includes/create_payments_table.php'>create_payments_table.php</a> first.</p>");
}

$success = "";
$error = "";

// Test 1: Create a test overdue payment
if (isset($_POST['create_test_payment'])) {
    $user_id = (int)$_POST['user_id'];
    $amount = (float)$_POST['amount'];
    $days_overdue = (int)$_POST['days_overdue'];
    
    // Get a borrow record for this user
    $borrow_record = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM borrow_records WHERE user_id = $user_id LIMIT 1"));
    
    if (!$borrow_record) {
        $error = "No borrow record found for this user. Please have them borrow a book first.";
    } else {
        $due_date = date('Y-m-d', strtotime("-$days_overdue days"));
        $payment_date = date('Y-m-d', strtotime("-$days_overdue days"));
        
        $stmt = mysqli_prepare($conn, "INSERT INTO payments (user_id, borrow_record_id, amount, payment_date, payment_method, status, due_date) VALUES (?, ?, ?, ?, 'cash', 'pending', ?)");
        mysqli_stmt_bind_param($stmt, "iidss", $user_id, $borrow_record['id'], $amount, $payment_date, $due_date);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Test payment created! Due date: $due_date ({$days_overdue} days overdue)";
        } else {
            $error = "Failed to create test payment.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Test 2: Create a test fine on a borrow record
if (isset($_POST['create_test_fine'])) {
    $user_id = (int)$_POST['user_id'];
    $fine_amount = (float)$_POST['fine_amount'];
    
    // Get a returned borrow record for this user
    $borrow_record = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM borrow_records WHERE user_id = $user_id AND status = 'returned' LIMIT 1"));
    
    if (!$borrow_record) {
        $error = "No returned borrow record found for this user. Please return a book first.";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE borrow_records SET fine_amount = ?, payment_status = 'unpaid' WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "di", $fine_amount, $borrow_record['id']);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Test fine of ₱$fine_amount assigned to borrow record #{$borrow_record['id']}";
        } else {
            $error = "Failed to assign test fine.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Test 3: Run punishment check manually
if (isset($_POST['run_punishment'])) {
    require 'includes/payment_punishment.php';
    $user_id = (int)$_POST['punishment_user_id'];
    checkPaymentPunishment($conn, $user_id);
    $success = "Punishment check run for user ID $user_id";
}

// Get all users for dropdown
$users = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role = 'user' ORDER BY name ASC");

// Get current payment status
$payments = mysqli_query($conn, "
    SELECT p.*, u.name AS member_name 
    FROM payments p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.due_date ASC
");

$fines = mysqli_query($conn, "
    SELECT br.*, u.name AS member_name 
    FROM borrow_records br 
    JOIN users u ON br.user_id = u.id 
    WHERE br.fine_amount > 0 
    ORDER BY br.return_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Payment System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #6b1a2a; padding-bottom: 10px; }
        h2 { color: #6b1a2a; margin-top: 30px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #6b1a2a; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #8b2438; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f0e8; }
        .overdue { background: #fdecea; }
        .status-pending { color: #e65100; }
        .status-overdue { color: #c0392b; }
        .status-completed { color: #2e7d32; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Payment System Test Tool</h1>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <p><strong>Instructions:</strong> Use this tool to create test payment records and fines to test the punishment system.</p>
        
        <h2>1. Create Test Overdue Payment</h2>
        <form method="POST">
            <div class="form-group">
                <label>Select User:</label>
                <select name="user_id" required>
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Payment Amount (₱):</label>
                <input type="number" name="amount" value="100" min="1" required>
            </div>
            <div class="form-group">
                <label>Days Overdue:</label>
                <input type="number" name="days_overdue" value="10" min="1" required>
                <small>Set to 8+ to test overdue status, 15+ to test ban, 31+ to test suspension</small>
            </div>
            <button type="submit" name="create_test_payment">Create Test Payment</button>
        </form>
        
        <h2>2. Create Test Fine</h2>
        <form method="POST">
            <div class="form-group">
                <label>Select User:</label>
                <select name="user_id" required>
                    <?php mysqli_data_seek($users, 0); while ($user = mysqli_fetch_assoc($users)): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Fine Amount (₱):</label>
                <input type="number" name="fine_amount" value="50" min="1" required>
            </div>
            <button type="submit" name="create_test_fine">Assign Test Fine</button>
        </form>
        
        <h2>3. Run Punishment Check</h2>
        <form method="POST">
            <div class="form-group">
                <label>Select User:</label>
                <select name="punishment_user_id" required>
                    <?php mysqli_data_seek($users, 0); while ($user = mysqli_fetch_assoc($users)): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="run_punishment">Run Punishment Check</button>
        </form>
        
        <h2>Current Payment Records</h2>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Days Overdue</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($payments) > 0): 
                    mysqli_data_seek($payments, 0);
                    while ($payment = mysqli_fetch_assoc($payments)):
                    $days_overdue = (strtotime(date('Y-m-d')) - strtotime($payment['due_date'])) / 86400;
                    $is_overdue = $days_overdue > 0;
                ?>
                <tr class="<?= $is_overdue ? 'overdue' : '' ?>">
                    <td><?= htmlspecialchars($payment['member_name']) ?></td>
                    <td>₱<?= number_format($payment['amount'], 2) ?></td>
                    <td><?= $payment['payment_date'] ?></td>
                    <td><?= $payment['due_date'] ?></td>
                    <td class="status-<?= $payment['status'] ?>"><?= ucfirst($payment['status']) ?></td>
                    <td><?= $is_overdue ? floor($days_overdue) . ' days' : 'On time' ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="6" style="text-align:center;">No payment records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <h2>Current Fines</h2>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Borrow Record ID</th>
                    <th>Fine Amount</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($fines) > 0): 
                    while ($fine = mysqli_fetch_assoc($fines)):
                ?>
                <tr>
                    <td><?= htmlspecialchars($fine['member_name']) ?></td>
                    <td><?= $fine['id'] ?></td>
                    <td>₱<?= number_format($fine['fine_amount'], 2) ?></td>
                    <td class="status-<?= $fine['payment_status'] ?>"><?= ucfirst($fine['payment_status']) ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="4" style="text-align:center;">No fines found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 30px; padding: 15px; background: #e8f5e9; border-radius: 5px;">
            <h3>📋 Punishment Rules:</h3>
            <ul>
                <li><strong>7+ days overdue:</strong> Status changes to "overdue"</li>
                <li><strong>7+ days overdue:</strong> Additional ₱10/day fine added</li>
                <li><strong>14+ days overdue:</strong> User gets banned</li>
                <li><strong>30+ days overdue:</strong> Account suspended</li>
            </ul>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="dashboard.php" style="display: inline-block; padding: 10px 20px; background: #333; color: white; text-decoration: none; border-radius: 5px;">← Back to Dashboard</a>
            <a href="payments.php" style="display: inline-block; padding: 10px 20px; background: #6b1a2a; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;">View Payments Page</a>
        </div>
    </div>
</body>
</html>

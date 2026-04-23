<?php
/**
 * setup_payment_demo.php
 * 
 * One-click setup script to create demo data for testing the payment system
 * This creates test borrow records, fines, and payments so you can test the payment pages
 */

session_start();
require 'includes/db_connect.php';

// Check if payment tables exist
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'payments'");
$column_check = mysqli_query($conn, "SHOW COLUMNS FROM borrow_records LIKE 'fine_amount'");

if (mysqli_num_rows($table_check) == 0 || mysqli_num_rows($column_check) == 0) {
    die("<h2>⚠️ Payment system not enabled!</h2>
        <p>Please run <a href='includes/create_payments_table.php'>create_payments_table.php</a> first.</p>
        <p><a href='setup_payment_demo.php'>Try again after enabling</a></p>");
}

$success = "";
$error = "";

// Setup demo data
if (isset($_POST['setup_demo'])) {
    // Get a test user (first user in database)
    $test_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, name, email FROM users WHERE role = 'user' LIMIT 1"));
    
    if (!$test_user) {
        $error = "No user found in database. Please register a user first.";
    } else {
        $user_id = $test_user['id'];
        $user_name = $test_user['name'];
        
        // Get a test book
        $test_book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, title FROM books LIMIT 1"));
        
        if (!$test_book) {
            $error = "No book found in database. Please add a book first.";
        } else {
            $book_id = $test_book['id'];
            $book_title = $test_book['title'];
            
            // 1. Create a returned book with fine (Scenario: Late return)
            $borrow_date = date('Y-m-d', strtotime('-15 days'));
            $due_date = date('Y-m-d', strtotime('-10 days'));
            $return_date = date('Y-m-d', strtotime('-5 days'));
            $fine_amount = 100; // ₱100 fine for late return
            
            $stmt = mysqli_prepare($conn, "INSERT INTO borrow_records (user_id, book_id, borrow_date, due_date, return_date, status, fine_amount, payment_status) VALUES (?, ?, ?, ?, ?, 'returned', ?, 'unpaid')");
            mysqli_stmt_bind_param($stmt, "iisssd", $user_id, $book_id, $borrow_date, $due_date, $return_date, $fine_amount);
            mysqli_stmt_execute($stmt);
            $borrow_record_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            // 2. Create a pending payment for this fine
            $payment_date = date('Y-m-d', strtotime('-3 days'));
            $due_date = date('Y-m-d', strtotime('+4 days'));
            $payment_amount = 50; // Partial payment
            
            $stmt = mysqli_prepare($conn, "INSERT INTO payments (user_id, borrow_record_id, amount, payment_date, payment_method, status, due_date) VALUES (?, ?, ?, ?, 'gcash', 'pending', ?)");
            mysqli_stmt_bind_param($stmt, "iidsd", $user_id, $borrow_record_id, $payment_amount, $payment_date, $due_date);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            // 3. Create another borrow record with different fine (Scenario: Very late return)
            $borrow_date2 = date('Y-m-d', strtotime('-25 days'));
            $due_date2 = date('Y-m-d', strtotime('-20 days'));
            $return_date2 = date('Y-m-d', strtotime('-15 days'));
            $fine_amount2 = 200; // ₱200 fine
            
            $stmt = mysqli_prepare($conn, "INSERT INTO borrow_records (user_id, book_id, borrow_date, due_date, return_date, status, fine_amount, payment_status) VALUES (?, ?, ?, ?, ?, 'returned', ?, 'unpaid')");
            mysqli_stmt_bind_param($stmt, "iisssd", $user_id, $book_id, $borrow_date2, $due_date2, $return_date2, $fine_amount2);
            mysqli_stmt_execute($stmt);
            $borrow_record_id2 = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            // 4. Create a completed payment for this
            $payment_date2 = date('Y-m-d', strtotime('-10 days'));
            $due_date2 = date('Y-m-d', strtotime('-3 days'));
            $payment_amount2 = 200; // Full payment
            
            $stmt = mysqli_prepare($conn, "INSERT INTO payments (user_id, borrow_record_id, amount, payment_date, payment_method, status, due_date) VALUES (?, ?, ?, ?, 'cash', 'completed', ?)");
            mysqli_stmt_bind_param($stmt, "iidsd", $user_id, $borrow_record_id2, $payment_amount2, $payment_date2, $due_date2);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            // Update the second record as paid
            mysqli_query($conn, "UPDATE borrow_records SET payment_status = 'paid' WHERE id = $borrow_record_id2");
            
            $success = "Demo data created successfully for user: <strong>$user_name</strong> ($test_user[email])<br><br>
                       Created 2 borrow records with fines:<br>
                       • Record #$borrow_record_id: ₱$fine_amount fine (unpaid) - with pending partial payment of ₱$payment_amount<br>
                       • Record #$borrow_record_id2: ₱$fine_amount2 fine (paid) - with completed payment of ₱$payment_amount2<br><br>
                       <strong>Next steps:</strong><br>
                       1. Login as user: <strong>$test_user[email]</strong><br>
                       2. Go to <a href='my_payments.php'>My Payments</a> to see outstanding fines and pay<br>
                       3. Login as admin to approve payments in <a href='payments.php'>Payments</a>";
        }
    }
}

// Clear demo data
if (isset($_POST['clear_demo'])) {
    // Delete test payments
    mysqli_query($conn, "DELETE FROM payments WHERE user_id IN (SELECT id FROM users WHERE role = 'user' LIMIT 1)");
    // Delete test borrow records with fines
    mysqli_query($conn, "DELETE FROM borrow_records WHERE fine_amount > 0 AND user_id IN (SELECT id FROM users WHERE role = 'user' LIMIT 1)");
    
    $success = "Demo data cleared successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Payment Demo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #6b1a2a; padding-bottom: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .info { background: #e8f4fd; color: #0c5460; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        button { background: #6b1a2a; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-right: 10px; }
        button:hover { background: #8b2438; }
        button.secondary { background: #666; }
        button.secondary:hover { background: #555; }
        .step { background: #f8f9fa; padding: 15px; border-left: 4px solid #6b1a2a; margin: 10px 0; }
        .step h3 { margin-top: 0; color: #6b1a2a; }
        a { color: #6b1a2a; text-decoration: none; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎭 Payment System Demo Setup</h1>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="info">
            <strong>What this does:</strong><br>
            Creates test data including borrow records with fines and payment records so you can test the payment system without needing to actually borrow and return books.
        </div>
        
        <form method="POST">
            <button type="submit" name="setup_demo">🚀 Setup Demo Data</button>
            <button type="submit" name="clear_demo" class="secondary">🗑️ Clear Demo Data</button>
        </form>
        
        <h2 style="margin-top: 30px;">📋 Testing Guide</h2>
        
        <div class="step">
            <h3>Step 1: Setup Demo Data</h3>
            <p>Click "Setup Demo Data" above to create test borrow records and payments.</p>
        </div>
        
        <div class="step">
            <h3>Step 2: Test User Payment Page</h3>
            <p>Login as the test user and go to <a href="my_payments.php" target="_blank">My Payments</a></p>
            <ul>
                <li>You'll see outstanding fines with "Pay Now" button</li>
                <li>Click "Pay Now" to open payment modal</li>
                <li>Select payment method (Cash, GCash, Bank Transfer)</li>
                <li>Submit payment - status becomes "pending"</li>
            </ul>
        </div>
        
        <div class="step">
            <h3>Step 3: Test Admin Payment Page</h3>
            <p>Login as admin and go to <a href="payments.php" target="_blank">Payments</a></p>
            <ul>
                <li>You'll see all payment records</li>
                <li>View outstanding fines from late returns</li>
                <li>Update payment status (pending → completed)</li>
                <li>Assign additional fines if needed</li>
            </ul>
        </div>
        
        <div class="step">
            <h3>Step 4: Test Punishment System</h3>
            <p>Go to <a href="test_payment_system.php" target="_blank">Test Payment System</a></p>
            <ul>
                <li>Create overdue payments with different days</li>
                <li>Run punishment check to see penalties</li>
                <li>Verify user gets banned if 14+ days overdue</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <a href="dashboard.php">← Back to Admin Dashboard</a>
            <a href="user_dashboard.php" style="margin-left: 20px;">Go to User Dashboard →</a>
        </div>
    </div>
</body>
</html>

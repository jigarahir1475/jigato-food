<?php
session_start();

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=wallet_add.php");
    exit;
}

include('config.php');

$user_id = $_SESSION['user_id'];

// ✅ Fetch Current Wallet Balance
$stmt = $conn->prepare("SELECT wallet_balance FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$current_balance = $stmt->get_result()->fetch_assoc()['wallet_balance'] ?? 0;

// ✅ Fetch Total Added Money (lifetime top-ups)
$sum_query = $conn->prepare("SELECT SUM(amount) AS total_added FROM wallet_history WHERE user_id = ? AND type = 'Add'");
$sum_query->bind_param("i", $user_id);
$sum_query->execute();
$total_added = $sum_query->get_result()->fetch_assoc()['total_added'] ?? 0;

// ✅ Handle Add Money Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = floatval($_POST['amount']);

    // ✅ Check total lifetime top-up limit
    if ($total_added >= 1500) {
        $error = "⚠ You have already reached your lifetime wallet deposit limit of ₹1500.";
    } elseif (($total_added + $amount) > 1500) {
        $remaining = 1500 - $total_added;
        $error = "⚠ You can only add ₹" . number_format($remaining, 2) . " more to reach the ₹1500 lifetime limit.";
    } elseif ($amount < 100) {
        $error = "⚠ Minimum deposit amount is ₹100.";
    } elseif ($amount > 1500) {
        $error = "⚠ Maximum deposit per transaction is ₹1500.";
    } else {
        // ✅ Update Wallet Balance
        $new_balance = $current_balance + $amount;
        $update = $conn->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
        $update->bind_param("di", $new_balance, $user_id);
        $update->execute();

        // ✅ Insert into wallet_history
        $desc = "Wallet Top-up (Added ₹" . number_format($amount, 2) . ")";
        $type = "Add";
        $insert = $conn->prepare("INSERT INTO wallet_history (user_id, type, amount, description, date) VALUES (?, ?, ?, ?, NOW())");
        $insert->bind_param("isds", $user_id, $type, $amount, $desc);
        $insert->execute();

        $_SESSION['wallet_message'] = "✅ ₹" . number_format($amount, 2) . " successfully added to your wallet!";
        header("Location: profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Money | Jigato Wallet</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #ffe9e9, #fff);
    font-family: 'Poppins', sans-serif;
    padding: 40px;
}
.container {
    background: #fff;
    width: 90%;
    max-width: 400px;
    margin: auto;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    text-align: center;
}
h2 { color: #ff4757; margin-bottom: 15px; }
input[type="number"] {
    width: 80%;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    margin-top: 10px;
    font-size: 16px;
}
button {
    margin-top: 20px;
    background: #ff4757;
    color: #fff;
    border: none;
    padding: 10px 25px;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
}
button:hover { background: #ff6b81; }
p { margin-top: 15px; color: #555; }
a { color: #ff4757; text-decoration: none; font-weight: 600; }
.success { color: green; font-weight: 600; }
.error { color: red; font-weight: 600; }
</style>
</head>
<body>
<div class="container">
    <h2>💰 Add Money to Wallet</h2>
    <p>Current Balance: ₹<?= number_format($current_balance, 2); ?></p>
    <p>Total Added (Lifetime): ₹<?= number_format($total_added, 2); ?> / ₹1500</p>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="number" name="amount" placeholder="Enter amount (₹100 - ₹1500)"
               min="100" max="1500" required
               <?= ($total_added >= 1500) ? 'disabled' : ''; ?>>
        <br>
        <button type="submit" <?= ($total_added >= 1500) ? 'disabled' : ''; ?>>Add Money</button>
    </form>

    <?php if ($total_added >= 1500): ?>
        <p class="error">⚠ You’ve reached your ₹1500 lifetime wallet top-up limit.</p>
    <?php endif; ?>

    <p><a href="profile.php">⬅ Back to Profile</a></p>
</div>
</body>
</html>

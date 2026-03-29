<?php
session_start();

include('config.php');

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user details
$stmt = $conn->prepare("SELECT name, phone, address, wallet_balance FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$wallet_balance = $user['wallet_balance'] ?? 0.00;

// ✅ Fetch cart items
$sql = "SELECT * FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: cart.php");
    exit;
}

$total = 0;
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

// ✅ Handle Checkout Form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $address = trim($_POST['address']);
    $payment = $_POST['payment'] ?? '';

    if (empty($address)) {
        $error = "Please enter your delivery address.";
    } elseif (empty($payment)) {
        $error = "Please select a payment method.";
    } else {
        // ✅ Save updated address
        $update = $conn->prepare("UPDATE users SET address = ? WHERE id = ?");
        $update->bind_param("si", $address, $user_id);
        $update->execute();

        // ✅ WALLET PAYMENT WITH 5% CASHBACK
        if ($payment === "Wallet") {
            $wallet_query = $conn->prepare("SELECT wallet_balance FROM users WHERE id = ?");
            $wallet_query->bind_param("i", $user_id);
            $wallet_query->execute();
            $wallet_result = $wallet_query->get_result()->fetch_assoc();
            $wallet_balance = $wallet_result['wallet_balance'];

            if ($wallet_balance < $total) {
                $error = "❌ Insufficient Wallet Balance! Please add funds or choose another payment method.";
            } else {
                $new_balance = $wallet_balance - $total;
                $update_wallet = $conn->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
                $update_wallet->bind_param("di", $new_balance, $user_id);
                $update_wallet->execute();

                // ✅ 5% Cashback
                $cashback = $total * 0.05;
                $final_balance = $new_balance + $cashback;
                $update_cashback = $conn->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
                $update_cashback->bind_param("di", $final_balance, $user_id);
                $update_cashback->execute();

                // ✅ Wallet History
                $desc_deduct = "Order Payment (₹$total)";
                $desc_cashback = "5% Cashback on Wallet Payment (₹" . number_format($cashback, 2) . ")";
                $insert_history = $conn->prepare("INSERT INTO wallet_history (user_id, type, amount, description) VALUES 
                    (?, 'Deduct', ?, ?), 
                    (?, 'Add', ?, ?)");
                $insert_history->bind_param("idssid", $user_id, $total, $desc_deduct, $user_id, $cashback, $desc_cashback);
                $insert_history->execute();

                $_SESSION['checkout_address'] = $address;
                $_SESSION['payment_method'] = $payment;
                $_SESSION['wallet_cashback'] = $cashback;
                header("Location: place_order.php");
                exit;
            }
        } elseif (in_array($payment, ['UPI', 'Card', 'COD'])) {
            $_SESSION['checkout_address'] = $address;
            $_SESSION['payment_method'] = $payment;
            header("Location: place_order.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jigato | Checkout</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #ffe9e9, #fff3e2, #f5faff);
    margin: 0;
    padding: 0;
}
.container {
    max-width: 900px;
    margin: 50px auto;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    padding: 30px 40px;
}
h1 {
    color: #ff4757;
    text-align: center;
}
.summary {
    background: #fff7f7;
    border-radius: 15px;
    padding: 15px;
    margin-bottom: 25px;
}
.summary table { width: 100%; border-collapse: collapse; }
.summary th, .summary td {
    text-align: left; padding: 10px; border-bottom: 1px solid #f3dcdc;
}
.summary th { color: #ff4757; }
.total { text-align: right; font-weight: 700; font-size: 18px; margin-top: 15px; }

textarea {
    width: 100%;
    height: 100px;
    padding: 12px;
    border-radius: 12px;
    border: 1px solid #ccc;
    font-size: 15px;
    margin-top: 8px;
    resize: none;
}
.method {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 10px;
    cursor: pointer;
    border: 2px solid transparent;
}
.method:hover {
    background: #fff0f0;
    border-color: #ff4757;
}
.wallet-info {
    display: none;
    background: #fff6e6;
    padding: 15px;
    border-radius: 10px;
    border: 1px dashed #ffc266;
    text-align: center;
    margin-bottom: 20px;
}
.qr-box {
    display: none;
    text-align: center;
    margin-top: 20px;
}
.qr-box #qrcode {
    margin: 0 auto;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(255,71,87,0.2);
}
.timer {
    margin-top: 10px;
    color: #ff4757;
    font-weight: 600;
}
#error-msg {
    display: none;
    background: #ffe0e0;
    color: #d00000;
    border: 1px solid #ffaaaa;
    border-radius: 10px;
    padding: 10px;
    text-align: center;
    font-weight: 600;
    margin-top: 15px;
    animation: fadeIn 0.6s ease;
}
button {
    background: linear-gradient(90deg, #ff4757, #ff8c69);
    border: none;
    color: white;
    padding: 15px 25px;
    border-radius: 30px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    margin-top: 30px;
    transition: 0.3s ease;
}
button:hover {
    background: linear-gradient(90deg, #ff6b81, #ff4757);
    transform: scale(1.03);
}
button:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}
</style>
</head>
<body>

<div class="container">
    <h1>Checkout 💳</h1>

    <?php if (!empty($error)): ?>
        <div style="color:red;text-align:center"><?= $error ?></div>
    <?php endif; ?>

    <div class="summary">
        <h3>🛒 Order Summary</h3>
        <table>
            <tr><th>Item</th><th>Qty</th><th>Price</th></tr>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="total">Total: ₹<?= number_format($total, 2) ?></div>
    </div>

    <form method="POST" action="">
        <h3>📍 Delivery Address</h3>
        <textarea name="address" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

        <h3>💳 Payment Method</h3>
        <div class="method"><input type="radio" name="payment" value="Wallet" onclick="showWallet()"> Wallet 💰</div>
        <div class="method"><input type="radio" name="payment" value="UPI" onclick="showQR()"> UPI / QR Code 📱</div>
        <div class="method"><input type="radio" name="payment" value="Card" onclick="hideAll()"> Card 💳</div>
        <div class="method"><input type="radio" name="payment" value="COD" onclick="hideAll()"> Cash on Delivery 💵</div>

        <div class="wallet-info" id="wallet-info">
            💼 Wallet Balance: ₹<?= number_format($wallet_balance, 2) ?><br>
            🎁 5% Cashback: ₹<?= number_format($total * 0.05, 2) ?>
        </div>

        <div class="qr-box" id="qr-box">
            <div id="qrcode"></div>
            <p>Scan & Pay to <strong>jigatofoog@upi</strong></p>
            <p>Amount: <strong>₹<?= number_format($total, 2) ?></strong></p>
            <div class="timer" id="timer">⏳ Payment time remaining: 03:00</div>
            <div id="error-msg">⚠️ Payment time out! Please choose another payment method.</div>
        </div>

        <button id="confirmBtn" type="submit">Confirm & Place Order 🛍️</button>
    </form>
</div>

<script>
let timerInterval;
function showWallet() {
    resetButton();
    document.getElementById('wallet-info').style.display = 'block';
    document.getElementById('qr-box').style.display = 'none';
    document.getElementById('error-msg').style.display = 'none';
    clearInterval(timerInterval);
}
function showQR() {
    resetButton();
    document.getElementById('wallet-info').style.display = 'none';
    document.getElementById('qr-box').style.display = 'block';
    document.getElementById('error-msg').style.display = 'none';
    document.getElementById('qrcode').innerHTML = "";
    const upiId = "jigatofoog@upi";
    const name = "Jigato";
    const amount = <?= json_encode($total) ?>;
    const upiLink = `upi://pay?pa=${upiId}&pn=${name}&am=${amount}&cu=INR&tn=Order%20Payment`;
    new QRCode(document.getElementById("qrcode"), { text: upiLink, width: 200, height: 200 });
    startTimer(3 * 60);
}
function hideAll() {
    resetButton();
    document.getElementById('wallet-info').style.display = 'none';
    document.getElementById('qr-box').style.display = 'none';
    document.getElementById('error-msg').style.display = 'none';
    clearInterval(timerInterval);
}
function resetButton() {
    const confirmBtn = document.getElementById('confirmBtn');
    confirmBtn.disabled = false;
    confirmBtn.textContent = "Confirm & Place Order 🛍️";
}
function startTimer(duration) {
    clearInterval(timerInterval);
    let timer = duration;
    const display = document.getElementById('timer');
    const errorMsg = document.getElementById('error-msg');
    const confirmBtn = document.getElementById('confirmBtn');
    timerInterval = setInterval(() => {
        let minutes = parseInt(timer / 60, 10);
        let seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        display.textContent = `⏳ Payment time remaining: ${minutes}:${seconds}`;
        if (--timer < 0) {
            clearInterval(timerInterval);
            display.textContent = "❌ Payment expired";
            errorMsg.style.display = 'block';
            document.getElementById('qrcode').innerHTML = '';
            confirmBtn.disabled = true;
            confirmBtn.textContent = "Payment Expired ❌";
        }
    }, 1000);
}
</script>
</body>
</html>

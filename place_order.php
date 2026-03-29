<?php
session_start();

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('config.php');

$user_id = $_SESSION['user_id'];
$address = $_SESSION['checkout_address'] ?? '';
$payment_method = $_SESSION['payment_method'] ?? '';
$wallet_deducted = $_SESSION['wallet_deducted'] ?? 0.00;

// ✅ Fetch Cart Items
$sql = "SELECT * FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h2 style='text-align:center;color:red;'>⚠️ Your cart is empty.</h2>";
    exit;
}

$total = 0;
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

// ✅ Create Order
$order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_date, status) VALUES (?, ?, NOW(), 'Pending')");
$order_stmt->bind_param("id", $user_id, $total);
$order_stmt->execute();
$order_id = $order_stmt->insert_id;

// ✅ Insert Order Items
$item_stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?)");
foreach ($items as $item) {
    $subtotal = $item['price'] * $item['quantity'];
    $item_stmt->bind_param("isdid", $order_id, $item['item_name'], $item['price'], $item['quantity'], $subtotal);
    $item_stmt->execute();
}

// ✅ Clear Cart
$conn->query("DELETE FROM cart WHERE user_id = $user_id");

// ✅ Clear checkout session data
unset($_SESSION['checkout_address'], $_SESSION['payment_method'], $_SESSION['wallet_deducted']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Success | Jigato</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e8fff1, #fff7f7);
    margin: 0;
    overflow-x: hidden;
    animation: fadeBg 2s ease-in-out;
}
@keyframes fadeBg {
    from { background-color: #fff; }
    to { background: linear-gradient(135deg, #e8fff1, #fff7f7); }
}

.container {
    max-width: 850px;
    margin: 80px auto;
    background: #fff;
    border-radius: 20px;
    padding: 45px 50px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    text-align: center;
    animation: fadeInUp 0.9s ease;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(60px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ✅ Green Tick Animation */
.success-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #28a7451a;
    border: 5px solid #28a745;
    margin-bottom: 20px;
    animation: popIn 0.8s ease forwards;
    position: relative;
}
@keyframes popIn {
    from { transform: scale(0.5); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.success-icon svg {
    width: 70px;
    height: 70px;
    stroke: #28a745;
    stroke-width: 5;
    fill: none;
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: draw 1.2s ease forwards 0.4s;
}
@keyframes draw {
    to { stroke-dashoffset: 0; }
}

h1 {
    color: #28a745;
    font-size: 26px;
    margin-top: 15px;
    animation: fadeInText 1.2s ease;
}
@keyframes fadeInText {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
p {
    font-size: 16px;
    color: #555;
    animation: fadeIn 1.4s ease;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* 📦 Order Details */
.order-summary {
    margin-top: 35px;
    text-align: left;
    animation: fadeSlide 1.6s ease;
}
@keyframes fadeSlide {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}
.order-summary h3 {
    color: #ff4757;
    margin-bottom: 10px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
th, td {
    border-bottom: 1px solid #eee;
    padding: 10px;
    text-align: left;
}
th {
    background: #ffeaea;
}
.total {
    text-align: right;
    font-weight: 700;
    font-size: 18px;
    margin-top: 12px;
    color: #333;
    animation: fadeIn 2s ease;
}

/* 🌈 Buttons */
.btn {
    display: inline-block;
    margin-top: 30px;
    padding: 12px 25px;
    background: linear-gradient(90deg, #ff4757, #ff4757);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    transition: 0.3s;
    box-shadow: 0 5px 15px rgba(40,167,69,0.3);
}
.btn:hover {
    transform: scale(1.05);
    background: linear-gradient(90deg, #33cc77, #28a745);
}

/* 💚 Small Fade */
.fade-delayed { animation-delay: 0.3s; animation-fill-mode: both; }
</style>
</head>
<body>

<div class="container">
    <div class="success-icon">
        <svg viewBox="0 0 52 52">
            <path d="M14 27l7 7 17-17"></path>
        </svg>
    </div>

    <h1>Order Placed Successfully!</h1>
    <p>Thank you for choosing <strong>Jigato</strong> 🍴<br>Your food will be delivered soon!</p>

    <div class="order-summary fade-delayed">
        <h3>🧾 Order Details</h3>
        <p><strong>Order ID:</strong> #<?= $order_id ?></p>
        <p><strong>Status:</strong> Pending</p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment_method) ?></p>
        <?php if ($payment_method === "Wallet"): ?>
            <p><strong>Wallet Deduction:</strong> ₹<?= number_format($wallet_deducted, 2) ?></p>
        <?php endif; ?>
        <p><strong>Order Date:</strong> <?= date("d M Y, h:i A") ?></p>
    </div>

    <h3 style="margin-top:25px;">🍕 Ordered Items</h3>
    <table>
        <tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['item_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₹<?= number_format($item['price'], 2) ?></td>
            <td>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="total">Total Paid: ₹<?= number_format($total, 2) ?></div>

    <a href="restaurants.php" class="btn">🍽️ Continue Ordering</a>
    <a href="index.php" class="btn">🏠 Back to Home</a>
</div>

</body>
</html>

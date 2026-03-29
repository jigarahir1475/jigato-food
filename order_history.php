<?php
session_start();

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ✅ InfinityFree Database Connection
$servername = "sql211.infinityfree.com";   // MySQL Hostname
$username   = "if0_40371874";              // MySQL Username
$password   = "4UDfCeEYgN";                // MySQL Password (from InfinityFree panel)
$database   = "if0_40371874_food_order";   // ✅ Your InfinityFree database name

$conn = new mysqli($servername, $username, $password, $database);

// ✅ Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch Orders
$query = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order History | Jigato</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
<style>
/* 🌈 Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(135deg, #fff5f5, #ffe9e9);
    padding: 40px 10px;
    overflow-x: hidden;
    animation: fadeInBody 1s ease-in-out;
}

@keyframes fadeInBody {
    from { opacity: 0; transform: scale(0.98); }
    to { opacity: 1; transform: scale(1); }
}

/* 🧾 Container */
.container {
    max-width: 900px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    animation: slideUp 0.8s ease;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ✨ Header */
h1 {
    text-align: center;
    color: #ff4757;
    font-size: 28px;
    margin-bottom: 25px;
    position: relative;
    animation: floatHeader 3s ease-in-out infinite;
}
@keyframes floatHeader {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

/* 📦 Order Card */
.order {
    border: 1px solid #ffe0e0;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 18px;
    background: #fffafa;
    box-shadow: 0 3px 12px rgba(255, 71, 87, 0.08);
    transition: all 0.3s ease;
    animation: fadeSlideUp 0.7s ease forwards;
}
.order:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(255, 71, 87, 0.15);
}
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.order h3 {
    margin-bottom: 8px;
    color: #333;
    font-size: 18px;
    animation: textPop 0.5s ease;
}
@keyframes textPop {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.order p {
    margin: 4px 0;
    color: #666;
    font-size: 14px;
}

/* 🍴 Items List */
.order ul {
    list-style: none;
    padding-left: 10px;
    margin-top: 10px;
    animation: fadeInItems 1s ease;
}
.order li {
    background: #fff2f2;
    margin: 5px 0;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 14px;
    color: #333;
    transition: 0.3s;
    opacity: 0;
    transform: translateX(-10px);
    animation: fadeInItem 0.6s ease forwards;
}
.order li:nth-child(1) { animation-delay: 0.2s; }
.order li:nth-child(2) { animation-delay: 0.3s; }
.order li:nth-child(3) { animation-delay: 0.4s; }
.order li:nth-child(4) { animation-delay: 0.5s; }

@keyframes fadeInItem {
    to { opacity: 1; transform: translateX(0); }
}

/* 🧭 Back Button */
.back-btn {
    display: block;
    text-align: center;
    margin: 30px auto 10px;
    background: #ff4757;
    color: white;
    padding: 12px 25px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    width: fit-content;
    transition: 0.3s ease;
    box-shadow: 0 3px 10px rgba(255, 71, 87, 0.3);
}
.back-btn:hover {
    background: #ff6b81;
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(255, 71, 87, 0.45);
}

/* 🌟 No Orders Message */
.no-orders {
    text-align: center;
    color: #777;
    font-size: 16px;
    animation: fadeIn 1s ease;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
</head>
<body>

<div class="container">
    <h1>🕓 Order History</h1>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="order">
                <h3>Order #<?= $order['id']; ?> — ₹<?= number_format($order['total_amount'],2); ?></h3>
                <p><strong>Status:</strong> <?= htmlspecialchars($order['status']); ?></p>
                <p><strong>Date:</strong> <?= date("d M Y, h:i A", strtotime($order['order_date'])); ?></p>
                <p><strong>Items:</strong></p>
                <ul>
                    <?php
                    $item_query = $conn->prepare("SELECT item_name, quantity FROM order_items WHERE order_id = ?");
                    $item_query->bind_param("i", $order['id']);
                    $item_query->execute();
                    $item_res = $item_query->get_result();
                    while ($item = $item_res->fetch_assoc()) {
                        echo "<li>🍽️ {$item['item_name']} × {$item['quantity']}</li>";
                    }
                    ?>
                </ul>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-orders">No past orders yet 😋</p>
    <?php endif; ?>

    <a href="profile.php" class="back-btn">⬅ Back to Profile</a>
</div>

</body>
</html>

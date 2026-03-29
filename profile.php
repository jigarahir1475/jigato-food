<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();


// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=profile.php");
    exit;
}

// ✅ InfinityFree Database Connection
$servername = "sql211.infinityfree.com";   // MySQL Hostname
$username   = "if0_40371874";              // MySQL Username
$password   = "4UDfCeEYgN";                // MySQL Password (from InfinityFree panel)
$database   = "if0_40371874_food_order";   // ✅ Your InfinityFree database name

$conn = new mysqli($servername, $username, $password, $database);

// ✅ Connection Check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Fetch user info
$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT id, name, phone, address, wallet_balance FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$user = $query->get_result()->fetch_assoc();

// ✅ Fetch orders
$order_query = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$order_query->bind_param("i", $user_id);
$order_query->execute();
$order_result = $order_query->get_result();

// ✅ Fetch wallet history
$wallet_query = $conn->prepare("SELECT * FROM wallet_history WHERE user_id = ? ORDER BY date DESC");
$wallet_query->bind_param("i", $user_id);
$wallet_query->execute();
$wallet_result = $wallet_query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($user['name']); ?> | Profile - Jigato</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
* {
    margin: 0; padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
    scroll-behavior: smooth;
}

/* 🌈 Background Animation */
body {
    background: linear-gradient(135deg, #ffe9e9, #fff);
    padding: 40px 10px;
    overflow-x: hidden;
    animation: fadeInBody 1.5s ease-in-out;
}
@keyframes fadeInBody {
    from { opacity: 0; transform: scale(0.98); }
    to { opacity: 1; transform: scale(1); }
}

/* ✨ Profile Card */
.profile-card {
    background: #fff;
    width: 95%;
    max-width: 900px;
    margin: auto;
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    animation: slideUp 0.8s ease-out;
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

/* 👤 Profile Header */
.profile-header {
    text-align: center;
    margin-bottom: 25px;
    animation: fadeIn 1s ease-in-out;
}
.profile-header img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #ff4757;
    margin-bottom: 10px;
    animation: float 3s ease-in-out infinite;
}
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}
.profile-header h2 { color: #ff4757; margin-bottom: 5px; }

/* ℹ️ Info Section */
.info {
    text-align: left;
    margin-top: 15px;
    background: #fff7f7;
    border-radius: 12px;
    padding: 15px;
    animation: fadeSlide 1s ease-in-out;
}
@keyframes fadeSlide {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}
.info p {
    font-size: 15px;
    margin-bottom: 8px;
    color: #333;
}

/* 💰 Wallet */
.wallet {
    margin-top: 25px;
    background: #ff4757;
    color: white;
    padding: 15px;
    border-radius: 12px;
    font-weight: bold;
    font-size: 18px;
    text-align: center;
    animation: bounceIn 1s ease;
}
@keyframes bounceIn {
    0% { transform: scale(0.5); opacity: 0; }
    60% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); }
}

/* 🎯 Buttons */
.btn {
    display: inline-block;
    margin-top: 25px;
    margin-right: 8px;
    padding: 10px 20px;
    background: #ff4757;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s ease;
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}
.btn:hover {
    background: #ff6b81;
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(255,71,87,0.4);
}
.logout-btn { background: #555; }
.logout-btn:hover { background: #333; transform: scale(1.05); }

/* 📦 Orders & Wallet */
.orders, .wallet-history {
    margin-top: 40px;
    animation: fadeIn 1.2s ease;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.orders h3, .wallet-history h3 {
    color: #ff4757;
    text-align: center;
    margin-bottom: 15px;
}

/* 🧾 Order Boxes */
.order-box {
    background: #fff7f7;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    animation: fadeSlideUp 0.6s ease forwards;
}
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(25px); }
    to { opacity: 1; transform: translateY(0); }
}
.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
}
.order-header span { color: #555; font-weight: normal; }

/* 💳 Wallet Table */
.wallet-table {
    width: 100%;
    border-collapse: collapse;
    animation: fadeSlideUp 0.8s ease;
}
.wallet-table th, .wallet-table td {
    padding: 10px;
    border-bottom: 1px solid #eee;
    text-align: left;
    font-size: 14px;
}
.wallet-table th {
    background: #ffeaea;
    color: #ff4757;
}
.wallet-table tr:hover {
    background: #fff2f2;
    transition: background 0.3s ease;
}
.credit { color: green; font-weight: 600; }
.debit { color: red; font-weight: 600; }

footer {
    text-align: center;
    font-size: 14px;
    color: #888;
    margin-top: 30px;
    animation: fadeIn 2s ease;
}

/* Toggle Animations */
.toggle-btn {
    cursor: pointer;
    color: #ff4757;
    font-weight: 600;
    font-size: 14px;
    transition: 0.3s ease;
}
.toggle-btn:hover { color: #ff6b81; }
.show { display: block !important; animation: expand 0.5s ease; }
@keyframes expand {
    from { opacity: 0; transform: scaleY(0); }
    to { opacity: 1; transform: scaleY(1); }
}
.order-items {
    display: none;
    margin-top: 10px;
    transform-origin: top;
}
.order-items table {
    width: 100%;
    border-collapse: collapse;
}
.order-items th, .order-items td {
    border-bottom: 1px solid #eee;
    padding: 8px;
    font-size: 14px;
}
.order-items th { background: #ffeaea; }
</style>
</head>
<body>

<div class="profile-card">
    <div class="profile-header">
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="User">
        <h2>👋 <?= htmlspecialchars($user['name']); ?></h2>
    </div>

    <div class="info">
        <p><strong>📞 Phone:</strong> <?= htmlspecialchars($user['phone']); ?></p>
        <p><strong>🏠 Address:</strong> <?= htmlspecialchars($user['address']); ?></p>
        <p><strong>🆔 User ID:</strong> <?= htmlspecialchars($user['id']); ?></p>
    </div>

    <div class="wallet">
        💰 Wallet Balance: ₹<?= number_format($user['wallet_balance'], 2); ?>
    </div>

    <div style="text-align:center;">
        <a href="wallet_add.php" class="btn">➕ Add Money</a>
        <a href="order_history.php" class="btn">🕓 Order History</a>
        <a href="index.php" class="btn">🏠 Home</a>
        <form method="post" action="logout.php" style="display:inline;">
            <button type="submit" class="btn logout-btn">🚪 Logout</button>
        </form>
    </div>

    <!-- 📦 My Orders -->
    <div class="orders">
        <h3>📦 My Orders</h3>
        <?php if ($order_result->num_rows > 0): ?>
            <?php while ($order = $order_result->fetch_assoc()): ?>
                <div class="order-box">
                    <div class="order-header">
                        <div>
                            🧾 <strong>Order #<?= $order['id']; ?></strong> — 
                            <span><?= date("d M Y, h:i A", strtotime($order['order_date'])); ?></span>
                        </div>
                        <div>
                            <span>Status: <?= htmlspecialchars($order['status']); ?></span> |
                            <span>Total: ₹<?= number_format($order['total_amount'], 2); ?></span>
                            <span class="toggle-btn" onclick="toggleItems(<?= $order['id']; ?>)">🔽 View Items</span>
                        </div>
                    </div>
                    <div class="order-items" id="items-<?= $order['id']; ?>">
                        <?php
                        $items_query = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
                        $items_query->bind_param("i", $order['id']);
                        $items_query->execute();
                        $items_result = $items_query->get_result();
                        ?>
                        <table>
                            <tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
                            <?php while ($item = $items_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['item_name']); ?></td>
                                    <td><?= $item['quantity']; ?></td>
                                    <td>₹<?= number_format($item['price'], 2); ?></td>
                                    <td>₹<?= number_format($item['subtotal'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;color:#666;">No orders yet 😋</p>
        <?php endif; ?>
    </div>

    <!-- 💰 Wallet History -->
    <div class="wallet-history">
        <h3>💳 Wallet Transaction History</h3>
        <?php if ($wallet_result->num_rows > 0): ?>
        <table class="wallet-table">
            <tr><th>Date</th><th>Type</th><th>Amount</th><th>Description</th></tr>
            <?php while ($row = $wallet_result->fetch_assoc()): ?>
                <tr>
                    <td><?= date("d M Y, h:i A", strtotime($row['date'])); ?></td>
                    <td><?= $row['type'] == 'Add' ? '➕ Added' : '➖ Deducted'; ?></td>
                    <td class="<?= $row['type'] == 'Add' ? 'credit' : 'debit'; ?>">₹<?= number_format($row['amount'], 2); ?></td>
                    <td>
                    <?php 
                        if (is_numeric($row['description']) && trim($row['description']) == "5") {
                            echo "💰 <strong style='color:#27ae60;'>Cashback</strong>";
                        } elseif (stripos($row['description'], 'cashback') !== false) {
                            echo "💰 <strong style='color:#27ae60;'>Cashback Received</strong>";
                        } else {
                            echo htmlspecialchars($row['description'] ?: '—');
                        }
                    ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <p style="text-align:center;color:#666;">No wallet transactions yet 💸</p>
        <?php endif; ?>
    </div>
</div>

<footer>&copy; <?= date("Y"); ?> Jigato | All Rights Reserved 🍴</footer>

<script>
function toggleItems(id) {
    document.getElementById('items-' + id).classList.toggle('show');
}
</script>
</body>
</html>

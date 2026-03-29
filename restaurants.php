<?php
session_start();

// ✅ InfinityFree Database Connection
$servername = "sql211.infinityfree.com";   // MySQL Hostname
$username   = "if0_40371874";              // MySQL Username
$password   = "4UDfCeEYgN";                // MySQL Password (from InfinityFree panel)
$database   = "if0_40371874_food_order";      // Replace _XXX with your actual database name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all restaurants
$result = $conn->query("SELECT * FROM restaurants ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Restaurants | Zotato</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: #f9f9f9;
    color: #333;
    overflow-x: hidden;
}

/* ✅ Navbar with underline hover animation */
.navbar {
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ff4757;
    padding: 15px 30px;
    color: #fff;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    z-index: 10;
}

.navbar .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 24px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

.navbar img {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    object-fit: cover;
}

/* ✅ Navigation links with underline animation */
.nav-links {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 25px;
}

.nav-links li a,
.nav-links li form button {
    position: relative;
    color: white;
    text-decoration: none;
    font-weight: 500;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    padding: 8px 0;
    transition: color 0.3s ease;
}

/* ✅ White underline animation */
.nav-links li a::after {
    content: "";
    position: absolute;
    bottom: -4px;
    left: 50%;
    width: 0;
    height: 2px;
    background: #fff;
    transform: translateX(-50%);
    transition: width 0.3s ease;
}

/* ✅ Hover effect (underline appears) */
.nav-links li a:hover::after {
    width: 100%;
}

/* ✅ Active page underline stays visible */
.nav-links li a.active::after {
    width: 100%;
}

/* ✅ Active color highlight */
.nav-links li a.active {
    font-weight: 600;
    color: #fff;
}

/* ✅ Hover fade effect */
.nav-links li a:hover,
.nav-links li form button:hover {
    opacity: 0.85;
}

/* ✅ Header Section */
.header {
    margin-top: 80px;
    background: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.6)),
                url('https://images.unsplash.com/photo-1601050690597-58a1a9ebf48f?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
    color: white;
    text-align: center;
    padding: 120px 20px;
}

.header h1 {
    font-size: 42px;
    margin-bottom: 15px;
}

.header p {
    font-size: 18px;
    opacity: 0.9;
}

/* ✅ Restaurant Cards */
.container {
    max-width: 1200px;
    margin: 60px auto;
    padding: 20px;
}

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

.card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.4s ease;
    cursor: pointer;
    animation: fadeIn 0.6s ease forwards;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(255,71,87,0.3);
}

.card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.card:hover img {
    transform: scale(1.08);
}

.card-content {
    padding: 18px 15px;
    text-align: center;
}

.card-content h3 {
    font-size: 20px;
    margin-bottom: 8px;
    color: #333;
}

.card-content p {
    font-size: 14px;
    color: #777;
    margin-bottom: 12px;
}

.view-btn {
    display: inline-block;
    background: #ff4757;
    color: white;
    text-decoration: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: background 0.3s, transform 0.3s;
}

.view-btn:hover {
    background: #ff6b81;
    transform: scale(1.05);
}

/* ✅ Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ✅ Footer */
footer {
    background-color: #ff4757;
    color: white;
    padding: 20px;
    text-align: center;
    font-size: 14px;
    margin-top: 50px;
    box-shadow: 0 -2px 6px rgba(0,0,0,0.2);
}

/* ✅ Responsive */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        gap: 10px;
    }
    .header h1 { font-size: 30px; }
    .header p { font-size: 15px; }
}
</style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar">
    <a href="index.php" class="logo">
        <img src="https://cdn-icons-png.flaticon.com/512/3595/3595455.png" alt="Zotato Logo">
        Jigato 🍴
    </a>

    <ul class="nav-links">
        <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Home</a></li>
        <li><a href="restaurants.php" class="<?= $current_page == 'restaurants.php' ? : '' ?>">Restaurants</a></li>
        <li><a href="cart.php" class="<?= $current_page == 'cart.php' ? 'active' : '' ?>">Cart 🛒</a></li>
        <?php if (isset($_SESSION["user_id"])): ?>
            <li><a href="profile.php">Hi, <?= htmlspecialchars($_SESSION["name"]); ?></a></li>
            <li>
                <form method="post" action="logout.php" style="display:inline;">
                    <button type="submit">Logout</button>
                </form>
            </li>
        <?php else: ?>
            <li><a href="login.php" class="<?= $current_page == 'login.php' ? 'active' : '' ?>">Login</a></li>
            <li><a href="register.php" class="<?= $current_page == 'register.php' ? 'active' : '' ?>">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- ✅ Header -->
<header class="header">
    <h1>Discover Top Restaurants 🍔</h1>
    <p>Explore local flavors, order quickly, and enjoy great food!</p>
</header>

<!-- ✅ Restaurant Cards -->
<div class="container">
    <div class="cards">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card" onclick="window.location.href='menu.php?restaurant=<?= urlencode($row['name']); ?>'">
                    <img src="<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['name']); ?>">
                    <div class="card-content">
                        <h3><?= htmlspecialchars($row['name']); ?></h3>
                        <p><?= htmlspecialchars($row['cuisine']); ?></p>
                        <a href="menu.php?restaurant=<?= urlencode($row['name']); ?>" class="view-btn">View Menu</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No restaurants found.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2025 Jigato | All Rights Reserved 🍴</p>
</footer>

</body>
</html>

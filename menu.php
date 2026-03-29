<?php
session_start();

include('config.php');

// ✅ Get Restaurant Name
$restaurant_name = $_GET['restaurant'] ?? '';
if (!$restaurant_name) {
    header("Location: restaurants.php");
    exit;
}

// ✅ Fetch Restaurant Info
$stmt = $conn->prepare("SELECT id, name, image FROM restaurants WHERE name = ?");
$stmt->bind_param("s", $restaurant_name);
$stmt->execute();
$restaurant_result = $stmt->get_result();

if ($restaurant_result->num_rows === 0) {
    die("Restaurant not found.");
}
$restaurant = $restaurant_result->fetch_assoc();
$restaurant_id = $restaurant['id'];

// ✅ Handle Add to Cart (new database)
$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_to_cart"])) {
    if (!isset($_SESSION["user_id"])) {
        $message = "Please log in to add items to your cart!";
        $message_type = "error";
    } else {
        $user_id = $_SESSION["user_id"];
        $item_name = $_POST["item_name"];
        $price = $_POST["price"];
        $image = $_POST["image"];

        // Check if the same item already exists for this user
        $check = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND item_name=?");
        $check->bind_param("is", $user_id, $item_name);
        $check->execute();
        $exists = $check->get_result();

        if ($exists->num_rows == 0) {
            // Insert full item info into new cart table
            $insert = $conn->prepare("INSERT INTO cart (user_id, item_name, price, image, quantity) VALUES (?, ?, ?, ?, 1)");
            $insert->bind_param("isds", $user_id, $item_name, $price, $image);
            $insert->execute();
            $message = "Item added to cart successfully!";
            $message_type = "success";
        } else {
            $message = "Item already exists in your cart!";
            $message_type = "error";
        }
    }
}

// ✅ Fetch Menu Items
$menu_stmt = $conn->prepare("SELECT * FROM menu WHERE restaurant_id = ?");
$menu_stmt->bind_param("i", $restaurant_id);
$menu_stmt->execute();
$menu_items = $menu_stmt->get_result();

// ✅ Get menu images for slideshow
$menu_images = [];
while ($row = $menu_items->fetch_assoc()) {
    if (!empty($row['image'])) {
        $menu_images[] = htmlspecialchars($row['image']);
    }
    $menu_data[] = $row;
}
// Reset pointer
$menu_stmt->execute();
$menu_items = $menu_stmt->get_result();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($restaurant['name']); ?> | Zotato Menu</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background: #f7f8fa; overflow-x: hidden; }

/* ✅ Navbar with underline hover effect */
.navbar {
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    background: #ff4757;
    display: flex; justify-content: space-between; align-items: center;
    padding: 15px 30px;
    color: #fff;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    z-index: 10;
}
.navbar .logo {
    display: flex; align-items: center; gap: 10px;
    font-size: 24px; font-weight: bold; color: #fff;
    text-decoration: none;
}
.navbar img { width: 38px; height: 38px; border-radius: 10px; object-fit: cover; }
.nav-links { list-style: none; display: flex; align-items: center; gap: 25px; }

.nav-links li a, .nav-links li form button {
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
.nav-links li a:hover::after { width: 100%; }
.nav-links li a.active::after { width: 100%; }
.nav-links li a.active { font-weight: 600; }
.nav-links li a:hover,
.nav-links li form button:hover { opacity: 0.85; }

/* ✅ Header with slideshow */
.header {
    margin-top: 80px;
    position: relative;
    height: 350px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #fff;
    overflow: hidden;
}
.bg-image {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-size: cover;
    background-position: center center;
    transition: opacity 1.5s ease-in-out;
    opacity: 0;
}
.bg-image.active { opacity: 1; }
.header::after {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1;
}
.header-content { position: relative; z-index: 2; }
.header h1 { font-size: 48px; margin-bottom: 10px; }
.header p { font-size: 20px; opacity: 0.9; }

/* ✅ Menu Cards */
.container { max-width: 1200px; margin: 60px auto; padding: 20px; }
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}
.card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(255,71,87,0.3);
}
.card img {
    width: 100%; height: 180px; object-fit: cover;
    transition: transform 0.3s ease;
}
.card:hover img { transform: scale(1.05); }
.card-content { padding: 15px; text-align: center; }
.card-content h3 { color: #333; font-size: 20px; margin-bottom: 6px; }
.card-content p { color: #777; font-size: 14px; margin-bottom: 8px; }
.price { font-weight: bold; color: #ff4757; margin-bottom: 12px; display: block; }
.add-btn {
    background: #ff4757;
    border: none; color: white;
    padding: 10px 18px;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.3s, transform 0.3s;
}
.add-btn:hover { background: #ff6b81; transform: scale(1.05); }

/* ✅ Popup Animation */
.popup {
    position: fixed;
    top: 0;
    left: 50%;
    transform: translateX(-50%) translateY(-100%);
    color: #fff;
    padding: 16px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    opacity: 0;
    z-index: 2000;
    animation: dropDown 0.5s ease forwards, returnUp 0.5s ease 1.5s forwards;
}
.popup.success { background: #2ed573; }
.popup.error { background: #ff4757; }
@keyframes dropDown {
    0% { opacity: 0; transform: translateX(-50%) translateY(-100%); }
    100% { opacity: 1; transform: translateX(-50%) translateY(60px); }
}
@keyframes returnUp {
    0% { opacity: 1; transform: translateX(-50%) translateY(60px); }
    100% { opacity: 0; transform: translateX(-50%) translateY(-100%); visibility: hidden; }
}

/* ✅ Footer */
footer {
    background-color: #ff4757;
    color: white;
    padding: 20px;
    text-align: center;
    font-size: 14px;
    margin-top: 60px;
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
        <li><a href="restaurants.php" class="<?= $current_page == 'restaurants.php' ? 'active' : '' ?>">Restaurants</a></li>
        <li><a href="cart.php" class="<?= $current_page == 'cart.php' ? 'active' : '' ?>">Cart 🛒</a></li>
        <?php if (isset($_SESSION["user_id"])): ?>
            <li><a href="profil.php">Hi, <?= htmlspecialchars($_SESSION["name"]); ?></a></li>
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

<!-- ✅ Header with slideshow -->
<header class="header">
    <?php foreach ($menu_images as $i => $img): ?>
        <div class="bg-image <?= $i === 0 ? 'active' : ''; ?>" style="background-image:url('<?= $img; ?>')"></div>
    <?php endforeach; ?>
    <div class="header-content">
        <h1><?= htmlspecialchars($restaurant['name']); ?> Menu 🍽️</h1>
        <p>Discover delicious dishes made just for you!</p>
    </div>
</header>

<!-- ✅ Popup -->
<?php if ($message): ?>
    <div class="popup <?= $message_type; ?>"><?= htmlspecialchars($message); ?></div>
<?php endif; ?>

<!-- ✅ Menu Items -->
<div class="container">
    <div class="cards">
        <?php if ($menu_items->num_rows > 0): ?>
            <?php while ($item = $menu_items->fetch_assoc()): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                    <div class="card-content">
                        <h3><?= htmlspecialchars($item['name']); ?></h3>
                        <p><?= htmlspecialchars($item['description']); ?></p>
                        <span class="price">₹<?= htmlspecialchars($item['price']); ?></span>
                        <form method="post">
                            <input type="hidden" name="item_name" value="<?= htmlspecialchars($item['name']); ?>">
                            <input type="hidden" name="price" value="<?= htmlspecialchars($item['price']); ?>">
                            <input type="hidden" name="image" value="<?= htmlspecialchars($item['image']); ?>">
                            <button type="submit" name="add_to_cart" class="add-btn">Add to Cart 🛒</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No items found for this restaurant.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2025 Jigato | All Rights Reserved 🍴</p>
</footer>

<!-- ✅ Slideshow -->
<script>
const images = document.querySelectorAll('.bg-image');
let current = 0;
if (images.length > 1) {
    setInterval(() => {
        images[current].classList.remove('active');
        current = (current + 1) % images.length;
        images[current].classList.add('active');
    }, 4000);
}
</script>

</body>
</html>

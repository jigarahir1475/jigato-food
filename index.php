<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jigato | Smart Food Ordering</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background-color: #fff;
        overflow-x: hidden;
    }

    /* Navbar */
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
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
    }

    .nav-links {
        list-style: none;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .nav-links li a, .nav-links li form button {
        position: relative;
        color: white;
        text-decoration: none;
        font-weight: 500;
        padding: 8px 0;
        transition: color 0.3s ease;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }

    .nav-links li a::after {
        content: "";
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 0%;
        height: 2px;
        background: white;
        transition: width 0.3s ease;
    }

    .nav-links li a:hover::after {
        width: 100%;
    }

    .nav-links li a:hover,
    .nav-links li form button:hover {
        color: #fff;
        opacity: 0.8;
    }

    /* Hero Section */
    .hero {
        margin-top: 80px;
        text-align: center;
        padding: 150px 20px;
        background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.5)),
                    url('https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
        color: white;
    }

    .hero h1 {
        font-size: 48px;
        margin-bottom: 10px;
    }

    .hero p {
        font-size: 18px;
        margin-bottom: 30px;
    }

    .search-bar-wrapper {
        position: relative;
        width: 80%;
        max-width: 450px;
        margin: auto;
    }

    .search-bar {
        width: 100%;
        padding: 14px 50px 14px 20px;
        border-radius: 50px;
        border: none;
        font-size: 16px;
        outline: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: box-shadow 0.3s ease;
    }

    .search-bar:focus {
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #ff4757;
    }

    section {
        padding: 60px 20px;
        text-align: center;
    }

    section h2 {
        font-size: 30px;
        margin-bottom: 25px;
        color: #333;
    }

    .cards {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 25px;
    }

    .card {
        background: white;
        padding: 15px;
        border-radius: 12px;
        width: 220px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 18px rgba(255,71,87,0.3);
    }

    .card img {
        width: 100%;
        border-radius: 10px;
        margin-bottom: 10px;
    }

    footer {
        background-color: #ff4757;
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 14px;
        box-shadow: 0 -2px 6px rgba(0,0,0,0.2);
    }

    /* ✅ Toast Notification */
    .toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #28a745;
        color: white;
        padding: 14px 25px;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }

    .toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .cards {
            flex-direction: column;
            align-items: center;
        }
        .navbar {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <a href="index.php" class="logo">
        <img src="https://cdn-icons-png.flaticon.com/512/3595/3595455.png" alt="Zotato Logo">
        Jigato 🍴
    </a>

    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="restaurants.php">Restaurants</a></li>
        <li><a href="cart.php">Cart 🛒</a></li>

        <?php if (isset($_SESSION["user_id"])): ?>
            <li><a href="profile.php">Welcome, <?= htmlspecialchars($_SESSION["name"]); ?></a></li>
            <li>
                <form method="post" action="logout.php" style="display:inline;">
                    <button type="submit">Logout</button>
                </form>
            </li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Hero Section -->
<header class="hero">
    <h1>Delicious Food Delivered To You</h1>
    <p>Fresh, Fast & Flavorful – order anytime, anywhere!</p>
    <div class="search-bar-wrapper">
        <input type="text" id="search" placeholder="Search restaurants or dishes..." class="search-bar">
        <span class="search-icon">🔍</span>
    </div>
</header>

<!-- Featured Restaurants -->
<section>
    <h2>Top Restaurants</h2>
    <div class="cards">
        <div class="card">
            <img src="https://whereyoueat.com/r_gallery_images/rgallery-25504/Pizza_Place_1________.JPG" alt="Pizza Place">
            <h3>Pizza Place</h3>
            <p>★★★★☆</p>
        </div>
        <div class="card">
            <img src="https://mir-s3-cdn-cf.behance.net/project_modules/max_1200/d45ba721370803.5630015f157ce.jpg" alt="Burger House">
            <h3>Burger House</h3>
            <p>★★★★☆</p>
        </div>
        <div class="card">
            <img src="https://b.zmtcdn.com/data/pictures/7/16716137/a5d5755dab1b29fa9856e341d4278f31_featured_v2.jpg" alt="Sushi World">
            <h3>Sushi World</h3>
            <p>★★★★★</p>
        </div>
    </div>
</section>

<!-- Featured Dishes -->
<section>
    <h2>Popular Dishes</h2>
    <div class="cards">
        <div class="card">
            <img src="https://tse3.mm.bing.net/th/id/OIP.T03aJagv5g4JwoG0Di4gFwHaEb" alt="Pizza">
            <h3>Margherita Pizza</h3>
            <p>$10</p>
        </div>
        <div class="card">
            <img src="https://tse4.mm.bing.net/th/id/OIP.KomAB7rg-OCyK3QEDO81fwHaEn" alt="Burger">
            <h3>Cheese Burger</h3>
            <p>$8</p>
        </div>
        <div class="card">
            <img src="https://tse2.mm.bing.net/th/id/OIP.C0cY3LIm-wvn9fYDjyJUQAHaEK" alt="Sushi">
            <h3>Sushi Platter</h3>
            <p>$15</p>
        </div>
    </div>
</section>

<footer>
    <p>&copy; 2025 Jigato | All Rights Reserved 🍴</p>
</footer>

<!-- ✅ Toast Notification -->
<div id="toast" class="toast">✅ You have successfully logged out!</div>

<script>
// 🔍 Search filter
document.getElementById('search').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        card.style.display = title.includes(filter) ? 'block' : 'none';
    });
});

// ✅ Show logout success toast
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('logout') === 'success') {
    const toast = document.getElementById('toast');
    toast.classList.add('show');
    setTimeout(() => { toast.classList.remove('show'); }, 3000);
}
</script>

</body>
</html>

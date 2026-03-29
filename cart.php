<?php
session_start();

include('config.php');


// ✅ Handle Remove Item (AJAX)
if (isset($_POST['remove_id'])) {
    $id = intval($_POST['remove_id']);
    $conn->query("DELETE FROM cart WHERE id = $id");
    echo "removed";
    exit();
}

// ✅ Handle Quantity Update (AJAX)
if (isset($_POST['update_id']) && isset($_POST['quantity'])) {
    $id = intval($_POST['update_id']);
    $qty = max(1, intval($_POST['quantity']));
    $conn->query("UPDATE cart SET quantity = $qty WHERE id = $id");
    echo "updated";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Cart | Jigato</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background: #f9f9f9; color: #333; min-height: 100vh; }

/* ✅ Navbar */
.navbar {
    background: #ff4757;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 40px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    position: sticky;
    top: 0;
    z-index: 1000;
}

/* ✅ Logo */
.navbar .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: white;
    font-size: 1.4rem;
    font-weight: 700;
}
.navbar .logo img {
    width: 42px;
    height: 42px;
    border-radius: 50%;
}

/* ✅ Menu Links */
.navbar ul {
    list-style: none;
    display: flex;
    gap: 22px;
    align-items: center;
}
.navbar ul li a {
    color: white;
    text-decoration: none;
    font-weight: 600;
    position: relative;
    padding: 6px 0;
}
.navbar ul li a::after {
    content: "";
    position: absolute;
    bottom: -4px;
    left: 50%;
    width: 0;
    height: 2px;
    background: white;
    transform: translateX(-50%);
    transition: width 0.3s ease;
}
.navbar ul li a:hover::after, .navbar ul li a.active::after {
    width: 100%;
}

/* ✅ Cart Table */
.container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 20px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
h1 { text-align: center; color: #ff4757; margin-bottom: 25px; }

table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
th { background: #ff4757; color: white; }
td img { width: 70px; height: 70px; border-radius: 10px; object-fit: cover; }

.qty-btn {
    background: #ff4757; color: white; border: none;
    padding: 4px 10px; border-radius: 50%; font-size: 16px;
    cursor: pointer; transition: 0.3s;
}
.qty-btn:hover { background: #ff6b81; }
.qty-value { padding: 5px 12px; min-width: 30px; text-align: center; font-weight: 600; }

.remove-btn {
    background: #ff6b6b; color: white; padding: 8px 14px;
    border-radius: 20px; cursor: pointer; border: none;
}
.remove-btn:hover { background: #ff4757; }

tr.fade-out {
    opacity: 0; transform: translateX(40px);
    transition: all 0.4s ease-in-out;
}

/* ✅ Popup */
.popup {
    position: fixed;
    top: 0; left: 50%;
    transform: translateX(-50%) translateY(-100%);
    background: #2ed573; color: white;
    padding: 16px 24px; border-radius: 12px;
    font-weight: 600; font-size: 16px;
    text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    opacity: 0; z-index: 2000;
    animation: dropDown 0.5s ease forwards, returnUp 0.5s ease 1.5s forwards;
}
@keyframes dropDown {
    0% { opacity: 0; transform: translateX(-50%) translateY(-100%); }
    100% { opacity: 1; transform: translateX(-50%) translateY(50px); }
}
@keyframes returnUp {
    0% { opacity: 1; transform: translateX(-50%) translateY(50px); }
    100% { opacity: 0; transform: translateX(-50%) translateY(-100%); visibility: hidden; }
}

/* ✅ Footer */
footer {
    text-align: center;
    padding: 20px;
    background: #ff4757;
    color: white;
    margin-top: 40px;
}

/* ✅ Buttons */
.actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 25px;
}
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 25px;
    background: #ff4757;
    color: white;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}
.btn:hover { background: #ff6b81; }
.empty { text-align: center; font-size: 18px; color: #777; margin: 40px 0; }
</style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar">
    <a href="index.php" class="logo">
        <img src="https://cdn-icons-png.flaticon.com/512/3595/3595455.png" alt="Jigato Logo">
        Jigato
    </a>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="restaurants.php">Restaurants</a></li>
        <li><a href="cart.php">Cart 🛒</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="profile.php">Hi, <?= htmlspecialchars($_SESSION['name']) ?></a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php?redirect=cart.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="container">
    <h1>Your Cart 🛒</h1>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="empty">
            <p>You need to <a href="login.php?redirect=cart.php" class="btn">Login</a> to view your cart.</p>
        </div>

    <?php else: ?>
        <?php
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT * FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0):
        ?>
            <table id="cart-table">
                <tr>
                    <th>Image</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                <?php 
                $grand_total = 0;
                while ($row = $result->fetch_assoc()): 
                    $total = $row['price'] * $row['quantity'];
                    $grand_total += $total;
                ?>
                <tr data-id="<?= $row['id'] ?>" data-price="<?= $row['price'] ?>">
                    <td><img src="<?= htmlspecialchars($row['image']) ?>" alt=""></td>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td>₹<?= number_format($row['price'], 2) ?></td>
                    <td>
                        <button class="qty-btn dec">−</button>
                        <span class="qty-value"><?= $row['quantity'] ?></span>
                        <button class="qty-btn inc">+</button>
                    </td>
                    <td class="item-total">₹<?= number_format($total, 2) ?></td>
                    <td><button class="remove-btn">Remove</button></td>
                </tr>
                <?php endwhile; ?>
                <tr id="grand-row">
                    <th colspan="4" style="text-align:right;">Grand Total</th>
                    <th id="grand-total">₹<?= number_format($grand_total, 2) ?></th>
                    <th></th>
                </tr>
            </table>

            <div class="actions" id="action-buttons">
                <a href="restaurants.php" class="btn">🛍️ Continue Shopping</a>
                <a href="checkout.php" class="btn" id="checkout">Proceed to Checkout 💳</a>
            </div>

        <?php else: ?>
            <div class="empty" id="empty-msg">🛒 Your cart is empty</div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<footer>
    &copy; 2025 Jigato | All Rights Reserved
</footer>

<!-- ✅ JS -->
<script>
document.querySelectorAll('.remove-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const id = row.dataset.id;
        row.classList.add('fade-out');
        setTimeout(() => {
            fetch('cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'remove_id=' + id
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === 'removed') {
                    row.remove();
                    updateGrandTotal();
                    showPopup('Item removed from cart');
                    checkIfEmpty();
                }
            });
        }, 400);
    });
});

document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const qtySpan = row.querySelector('.qty-value');
        let qty = parseInt(qtySpan.textContent);
        if (this.classList.contains('inc')) qty++;
        else if (this.classList.contains('dec') && qty > 1) qty--;
        qtySpan.textContent = qty;
        const price = parseFloat(row.dataset.price);
        const totalCell = row.querySelector('.item-total');
        totalCell.textContent = "₹" + (price * qty).toFixed(2);
        updateGrandTotal();
        fetch('cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'update_id=' + row.dataset.id + '&quantity=' + qty
        }).then(() => showPopup('Quantity updated'));
    });
});

function updateGrandTotal() {
    let grand = 0;
    document.querySelectorAll('.item-total').forEach(cell => {
        grand += parseFloat(cell.textContent.replace('₹', '')) || 0;
    });
    document.getElementById('grand-total').textContent = "₹" + grand.toFixed(2);
}

// ✅ Live check if cart empty
function checkIfEmpty() {
    const rows = document.querySelectorAll('#cart-table tr[data-id]');
    if (rows.length === 0) {
        document.getElementById('cart-table').remove();
        const actionButtons = document.getElementById('action-buttons');
        if (actionButtons) actionButtons.remove();

        const msg = document.createElement('div');
        msg.className = 'empty';
        msg.id = 'empty-msg';
        msg.textContent = 'Your cart is empty 😔';
        document.querySelector('.container').appendChild(msg);
    }
}

// ✅ Popup animation
function showPopup(message) {
    const popup = document.createElement('div');
    popup.className = 'popup';
    popup.textContent = message;
    document.body.appendChild(popup);
    setTimeout(() => popup.remove(), 3500);
}
</script>
</body>
</html>

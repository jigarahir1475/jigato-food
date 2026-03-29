<?php
session_start();
include('config.php');
// ✅ Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = trim($_POST["phone"]);
    $password = trim($_POST["password"]);

    if (empty($phone) || empty($password)) {
        $error = "Please enter both phone and password.";
    } else {
        // ✅ Check if user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // ✅ Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["phone"] = $user["phone"];
                $_SESSION["address"] = $user["address"];

                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that phone number.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jigato | Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #e52e71, #ff8a00);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .login-box {
        background: #fff;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
        animation: fadeIn 0.6s ease-in-out;
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }
    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 16px;
        outline: none;
    }
    button {
        width: 100%;
        background: linear-gradient(90deg, #ff8a00, #e52e71);
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-size: 18px;
        cursor: pointer;
        transition: 0.3s;
    }
    button:hover {
        opacity: 0.9;
    }
    p {
        text-align: center;
        margin-top: 15px;
    }
    a {
        color: #e52e71;
        text-decoration: none;
        font-weight: 600;
    }
    .error {
        color: red;
        text-align: center;
        font-size: 14px;
        margin-bottom: 10px;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(-10px);}
        to {opacity: 1; transform: translateY(0);}
    }
</style>
</head>
<body>

<div class="login-box">
    <h2>Welcome Back!</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success" style="color: green; text-align:center;">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input maxlength="10" name="phone" placeholder="Mobile Number" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>

</body>
</html>

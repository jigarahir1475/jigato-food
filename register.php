<?php
error_reporting(0);
session_start();
require_once "config.php";
require_once "email_helper.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $phone    = trim($_POST["phone"]);
    $address  = trim($_POST["address"]);
    $password = trim($_POST["password"]);
    $confirm  = trim($_POST["confirm"]);

    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($password) || empty($confirm)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address!";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // Generate OTP & timestamp
            $otp = rand(100000, 999999);
            $otp_created_at = date("Y-m-d H:i:s");
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, password, wallet_balance, otp, otp_created_at, is_verified)
                                    VALUES (?, ?, ?, ?, ?, 0, ?, ?, 0)");
            $stmt->bind_param("sssssss", $name, $email, $phone, $address, $hashed, $otp, $otp_created_at);

            if ($stmt->execute()) {
                $_SESSION['email'] = $email;

                // ✅ Send email quietly (no output)
                sendVerificationEmail($email, $otp);

                // ✅ Redirect immediately
                header("Location: verify.php");
                exit;
            } else {
                $error = "Database error: " . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Foody | Register</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #ff8a00, #e52e71);
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
}
.register-box {
  background: #fff;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 400px;
}
h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #333;
}
input, textarea {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 16px;
}
textarea {
  resize: none;
  height: 80px;
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
button:hover { opacity: 0.9; }
.error {
  color: red;
  text-align: center;
  font-size: 14px;
  margin-bottom: 10px;
}
p {
  text-align: center;
  margin-top: 10px;
}
a {
  color: #e52e71;
  text-decoration: none;
  font-weight: 600;
}
</style>
</head>
<body>

<div class="register-box">
  <h2>Create Account</h2>

  <?php if (!empty($error)): ?>
      <div class="error"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" action="">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <input maxlength="10" name="phone" placeholder="Phone Number" required>
      <textarea name="address" placeholder="Delivery Address" required></textarea>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm" placeholder="Confirm Password" required>
      <button type="submit">Register & Get OTP</button>
  </form>

  <p>Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>

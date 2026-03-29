<?php
session_start();
require_once "config.php";
require_once "email_helper.php";

if (!isset($_SESSION['email'])) {
    die("<h3>No OTP found. <a href='register.php'>Register again</a></h3>");
}

$email = $_SESSION['email'];
$message = "";

// ✅ Check if already verified
$check = $conn->prepare("SELECT is_verified FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$res = $check->get_result();
$user = $res->fetch_assoc();

if ($user && $user['is_verified'] == 1) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// ✅ Verify OTP
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify'])) {
    $entered = preg_replace("/[^0-9]/", "", trim($_POST['otp']));

    $stmt = $conn->prepare("SELECT otp, otp_created_at FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $real_otp = trim($row['otp']);
        $otp_time = strtotime($row['otp_created_at']);
        $now = time();

        if (($now - $otp_time) > 60) {
            $message = "<p style='color:red;'>❌ OTP expired! Please resend a new one.</p>";
        } elseif ($entered === $real_otp) {
            $update = $conn->prepare("UPDATE users SET is_verified = 1, otp = NULL, otp_created_at = NULL WHERE email = ?");
            $update->bind_param("s", $email);
            $update->execute();

            $message = "<p style='color:green;font-weight:bold;'>✅ Email Verified Successfully! Redirecting...</p>";
            echo "<script>setTimeout(()=>{window.location.href='login.php';},2000);</script>";
            unset($_SESSION['email']);
        } else {
            // ❌ Wrong OTP – timer should continue (no reset)
            $message = "<p style='color:red;font-weight:bold;'>❌ Invalid OTP! Please try again.</p>";
            echo "<script>localStorage.setItem('otp_timer_continue', '1');</script>";
        }
    } else {
        $message = "<p style='color:red;'>❌ User not found!</p>";
    }
}

// ✅ Handle Resend OTP (via GET link)
if (isset($_GET['resend'])) {
    $otp = strval(rand(100000, 999999));
    $otp_created_at = date("Y-m-d H:i:s");

    $update = $conn->prepare("UPDATE users SET otp = ?, otp_created_at = ? WHERE email = ?");
    $update->bind_param("sss", $otp, $otp_created_at, $email);

    if ($update->execute()) {
        sendVerificationEmail($email, $otp);
        $message = "<p style='color:green;'>✅ New OTP sent successfully! Check your inbox.</p>";
        echo "<script>
            localStorage.removeItem('otp_timer_continue');
            localStorage.setItem('otp_timer', 60);
        </script>";
    } else {
        $message = "<p style='color:red;'>❌ Error sending new OTP. Try again.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Verify OTP | Foody</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
  font-family:'Poppins',sans-serif;
  background:linear-gradient(135deg,#ff8a00,#e52e71);
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh;
  margin:0;
}
.verify-box {
  background:#fff;
  padding:40px;
  border-radius:20px;
  box-shadow:0 10px 25px rgba(0,0,0,0.1);
  width:100%;
  max-width:400px;
  text-align:center;
}
input {
  width:100%;
  padding:12px;
  margin:10px 0;
  border-radius:10px;
  border:1px solid #ccc;
  font-size:16px;
}
button {
  width:100%;
  background:linear-gradient(90deg,#ff8a00,#e52e71);
  color:#fff;
  border:none;
  padding:12px;
  border-radius:10px;
  font-size:18px;
  cursor:pointer;
  transition:0.3s;
}
button:hover {opacity:0.9;}
a {
  color:#e52e71;
  font-weight:bold;
  text-decoration:none;
  transition:0.3s;
}
a.disabled {
  color:gray;
  pointer-events:none;
  text-decoration:none;
}
.timer {
  color:#555;
  font-size:14px;
  margin-top:15px;
}
.message {
  margin-bottom:10px;
}
</style>
</head>
<body>
<div class="verify-box">
  <h2>Email OTP Verification</h2>
  <div class="message"><?= $message ?></div>

  <form method="POST">
    <input type="text" name="otp" placeholder="Enter OTP" required>
    <button type="submit" name="verify">Verify</button>
  </form>

  <div class="timer" id="timer">
    Resend available in <span id="countdown">60</span>s
  </div>
  <a href="?resend=1" id="resendLink" class="disabled">Resend OTP</a>
</div>

<script>
let seconds = parseInt(localStorage.getItem("otp_timer") || "60");
let continueFlag = localStorage.getItem("otp_timer_continue");
const countdown = document.getElementById("countdown");
const resendLink = document.getElementById("resendLink");

// Continue old timer (when invalid OTP)
if (!continueFlag) {
  seconds = 60;
}

function startTimer() {
  resendLink.classList.add("disabled");
  const interval = setInterval(() => {
    seconds--;
    countdown.textContent = seconds;
    localStorage.setItem("otp_timer", seconds);

    if (seconds <= 0) {
      clearInterval(interval);
      document.getElementById("timer").textContent = "";
      resendLink.classList.remove("disabled");
      localStorage.removeItem("otp_timer");
      localStorage.removeItem("otp_timer_continue");
    }
  }, 1000);
}

if (seconds > 0) startTimer();
</script>
</body>
</html>

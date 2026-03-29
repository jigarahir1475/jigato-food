<?php
function sendVerificationEmail($to, $otp) {
    // ⚠️ Enter your Brevo API key here (without spaces)
   $apiKey = "YOUR_API_KEY_HER";

    $senderEmail = "jigatofood@gmail.com";
    $senderName  = "JigatoFood";

    $url = "https://api.brevo.com/v3/smtp/email";

    $data = [
        "sender" => ["name" => $senderName, "email" => $senderEmail],
        "to" => [["email" => $to]],
        "subject" => "🍽️ JigatoFood - Verify Your Account & Start Ordering Online!",
        "htmlContent" => "
            <html>
            <body style='font-family:Poppins, sans-serif; background:#f5f6fa; padding:30px;'>
                <div style='max-width:520px; margin:auto; background:#fff; border-radius:16px; box-shadow:0 4px 15px rgba(0,0,0,0.1); padding:30px;'>
                    <div style='text-align:center; margin-bottom:20px;'>
                        <h2 style='color:#e52e71; margin:0;'>Welcome to <span style=\"color:#ff8a00;\">JigatoFood</span>!</h2>
                        <p style='color:#777; font-size:15px;'>Delicious meals are just a click away 🍕</p>
                    </div>
                    <div style='text-align:center; background:#fff4e6; border-radius:12px; padding:25px; margin:20px 0;'>
                        <h3 style='color:#555; margin-bottom:10px;'>Your One-Time Password (OTP)</h3>
                        <h1 style='color:#ff8a00; letter-spacing:3px;'>$otp</h1>
                        <p style='color:#888; font-size:14px;'>This code is valid for <b>60 seconds</b>.</p>
                    </div>
                    <p style='color:#555; text-align:center; font-size:14px;'>Please do not share this OTP with anyone for your security.</p>
                    <hr style='border:none; border-top:1px solid #eee; margin:25px 0;'>
                    <p style='text-align:center; font-size:13px; color:#999;'>© " . date('Y') . " JigatoFood. All rights reserved.</p>
                </div>
            </body>
            </html>"
    ];

    $headers = [
        "accept: application/json",
        "api-key: $apiKey",
        "content-type: application/json",
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // ✅ Return true if email sent successfully
    return ($httpCode == 201);
}
?>

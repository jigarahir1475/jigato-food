
<?php
// Simple Maintenance Mode Script

// Agar maintenance mode ON hai to true rakho, OFF karne ke liye false
$maintenance_mode = true;

if ($maintenance_mode) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Website Under Maintenance</title>
        <style>
            body {
                background-color: #f2f2f2;
                font-family: Arial, sans-serif;
                text-align: center;
                margin-top: 100px;
                color: #333;
            }
            .container {
                background: white;
                display: inline-block;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #ff9800;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🚧 Website Under Maintenance 🚧</h1>
            <p>We’ll be back soon. Thank you for your patience!</p>
        </div>
    </body>
    </html>
    <?php
    exit; // Stop further PHP execution
}

// Agar maintenance_mode = false hai, to yahan se apna main site load hoga
include('main_site.php'); // <-- yahan apni asli site ka file include kar dena
?>
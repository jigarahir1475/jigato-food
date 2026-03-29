<?php
session_start();

// Destroy all sessions
session_unset();
session_destroy();

// Redirect to index page with a logout success message
header("Location: index.php?logout=success");
exit();
?>

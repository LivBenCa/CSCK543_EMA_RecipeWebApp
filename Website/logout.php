<?php
// Start or resume the current session.
session_start();

// Destroy the current session along with all session data.
session_destroy();

// Redirect to the index page.
header('Location: index.php');

// Terminate the script execution.
exit;
?>



<?php
// logout.php (Cookie Version)

// To "delete" a cookie, you set a new cookie with the same name,
// an empty value, and an expiration time in the past.
setcookie('user_id', '', time() - 3600, "/");

// Redirect the user to the login page.
header("location: login.php");
exit;
?>
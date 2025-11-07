<?php
// logout.php

// 1. Always start the session to access it.
session_start();

// 2. Unset all of the session variables.
$_SESSION = array();

// 3. Destroy the session itself. This removes the session data from the server.
session_destroy();

// 4. Redirect the user to the login page.
//    This prevents them from being stuck on a blank page after logging out.
header("location: login.php");
exit;
?>
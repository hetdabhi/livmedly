<?php
session_start();
$_SESSION['test'] = "Hello, session!";
echo "Session set. Refresh the page to check.";
?>

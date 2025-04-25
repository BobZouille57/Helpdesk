<?php
require_once 'includes/bdd.php';
session_destroy();
header("Location: login.php");
exit();
?>

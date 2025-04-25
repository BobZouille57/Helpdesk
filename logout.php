<?php
require_once 'bdd.php';
session_destroy();
header("Location: login.php");
exit();
?>

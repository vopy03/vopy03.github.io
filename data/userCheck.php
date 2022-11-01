<?php
if (isset($_POST['logout'])) {
	unset($_SESSION['login']);
	session_unset();
}
function isLogged() {
	if (isset($_SESSION['login'])) return true;
	else return false;
}
function userCheck() { 
	if (!isset($_SESSION['login'])) header('Location: ../index.php'); 
}
 ?>
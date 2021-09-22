<?php
	session_start();
	if ($_SESSION['authenticated'])
	{
		echo "Welcome back ";
	}
	else 
		echo "<a href='Authentication.php'>Please click here to log in.</a>";


	function destroy_session_and_data() {
		$_SESSION = array();
		setcookie(session_name(), '', time() - 2592000, '/');
		session_destroy();
	}
?>
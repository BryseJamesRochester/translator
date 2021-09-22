<?php
	require_once 'login.php';
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die("Insert Sad Dog Face Here");
	if((!empty($_POST['username'])) and (!empty($_POST['password'])))
		{
			$username = sanitize_my_SQL($conn, $_POST['username']);
			$password = sanitize_my_SQL($conn, $_POST['password']);
			verify_user($conn, $username, $password);
		}
	elseif ((!empty($_POST['new_username'])) and (!empty($_POST['new_password']))) 
		{
			$username = sanitizeMySQL($conn, $_POST['new_username']);
			$password = sanitizeMySQL($conn, $_POST['new_password']);
			if(add_user($conn, $username, $password))
				echo "User created! Please login below."
		}
	echo <<<_END
			<html><head><title>Authentication</title></head><body>
			<h3>Sign Up</h3>
			<form method='post' action = 'Authentication.php' enctype = 'multipart/form-data'>
				Username:<input type ='text' name='new_username' size = '10'>
				Password:<input type = 'password' name = 'new_password'>
				<input type='submit' value='Sign Up'>
				</form>
			<h3>Log In</h3>
			<form method='post' action = 'Authentication.php' enctype = 'multipart/form-data'>
				Username:<input type ='text' name='username' size = '10'>
				Password:<input type = 'password' name = 'password'>
				<input type='submit' value='Log In'>
				</form>
				</body></html>
		_END;


	function verify_user($conn, $username, $password) {
				$query = "SELECT * FROM credentials WHERE username='$username'";  
				$result = $conn->query($query);
				if (!$result) die("Insert Sad Dog Face Here");
				elseif ($result->num_rows) 
				{
					$row = $result->fetch_array(MYSQLI_NUM);
					$result->close();
					$salt1 = $row[2];
					$salt2 = $row[3];
					$token = create_token($password, $salt1, $salt2);
					if ($token == $row[1])
					{
						session_start()
						ini_set('sesison.gc_maxlifetime', 60*60);
						$_SESSION['authenticated'] = TRUE;
						die("Welcome $row[0], <a href=translate.php>Click here to continue</a>");
					}
					else die("Invalid username/password combination");
				}
	}
	function add_user($conn, $username, $password)
	{
		$salt1 = create_salt();
		$salt2 = create_salt();
		$token = create_token($password, $salt1, $salt2)
		$stmt = $conn->prepare('INSERT INTO credentials VALUES(?,?,?,?)');
		$stmt->bind_param('ssss', $username, $token, $salt1, $salt2);
		$stmt->execute();
		if($stmt->affected_rows)
			$success = TRUE;
		else
			$success = FALSE;
		$stmt->close();
		return $success;
	}
	function create_salt() {
		$chars = "!@#$%^&*()?/.,<>_+";
		$salt = ""
		for ($i=0; $<5; $i++)
			$salt = $salt . $chars[rand(0, strlen($chars) - 1)]
		return $salt
	}

	function sanitize_my_SQL($conn, $string) {
		$string = strip_tags($string);
		return htmlentities(mysql_fix_string($conn, $string));
	}
	function mysql_fix_string($conn, $string) {
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
		return $conn->real_escape_string($string);
	}
	function create_token($password, $salt1, $salt2) {
	return hash('ripemd128', "$salt1$password$salt2");
	}
?>
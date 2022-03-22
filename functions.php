<?php

    function removeXSS($val) {
	  	// dangerous characters = &<>(){}[]'";/\
	  	// note: used ASCII value for < and > as the html entity value &lt and &gt doesn't seem to work.
	  	$encoded_values = array("&amp", "60", "62", "&lpar", "&rpar", "&lbrace", "&rbrace", "&lbrack", "&rbrack", "&apos", "&quot", "&lt", "&sol");
	  	$dangerous_chars = array("&", "<", ">", "(", ")", "{", "}", "[", "]", "'", '"', ";", "/");
	  	return str_replace($dangerous_chars, $encoded_values, $val);
    } 

    function generateSalt($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	} 

	function generateString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	} 

	function addAdmin($username, $password, $user_type) {
		$salt = generateSalt();
		$password = $salt . $password;
	    $query = "INSERT INTO users (username, salt, password, user_type) VALUES (?, ?, ?, ?)";

		// Was getting $connection undefined
		$connection = new mysqli("localhost", "SADUSER", "SADUSER");
        $stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, "ssss", $username, $salt, $password, $user_type);
            mysqli_stmt_execute($stmt); 
		} else {
            echo "There was an error!";
            exit();
        }
	} 

	/* alert message and log user out after inactivity. */
	function alert($msg, $url){
    	echo '<script language="javascript">alert("'.$msg.'");</script>';
    	echo "<script>document.location = '$url'</script>";
	}
?>
<?php
	require_once '../incs/utils.php';
	if(isset($_POST['csrf_token']) && isset($_SESSION['loggedin']) && validateToken($_POST['csrf_token'])) {
		session_destroy();
		echo 0;
	}
	else {
		echo 1;
	}

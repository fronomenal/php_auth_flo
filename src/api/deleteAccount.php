<?php
	require_once '../incs/utils.php';

	try {
		
		$errors = ["count" => 0];

		if(isset($_POST['csrf_token']) && validateToken($_POST['csrf_token'])) {
			if(isset($_SESSION['loggedin']) && isset($_SESSION['userID']) && $_SESSION['loggedin'] === true) {
				$C = connect();
				if($C) {
					if(sqlUpdate($C, 'DELETE FROM users WHERE id=?', [$_SESSION['userID']])) {
						sqlUpdate($C, 'DELETE FROM requests WHERE user=?', [$_SESSION['userID']]);
						sqlUpdate($C, 'DELETE FROM loginattempts WHERE user=?', [$_SESSION['userID']]);
						session_destroy();
					} else {
						$errors["count"] = 1;
						$errors["db_error"] = "account deletion failed. Try again later";
					}
				} else {
					$errors["count"] = 1;
					$errors["db_error"] = "error connecting to database";
				}
			} else {
				$errors["count"] = 1;
				$errors["naughty_user_error"] = "not logged in. You shouldn't be here";
			}
		} else {
			$errors["count"] = 1;
			$errors["400"] = "bad request";
		}

		echo json_encode($errors);
	
	} catch (\Throwable $th) {
		echo json_encode(array("500" => "internal server error", "count" => "1", "DEBUG" => $th->getMessage()));
	}


<?php
	require_once '../incs/utils.php';

	try {
		
		$errors = ["count" => 0];

		if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['csrf_token']) && validateToken($_POST['csrf_token'])) {
			$email = $_POST['email'];
			$password = $_POST['password'];
	
			$C = connect();
			if($C) {
				$hourAgo = time() - 60*60;
				$res = sqlSelect($C, 'SELECT users.id,password,verified,COUNT(loginattempts.id) FROM users LEFT JOIN loginattempts ON users.id = user AND timestamp>? WHERE email=? GROUP BY users.id', [$hourAgo, $email]);
				if($res) {
					$user = $res;
					if($user['COUNT(loginattempts.id)'] <= MAX_LOGIN_ATTEMPTS_PER_HOUR) {
						if($user['verified']) {
							if(password_verify($password, $user['password'])) {
								$_SESSION['loggedin'] = true;
								$_SESSION['userID'] = $user['id'];
								sqlUpdate($C, 'DELETE FROM loginattempts WHERE user=?', [$user['id']]);
								$errors["DEBUG"] = $user["users.id"];
							} else {
								$id = sqlInsert($C, 'INSERT INTO loginattempts VALUES (NULL, ?, ?, ?)', [$user['id'], $_SERVER['REMOTE_ADDR'], time()]);
								if($id !== -1) {
									$errors["count"] = 1;
									$errors["user_error"] = "incorrect user credentials";
								} else {
									$errors["count"] = 1;
									$errors["db_error"] = "something terrible happened";
								}
							}
						} else {
							$errors["count"] = 1;
							$errors["user_error"] = "user with {$_POST['email']} not verified. Please verify your account";
						}
					} else {
						$errors["count"] = 1;
						$errors["attempt_error"] = "too many login attempts. Please try again in an hour";
					}
				} else {
					$errors["count"] = 1;
					$errors["user_error"] = "incorrect user credentials";
				}
			} else {
				$errors["count"] = 1;
				$errors["db_error"] = "error connecting to database";
			}
		} else {
			$errors["count"] = 1;
			$errors["400"] = "bad request";
		}

		echo json_encode($errors);
	
	} catch (\Throwable $th) {
		echo json_encode(array("500" => "internal server error", "count" => "1", "DEBUG" => $th->getMessage()));
	}

	
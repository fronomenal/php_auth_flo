<?php
	require_once '../incs/utils.php';


	try {
		
		$errors = ["count" => 0];

		if(empty($_POST['id'])) {
			$errors["400"] = 'invalid password reset request. No ID';
			$errors["count"] += 1;
		}
		if(empty($_POST['hash'])) {
			$errors["400"] = 'invalid password reset request. No hash';
			$errors["count"] += 1;
		}
		if(!isset($_POST['password']) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\~?!@#\$%\^&\*])(?=.{8,})/', $_POST['password'])) {
			$errors["password_error"] = 'Password must have upper & lower letters + at least one number + at least one symbol and be 8 or more chars long';
			$errors["count"] += 1;
		}
		else if(!isset($_POST['confirm-password']) || $_POST['confirm-password'] !== $_POST['password']) {
			$errors["password_error"] = 'Passwords do not match';
			$errors["count"] += 1;
		}

		if($errors["count"] === 0) {
			if(isset($_POST['csrf_token']) && validateToken($_POST['csrf_token'])) {
				
				$C = connect();
				if($C) {
					 
					$res = sqlSelect($C, 'SELECT user,hash,timestamp FROM requests WHERE id=? LIMIT 1', [$_POST['id']]);
					if($res) {
						$request = $res;

						if(password_verify(urlSafeDecode($_POST['hash']), $request['hash'])) {
							
							if($request['timestamp'] >= time() - PASSWORD_RESET_REQUEST_EXPIRY_TIME) {
								 
								$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
								if(sqlUpdate($C, 'UPDATE users SET password=? WHERE id=?', [$hash, $request['user']])) {
									sqlUpdate($C, 'DELETE FROM requests WHERE user=? AND type=1', [$request['user']]);
								} else {
									$errors["db_error"] = 'failed to update password';
									$errors["count"] = 1;
								}
							} else {
								$errors["val_error"] = 'this reset request has expired';
								$errors["count"] = 1;
							}
						} else {
							$errors["val_error"] = 'invalid password reset request';
							$errors["count"] = 1;
						}
					} else {
						$errors["val_error"] = 'invalid password reset request';
						$errors["count"] = 1;
					}
				}
				else {
					$errors["db_error"] = 'failed to connect to database';
					$errors["count"] = 1;
				}
			}
			else {
				$errors["400"] = 'invalid CSRF token';
				$errors["count"] = 1;
			}
		}

		echo json_encode($errors);
	
	} catch (\Throwable $th) {
		echo json_encode(array("500" => "internal server error", "count" => "1", "DEBUG" => $th->getMessage()));
	}
		
		
		
		


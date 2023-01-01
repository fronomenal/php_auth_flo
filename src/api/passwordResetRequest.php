<?php
	require_once '../incs/utils.php';


	try {
		
		$errors = ["count" => 0];
	
		if(!empty($_POST['email'])) {
			if(!empty($_POST['csrf_token']) && validateToken($_POST['csrf_token'])) {
				
				$C = connect();
				if($C) {
					
					$dayago = time() - 60 * 60 * 24;
					$res = sqlSelect($C, 'SELECT users.id,name,COUNT(requests.id) FROM users LEFT JOIN requests ON users.id = user AND type=1 AND timestamp>? WHERE email=? GROUP BY users.id', [$dayago, $_POST['email']]);
					if($res) {
						$user = $res;

						if($user['COUNT(requests.id)'] < MAX_PASSWORD_RESET_REQUESTS_PER_DAY) { 

							$code = random_bytes(32);
							$hash = password_hash($code, PASSWORD_DEFAULT);
							$insertID = sqlInsert($C, 'INSERT INTO requests VALUES (NULL, ?, ?, ?, 1)', [$user['id'], $hash, time()]);
							if($insertID !== -1) {
								
								$msg = '<a href="'. RESET_PASSWORD_ENDPOINT . '/?id=' . $insertID . '&hash=' . urlSafeEncode($code) .'">Click Here to Reset your Password</a>';
								if(!sendEmail($_POST['email'], $user['name'], 'Password Reset', $msg)) {
									$errors["count"] = 1;
									$errors["mailer_error"] = "failed to send verification email. Please try again";
								}
							} else {
								$errors["count"] = 1;
								$errors["db_error"] = "failed to initialize verification process. Please try again";
							}
						} else {
							$errors["count"] = 1;
							$errors["val_error"] = "Too many requests in the last 24 hours... try again later";
						}
					}
				} else {
					$errors["count"] = 1;
					$errors["db_error"] = "error connecting to database";
				}
			} else {
				$errors["count"] = 1;
				$errors["400"] = "invalid csrf token";
			}
		} else {
			$errors["count"] = 1;
			$errors["400"] = "please provide an email address";
		}

	echo json_encode($errors);

} catch (\Throwable $th) {
	echo json_encode(array("500" => "internal server error", "count" => "1", "DEBUG" => $th->getMessage()));
}
	
	
	
	
	

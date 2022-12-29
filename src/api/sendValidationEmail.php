<?php
	require_once '../incs/utils.php';

	try {

	function sendValidationEmail($email) {
		$C = connect();
		if($C) {
			$oneDayAgo = time() - 60 * 60 * 24;
			$res = sqlSelect($C, 'SELECT users.id,name,verified,COUNT(requests.id) FROM users LEFT JOIN requests ON users.id = requests.user AND type=0 AND timestamp>? WHERE email=? GROUP BY users.id ', [$oneDayAgo, $email]);
			if($res) {
				$user = $res;
				if($user['verified'] === 0) {
					if($user['COUNT(requests.id)'] <= MAX_EMAIL_VERIFICATION_REQUESTS_PER_DAY) {
						$verifyCode = random_bytes(32);
						$hash = password_hash($verifyCode, PASSWORD_DEFAULT);
						$requestID = sqlInsert($C, 'INSERT INTO requests VALUES (NULL, ?, ?, ?, 0)', [$user['id'], $hash, time()]);
						if($requestID !== -1) {
							if(sendEmail($email, $user['name'], 'Email Verification', '<a href="' . VALIDATE_EMAIL_ENDPOINT . '/?id=' . $requestID . '&hash=' . urlSafeEncode($verifyCode). '" />Click this link to verify your email</a>')) {
								return json_encode(array("count" => 0));
							}
							else {
								return json_encode(array("count" => 1, "mailer_error" => "failed to send verification email. Please try again"));
							}
						}
						else {
							// return 'failed to insert request';
							return json_encode(array("count" => 1, "db_error" => "failed to initialize verification process. Please try again"));
						}
					}
					else {
						return json_encode(array("count" => 1, "val_error" => "daily request limit reached. Please try again in 24hrs"));
					}
				}
				else {
					return json_encode(array("count" => 1, "val_error" => "User already verified"));
				}
			}
			else {
				return json_encode(array("count" => 1, "val_error" => "user with this email does not exist"));
			}
		}
		else {
			return json_encode(array("count" => 1, "db_error" => "error connecting to database"));
		}
	}
	

	if(isset($_POST['validateEmail']) && isset($_POST['csrf_token']) && validateToken($_POST['csrf_token'])) {
		echo sendValidationEmail($_POST['validateEmail']);
	}else{
		echo json_encode(array("400" => "bad request"));
	}
} catch (Throwable $th) {
	echo json_encode(array("500" => "internal server error", "count" => "1", "DEBUG" => $th->getMessage()));
}
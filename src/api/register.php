<?php
	require_once '../incs/utils.php'; 

$errors = ["count" => 0];


	if(!isset($_POST['name']) || strlen($_POST['name']) > 255 || !preg_match('/^[a-zA-Z- ]+$/', $_POST['name'])) {
		$errors["name_error"] = "username is invalid; must be below 255 characters and only consist of alphabets";
		$errors["count"] += 1;
	}
	if(!isset($_POST['email']) || strlen($_POST['email']) > 255 || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$errors["email_error"] = "please provide a valid email";
		$errors["count"] += 1;
	}
	else if(!checkdnsrr(substr($_POST['email'], strpos($_POST['email'], '@') + 1), 'MX')) {
		$errors["email_error"] = "email is invalid";
		$errors["count"] += 1;
	}
	if(!isset($_POST['password']) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\~?!@#\$%\^&\*])(?=.{8,})/', $_POST['password'])) {
		$errors["password_error"] = "password is invalid; must contain upcases, downcases, numbers and special chars";
		$errors["count"] += 1;
	}
	else if(!isset($_POST['confirm-password']) || $_POST['confirm-password'] !== $_POST['password']) {
		$errors["password_error"] = "passwords should match";
		$errors["count"] += 1;
	}



	if($errors["count"] === 0) {

		if(isset($_POST['csrf_token']) && validateToken($_POST['csrf_token'])) {$C = connect();
			if($C) {
				
				$res = sqlSelect($C, 'SELECT id FROM users WHERE email=?', 's', $_POST['email']);
				if($res && $res->num_rows === 0) {
					
					$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
					$id = sqlInsert($C, 'INSERT INTO users VALUES (NULL, ?, ?, ?, 0)', 'sss', $_POST['name'], $_POST['email'], $hash);
					if($id !== -1) {
            $errors["count"] = 0;
					}
					else {
						$errors["db_error"] = "something terrible happened";
					}
					$res->free_result();
				}
        else {
          $errors["user_error"] = "user with {$_POST['email']} already exists";
				}
			}
			else {
				$errors["db_error"] = "error connecting to database";
			}
		}else{
			$errors["con_error"] = "csrf token validation failed";
		}
	}


	echo json_encode($errors);
	
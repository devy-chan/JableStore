<?php 
require_once '../config/db_connect.php';

session_start();

if(isset($_SESSION['userId'])) {
	header('location:'.$store_url.'backend/admin/dashboard.php');		
}

$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        if ($username === '') {
            $errors[] = 'Username is required';
        }
        if ($password === '') {
            $errors[] = 'Password is required';
        }
    } else {
        $stmt = $connect->prepare('SELECT user_id, username, password FROM users WHERE username = ? LIMIT 1');

        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            $validPassword = false;
            $needsRehash = false;

            if ($user) {
                // Support existing MD5 passwords during migration, then upgrade
                // the password to password_hash() after a successful login.
                if (password_verify($password, $user['password'])) {
                    $validPassword = true;
                    $needsRehash = password_needs_rehash($user['password'], PASSWORD_DEFAULT);
                } elseif (hash_equals($user['password'], md5($password))) {
                    $validPassword = true;
                    $needsRehash = true;
                }
            }

            if ($user && $validPassword) {
                if ($needsRehash) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $update = $connect->prepare('UPDATE users SET password = ? WHERE user_id = ?');
                    if ($update) {
                        $update->bind_param('si', $newHash, $user['user_id']);
                        $update->execute();
                        $update->close();
                    }
                }

                session_regenerate_id(true);
                $_SESSION['userId'] = $user['user_id'];

                header('location:' . $store_url . 'backend/admin/dashboard.php');
                exit;
            }

            $errors[] = 'Incorrect username/password combination';
        } else {
            $errors[] = 'Unable to process login. Please try again later.';
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>JABLE STORE | Admin Login</title>
	<link rel="icon" type="image/png" href="../../logo5.png">

	<!-- bootstrap -->
	<link rel="stylesheet" href="../../assests/bootstrap/css/bootstrap.min.css">
	<!-- bootstrap theme-->
	<link rel="stylesheet" href="../../assests/bootstrap/css/bootstrap-theme.min.css">
	<!-- font awesome -->
	<link rel="stylesheet" href="../../assests/font-awesome/css/font-awesome.min.css">

  <!-- custom css -->
  <link rel="stylesheet" href="../../custom/css/custom.css">	

  <!-- jquery -->
	<script src="../../assests/jquery/jquery.min.js"></script>
  <!-- jquery ui -->  
  <link rel="stylesheet" href="../../assests/jquery-ui/jquery-ui.min.css">
  <script src="../../assests/jquery-ui/jquery-ui.min.js"></script>

  <!-- bootstrap js -->
	<script src="../../assests/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<div class="row vertical">
			<div class="col-md-5 col-md-offset-4">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Please Sign in</h3>
					</div>
					<div class="panel-body">

						<div class="messages">
							<?php if($errors) {
								foreach ($errors as $key => $value) {
									echo '<div class="alert alert-warning" role="alert">
									<i class="glyphicon glyphicon-exclamation-sign"></i>
									'.$value.'</div>';										
									}
								} ?>
						</div>

						<form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="loginForm">
							<fieldset>
							  <div class="form-group">
									<label for="username" class="col-sm-2 control-label">Username</label>
									<div class="col-sm-10">
									  <input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="off" />
									</div>
								</div>
								<div class="form-group">
									<label for="password" class="col-sm-2 control-label">Password</label>
									<div class="col-sm-10">
									  <input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="off" />
									</div>
								</div>								
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-10">
									  <button type="submit" class="btn btn-default"> <i class="glyphicon glyphicon-log-in"></i> Sign in</button>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<!-- panel-body -->
				</div>
				<!-- /panel -->
			</div>
			<!-- /col-md-4 -->
		</div>
		<!-- /row -->
	</div>
	<!-- container -->	
</body>
</html>







	
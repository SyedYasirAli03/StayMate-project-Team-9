<?php
// Start output buffering to prevent header-related issues.
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin: 30px 0;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
			padding-right:50px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
		
		.header {
            text-align: center;
            margin-bottom: 20px;
        }

        img {
            width: 158px;
            height: 185px;
            display: block;
            margin: 0 auto;
            border-radius: 50%;
            //background-color: #ccc; /* Placeholder background color */
        }

        input[type="submit"],
        input[type="reset"] {
            width: 49%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-top: 5px;
        }

        .success {
            color: green;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
			<img src="images/logo.png" alt="Logo" />
        </div>
        <?php
        // Include the database credentials file
        require_once 'db_credentials.php';

		$activated=0;
        // Function to authenticate user login.
        function authenticateUser($email, $password) {
            // Connect to the database using constants from db_credentials.php
            $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

            // Check connection.
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Prepare the query to fetch user data based on email.
            $stmt = $conn->prepare("SELECT id, name, password, activated FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($userId, $name, $hashedPassword, $activated);
            $stmt->fetch();
            $stmt->close();

            // Verify the password using password_verify().
            if (password_verify($password, $hashedPassword)) {
                // Start a session and store user ID and name in session variables.
                session_start();
                $_SESSION['user_id'] = $userId;
                $_SESSION['name'] = $name;
				$_SESSION['activated'] = $activated;
                return true;
            } else {
                return false;
            }
        }

        // Handling user login.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			
			$email="";
			$password="";
			if (isset($_POST['semail'])) {
				$email = $_POST['semail'];
			}
			
			if (isset($_POST['spassword'])) {
				$password = $_POST['spassword'];
			}
			
			// Authenticate user login.
            $isAuthenticated = authenticateUser($email, $password);

			if ($isAuthenticated) {
			
				// Debugging purposes: Check the value of $activated
				//echo "Value of activated: " . $_SESSION['activated']; // Make sure the value is correct (0 or 1)

			
				if ($_SESSION['activated'] == 0) 
				{
					echo '<div class="error">Please check your email to activate your account first!</div>';
				}
				else {				
					// Redirect to dashboard on successful login.
					header("Location: dashboard.php");
					exit;
				}
			}
			else
			{
				echo '<div class="error">Invalid email or password. Please try again.</div>';
			}
		
        }
        ?>

		<div>
			<h1>User Login</h1>
		</div>
		
        <form method="post" action="">
            <label>Email:</label>
			<input type="email" name="semail" value="<?php echo isset($email) ? $email : ''; ?>" required>

            <label>Password:</label>
            <input type="password" name="spassword" required>

            <input type="submit" value="Login">
            <input type="reset" value="Reset">
        </form>
		<p>New user? <a href="register.php">Sign up</a>.</p>

		<p>No activation link? <a href="request_activation.php">Resend Link</a>.</p>

		<p>Forgot password? <a href="reset_password.php">Reset Password</a>.</p>


    </div>
</body>
</html>
<?php
// Clear the output buffer and send the content to the browser.
ob_end_flush();
?>

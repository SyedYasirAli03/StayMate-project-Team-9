<?php
// Start output buffering to prevent header-related issues.
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <style>
      body {
        font-family: "Arial", sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #2a3950;
      }

      .toolbar {
        background-color: #2a3950;
        color: #fff;
        padding: 10px;
        text-align: left;
        width: 100%;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        position: absolute;
        top: 0;
        left: 0;
      }

      .toolbar img {
        height: 40px; /* Adjust the height of the logo */
        margin-right: 10px;
      }

      .login-container {
        background-color: #fff;
        box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        padding: 20px;
        border-radius: 8px;
        width: 300px;
        text-align: center;
        margin-top: 60px; /* Adjust the margin to leave space for the toolbar */
      }

      .login-container h2 {
        color: #333;
      }

      .login-form {
        margin-top: 20px;
      }

      .form-group {
        margin-bottom: 20px;
      }

      .form-group label {
        display: block;
        font-size: 14px;
        margin-bottom: 8px;
        color: #555;
        text-align: left;
      }

      .form-group input {
        width: 100%;
        padding: 10px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
      }

      .form-group button {
        background-color: #4caf50;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
      }

      .form-group button:hover {
        background-color: #45a049;
      }

      .form-group .signup-link {
        margin-top: 10px;
        font-size: 14px;
        color: #333;
      }

      .form-group .signup-link a {
        color: #4caf50;
        text-decoration: none;
      }
      .error-message {
        color: red;
        margin-top: 10px;
        margin-bottom: 10px;
      }
    </style>
  </head>
  <body>
    <div class="toolbar">
      <img src="images/logo.png" alt="Logo" />
      <h2>
      <a style="color:#fff;text-decoration:none;" href="index.php">Stay Mate! </a></h2>
    </div>

    <div class="login-container">
	
	<?php
        // Include the database credentials file
        require_once 'db_credentials.php';

		$activated=0;
        // Function to authenticate user login.
        function authenticateUser($email, $password) {
			//echo "authenticateUser calleD";
            // Connect to the database using constants from db_credentials.php
            $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

            // Check connection.
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Prepare the query to fetch user data based on email.
            $stmt = $conn->prepare("SELECT id, name, password, activated, email FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($userId, $name, $hashedPassword, $activated, $email);
            $stmt->fetch();
            $stmt->close();

            // Verify the password using password_verify().
            if (password_verify($password, $hashedPassword)) {
                // Start a session and store user ID and name in session variables.
                session_start();
                $_SESSION['user_id'] = $userId;
                $_SESSION['name'] = $name;
                $_SESSION['activated'] = $activated;
                $_SESSION['email'] = $email;
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
				//echo "TEST";
				//echo $email;
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
					header("Location: main.php");
					exit;
				}
			}
			else
			{
				echo '<div class="error">Invalid email or password. Please try again.</div>';
			}
		
        }
	?>

    <h2>Login</h2>
	  <form id="login-form" class="login-form" method="post" action="">
        <div class="form-group">
          <label for="username">Email</label>
          <input type="email" id="semail" name="semail" value="<?php echo isset($email) ? $email : ''; ?>" required />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="spassword" name="spassword" required />
        </div>
        <!-- Display errors if there are any -->
        <div id="error-container" class="error-message"></div>

        <div class="form-group">
          <button type="button" href="main.html">
			<a onclick="document.getElementById('login-form').submit();">submit</a>
		   </button>
        </div>

        <div class="form-group">
          <p class="signup-link">
            Don't have an account? <a href="register.php">Sign up</a> 
          </p>

          <p class="signup-link">
              No activation link? <a href="request_activation.php">Resend Link</a> <br />
          </p>

          <p class="signup-link">
              Forgot password? <a href="reset_password.php">Reset Password</a>
          </p>
        </div>
      </form>
    </div>
    <script></script>
  </body>
</html>


<?php
// Clear the output buffer and send the content to the browser.
ob_end_flush();
?>

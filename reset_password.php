
<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include the database credentials file
require_once 'db_credentials.php';
require 'mailer/src/PHPMailer.php';
require 'mailer/src/SMTP.php';
require 'mailer/src/Exception.php';

// Start the session.
session_start();


// Function to generate a complex random password
function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_=+[]{}|;:,.<>?';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}



function sendPasswordEmail($email, $newPassword) {
    // Replace with your email content and headers.
    $message = "Hello,\n\nYour password has been reset. Your new password is: " . $newPassword;
    $headers = "From: noreply@yourdomain.com";

    //Create an instance; passing `true` enables exceptions
	$mail = new PHPMailer(true);

	try {
        //Server settings
		//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		$mail->isSMTP();                                            //Send using SMTP
		$mail->Host       = MAIL_SERVER;                     //Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		$mail->SMTPSecure = 'tls';
        $mail->Username   = MAIL_USERNAME;                     //SMTP username
		$mail->Password   = MAIL_USER_PWD;                               //SMTP password
		//$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
		$mail->Port       = MAIL_SERVER_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
		//Recipients
		$mail->setFrom( MAIL_USERNAME , 'Mailer');
		$mail->addAddress($email);     //Add a recipient
		$mail->addReplyTo( MAIL_USERNAME, 'Information');
		$mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

		//Content
		$mail->isHTML(true);                                  //Set email format to HTML
		$mail->Subject = 'Account Activation - ';
		$mail->Body    = $message;
		$mail->AltBody = $message ;

		$mail->send();

        

        return true;
	} catch (Exception $e) { 
        echo $e;
        return false;
	}
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate email address
    if (empty($_POST['email'])) {
        $error_message = "Please enter your email address.";
    } else {
        $email = $_POST['email'];

        // Connect to the database.
        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the user exists in the database
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists, generate a new random password
            $newPassword = generateRandomPassword();

            // Update the user table with the new password
            $updateQuery = "UPDATE users SET password = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $updateStmt->bind_param('ss', $hashedPassword, $email);
            $updateStmt->execute();

            // Send the new password to the user's email
            if (sendPasswordEmail($email, $newPassword)) {
                $success_message = "Your password has been reset. Please check your email for the new password.";
            } else {
                $error_message = "Failed to send the new password. Please try again later.";
            }
        } else {
            $error_message = "No user found with the provided email address.";
        }

        // Close the database connection
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Request Activation Link</title>
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

      .form-group input[type=text], input[type=password], input[type=email] {
        width: 100%;
        padding: 10px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
      }

      .form-group button, input[type=submit], input[type=reset] {
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
      <h2> <a style="color:#fff;text-decoration:none;" href="index.php">Stay Mate! </a></h2>
    </div>


    <div class="login-container">
      <h2> Reset Your Password </h2>
	  <form id="register-form" method="post" action="" onsubmit="return validateForm();">

        <?php
		
        if (isset($error_message)) {
            echo '<div class="error">' . $error_message . '</div>';
        } 
		
		if (isset($success_message)) {
            echo '<div class="success">' . $success_message . '</div>';
        }
        ?>
        <br />
        <p>Please enter your email address below to reset your password.</p>
       <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        
        <div class="form-group">
            <input type="submit" value="Reset Your Password">
        </div>


        <div class="form-group">
          <p class="signup-link">
                Already have an account? <a href="login.php">Log in here</a>
          </p>
        </div>


      </form>
    </div>
    <script></script>
  </body>
</html>
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

// Function to send account activation email.
function sendActivationEmail($email) {
    
	
	// Get the current server location
    $baseURL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    // Remove the current page name from the URL to get the base directory
    $baseURL = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $baseURL);
    $baseURL .= "activate.php?email=";
	
	
	
    $message = "Hello,\n\nYour account has been registered. Please click on the link below to activate your account.\n\nActivation Link: ". $baseURL . urlencode($email);
    $headers = "From: yasirsyedali003@gmail.com";

    // Send the email.
//    mail($email, $subject, $message, $headers);
	
	//Use PHP Mailer instead of mail 
	
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
    $email = $_POST['email'];

    // Validate input data (you can add more validation as needed).
    if (empty($email)) {
        $error_message = "All fields are required.";
    } else {
        // Try sending the activation email
        if (sendActivationEmail($email)) {
            $success_message = "Activation link sent successfully.";
        } else {
            $error_message = "Failed to send activation link. Please try again later. \n\n";
        }
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
      <h2>Request Activation Link</h2>
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

       <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        
        <div class="form-group">
            <input type="submit" value="Request Activation Link">
        </div>


        <div class="form-group">
          <p class="signup-link">
                Already have an account? <a href="login2.php">Log in here</a>
          </p>
        </div>


      </form>
    </div>
    <script></script>
  </body>
</html>
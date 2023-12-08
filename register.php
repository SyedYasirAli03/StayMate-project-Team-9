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
    // Replace with your email content and headers.
    //$subject = "Account Activation";
    //$headers = "From: noreply@technochannels.com";

	// Get the current server location
    $baseURL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    // Remove the current page name from the URL to get the base directory
    $baseURL = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $baseURL);
    $baseURL .= "activate.php?email=";
    $var1 = base64_encode($email);
    //$message = "Hello,\n\nYour account has been registered. Please click on the link below to activate your account.\n\nActivation Link:". $baseURL . urlencode($var1);    
    $message= "
    <html>
    <head> </head>
    <body>
    <img src ='data:image/png;base64,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' alt='Logo' />
     <p> Your account has been registered <br /> Please click on the link below to activate your account  </p>
    <p> <a href='" . $baseURL . urlencode($var1) ."'> Click here to activate </a> </p>
    </body>
   </html>
    ";
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
		$mail->setFrom( MAIL_USERNAME , 'Stay Mate');
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
		$mail->Subject = 'Account Activiation';
		$mail->Body    = $message;
		$mail->AltBody = $message ;

		$mail->send();
		//echo 'Message has been sent';
	} catch (Exception $e) {
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
}

// Function to check if the email or phone is already registered.
function isEmailOrPhoneRegistered($email, $phone) {

    // Connect to the database.
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	
    // Check connection.
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the query to check if the email exists.
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? or phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    // If a row is found, the email is already registered.
    $exists = $result->num_rows > 0;

    $stmt->close();
    $conn->close();

    return $exists;
}

// Function to register a new user.
function registerUser($name, $email, $password, $phone) {
	// Connect to the database.
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check connection.
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Hash the password.
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the query to insert the user data.
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $phone);
    $stmt->execute();

    // Send activation email.
    sendActivationEmail($email);

    $stmt->close();
    $conn->close();
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['fname'])) {
		$fname = $_POST['fname'];
	}
	if (isset($_POST['email'])) {
		$email = $_POST['email'];
	}
	if (isset($_POST['password'])) {
		$password = $_POST['password'];
    }
	if (isset($_POST['phone'])) {
		$phone = $_POST['phone'];
	}

    //var_dump($fname, $email, $password, $phone);

    	
    // Validate input data (you can add more validation as needed).
    if (empty($fname) || empty($email) || empty($password) || empty($phone)) {    
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email address.";
    } elseif (isEmailOrPhoneRegistered($email, $phone)) {
        $error_message = "Email or Phone is already registered.";
    } else {
        // Register the user and store the data in the database.
        registerUser($fname, $email, $password, $phone);
        $success_message = "Registration successful! An activation email has been sent to your email address.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register User</title>
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
    <script>
        // Client-side form validation
        function validateForm() {
            
            var fname = document.getElementById('fname').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            var phone = document.getElementById('phone').value;

            // Name validation
            if (fname.length < 5 || fname.length > 500) {
                alert("Name must be between 5 and 500 characters.");
                return false;
            }

            // Email validation
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            // Password validation
            if (password.length < 8 || password.length > 20) {
                alert("Password must be between 8 and 20 characters.");
                return false;
            }

            // Phone validation
            var phonePattern = /^[0-9]{10,}$/;
            if (!phonePattern.test(phone)) {
                alert("Phone number must be at least 10 digits.");
                return false;
            }

            return true;
        }
    </script>
  </head>
  <body>
    <div class="toolbar">
      <img src="images/logo.png" alt="Logo" />
      <h2> <a style="color:#fff;text-decoration:none;" href="index.php">Stay Mate! </a></h2>
    </div>


    <div class="login-container">
      <h2>User Registration</h2>

      <?php
        if (isset($error_message)) {
            echo '<div class="error">' . $error_message . '</div>';
        } elseif (isset($success_message)) {
            echo '<div class="success">' . $success_message . '</div>';
            //echo '<p>Already have an account? <a href="login.php">Log in here</a>.</p>';
        }
        ?>

      <form id="register-form" method="post" action="" onsubmit="return validateForm();">

       <div class="form-group">
          <label for="name">Name</label>
          <input type="text" id="fname" name="fname" required />
        </div>

       <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required />
        </div>

        <div class="form-group">
            <label>Phone Number </label>
            <input type="text" name="phone" id="phone" required>
        </div>

        <!-- Display errors if there are any -->
        <div id="error-container" class="error-message"></div>

        
        <div class="form-group">
            <input type="submit" value="Register">
            <input type="reset" value="Reset">
        </div>


        <div class="form-group">
          <p class="signup-link">
                Already have an account? <a href="login.php">Log in here</a>
          </p>
        </div>


      </form>
    </div>
  </body>
</html>
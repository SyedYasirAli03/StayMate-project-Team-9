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
    
	//$baseURL = "http://www.technochannels.com/careers/activate.php?email=";
	//$baseURL = "https://lgdsindh.gov.pk/slgb/jobsslgb/activate.php?email=";
	
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
		$mail->Host       = 'mail.trainings4u.com';                     //Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		//$mail->SMTPSecure = 'tls';
        //$mail->Port       = 587;
        $mail->Username   = 'yasir@trainings4u.com';                     //SMTP username
		$mail->Password   =  INFO_EMAIL_PW;                               //SMTP password
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
		$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
		//Recipients
		$mail->setFrom('yasir@trainings4u.com', 'Mailer');
		$mail->addAddress($email);     //Add a recipient
		$mail->addReplyTo('yasir@trainings4u.com', 'Information');
		
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
            $error_message = "Failed to send activation link. Please try again later.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Activation Link</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assuming styles.css contains the login page styles -->
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
            width: 111px;
            height: 130px;
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
        <h1>Request Activation Link</h1>
        
		<?php
		
        if (isset($error_message)) {
            echo '<div class="error">' . $error_message . '</div>';
        } 
		
		if (isset($success_message)) {
            echo '<div class="success">' . $success_message . '</div>';
        }
        ?>

		
		<p>Please enter your email address below to request an activation link.</p>
        <form action="" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Request Activation Link">
            </div>
        </form>
        <p>Already have an account? <a href="login.php">Log in here</a>.</p>
	
		
    </div>
</body>
</html>


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
    $message = "Hello,\n\nYour account has been registered. Please click on the link below to activate your account.\n\nActivation Link:". $baseURL . urlencode($var1);    
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
    if (isset($_POST['name'])) {
		$name = $_POST['name'];
	}
	if (isset($_POST['name'])) {
		$email = $_POST['email'];
	}
	if (isset($_POST['name'])) {
		$password = $_POST['password'];
    }
	if (isset($_POST['name'])) {
		$phone = $_POST['phone'];
	}
	
    // Validate input data (you can add more validation as needed).
    if (empty($name) || empty($email) || empty($password) || empty($phone)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email address.";
    } elseif (isEmailOrPhoneRegistered($email, $phone)) {
        $error_message = "Email or Phone is already registered.";
    } else {
        // Register the user and store the data in the database.
        registerUser($name, $email, $password, $phone);
        $success_message = "Registration successful! An activation email has been sent to your email address.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
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

    <script>
        // Client-side form validation
        function validateForm() {
            alert("validateform");
            var name = document.getElementById('name').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            var phone = document.getElementById('phone').value;

            // Name validation
            if (name.length < 5 || name.length > 500) {
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
    <div class="container">
	
		<?php
        if (isset($error_message)) {
            echo '<div class="error">' . $error_message . '</div>';
        } elseif (isset($success_message)) {
            echo '<div class="success">' . $success_message . '</div>';
            //echo '<p>Already have an account? <a href="login.php">Log in here</a>.</p>';
        }
        ?>
	
        <div class="header">
            <img src="images/logo.png" alt="Logo">
            <h1>User Registration</h1>
        </div>
        <form method="post" action="" onsubmit="return validateForm();">
            <label>Name:</label>
            <input type="text" name="name" id="name" required>

            <label>Email:</label>
            <input type="email" name="email" id="email" required>

            <label>Password:</label>
            <input type="password" name="password" id="password" required>

            <label>Phone Number:</label>
            <input type="text" name="phone" id="phone" required>

            <input type="submit" value="Register">
            <input type="reset" value="Reset">
        </form>
		<p>Already have an account? <a href="login.php">Log in here</a>.</p>

    </div>
</body>
</html>

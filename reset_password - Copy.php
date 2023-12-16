<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include the database credentials file
require_once 'db_credentials.php';
require 'mailer/src/PHPMailer.php';
require 'mailer/src/SMTP.php';
require 'mailer/src/Exception.php';

// Function to generate a complex random password
function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_=+[]{}|;:,.<>?';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

// Function to send email with new password
function sendPasswordEmail($email, $newPassword) {
    // Replace with your email content and headers.
    $message = "Hello,\n\nYour password has been reset. Your new password is: " . $newPassword;
    $headers = "From: noreply@yourdomain.com";

    // Use PHP Mailer instead of mail
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP(); // Send using SMTP
        $mail->Host       = '';                     //Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		$mail->Username   = '';                     //SMTP username
		$mail->Password   =  ;                               //SMTP password
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
		$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		//Recipients
		$mail->setFrom('info@lgdsindh.gov.pk', 'Mailer');
		$mail->addAddress($email);     //Add a recipient
		$mail->addReplyTo('info@lgdsindh.gov.pk', 'Information');
		
		//Content
		$mail->isHTML(true);                                  //Set email format to HTML
		$mail->Subject = '';
		$mail->Body    = $message;
		$mail->AltBody = $message ;

        $mail->send();
        return true;
    } catch (Exception $e) {
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

<!-- Rest of your HTML and CSS code -->


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
        <h1>Reset Your Password</h1>
        
		<?php
		
        if (isset($error_message)) {
            echo '<div class="error">' . $error_message . '</div>';
        } 
		
		if (isset($success_message)) {
            echo '<div class="success">' . $success_message . '</div>';
        }
        ?>

		
		<p>Please enter your email address below to reset your password.</p>
        <form action="" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Request Activation Link">
            </div>
        </form>
        <p>To Login : <a href="login.php">Log in here</a>.</p>
	
		
    </div>
</body>
</html>


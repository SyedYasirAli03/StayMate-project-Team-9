<!DOCTYPE html>
<html>
<head>
    <title>Account Activation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .success {
            color: green;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Include the database credentials file
        require_once 'db_credentials.php';

        // Function to update activation date/time and set 'activated' to 1 if not already activated.
        function activateUser($email) {
            // Connect to the database using constants from db_credentials.php
            $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

            // Check connection.
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check if the record is already activated.
            $stmt = $conn->prepare("SELECT activated FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($activated);
            $stmt->fetch();
            $stmt->close();

            if ($activated == 1) {
                // User is already activated.
                return false;
            }

            // Update activation date/time and set 'activated' to 1.
            $stmt = $conn->prepare("UPDATE users SET activated = 1, activated_at = CURRENT_TIMESTAMP WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            $activationSuccessful = $stmt->affected_rows > 0;

            $stmt->close();
            $conn->close();

            return $activationSuccessful;
        }

        // Handling account activation.
        $activationMessage = '';
        if (isset($_GET['email']) && !empty($_GET['email'])) {
            $email = $_GET['email'];
            $email= base64_decode($email);
            $activationSuccessful = activateUser($email);

            if ($activationSuccessful) {
                $activationMessage = "Account activated successfully!";
            } else {
                $activationMessage = "Activation link is expired or already used.";
            }
        }
        ?>

        <div class="header">
            <h1>Account Activation</h1>
        </div>

        <?php if (!empty($activationMessage)): ?>
            <div class="<?php echo $activationSuccessful ? 'success' : 'error'; ?>"><?php echo $activationMessage; ?></div>
        <?php endif; ?>

        <p>Click <a href="login.php">here</a> to login to your account.</p>
    </div>
</body>
</html>

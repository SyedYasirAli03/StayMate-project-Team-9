<?php
// Start output buffering to prevent header-related issues.
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
            padding: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .welcome-message {
            font-size: 18px;
            font-weight: bold;
            color: #333333;
        }

        .container {
            max-width: 80%;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
		
		 /* New styles for the tiles */
        .tile-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .tile {
            width: 45%;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .tile:hover {
            background-color: #e0e0e0;
        }

        .tile-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .tile-description {
            font-size: 14px;
        }
		
		
    </style>
</head>
<body>
    <?php
    // Start the session to access session variables.
    session_start();

    // Verify if the user session exists and contains user ID and name.
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
        // If the user is not logged in, redirect to the login page.
        header("Location: login.php");
        exit;
    }

    // Retrieve user ID and name from session variables.
    $userId = $_SESSION['user_id'];
    $name = $_SESSION['name'];
	
	/*if($userId == 2)
	{
		$_SESSION['ISADMIN'] = 1;
	}*/
	
    ?>

    <div class="header">
        <img src="images/logo.png" alt="Logo" class="logo">
		<a href="dashboard.php">Dashboard</a>
        <div class="welcome-message">Welcome, <?php echo $name; ?>!</div>
		<a href="#"><img src="images/notification_icon.png" height="30px" width="30px" alt="Notification Icon"></a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>User Dashboard</h1>
        <!-- Your dashboard content goes here... -->
		
		<!--
		<?php //if ($_SESSION['ISADMIN'] == 1): ?>
		<div>
			<a href="adminconsole.php">Admin</a>
		</div>
		<?php //endif; ?>
		-->
		
		<div class="tile-container">
        
		<!-- Tile 1: Job Application 1 -->
        <!--
		<div class="tile" onclick="location.href='job_application.php?grade=14';">
            <div class="tile-title">Job Application for BPS-14 </div>
            <div class="tile-description">Apply for Accountant position.</div>
        </div>
		-->
		
        <!-- Tile 2: Job Application 2 -->
        <div class="tile" onclick="location.href='add_lease_agreement';">
            <div class="tile-title">Add new lease agreement </div>
            <div class="tile-description">Lease agreement.</div>
        </div>
        <div class="tile" onclick="location.href='view_lease_agreement';">
            <div class="tile-title">view lease agreement </div>
            <div class="tile-description">Lease agreement.</div>
        </div>
    </div>
	
    </div>
</body>
</html>
<?php
// Clear the output buffer and send the content to the browser.
ob_end_flush();
?>


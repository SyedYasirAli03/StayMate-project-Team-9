<?php
// Include the database credentials file and any other necessary libraries
require_once 'db_credentials.php';
// Handle logout
// if (isset($_GET['logout'])) {
// 	unset($_SESSION['key']);
//     session_destroy();
//     header("Location: admin_console.php");
//     exit();
// }

// // Pre-defined key for authentication.
// $predefinedKey = '5C$wCY46dv8W8';

// Start the session.
session_start();
// Check if the session key is set and matches the predefined key
// if (!(isset($_SESSION['key']) && $_SESSION['key'] === $predefinedKey)) {
//     //echo "You are not authorized to access this page.<a href='?logout=1'>Logout</a>";
// 	header("Location: admin_console.php");
//     exit();
// }


// All good, now code will proceed!

// Number of users per page
$pageSize = 25;

// Current page number, default to 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $pageSize;

// Connect to the database.
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch last 10 users
$sqlcmd = "SELECT id,`landlord_name`,`tenant_name`,`created_at` FROM `lease_agreement` ";
$result = $conn->query($sqlcmd);




// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

	<header class="navbar navbar-expand-lg navbar-light bg-light">
	  <div class="container-fluid">
		<!-- Logo and menu items on the left -->
		<a class="navbar-brand" href="#">
		  <img src="images/full_logo.png" alt="Logo" height="43" width="200">
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
				aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		  <span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNav">
		  <ul class="navbar-nav">
			<li class="nav-item active">
			  <a class="nav-link" target="_blank" href="wall-614bd5ab-c186-4c31-991c-bf9c53e24489.php">Wallboard</a>
			</li>
			<li class="nav-item">
			  <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
			</li>
			<li class="nav-item">
			  <a class="nav-link" href="userlist_9c23ee53-a646-4c2c-b760-7089504f2f6c.php">User List</a>
			</li>
			<li class="nav-item">
			  <a class="nav-link" href="applicationlist_5422e01b-fa4c-4944-8fa3-22bc1e7196a6.php">Application</a>
			</li>
			<li class="nav-item">
			  <a class="nav-link" href="search_user_c6143026-c24f-4177-bcb1-a92afb4e8cf3.php">Search</a>
			</li>
		  </ul>
		</div>
        

		<!-- Logout link on the right -->
		<ul class="navbar-nav ms-auto">
		  <li class="nav-item">
			<a class="nav-link" href="?logout=1">Logout</a>
		  </li>
		</ul>
	  </div>
	</header>

    <div class="row">
		<div class="col-md-12">
			<h2>Last 10 Users</h2>
			<table class="table table-striped table-dark">
			<thead>
				<tr>
					<th>ID</th>
					<th>Landlord name</th>
					<th>Tenant name</th>
				</tr>
			</thead>
			<tbody>
			<?php while ($row = $result->fetch_assoc()) { ?>
			<tr>
				<td><?php echo $row['id']; ?></td>
				<td><?php echo $row['landlord_name']; ?></td>
				<td><?php echo $row['tenant_name']; ?></td>
			</tr>
			<?php } ?>
			</tbody>
			</table>
		</div>
	</div>

</body>
</html>

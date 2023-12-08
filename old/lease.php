<?php
// Include the database credentials file and any other necessary libraries
require_once 'db_credentials.php';


// Start the session.
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

    // All good, now code will proceed!

    // Connect to the database.
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Landlord View Query
   // Fetch all records
   $sql2 = "SELECT  * FROM `lease_agreement` where created_by = " . $userId ;
   $landlord_leases = $conn->query($sql2);

    // Close the database connection
    $conn->close();
?>

<!DOCTYPE html>
<html>
  <head>
    <style>
      #search-bar {
        margin-bottom: 20px;
        padding: 10px;
        background: #fff;
        border: none;
        border-radius: 8px;
        color: rgba(255, 255, 255, 0.1);
        width: 100%;
        box-sizing: border-box;
      }

      #content {
        flex: 1;
        padding: 20px;
      }

      #tiles {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 20px;
      }

      .tile {
        background: #e2ecf8;
        padding: 20px;
        text-align: center;
        border-radius: 8px;
        color: #3d3d3d;
      }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  </head>
  <body>
    <div id="content">
               
        <div class="app">
            <div class="col-md-12">
            <h3 class="white--text">Your Agreements</h3>

            <v-app>
                <v-app-bar color="#2A3950" dark clipped-left clipped-right flat app>
                    <a href="register2.php">
                        <v-btn class="ml-2" color="#325F85" dark> Register </v-btn>
                    </a>
                </v-app-bar>
            </v-app>
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Landlord name</th>
                            <th>Tenant name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $landlord_leases->fetch_assoc()) { ?>
                            <tr class="table-light">
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['landlord_name']; ?></td>
                                <td><?php echo $row['tenant_name']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
  </body>
</html>

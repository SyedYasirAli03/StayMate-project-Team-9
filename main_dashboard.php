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
  $sql = "select 
  (select count(1) from lease_agreement Where status=0 and created_by = " . $userId . " ) 'ACTIVE',
  (select count(1) from lease_agreement Where status=1 and created_by = " . $userId . ") 'COMPLETED' from dual;";

  $stmt = $conn->query($sql);

  if ($stmt) {
    $result = $stmt->fetch_assoc();

    $active_landlord = 0;
    $completed_landlord = 0;

    if ($result) {
        $active_landlord = $result['ACTIVE'];
        $completed_landlord = $result['COMPLETED'];

        //echo "Active: $active_landlord, Completed: $completed_landlord";
    }    
  } else {
    echo "Query failed";
  }


  // Fetch last 3 records
  $sql2 = "SELECT  * FROM `lease_agreement` where created_by = " . $userId . " ORDER BY created_at DESC LIMIT 3" ;
  //echo $sql2;
  $landlord_last3 = $conn->query($sql2);

  // Fetch Agreements to review as Tenant
  $sql3 = "SELECT l.* FROM `lease_agreement` l inner join `users` u WHERE l.tenant_email = u.email and u.id = " . $userId . " ORDER BY created_at";
  $tenant_to_review = $conn->query($sql3);


// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
  <head>
    <style>
      #content {
        flex: 1;
        padding: 10px;
      }

      #tiles {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin: 0px;
      }

      .tile {
        background: #d9d9d9;
        padding: 0px;
        text-align: center;
        border-radius: 8px;
        color: #3d3d3d;
        font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
      }
      .tile_number
      {
        font-size: 34px;
      }
    </style>
  </head>
  <body>
    <div id="content">
      <!-- Search Bar -->
      <!-- <input type="text" id="search-bar" placeholder="Search..." /> -->

      <!-- Admin View -->
      <!-- Check logged in user has ADMIN role, if yes show a summary dashboard -->

      <!-- Landlord View -->
          <!-- Check logged in user has any lease agreement created by him! This confirms he is a landlord -->
          <!-- Show Total agreements created by him with Active OR Completed status and a total count box -->
          <!-- We can also show last five (5) lease agreements -->

          <!-- Landlord Tiles -->
          <div id="tiles">
            <div class="tile">
              <h3>Total Agreements</h3>
              <p class="tile_number"> <?php echo $active_landlord + $completed_landlord; ?> </p>
            </div>

            <div class="tile">
              <h3>Active</h3>
              <p class="tile_number"> <?php echo $active_landlord; ?> </p>
            </div>
            <div class="tile">
              <h3> Completed </h3>
              <p class="tile_number"> <?php echo $completed_landlord; ?> </p>
            </div>
          </div>

          <br />
          <!-- Landlord records -->
          <style>
            
            h3 {
              font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            }
            table {
              border-collapse: collapse;
              width: 100%;
              border-radius: 10px; /* Set rounded corners */
              overflow: hidden; /* Ensure rounded corners are applied */
              background-color: #d9d9d9; /* Set grey background color */
              color:#000;
              margin-top: 10px;
              font-size: 14px;
              font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            }

            th, td {
              padding-left: 10px;
              padding-right: 10px;
              padding-top: 10px;
              padding-bottom: 10px;

              text-align: left;
              color:#000;
              border-bottom: 0px solid #ffffff; /* White border between rows */
            }

            th {
              background-color: #dddddd; /* Dark grey background for headers */
              color:#000;
            }
          </style>
          <h3 style="color: white"> Your Recent Lease Agreement(s) </h3>          
          <div style="padding-left: 0px; color: white; margin-top:   10px">

          <!-- $landlord_last3  -->
            <?php while ($row = $landlord_last3->fetch_assoc()) { ?>
            <table>
              <tr>
                <td>
                    <b> <a href="main.php?custom=view_agreement1.php?id=<?php echo strtoupper($row['id']) ?>" > <?php echo strtoupper($row['property_postcode']) ?> <a/> </b>
                      &nbsp; - &nbsp;  
                      <?php echo  date('d M Y', strtotime( $row['term_startdate'])) . " To " . date('d M Y', strtotime( $row['term_enddate']));  ?>
                      &nbsp; - &nbsp;  
                      <?php 
                        $status = $row['status'];
                        $status_string = ($status == 0 ) ? "Active" : "Completed" ;
                        echo $status_string;
                      ?>
                </td>  
                <td>
                </td>  
                <td>
                </td>  
                <td style="text-align:right;">
                <?php echo  date('d M Y H:i', strtotime( $row['created_at'])) ; ?>
                </td>  
              </tr>
              <tr>
                <td colspan="2">
                  <?php echo strtoupper ($row['property_address']); ?>  
                </td>  
                <td colspan="2" style="text-align:right;">
                  <b>Tenant: </b>
                  <?php echo $row['tenant_name'] . " (" .  $row['tenant_email'] . ", " . $row['tenant_phone']  . ")" ; ?>
                </td>  
              </tr>
            </table>
            <?php } ?>

          </div>

      <!-- Tenant View -->
      <!-- Check logged in user has any lease agreement created by him! This confirms he is a landlord -->
      <!-- Check lease agreements created by someone else as assigned to you. Show total Pending agreements and also previously completed -->
      <!-- We can also show last five (5) lease agreements created by someone else and assigned to you -->
    
      <br />
      <h3 style="color: white"> Agreements for your review as Tenant </h3>          
          <div style="padding-left: 0px; color: white; margin-top: 10px">

          <!-- $landlord_last3  -->
            <?php 
              $count = 0;
              while ($row = $tenant_to_review->fetch_assoc()) { ?>
              <table>
                <tr>
                  <td>
                      <b> <a href="main.php?custom=view_agreement1.php?id=<?php echo strtoupper($row['id']) ?>" >
                         <?php echo strtoupper($row['property_postcode']) ?> </a> </b>
                        &nbsp; - &nbsp;  
                        <?php echo  date('d M Y', strtotime( $row['term_startdate'])) . " To " . date('d M Y', strtotime( $row['term_enddate']));  ?>
                        &nbsp; - &nbsp;  
                        <?php 
                          $status = $row['status'];
                          $status_string = ($status == 0 ) ? "Active" : "Completed" ;
                          echo $status_string;
                        ?>
                  </td>  
                  <td>
                  </td>  
                  <td>
                  </td>  
                  <td style="text-align:right;">
                  <?php echo  date('d M Y H:i', strtotime( $row['created_at'])) ; ?>
                  </td>  
                </tr>
                <tr>
                  <td colspan="2">
                    <?php echo strtoupper ($row['property_address']); ?>  
                  </td>  
                  <td colspan="2" style="text-align:right;">
                    <b>Landlord: </b>
                    <?php echo $row['landlord_name'] . " (" .  $row['landlord_email'] . ", " . $row['landlord_phone']  . ")" ; ?> 
                  </td>  
                </tr>
              </table>
            <?php 
              $count = $count + 1;
              //echo $count;
              } 
              
              if($count <= 0)
              {
                echo "<h4> No Agreements to review! </h4>";
              }

              ?>

          </div>
    

      
    </div>
  </body>
</html>

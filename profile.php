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
    $sql2 = "SELECT  * FROM `users` where id = " . $userId;
    $stmt = $conn->query($sql2);

    if ($stmt) {
      $result = $stmt->fetch_assoc();
  
    
      if ($result) {
          $userEmail = $result['email'];
          $userPhone = $result['phone'];
          $created_at = $result['created_at'];
  
          //echo "Active: $active_landlord, Completed: $completed_landlord";
      }    
    } else {
      echo "Query failed";
    }

    
    // Close the database connection
    $conn->close();
  
?>



<!DOCTYPE html>
<html>
  <head>
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@mdi/font@3.x/css/materialdesignicons.min.css"
    />
  </head>

  <body>
    <div id="profile">
      <v-app style="background-color: #325f85">
        <v-container fluid>
          <v-row justify="center">
            <v-col cols="12" md="8">
              <v-card class="text-center pa-5">
                <v-avatar size="150">
                  <v-img
                    src="https://cdn.iconscout.com/icon/free/png-256/free-avatar-370-456322.png?f=webp"
                    alt="Profile Image"
                  ></v-img>
                </v-avatar>
                <div class="text-center">
                  <v-card-text class="headline pb-0 mb-0"> <?php echo $name; ?> </v-card-text>
                </div>
                <v-card-subtitle class="text-center"> Created At : <?php echo  date('d M Y', strtotime( $created_at )); ?> </v-card-subtitle>
                <v-divider></v-divider>
                <v-card-text>
                  <v-row>
                    <v-col cols="6">
                      <v-list dense>
                        <v-list-item-group v-if="buttonClicked > 0">
                          <v-list-item>
                            <v-list-item-icon>
                              <v-icon color="success">mdi-check</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title
                                >Profile Updated</v-list-item-title
                              >
                            </v-list-item-content>
                          </v-list-item>
                        </v-list-item-group>
                      </v-list>
                      <v-btn @click="handleButtonClick" color="primary"
                        > Update Profile </v-btn
                      >
                    </v-col>
                    <v-col cols="6">
                      <!-- Additional profile information goes here -->
                      <v-list dense>
                        <v-list-item>
                          <v-list-item-content>
                            <v-list-item-title> <?php echo $userEmail; ?> </v-list-item-title
                            >
                          </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                          <v-list-item-content>
                            <v-list-item-title> Contact: <?php echo $userPhone; ?> </v-list-item-title
                            >
                          </v-list-item-content>
                        </v-list-item>
                      </v-list>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-app>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
    <script>
      new Vue({
        el: "#profile",
        vuetify: new Vuetify(),
      });
    </script>
  </body>
</html>

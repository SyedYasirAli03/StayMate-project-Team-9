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
    <div id="legal">
      <v-app style="background-color: #325f85">
        <v-container fluid>
          <v-row justify="center">
            <v-col cols="12" md="12">
              <v-card dark flat color="#325F85">
                <v-card-title class="headline text-center"
                  >Legal Information</v-card-title
                >
                <v-divider></v-divider>
                <v-card-text>
                  <v-alert icon="mdi-information" color="info" dense outlined>
                    Please read the following legal information carefully before
                    using the Lease Agreement Portal.
                  </v-alert>
                  <v-subheader class="headline pl-0"
                    >Terms of Service</v-subheader
                  >
                  <v-divider></v-divider>
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Quisque mattis quam eget risus feugiat, in euismod nisi
                    bibendum. Nullam id ultrices velit. Proin feugiat metus vel
                    libero ultrices, nec elementum velit volutpat.
                  </p>

                  <v-subheader class="headline pl-0"
                    >Privacy Policy</v-subheader
                  >
                  <v-divider></v-divider>
                  <p>
                    Integer vestibulum urna sit amet purus euismod, et elementum
                    purus ullamcorper. Aenean ut odio ut mauris maximus
                    fermentum. Vivamus commodo urna nec feugiat lacinia. Duis
                    feugiat quam nec metus vulputate, eu cursus turpis gravida.
                  </p>

                  <v-subheader class="headline pl-0">Disclaimer</v-subheader>
                  <v-divider></v-divider>
                  <p>
                    Sed nec tincidunt turpis, vel tempus purus. Curabitur
                    consequat neque eget mauris aliquam, sit amet congue ligula
                    eleifend. Aenean non odio nec elit venenatis tristique.
                  </p>
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
        el: "#legal",
        vuetify: new Vuetify(),
      });
    </script>
  </body>
</html>

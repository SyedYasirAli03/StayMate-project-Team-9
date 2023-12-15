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
  $email = $_SESSION['email'];

  $custom = "main_dashboard.php";
  if (isset($_GET['custom']) && !empty($_GET['custom'])) {
    // If the user is not logged in, redirect to the login page.
    $custom = $_GET['custom'];
  }
   

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
    <script src="./lib/html2pdf.bundle.js"></script>

  </head>


  <body>
    <div id="app">
      <v-app>
        <v-navigation-drawer color="#E2ECF8" clipped app left>
          <!-- Left Drawer for Menu Items -->
          <v-list>
            <v-list-item-group>
              <v-list-item
                v-for="(item, index) in menuItems"
                :key="index"
                @click="loadContent(item.route)"
                :class="{ 'v-list-item--active': item.route === activeItem }"
              >
                <v-list-item-icon>
                  <v-icon>{{ item.icon }}</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>{{ item.text }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
          <template v-slot:append>
            <div class="pa-2">
            <a href="logout.php">
              <v-btn color="#2A3950" dark block> Logout </v-btn>
            </a>
            </div>
          </template>
        </v-navigation-drawer>

        <v-app-bar color="#2A3950" dark clipped-left clipped-right flat app>
          <!-- Add the logo using v-img -->
          <v-img
            style="max-width: 40px"
            src="images/logo.png"
            max-height="40"
            contain
          ></v-img>

          <!-- Add the toolbar title -->
          <v-toolbar-title class="ml-2">Stay Mate!</v-toolbar-title>
          <v-spacer></v-spacer>

          <!-- Add your label on the top right -->
          <v-toolbar-title style="text-align:right;">
          Welcome <span style="color: #1ee;"> <?php echo $name;  ?> </span> <br />
            <span style="color: #1ee; font-size: small;">  <?php echo $email;  ?> </span>
          </v-toolbar-title>
        </v-app-bar>

        <!-- Main Content -->
        <v-main style="background-color: #325f85">
          <v-container style="height: 100%; width: 100%;">           
            <div style="height: 100%; width: 100%" id="appForm"></div>
          </v-container>
        </v-main>
      </v-app>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
    <script src="https://unpkg.com/vue-router@3.0.0/dist/vue-router.js"></script>
    <!-- <script type="text/x-template" src="/dashboard.html"></script> -->
    <script src="https://cdn.botpress.cloud/webchat/v1/inject.js"></script>
    <script
      src="https://mediafiles.botpress.cloud/010386d7-491d-4ed8-bc44-5181875327c8/webchat/config.js"
      defer
    ></script>
  </body>
  <script>
    new Vue({
      el: "#app",
      vuetify: new Vuetify(),
      data() {
        return {
          drawerLeft: false,
          drawerRight: false,
          activeItem: "<?php echo $custom; ?>",
          selectedRoute: "<?php echo $custom; ?>", // Default route
          menuItems: [
            {
              text: "Dashboard",
              icon: "mdi-view-dashboard-outline",
              route: "main_dashboard.php",
            },
            {
              text: "Lease Agreement",
              icon: "mdi-newspaper",
              route: "lease2.php",
            },
            {
              text: "Legal Information",
              icon: "mdi-information",
              route: "legalInfo.php",
            },
            {
              text: "Account Settings",
              icon: "mdi-account",
              route: "profile.php",
            },
            {
              text: "Notifications",
              icon: "mdi-bell",
              route: "notifications.php",
            },
            // { text: "Chat Bot", icon: "mdi-message", route: "chat.html" },
          ],
          notifications: [
            { text: "New Message", icon: "mdi-message" },
            { text: "Notification 1", icon: "mdi-bell" },
            { text: "Notification 2", icon: "mdi-bell" },
          ],
        };
      },
      methods: {
        loadContent(file) {
          // Update the selectedRoute property
          // this.selectedRoute = file;
          this.activeItem = file;
          fetch(file)
            .then((response) => response.text())
            .then((html) => {
              document.getElementById("appForm").innerHTML = html;

              // Execute scripts in the loaded HTML
              const scripts = document
                .getElementById("appForm")
                .querySelectorAll("script");
              scripts.forEach((script) => {
                eval(script.innerHTML);
              });
            })
            .catch((error) => {
              console.error("Error loading file:", error);
            });
        },
      },
      mounted() {
        this.loadContent("<?php echo $custom; ?>");
      },      
    });
  </script>
</html>


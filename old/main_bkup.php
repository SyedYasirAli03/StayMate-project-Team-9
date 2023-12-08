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
    <div id="app">
      <v-app>
        <v-navigation-drawer color="#E2ECF8" clipped app left>
          <!-- Left Drawer for Menu Items -->
          <v-list>
            <v-list-item-group>
              <v-list-item
                v-for="(item, index) in menuItems"
                :key="index"
                :to="item.route"
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
          <script src='https://cdn.botpress.cloud/webchat/v1/inject.js'></script>
    <script src='https://mediafiles.botpress.cloud/010386d7-491d-4ed8-bc44-5181875327c8/webchat/config.js' defer></script>

        <template v-slot:append>
            <div class="pa-2">
            <a href="logout2.php">
              <v-btn color="#2A3950" dark block> Logout </v-btn>
            </a>
            </div>
          </template>

        </v-navigation-drawer>

        <v-navigation-drawer color="#E2ECF8" clipped app right>
          <!-- Right Drawer for Notifications -->
          <v-list>
            <v-list-item-group>
              <v-list-item
                v-for="(notification, index) in notifications"
                :key="index"
              >
                <v-list-item-icon>
                  <v-icon>{{ notification.icon }}</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>{{ notification.text }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
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
          <v-toolbar-title class="ml-2">
          <a style="color:#fff;text-decoration:none;" href="index.php">Stay Mate! </a>  
          </v-toolbar-title>
          <v-spacer></v-spacer>

          <!-- Add your label on the top right -->
          <v-toolbar-title>
            <span style="color: #fff;"> Welcome <?php echo $name; ?> </span>
          </v-toolbar-title>
        
        </v-app-bar>

        <!-- Main Content -->
        <v-main style="background-color: #325f85">
          <v-container>
            <!-- Your main content goes here -->
            <router-view></router-view>
            <!-- <v-row>
              <v-col>
                <v-card>
                  <v-card-text> Your content goes here. </v-card-text>
                </v-card>
              </v-col>
            </v-row> -->
          </v-container>
        </v-main>
      </v-app>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
    <script src="https://unpkg.com/vue-router@3.0.0/dist/vue-router.js"></script>
  </body>
  <script>
    const Home = {
      template: '<div v-html="htmlContent"></div>',
      data() {
        return {
          htmlContent: "",
        };
      },
      created() {
        // Fetch the content of the HTML file
        fetch("main_dashboard.php")
          .then((response) => response.text())
          .then((html) => {
            // Set the HTML content
            this.htmlContent = html;
          })
          .catch((error) => console.error("Error loading content:", error));
      },
    };

    const Lease = {
      template: '<div v-html="htmlContent"></div>',
      data() {
        return {
          htmlContent: "",
        };
      },
      created() {
        // Fetch the content of the HTML file
        fetch("lease.php")
          .then((response) => response.text())
          .then((html) => {
            // Set the HTML content
            this.htmlContent = html;
          })
          .catch((error) => console.error("Error loading content:", error));
      },
    };

    const LegalInfo = {
      template: `
    <v-container fluid>
      <v-row justify="center">
        <v-col cols="12" md="12">
          <v-card dark flat color="#325F85">
            <v-card-title class="headline text-center">Legal Information</v-card-title>
            <v-divider></v-divider>
            <v-card-text>
              <v-alert
                icon="mdi-information"
                color="info"
                dense
                outlined
              >
                Please read the following legal information carefully before using the Lease Agreement Portal.
              </v-alert>
              <v-subheader class="headline pl-0">Terms of Service</v-subheader>
              <v-divider></v-divider>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque mattis quam eget risus feugiat, in euismod nisi bibendum.
                Nullam id ultrices velit. Proin feugiat metus vel libero ultrices, nec elementum velit volutpat.
              </p>

              <v-subheader class="headline pl-0">Privacy Policy</v-subheader>
              <v-divider></v-divider>
              <p>
                Integer vestibulum urna sit amet purus euismod, et elementum purus ullamcorper. Aenean ut odio ut mauris maximus fermentum.
                Vivamus commodo urna nec feugiat lacinia. Duis feugiat quam nec metus vulputate, eu cursus turpis gravida.
              </p>

              <v-subheader class="headline pl-0">Disclaimer</v-subheader>
              <v-divider></v-divider>
              <p>
                Sed nec tincidunt turpis, vel tempus purus. Curabitur consequat neque eget mauris aliquam, sit amet congue ligula eleifend.
                Aenean non odio nec elit venenatis tristique.
              </p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  `,
    };

    const Notifications = {
      data() {
        return {
          notifications: [
            {
              id: 1,
              message: "New message from John Doe",
              timestamp: "2 minutes ago",
              type: "info",
            },
            {
              id: 2,
              message: "Task completed successfully",
              timestamp: "1 hour ago",
              type: "success",
            },
            {
              id: 3,
              message: "Error: Unable to save changes",
              timestamp: "yesterday",
              type: "error",
            },
          ],
        };
      },
      methods: {
        removeNotification(id) {
          this.notifications = this.notifications.filter(
            (notification) => notification.id !== id
          );
        },
      },
      template: `
    <v-container fluid>
      <v-row justify="center">
        <v-col cols="12" md="12">
          <v-card flat color="#325F85">
            <v-card-title class="headline text-center white--text">Notifications</v-card-title>
            <v-divider dark></v-divider>
            <v-list color="#325F85" dark>
              <v-list-item-group v-if="notifications.length > 0">
                <v-list-item
                  v-for="notification in notifications"
                  :key="notification.id"
                  :class="{ 'notification-info': notification.type === 'info', 'notification-success': notification.type === 'success', 'notification-error': notification.type === 'error' }"
                >
                  <v-list-item-content>
                    <v-list-item-title>{{ notification.message }}</v-list-item-title>
                    <v-list-item-subtitle>{{ notification.timestamp }}</v-list-item-subtitle>
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-btn icon @click="removeNotification(notification.id)">
                      <v-icon>mdi-close</v-icon>
                    </v-btn>
                  </v-list-item-action>
                </v-list-item>
              </v-list-item-group>
              <v-list-item v-else>
                <v-list-item-content>No notifications</v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  `,
    };

    const Profile = {
      data() {
        return {
          buttonClicked: 0,
        };
      },
      methods: {
        handleButtonClick() {
          this.buttonClicked++;
        },
      },
      template: `
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
            <v-card-text class="headline pb-0 mb-0">User Profile</v-card-text>
            </div>
            <v-card-subtitle class="text-center">Web Developer</v-card-subtitle>
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
                          <v-list-item-title>Profile Updated</v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                  <v-btn @click="handleButtonClick" color="primary">Update Profile</v-btn>
                </v-col>
                <v-col cols="6">
                  <!-- Additional profile information goes here -->
                  <v-list dense>
                    <v-list-item>
                      <v-list-item-content>
                        <v-list-item-title>Email: user@example.com</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                      <v-list-item-content>
                        <v-list-item-title>Location: City, Country</v-list-item-title>
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
  `,
    };

    const Settings = { template: "<div><h2>Settings</h2></div>" };
    const Chatbot = {
      template: '<div v-html="htmlContent"></div>',
      data() {
        return {
          htmlContent: "",
        };
      },
      created() {
        // Fetch the content of the HTML file
        fetch("chat.html")
          .then((response) => response.text())
          .then((html) => {
            // Set the HTML content
            this.htmlContent = html;
          })
          .catch((error) => console.error("Error loading content:", error));
      },
    };

    const router = new VueRouter({
      routes: [
        { path: "/", component: Home },
        { path: "/lease", component: Lease },
        { path: "/profile", component: Profile },
        { path: "/settings", component: Settings },
        { path: "/chat", component: Chatbot },
        { path: "/notification", component: Notifications },
        { path: "/legal", component: LegalInfo },
      ],
    });
    new Vue({
      el: "#app",
      vuetify: new Vuetify(),
      router,
      data() {
        return {
          drawerLeft: false,
          drawerRight: false,
          menuItems: [
            {
              text: "Dashboard",
              icon: "mdi-view-dashboard-outline",
              route: "/",
            },
            {
              text: "Lease Agreement",
              icon: "mdi-newspaper",
              route: "/lease",
            },
            {
              text: "Legal Information",
              icon: "mdi-information",
              route: "/legal",
            },
            {
              text: "Account Settings",
              icon: "mdi-account",
              route: "/profile",
            },
            { text: "Notifications", icon: "mdi-bell", route: "/notification" },
            { text: "Chat Bot", icon: "mdi-message", route: "/chat" },
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
          // Fetch the content of the HTML file
          fetch(file)
            .then((response) => response.text())
            .then((html) => {
              // Inject the HTML content into the main-content div
              document.getElementById("main-content").innerHTML = html;
            })
            .catch((error) => console.error("Error loading content:", error));
        },
      },
    });
  </script>
</html>

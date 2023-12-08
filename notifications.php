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
    <div id="noti">
      <v-app style="background-color: #325f85">
        <v-container fluid>
          <v-row justify="center">
            <v-col cols="12" md="12">
              <v-card flat color="#325F85">
                <v-card-title class="headline text-center white--text"
                  >Notifications</v-card-title
                >
                <v-divider dark></v-divider>
                <v-list color="#325F85" dark>
                  <v-list-item-group v-if="notifications.length > 0">
                    <v-list-item
                      v-for="notification in notifications"
                      :key="notification.id"
                      :class="{ 'notification-info': notification.type === 'info', 'notification-success': notification.type === 'success', 'notification-error': notification.type === 'error' }"
                    >
                      <v-list-item-content>
                        <v-list-item-title
                          >{{ notification.message }}</v-list-item-title
                        >
                        <v-list-item-subtitle
                          >{{ notification.timestamp }}</v-list-item-subtitle
                        >
                      </v-list-item-content>
                      <v-list-item-action>
                        <v-btn
                          icon
                          @click="removeNotification(notification.id)"
                        >
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
      </v-app>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
    <script>
      new Vue({
        el: "#noti",
        vuetify: new Vuetify(),
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
      });
    </script>
  </body>
</html>

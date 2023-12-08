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
        <v-app-bar color="#2A3950" dark clipped-left clipped-right flat app>
          <!-- Add the logo using v-img -->
          <v-img style="max-width: 40px" src="images/logo.png" max-height="40" contain></v-img>
          <!-- Add the toolbar title -->
          <v-toolbar-title class="ml-2">Stay Mate!</v-toolbar-title>
          <v-spacer></v-spacer>
          <a href="register.php">
            <v-btn class="ml-2" color="#325F85" dark> Register </v-btn>
          </a>
          <a href="login.php">
            <v-btn class="ml-2" color="#F1D185" dark> Login </v-btn>
          </a>
        </v-app-bar>
        <!-- Main Content -->
        <v-main style="background: url('images/landing.jpg') center center fixed;
              background-size: cover;">
          <v-card-text>
            <v-row>
              <v-col cols="12">
                <v-card class="mx-5 pa-5 rounded-xl" style="justify-content: center;
                          display: flex;opacity: 0.9">
                  <v-card-text style="max-width: 500px" class="text-center">
                    <div style="font-size: 22px" class="font-weight-bold black--text">Stay Mate! Lease Revolution</div>
                    <div class="mt-5 text--primary"> Tire of leasing the old-fashioned way? Time to experience the thrill of state-of-the-art lease agreements, exclusively with Stay Mate! Click the button to begin your journey </div>
                    <a href="#">
                      <v-btn class="mt-5" color="#55a149" dark rounded> Get Started </v-btn>
                    </a>
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12">
                <v-card class="mx-5 pa-5 rounded-xl" style="justify-content: center;
                          display: flex;opacity: 0.9">
                  <v-card-text class="text-center">
                    <div style="font-size: 22px" class="font-weight-bold black--text">Why Stay Mate! ?</div>
                    <v-row class="mt-5">
                      <v-col class="text-center">
                        <div style="font-size: 16px" class="font-weight-bold black--text pb-2">Intuitive Interface</div>
                        <div style="max-width: 180px" class="text--primary mx-auto"> Our Platform is a piece of cake to use. You'll love it! </div>
                      </v-col>
                      <v-col class="text-center">
                        <div style="font-size: 16px" class="font-weight-bold black--text pb-2">Interactive Agreements</div>
                        <div style="max-width: 180px" class="text--primary mx-auto"> Solve leasing issues with our interactive agreements. No more headaches. </div>
                      </v-col>
                    </v-row>
                    <a href="#">
                      <v-btn class="mt-5" color="#55a149" dark rounded> Get Started </v-btn>
                    </a>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-main>
      </v-app>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
    <script src="https://unpkg.com/vue-router@3.0.0/dist/vue-router.js"></script>
    <script src='https://cdn.botpress.cloud/webchat/v1/inject.js'></script>
    <script src='https://mediafiles.botpress.cloud/010386d7-491d-4ed8-bc44-5181875327c8/webchat/config.js' defer></script>
  </body>
  <script>
    new Vue({
      el: "#app",
      vuetify: new Vuetify(),
    });
  </script>
</html>

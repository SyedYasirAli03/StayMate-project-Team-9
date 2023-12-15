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
   $sql2 = "SELECT  * FROM `lease_agreement` where created_by = " . $userId . " ORDER BY created_at desc ";
   $landlord_leases = $conn->query($sql2);

    // Close the database connection
    $conn->close();
?>


<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
    <style>
      .theme--light .v-text-field .v-icon {
        font-size: 20px;
      }
      body {
        overflow-y: auto;
      }
    </style>
  </head>

  <body>
    <div id="lease">
      <v-app style="background-color: #325f85">
        <v-container style="min-height: 100vh;">
          <div v-if="!addAgreement">
            <h2 class="white--text">Your Agreements</h2>

            <v-row justify="end" class="pa-2">
              <v-btn
                color="#2A3950"
                dark
                class="mb-2"
                @click="openAgreementForm"
                id="addAgreementBtn"
              >
                Add New Agreement
              </v-btn>
            </v-row>

            <v-row style=" height:80vh; overflow:auto; ">
            <v-card
              color="#d9d9d9"
              style="width: 100%;"
              v-for="(agreement, index) in agreements"
              :key="index"
              class="mb-3 pa-2 px-4"
            >
              <v-row>
                <v-col>
                  <div class="font-weight-medium">Tenant Name:</div>
                  <div>{{ agreement.name }}</div>
                  <small>Signed On: {{ agreement.signedOn }} </small>
                </v-col>

                <v-col>
                  <div class="font-weight-medium">Landlord:</div>
                  <div>{{ agreement.landlord }}</div>
                </v-col>

                <v-col>
                  <div class="font-weight-medium">Dates:</div>
                  <small>{{ agreement.dates }}</small>
                </v-col>
                

                <v-col class="d-flex align-center justify-end" cols="4">
                
                <small> {{ agreement.propertyAddress }}  - {{ agreement.propertyPostCode }}  </small>
                  <v-icon
                    @click="downloadPdf(agreement.name)"
                    color="blue"
                    style="margin-right: 10px"
                    >mdi-download</v-icon
                  >
                  <v-icon color="blue" @click="openPdfInNewTab(agreement.name)">
                    mdi-eye
                  </v-icon>
                </v-col>
              </v-row>
            </v-card>
            </v-row>
          </div>
          <div
            style="width: 100%; height: 100%"
            id="addAgreement"
            v-else-if="addAgreement"
          >
            <h2 class="white--text">Add Agreement</h2>
            <div id="appForm"></div>
            <!-- <iframe
              src="agreementForm.php"
              frameborder="0"
              style="width: 100%; height: calc(100vh - 80px)"
            ></iframe> -->
            <!-- <object
              data="agreementForm.html"
              type="text/html"
              width="100%"
              height="500"
            ></object> -->
          </div>
        </v-container>
      </v-app>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
    <script>
      new Vue({
        el: "#lease",
        vuetify: new Vuetify(),
        data() {
          return {
            addAgreement: false,
            agreements: [
              <?php while ($row = $landlord_leases->fetch_assoc()) { ?>            
              {
                name: "<?php echo $row['tenant_name']; ?>",
                signedOn: "<?php echo  date('d M Y', strtotime( $row['term_startdate'])); ?>",
                propertyPostCode: "<?php echo strtoupper($row['property_postcode']) ?>",
                propertyAddress: "<?php echo str_replace(["\r\n", "\r", "\n"], ' ', strtoupper($row['property_address'])); ?>",
                landlord: "<?php echo $row['landlord_name']; ?>",
                dates: "<?php echo  date('d M Y', strtotime( $row['term_startdate'])) . " To " . date('d M Y', strtotime( $row['term_enddate']));  ?>",
              },
              <?php } ?>
              // Add more agreements as needed
            ],
          };
        },
        methods: {
          openAgreementForm() {
            debugger;
            // Store a reference to the outer 'this'
            const outerThis = this;
            this.addAgreement = true;
            fetch("agreementForm.php")
              .then((response) => response.text())
              .then((html) => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");

                // Extract the div and script elements
                const divContent = doc.querySelector("template").innerHTML;
                const scriptContent = doc.querySelector("script").innerHTML;

                // Set the div content into the app container
                document.getElementById("appForm").innerHTML = divContent;
                eval(scriptContent);
              });
          },

          // closeAddAgreementDialog() {
          //   this.addAgreementDialog = false;
          // },
          loadFileAndShowContent() {
            // Your logic for loading file and showing content
          },
          downloadPdf(fileName) {
            // Assuming the PDF file is in the same folder
            const filePath = `${fileName}.pdf`;

            // Create an anchor element to trigger the download
            const link = document.createElement("a");
            link.href = filePath;
            link.download = fileName;

            // Append the anchor to the document and click it to trigger the download
            document.body.appendChild(link);
            link.click();

            // Remove the anchor from the document
            document.body.removeChild(link);
          },
          openPdfInNewTab(fileName) {
            const filePath = `${fileName}.pdf`;
            window.open(filePath, "_blank");
          },
        },
      });
    </script>
  </body>
</html>

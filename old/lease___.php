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
    </style>
  </head>

  <body style="overflow-y: auto; padding: 10px">
    <div id="lease">
      <v-app style="background-color: #325f85">
        <v-container>
          <h2 class="white--text">Your Agreements</h2>

          <v-row justify="end" class="pa-2">
            <v-btn
              color="#2A3950"
              dark
              class="mb-2"
              @click="openAddAgreementDialog"
              id="addAgreementBtn"
            >
              Add New Agreement
            </v-btn>
          </v-row>

          <v-row>
            <v-card
              color="#d9d9d9"
              style="width: 100%"
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
          <v-dialog v-model="addAgreementDialog" max-width="600">
            <v-card>
              <v-card-title>
                <span class="headline">Add New Agreement</span>
              </v-card-title>

              <v-card-text>
                <!-- Form fields for the new agreement -->
                <v-form @submit.prevent="addNewAgreement">
                  <!-- Example form fields, replace with your actual form fields -->
                  <v-text-field
                    v-model="newAgreement.landlord_name"
                    label="Landlord Name"
                  ></v-text-field>
                  <v-text-field
                    v-model="newAgreement.landlord_address"
                    label="Landlord Address"
                  ></v-text-field>
                  <v-text-field
                    v-model="newAgreement.tenant_name"
                    label="Tenant Name"
                  ></v-text-field>
                  <v-text-field
                    v-model="newAgreement.tenant_address"
                    label="Tenant Address"
                  ></v-text-field>
                  <v-text-field
                    v-model="newAgreement.landlord"
                    label="Landlord"
                  ></v-text-field>
                  <v-text-field
                    v-model="newAgreement.dates"
                    label="Dates"
                  ></v-text-field>

                  <!-- Add more form fields as needed -->

                  <!-- Dialog actions -->
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="closeAddAgreementDialog">Cancel</v-btn>
                    <v-btn type="submit" color="primary">Add Agreement</v-btn>
                  </v-card-actions>
                </v-form>
              </v-card-text>
            </v-card>
          </v-dialog>

          <div id="loadedContent" style="display: none"></div>
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
            addAgreementDialog: false,
            newAgreement: {
              landlord_name: "",
              landlord_address: "",
              landlord_email: "",
              landlord_phone: "",
              tenant_name: "",
              tenant_address: "",
              tenant_email: "",
              tenant_phone: "",
              propertyPostCode: "",
              propertyAddress: "",
              term_startdate: "",
              term_enddate: "",
              dates: "",
              // Add more fields as needed
            },
          };
        },
        methods: {
          openAddAgreementDialog() {
            this.addAgreementDialog = true;
          },
          closeAddAgreementDialog() {
            this.addAgreementDialog = false;
          },
          addNewAgreement() {
            // Your logic to add the new agreement to the agreements array
            this.agreements.push({ ...this.newAgreement });
            // Reset the form fields
            this.newAgreement = {
              landlord_name: "",
              landlord_address: "",
              landlord_email: "",
              landlord_phone: "",
              tenant_name: "",
              tenant_address: "",
              tenant_email: "",
              tenant_phone: "",
              propertyPostCode: "",
              propertyAddress: "",
              term_startdate: "",
              term_enddate: "",
              dates: "",
            };
            // Close the dialog
            this.closeAddAgreementDialog();
          },
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

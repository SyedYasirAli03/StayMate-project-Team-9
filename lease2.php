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

    //Now Check if form is loading for a GET request!
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
      //Set default data for dates etc.
      $todayDate = date("Y-m-d");
    }
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

  <body>
    <div id="lease">
      <v-app style="background-color: #325f85">
        <v-container>
        <div v-if="viewAgreement">
          <h2 class="white--text">Your Agreements</h2>
          <v-btn @click="closeViewAgreementDialog">Cancel</v-btn>

        </div>
          <div v-if="!addAgreement && !viewAgreement">
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

            <v-row>
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
                  <!-- <v-icon
                    @click="downloadPdf(agreement.name)"
                    color="blue"
                    style="margin-right: 10px"
                    >mdi-download</v-icon
                  > -->

                  <a :href="getAgreementUrl(agreement.id)">                    
                    <v-icon color="blue">
                      mdi-eye
                    </v-icon>
                  </a>
                </v-col>
              </v-row>
            </v-card>
            </v-row>
          </div>
          <div
            style="width: 100%; height: 100%"
            id="addAgreement"
            v-else-if="addAgreement && !viewAgreement"
          >
            <h2 class="white--text">Add Agreements</h2>
            <v-card-text style="background-color: #325f85">
              <!-- Form fields for the new agreement -->
              <v-form ref="myForm" @submit.prevent="submitForm">
            <v-row>
              <v-col class="pb-0">
                <v-menu
                  ref="termStartDateMenu"
                  v-model="termStartDateMenu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      class="pt-2"
                      dense
                      outlined
                      solo
                      v-model="newAgreement.term_start_date"
                      label="Term Start Date"
                      readonly
                      v-on="on"
                      prepend-inner-icon="mdi-calendar"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="newAgreement.term_start_date"
                    @input="termStartDateMenu = false"
                  ></v-date-picker>
                </v-menu>
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.landlord_name"
                  label="Landlord Name"
                  :rules="requiredRule"
                  prepend-inner-icon="mdi-account"
                ></v-text-field>
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.landlord_address"
                  label="Landlord Address"
                  :rules="requiredRule"
                  prepend-inner-icon="mdi-map-marker"
                ></v-text-field>
                <v-text-field
                  dense
                  solo
                  v-model="newAgreement.landlord_email"
                  label="Landlord Email"
                  :rules="[requiredRule, emailRule]"
                  prepend-inner-icon="mdi-email"
                ></v-text-field>
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.landlord_phone"
                  label="Landlord Phone"
                  :rules="[requiredRule, phoneRule]"
                  prepend-inner-icon="mdi-phone"
                ></v-text-field>
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.landlord_sign"
                  label="Landlord Signature"
                  :rules="requiredRule"
                  prepend-inner-icon="mdi-draw"
                ></v-text-field>
                <v-menu
                  ref="landlordSignDateMenu"
                  v-model="landlordSignDateMenu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      outlined
                      dense
                      solo
                      v-model="newAgreement.landlord_sign_date"
                      label="Landlord Signature Date"
                      readonly
                      v-on="on"
                      prepend-inner-icon="mdi-calendar"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="newAgreement.landlord_sign_date"
                    @input="landlordSignDateMenu = false"
                  ></v-date-picker>
                </v-menu>

                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.property_address"
                  label="Property Address"
                  :rules="requiredRule"
                  prepend-inner-icon="mdi-home"
                ></v-text-field>
              </v-col>

              <v-col class="pb-0">
                <v-menu
                  ref="termEndDateMenu"
                  v-model="termEndDateMenu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      class="pt-2"
                      outlined
                      dense
                      solo
                      v-model="newAgreement.term_end_date"
                      label="Term End Date"
                      readonly
                      v-on="on"
                      prepend-inner-icon="mdi-calendar"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="newAgreement.term_end_date"
                    @input="termEndDateMenu = false"
                  ></v-date-picker>
                </v-menu>
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.tenant_name"
                  label="Tenant Name"
                  :rules="requiredRule"
                  prepend-inner-icon="mdi-account"
                ></v-text-field>
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.tenant_address"
                  label="Tenant Address"
                  :rules="requiredRule"
                  prepend-inner-icon="mdi-map-marker"
                ></v-text-field>
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.tenant_email"
                  label="Tenant Email"
                  :rules="[requiredRule, emailRule]"
                  prepend-inner-icon="mdi-email"
                ></v-text-field>
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.tenant_phone"
                  label="Tenant Phone"
                  :rules="[requiredRule, phoneRule]"
                  prepend-inner-icon="mdi-phone"
                ></v-text-field>
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.tenant_sign"
                  label="Tenant Signature"
                  :rules="requiredRule"
                  prepend-inner-icon="mdi-draw"
                ></v-text-field>
                <v-menu
                  ref="tenantSignDateMenu"
                  v-model="tenantSignDateMenu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      outlined
                      dense
                      solo
                      v-model="newAgreement.tenant_sign_date"
                      label="Tenant Signature Date"
                      readonly
                      v-on="on"
                      prepend-inner-icon="mdi-calendar"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="newAgreement.tenant_sign_date"
                    @input="tenantSignDateMenu = false"
                  ></v-date-picker>
                </v-menu>

                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.property_postcode"
                  label="Property Postcode"
                  :rules="requiredRule"
                  prepend-inner-icon="mdi-map-marker"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="pt-0">
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.total_rent"
                  label="Total Rent"
                  :rules="[requiredRule, numericRule]"
                  prepend-inner-icon="mdi-cash"
                >
                </v-text-field>
              </v-col>
              <v-col class="pt-0">
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.rent_paid_on_day"
                  label="Rent Paid On Day"
                  :rules="[requiredRule, numericRule]"
                  prepend-inner-icon="mdi-cash"
                >
                </v-text-field>
              </v-col>
              <v-col class="pt-0">
                <v-text-field
                  outlined
                  dense
                  solo
                  v-model="newAgreement.installment_amount"
                  label="Installment Amount"
                  :rules="[requiredRule, numericRule]"
                  prepend-inner-icon="mdi-cash"
                ></v-text-field>
              </v-col>
            </v-row>

            <!-- Dialog actions -->
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn @click="closeAddAgreementDialog">Cancel</v-btn>
              <!-- <v-btn @click="addNewAgreement" color="primary"
                >Add Agreement</v-btn -->
                <v-btn type="submit" color="primary">Add Agreement</v-btn>
            </v-card-actions>
          </v-form>
            </v-card-text>
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
            viewAgreement: false,
            agreements: [
              <?php while ($row = $landlord_leases->fetch_assoc()) { ?>            
              {
                id:<?php echo $row['id']; ?>,
                name: "<?php echo $row['tenant_name']; ?>",
                signedOn: "<?php echo  date('d M Y', strtotime( $row['term_startdate'])); ?>",
                propertyPostCode: "<?php echo strtoupper($row['property_postcode']) ?>",
                propertyAddress: "<?php echo str_replace(["\r\n", "\r", "\n"], ' ', strtoupper($row['property_address'])); ?>",
                landlord: "<?php echo $row['landlord_name']; ?>",
                dates: "<?php echo  date('d M Y', strtotime( $row['term_startdate'])) . " To " . date('d M Y', strtotime( $row['term_enddate']));  ?>",
              },
              <?php } ?>
            ],
            requiredRule: [(v) => !!v || "This field is required"],
            emailRule: (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
            phoneRule: (v) =>
              /^(\+\d{1,2}\s?)?(\(\d{1,4}\))?[0-9\- ]+$/.test(v) ||
              "Phone must be valid",
            numericRule: (v) => /^\d+$/.test(v) || "Must be a number",
            landlordSignDateMenu: false,
            tenantSignDateMenu: false,
            termStartDateMenu: false,
            termEndDateMenu: false,
            newAgreement: {
              landlord_name: "<?php echo $name;?>",
              landlord_address: "landlord address",
              landlord_email: "<?php echo $email;?>",
              landlord_phone: "07746474647",
              landlord_sign: "aaa",
              landlord_sign_date: "",
              tenant_name: "Tenant Name",
              tenant_address: "Tenant Address",
              tenant_email: "tenant@email.com",
              tenant_phone: "07772388322",
              tenant_sign: "bbb",
              tenant_sign_date: "<?php echo $todayDate;?>",
              property_address: "119-A, Elizabeth Road, Upton Park",
              property_postcode: "E6 1GB",
              term_start_date: "<?php echo $todayDate;?>",
              term_end_date: "<?php echo $todayDate;?>",
              total_rent: "10000",
              rent_paid_on_day: "1",
              installment_amount: "1000",
            },
          };
        },
        methods: {
          addNewAgreement() {
            if (this.$refs.myForm.validate()) {
              this.agreements.push({ ...this.newAgreement });
              console.log(this.newAgreement);
              // Reset the form fields
              this.newAgreement = {
                name: "",
                landlord: "",
                dates: "",
              };
              // Close the dialog
              this.closeAddAgreementDialog();
            }
          },
          closeAddAgreementDialog() {
            this.addAgreement = false;
          },
          openAgreementForm() {
            this.addAgreement = true;
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
            this.viewAgreement = true;
            this.addAgreement = false;
            
            
          },
          closeViewAgreementDialog()
          {
            this.viewAgreement = false;
            this.addAgreement = false;
          },
          getAgreementUrl(id) {
            // You can perform any custom logic here to build the URL
            return `main.php?custom=view_agreement1.php?id=${id}`;
          },
          submitForm() {
            // Handle the form submission here
            // You can use Axios or another method to send the data to the server
            // For simplicity, let's use a simple POST request using the Fetch API
            fetch('agreementForm.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                },
                body: JSON.stringify(this.newAgreement),
              })
                .then(data => {
                  // Handle the response from the server if needed
                  this.agreements;
                  this.addAgreement = false;
                })
                .catch(error => {
                  console.error('Error:', error);
                });
            },
        },        
      });
    </script>
  </body>
</html>

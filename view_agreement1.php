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

  // Retrieve the lease agreement ID from the request, assuming it comes from a URL parameter
  $lease_agreement_id = isset($_GET['id']) ? $_GET['id'] : null;

  if (!isset($lease_agreement_id) || !isset($lease_agreement_id)) 
  {
    echo "Invalid or missing record ID!";
    exit;
  }

  // Convert the ID to an integer for further use (optional, depending on your needs)
  $lease_agreement_id = (int)$lease_agreement_id;
  
  if( $lease_agreement_id <= 0 )
  {
    echo "No record id found!";
    exit;
  }

    
  // All good, now code will proceed!

    // Connect to the database.
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Landlord View Query
    // Fetch all records
    $sql2 = "SELECT  * FROM `lease_agreement` where id = " . $lease_agreement_id;
    $stmt = $conn->query($sql2);

    if ($stmt) {
      $result = $stmt->fetch_assoc();
      $isLandlord = 0;
      $userType = "T";
      if($result["created_by"] == $userId  ) {
          $isLandlord = 1;
          $userType = "L";
      }

    } else {
      echo "Query failed";
      exit;
    }
    
    //Fetch Notes
    $sql3 = "SELECT * FROM `lease_agreement_notes` n WHERE `lease_agreement_id` = " . $lease_agreement_id ." order by created_at desc;";
    
    $stmtNotes = $conn->query($sql3);

    if (!$stmtNotes) {
      echo "Query failed";
      exit;
    }
    

    // Close the database connection
    $conn->close();
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <!-- Include Vue.js and Vuetify from CDN -->
  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>

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
    body{
      font-family:Arial;
      background-color:#fff;
    } 
    .comment-box {
  height: 100%;
  border: 1px solid #ccc;
  padding: 10px;
  border-radius: 5px;
  background-color: #f8f8f8;
  display: flex;
  flex-direction: column;
}

.user-comment {
  margin-left: auto;
  background-color: #c1e1c5;
  padding: 8px;
  border-radius: 8px;
  margin-bottom: 8px;
  min-width: 200px;
  max-width: 70%;
}

.other-comment {
  margin-right: auto;
  background-color: #f0f0f0;
  padding: 8px;
  border-radius: 8px;
  margin-bottom: 8px;
  min-width: 200px;
  max-width: 70%;
}

.single-comment {
  margin-bottom: 8px;
}
  </style>
</head>
<body>




<div id="view_agreement">
  <v-container style="background-color: #ffffff;">

  <v-btn  v-if="isLandlord" @click="toggleEditMode" color="primary">{{ editMode ? 'Cancel' : 'Edit' }}</v-btn>
  <v-btn v-if="isLandlord && editMode" @click="saveChanges" color="primary">Save</v-btn>

  <v-row>
    <v-col cols="9">
      <v-row>
      <v-col>
        <p style="text-align: center;"> <b>  Residential Lease for Single Family Home or Duplex </b>  </p>
        <br />
        <p style="text-align: center;">  <b> (FOR A TERM NOT TO EXCEED ONE YEAR)  </b> </p>
        <br />
        <br />
        <p>A BOX ( ) OR A BLANK SPACE ( ____ ) INDICATES A PROVISION WHERE A CHOICE OR DECISION MUST BE MADE BY THE PARTIES.</p>
        <br />
        <p>THE LEASE IMPOSES IMPORTANT LEGAL OBLIGATIONS. MANY RIGHTS AND RESPONSIBILITIES OF THE PARTIES ARE GOVERNED
         BY CHAPTER 83, PART II, RESIDENTIAL LANDLORD AND TENANT ACT, FLORIDA STATUTES. 
        A COPY OF THE RESIDENTIAL LANDLORD AND TENANT ACT IS ATTACHED TO THIS LEASE.</p>
      </v-col>
    </v-row>

    <v-row>
		<v-col>
			<h3 style='margin-bottom:5px;'>1. PARTIES</h3>
			<p  style='margin-bottom:5px;'>This is a lease ("the Lease") between 
			
      
      <v-text-field
          v-if="isLandlord && editMode"
          outlined
          dense
          solo
          small
          v-model="landlord.name"
          label="Landlord Name"
          :rules="requiredRule"
          prepend-inner-icon="mdi-account"
          style="max-width: 250px;"
        ></v-text-field> </span>
      <span v-else style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{  landlord.name }} &nbsp;&nbsp;&nbsp;</span> 

			 (name & address of owner of the property)

       <v-text-field
          v-if="isLandlord && editMode"
          outlined
          dense
          solo
          small
          v-model="landlord.address"
          label="Landlord Address"
          :rules="requiredRule"
          style="max-width: 350px;"
          prepend-inner-icon="mdi-map-marker"
        ></v-text-field> </span>
        <span v-else style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{  landlord.address }} &nbsp;&nbsp;&nbsp;</span> 

        UK. (“Landlord”) and <br /> <br />


        <v-text-field
          v-if="isLandlord && editMode"
          outlined
          dense
          solo
          small
          v-model="tenant.name"
          label="Tenant Name"
          :rules="requiredRule"
          style="max-width: 250px;"
        ></v-text-field> </span>
        <span v-else style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{  tenant.name }} &nbsp;&nbsp;&nbsp;</span> 

			 (name(s) of person(s) to whom the property is leased) 
       <v-text-field
          v-if="isLandlord && editMode"
          outlined
          dense
          solo
          small
          v-model="tenant.address"
          label="Tenant Address"
          :rules="requiredRule"
          style="max-width: 350px;"
          prepend-inner-icon="mdi-map-marker"
        ></v-text-field> </span>
        <span v-else style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{  tenant.address }} &nbsp;&nbsp;&nbsp;</span>  
			 (“Tenant.”)</p>
			<br />
		</v-col>
	</v-row>
	<v-row>
        <v-col>
          <p style='margin-bottom:15px;'><strong>Landlord’s E-mail:</strong> 


          <v-text-field
                  v-if="isLandlord && editMode"
                  outlined
                  dense
                  solo
                  small
                  v-model="landlord.email"
                  label="Landlord Email"
                  :rules="[requiredRule, emailRule]"
                  prepend-inner-icon="mdi-email"
                ></v-text-field>  </span>
          <span v-else style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{  landlord.email }} &nbsp;&nbsp;&nbsp;</span> 

        </p>
          <p><strong>Tenant’s E-mail:</strong> 

              <v-text-field
                      v-if="isLandlord && editMode"
                      outlined
                      dense
                      solo
                      small
                      v-model="tenant.email"
                      label="Tenant Email"
                      :rules="[requiredRule, emailRule]"
                      prepend-inner-icon="mdi-email"
                    ></v-text-field>  </span>
              <span v-else style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{  tenant.email }} &nbsp;&nbsp;&nbsp;</span> 

          </p>
        </v-col>
        <v-col>

          <p style='margin-bottom:15px;'><strong>Landlord’s Telephone:</strong> 
          
          <v-text-field
                  v-if="isLandlord && editMode"
                  outlined
                  dense
                  solo
                  small
                  v-model="landlord.phoneNumber"
                  label="Landlord Phone"
                  :rules="[requiredRule, phoneRule]"
                  prepend-inner-icon="mdi-phone"
                ></v-text-field> </span>          
          <span v-else style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{  landlord.phoneNumber }} &nbsp;&nbsp;&nbsp;</span> 


          <p><strong>Tenant’s Telephone:</strong> 
            <v-text-field
                    v-if="isLandlord && editMode"
                    outlined
                    dense
                    solo
                    small
                    v-model="tenant.phoneNumber"
                    label="Tenant Phone"
                    :rules="[requiredRule, phoneRule]"
                    prepend-inner-icon="mdi-phone"
                  ></v-text-field> </span>          
            <span v-else style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{  tenant.phoneNumber }} &nbsp;&nbsp;&nbsp;</span>         
          </p>
        </v-col>
    </v-row>
    <v-row>
      <v-col>
        <h3 style='margin-bottom:5px;'>2. PROPERTY RENTED</h3>
        <p>Landlord leases to Tenant the land and buildings located at 
            <v-text-field
              v-if="isLandlord && editMode"
              outlined
              dense
              solo
              small
              v-model="property.address"
              label="Property Address"
              :rules="requiredRule"
              prepend-inner-icon="mdi-home"
            ></v-text-field> </span>
            <span v-else style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{ property.address }} &nbsp;&nbsp;&nbsp;</span>


         (street address), stoke 

            <v-text-field
              v-if="isLandlord && editMode"
              outlined
              dense
              solo
              small
              v-model="property.postCode"
              label="Property Postcode"
              :rules="requiredRule"
              prepend-inner-icon="mdi-map-marker"
            ></v-text-field>
        <span v-else style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{ property.postCode }} &nbsp;&nbsp;&nbsp;</span>
         (post code)</p>
      </v-col>
    </v-row>

    <v-row>
      <v-col>
        <h3 style='margin-bottom:5px;'>3. TERM</h3>
        <p>This is a lease for a term, not to exceed twelve months, beginning on </p>

            <v-menu
              v-if="isLandlord && editMode"
              ref="termStartDateMenu"
              v-model="termStartDateMenu"
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
                  v-model="leaseTerm.startDateFormat"
                  label="Term Start Date"
                  readonly
                  v-on="on"
                  prepend-inner-icon="mdi-calendar"
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="leaseTerm.startDateFormat"
                @input="termStartDateMenu = false"
              ></v-date-picker>
            </v-menu>
        <span v-else style="text-decoration: underline;"> &nbsp;&nbsp;&nbsp; {{ leaseTerm.startDate }} &nbsp;&nbsp;&nbsp;</span>  (month, day, year)
         and ending  
              <v-menu
              v-if="isLandlord && editMode"
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
                  v-model="leaseTerm.endDateFormat"
                  label="Term End Date"
                  readonly
                  v-on="on"
                  prepend-inner-icon="mdi-calendar"
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="leaseTerm.endDateFormat"
                @input="termEndDateMenu = false"
              ></v-date-picker>
            </v-menu>
            <span v-else style="text-decoration: underline;"> &nbsp;&nbsp;&nbsp; {{ leaseTerm.endDate }} 
         &nbsp;&nbsp;&nbsp;</span> (month, day, year) (the "Lease Term").</p>
      </v-col>
    </v-row>

    <v-row>
      <v-col>
        <h3 style='margin-bottom:5px;'>4. RENT PAYMENTS, TAXES AND CHARGES</h3>
        <p style='margin-bottom:15px;'>Tenant shall pay total rent in the amount of £
              <v-text-field
                v-if="isLandlord && editMode"
                outlined
                dense
                solo
                v-model="rentDetails.amount"
                label="Total Rent"
                :rules="[requiredRule, numericRule]"
                prepend-inner-icon="mdi-cash"
              >
              </v-text-field>
              <span v-else style="text-decoration: underline;"> &nbsp;&nbsp;&nbsp; {{ rentDetails.amount }}
                &nbsp;&nbsp;&nbsp;</span>

          (excluding taxes) for the Lease Term.
        </p>
        
        
        <p style='margin-bottom:15px;'>The rent shall be payable by Tenant in advance in installments or in full as provided in the options below:</p>
        <p v-if="rentDetails.installments">If in installments, rent shall be payable monthly, on the 
        
        
        <v-text-field
          v-if="isLandlord && editMode"
          outlined
          dense
          solo
          v-model="rentDetails.dayOfMonth"
          label="Rent Paid On Day"
          :rules="[requiredRule, numericRule]"
          prepend-inner-icon="mdi-cash"
        > </v-text-field>
        <span v-else style="text-decoration: underline;"> &nbsp;&nbsp;&nbsp; {{ rentDetails.dayOfMonth || 'first' }} 
         &nbsp;&nbsp;&nbsp;</span>         
        day of each month in the amount of £ 

        <v-text-field
          v-if="isLandlord && editMode"
          outlined
          dense
          solo
          v-model="rentDetails.installmentAmount"
          label="Installment Amount"
          :rules="[requiredRule, numericRule]"
          prepend-inner-icon="mdi-cash"
        ></v-text-field>
        <span v-else style="text-decoration: underline;"> &nbsp;&nbsp;&nbsp; {{ rentDetails.installmentAmount }} 
         &nbsp;&nbsp;&nbsp;</span> 
        per installment.</p>
      </v-col>
    </v-row>


    <v-row>
        <v-col>
          <p style='margin-bottom:10px; margin-top:45px;'>The Lease has been executed by the parties on the dates indicated below.</p>
          </v-col>
    </v-row>

    <v-row>
      <v-col>
        <p > {{ landlord.name }} </p>
        <p > _____________________________ </p>
        <p>Landlord's Signature</p> <br /><br />
        
        <p > {{ tenant.name }} </p>
        <p> _____________________________ </p>
        <p>Tenant's Signature</p>
      </v-col>
      <v-col>
        <p > {{ leaseTerm.startDate }} </p>
        <p > _____________________________ </p>
        <p>Landlord's Signature Date</p> <br /><br />

        <p> {{ leaseTerm.startDate }} </p>
        <p > _____________________________ </p>
        <p>Tenant Signature Date</p>
      </v-col>
    </v-row>
  
    <!-- <v-btn @click="exportToPDF" color="primary">Export PDF</v-btn> -->
    </v-col>
    <v-col cols="3">
      <div class="comment-box">
        <div class="mb-5">
        <v-text-field v-model="newComment" label="Add a comment"></v-text-field>
        <v-btn @click="addComment" color="primary">Add Comment</v-btn>
      </div>

      
      <div v-for="(comment, index) in comments" :key="comment.id" :class="{ 'user-comment': comment.entered_by === 'T', 'other-comment': comment.entered_by !== 'T' }">
          <div style="display: flex; justify-content: space-between; font-size: x-small;">
              <span style="align-self: flex-start;">{{ comment.entered_by === 'T' ? 'Tenant' : 'Landlord' }}</span> 
              <span style="align-self: flex-end;">{{ comment.created_at }}</span>
          </div>
          <div style="display: flex; justify-content: space-between; font-size: small;">
              <br />
              {{ comment.text }}
          </div>
      </div>
      
    </div>
    </v-col>
  </v-row>
    
</v-container>
</div>


<script>
  new Vue({
    el: '#view_agreement',
    vuetify: new Vuetify(),
    data: {
      editMode: false,
      currentUserType:"<?php echo $userType; ?>",
      isLandlord:"<?php echo $isLandlord == 0 ? false : true; ?>",
      id:<?php echo $result["id"]; ?>,
      comments: [

        <?php 
          while ($row = $stmtNotes->fetch_assoc()) {
            echo "{ id: ".$row["id"] .", text: '" . $row["notes"] . "', entered_by: '"
              . $row["entered_by"] ."', created_at:'". $row["created_at"]  ."' },"; 
          }
        ?>
      ],
      newComment: '',
      landlord: {
        name: "<?php echo $result['landlord_name']; ?>",
        address: "<?php echo str_replace(["\r\n", "\r", "\n"], ' ', strtoupper($result['landlord_address'])); ?>",
        email: "<?php echo $result['landlord_email']; ?>",
        phoneNumber: "<?php echo $result['landlord_phone']; ?>",
      },
      tenant: {
        name: "<?php echo $result['tenant_name']; ?>",
        address: "<?php echo str_replace(["\r\n", "\r", "\n"], ' ', strtoupper($result['tenanat_address'])); ?>",
        email: "<?php echo $result['tenant_email']; ?>",
        phoneNumber: "<?php echo $result['tenant_phone']; ?>",
      },
      property: {
        address: "<?php echo str_replace(["\r\n", "\r", "\n"], ' ', strtoupper($result['property_address'])); ?>",
        postCode: "<?php echo $result['property_postcode']; ?>",
      },
      leaseTerm: {
        startDate: "<?php echo date('d M Y', strtotime( $result['term_startdate'])) ; ?>",
        startDateFormat : "<?php echo date('Y-m-d', strtotime( $result['term_startdate'])) ; ?>",
        endDate: "<?php echo date('d M Y', strtotime( $result['term_enddate'])) ; ?>",
        endDateFormat : "<?php echo date('Y-m-d', strtotime( $result['term_enddate'])) ; ?>",
      },
      rentDetails: {
        amount: "<?php echo $result['total_rent']; ?>",
        installments: true,
        dayOfMonth: "<?php echo $result['rent_paid_on_day']; ?>",
        installmentAmount: "<?php echo $result['installment_amount']; ?>",
      }
    },
      methods: {
        toggleEditMode() {
          this.editMode = !this.editMode;
        },

        exportToPDF() {
          setTimeout(() => {
            const element = document.getElementById('view_agreement'); // Replace with the ID of your content element
            debugger
            console.log(element.body);
            // Define options for html2pdf
            const options = {
              margin: 1,
              filename: 'exported-document.pdf',
              image: { type: 'jpeg', quality: 0.98 },
              html2canvas: { scale: 2 },
              jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
            };

            // Use html2pdf to generate and download the PDF
            html2pdf(element, options);
          }, 2000); // Adjust the delay time as needed
        },


        async addComment() {
          if (this.newComment.trim() !== '') {
            //const user = this.comments.length % 2 === 0 ? 'User1' : 'User2';
            // debugger
            // Send the comment to the PHP script
            const response = await fetch('addNotes.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                text: this.newComment,
                //user: user,
                lease_agreement_id: <?php echo $lease_agreement_id; ?>,
                entered_by: '<?php  echo $userType;?>',
              }),
            });

            
            if (response.ok) {

              const responseData = await response.json();

              if (responseData.status === 'success') {
                  const insertedId = responseData.insertedId;
                  // Handle the insertedId as needed
                  console.log('Inserted ID:', insertedId);
              } else {
                  // Handle the error case
                  console.error('Error:', responseData.message);
              }
              
              this.comments.unshift({
                id: responseData.insertedId,
                text: this.newComment,
                entered_by: '<?php echo $userType; ?>',
                created_at: new Date().toISOString(), 
              });
              this.newComment = '';
            } else {
              console.error('Error adding comment');
            }
          }
        },

        async saveChanges() {
          // Perform the logic to save changes to the server using PHP
          const response = await fetch('saveAgreement.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              lease_agreement_id: <?php echo $lease_agreement_id; ?>,
              landlord_name: this.landlord.name,
              landlord_address: this.landlord.address,
              landlord_email: this.landlord.email,
              landlord_phone: this.landlord.phoneNumber,
              tenant_name: this.tenant.name,
              tenant_address: this.tenant.address,
              tenant_email : this.tenant.email,
              tenant_phone: this.tenant.phoneNumber,
              property_address : this.property.address,
              property_postcode : this.property.postCode,
              term_startdate : this.leaseTerm.startDateFormat,
              term_enddate : this.leaseTerm.endDateFormat,
              total_rent : this.rentDetails.amount,
              rent_paid_on_day : this.rentDetails.dayOfMonth,
              installment_amount : this.rentDetails.installmentAmount,
            }),
          });

          if (response.ok) {
            debugger
            const responseData = await response.json();
            if (responseData.status === 'success') {
              // Handle success, e.g., show a confirmation message
              console.log('Changes saved successfully');
              // Disable edit mode after saving
              this.editMode = false;
            } else {
              // Handle the error case
              console.error('Error saving changes:', responseData.message);
            }
          } else {
            console.error('Error saving changes');
          }
        },
      },
    });
</script>

</body>
</html>
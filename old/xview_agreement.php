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
    } else {
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
  <title>Your PHP Page</title>
  <!-- Include Vue.js and Vuetify from CDN -->
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.5.10/dist/vuetify.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.5.10/dist/vuetify.js"></script>
  <style>
    body{
      font-family:Arial;
      background-color:#fff;
    } 
    
  </style>
</head>
<body>

<div id="app"></div>

<script>
  new Vue({
    el: '#app',
    data: {
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
        endDate: "<?php echo date('d M Y', strtotime( $result['term_enddate'])) ; ?>",
      },
      rentDetails: {
        amount: "<?php echo $result['property_postcode']; ?>",
        installments: true,
        dayOfMonth: "<?php echo $result['property_postcode']; ?>",
        installmentAmount: "<?php echo $result['property_postcode']; ?>",
      }
    },
    template: `
    <v-container style="min-height: 100vh;">
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
			<span style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{ landlord.name }} &nbsp;&nbsp;&nbsp;</span> 
			 (name & address of owner of the property)
			 <span style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{ landlord.address }} &nbsp;&nbsp;&nbsp;</span>
			  UK. (“Landlord”) and <br /> <br />
			  <span style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{  tenant.name }} &nbsp;&nbsp;&nbsp;</span> 
			 (name(s) of person(s) to whom the property is leased) 
			 <span style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{  tenant.address }} &nbsp;&nbsp;&nbsp;</span> 
			 (“Tenant.”)</p>
			<br />
		</v-col>
	</v-row>
	<v-row>
        <v-col>
          <p style='margin-bottom:15px;'><strong>Landlord’s E-mail:</strong> 
            <span style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{ landlord.email }} &nbsp;&nbsp;&nbsp;</span>
          </p>
          <p><strong>Tenant’s E-mail:</strong> 
            <span style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{ tenant.email }} &nbsp;&nbsp;&nbsp;</span>
          </p>
        </v-col>
        <v-col>
          <p style='margin-bottom:15px;'><strong>Landlord’s Telephone:</strong> {{ landlord.phoneNumber }}</p>

          <p><strong>Tenant’s Telephone:</strong> {{ tenant.phoneNumber }}</p>
        </v-col>
    </v-row>
    <v-row>
      <v-col>
        <h3 style='margin-bottom:5px;'>2. PROPERTY RENTED</h3>
        <p>Landlord leases to Tenant the land and buildings located at 
        <span style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{ property.address }} &nbsp;&nbsp;&nbsp;</span>
         (street address), stoke 
        <span style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{ property.postCode }} &nbsp;&nbsp;&nbsp;</span>
         (post code)</p>
      </v-col>
    </v-row>

    <v-row>
      <v-col>
        <h3 style='margin-bottom:5px;'>3. TERM</h3>
        <p>This is a lease for a term, not to exceed twelve months, beginning on 
        <span style="text-decoration: underline;"> &nbsp;&nbsp;&nbsp; {{ leaseTerm.startDate }} &nbsp;&nbsp;&nbsp;</span>  (month, day, year)
         and ending  
         <span style="text-decoration: underline;"> &nbsp;&nbsp;&nbsp; {{ leaseTerm.endDate }} 
         &nbsp;&nbsp;&nbsp;</span> (month, day, year) (the "Lease Term").</p>
      </v-col>
    </v-row>

    <v-row>
      <v-col>
        <h3 style='margin-bottom:5px;'>4. RENT PAYMENTS, TAXES AND CHARGES</h3>
        <p style='margin-bottom:15px;'>Tenant shall pay total rent in the amount of £
        <span style="text-decoration: underline;"> &nbsp;&nbsp;&nbsp;  {{ rentDetails.amount }}
         &nbsp;&nbsp;&nbsp;</span>
          (excluding taxes) for the Lease Term.</p>
        <p style='margin-bottom:15px;'>The rent shall be payable by Tenant in advance in installments or in full as provided in the options below:</p>
        <p v-if="rentDetails.installments">If in installments, rent shall be payable monthly, on the 
        <span style="text-decoration: underline;"> &nbsp;&nbsp;&nbsp; {{ rentDetails.dayOfMonth || 'first' }} 
         &nbsp;&nbsp;&nbsp;</span>         
        day of each month in the amount of £ 
        <span style="text-decoration: underline;"> &nbsp;&nbsp;&nbsp; {{ rentDetails.installmentAmount }} 
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
          <p style='margin-bottom:15px;'><strong>{{ landlord.name }}</strong> 
            <span style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{ landlord.email }} &nbsp;&nbsp;&nbsp;</span>
          </p>
          <p style="text-decoration:center;"><strong>Landord's Signature</strong> 
            <span style="text-decoration: underline; "> &nbsp;&nbsp;&nbsp; {{ tenant.email }} &nbsp;&nbsp;&nbsp;</span>
          </p>
        </v-col>
        <v-col>
          <p style='margin-bottom:15px;'><strong>Landlord’s Telephone:</strong> {{ landlord.phoneNumber }}</p>

          <p><strong>Tenant’s Telephone:</strong> {{ tenant.phoneNumber }}</p>
        </v-col>
    </v-row>

    <v-row>
      <v-col>
        <p > {{ landlord.name }} </p>
        <p > _____________________________ </p>
        <p>Landlord's Signature</p> <br /><br />
        
        <p > {{ tenant.name }} </p>
        <p> _____________________________ </p>
        <p>Landlord Signature</p>
      </v-col>
      <v-col>
        <p > {{ leaseTerm.startDate }} </p>
        <p > _____________________________ </p>
        <p>Tenant’s Signature Date</p> <br /><br />

        <p> {{ leaseTerm.startDate }} </p>
        <p > _____________________________ </p>
        <p>Tenant Signature Date</p>
      </v-col>
    </v-row>
</v-container>
`});
</script>

</body>
</html>
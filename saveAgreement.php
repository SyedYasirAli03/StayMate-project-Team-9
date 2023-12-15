<?php
// Include the database credentials file and any other necessary libraries
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include the database credentials file
require_once 'db_credentials.php';
require 'mailer/src/PHPMailer.php';
require 'mailer/src/SMTP.php';
require 'mailer/src/Exception.php';


// Start the session.
session_start();
  // Verify if the user session exists and contains user ID and name.
  if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    // If the user is not logged in, redirect to the login page.
    header("Location: login.php");
    exit;
  }

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  //error_log("Request received in saveAgreement.php"); 


  // Connect to the database using constants from db_credentials.php
  $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

  // Check the connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  
  // Get the JSON data from the request body
  $json_data = file_get_contents('php://input');

  error_log($json_data);
  //var_dump($json_data);

  // Decode the JSON data
  $data = json_decode($json_data, true);
  
  // Retrieve form data
  $lease_agreement_id=$data["lease_agreement_id"]; 
  
  $landlord_name=$data["landlord_name"];
  $landlord_address=$data["landlord_address"];
  $landlord_email=$data["landlord_email"];
  $landlord_phone=$data["landlord_phone"];

  $tenant_name=$data["tenant_name"];
  $tenant_address=$data["tenant_address"];
  $tenant_email=$data["tenant_email"];
  $tenant_phone=$data["tenant_phone"];

  $property_address=$data["property_address"];
  $property_postcode=$data["property_postcode"];
  $term_startdate=$data["term_startdate"];
  $term_enddate=$data["term_enddate"];
  
  $total_rent=$data["total_rent"];
  $rent_paid_on_day=$data["rent_paid_on_day"];
  $installment_amount=$data["installment_amount"];

  $createdBy = $_SESSION['user_id']; 
  
    // Update data in the lease_agreement table
    $stmt = $conn->prepare("
              UPDATE lease_agreement 
              SET 
                  landlord_name = ?, 
                  landlord_address = ?,
                  landlord_email = ?,
                  landlord_phone = ?,
                  tenant_name = ?, 
                  tenanat_address = ?,
                  tenant_email = ?,
                  tenant_phone = ?,
                  property_address = ?,
                  property_postcode = ?,
                  term_startdate = ?,
                  term_enddate = ?,
                  total_rent = ?,
                  rent_paid_on_day = ?,
                  installment_amount = ?
              WHERE id = ?");


  // Check if the prepare statement is successful
  if (!$stmt) {
      // Output the error message
      die('Error in preparing the statement: ' . $conn->error);
  }

  $stmt->bind_param("ssssssssssssdidi", 
        $landlord_name, 
        $landlord_address, 
        $landlord_email, 
        $landlord_phone, 
        $tenant_name, 
        $tenant_address, 
        $tenant_email,
        $tenant_phone, 
        $property_address,
        $property_postcode,
        $term_startdate,
        $term_enddate,
        $total_rent,
        $rent_paid_on_day,
        $installment_amount,
        $lease_agreement_id
      );

  if ($stmt->execute()) {
      // Data inserted successfully. 
      // Retrieve the affected rows
      $rowsAffected = $conn->affected_rows;
      
       // Prepare JSON response
        $response = array(
          'status' => 'success',
          'message' => 'Lease Agreement updated successfully!',
          'insertedId' => $rowsAffected
      );

      // Send the JSON response
      header('Content-Type: application/json');
      echo json_encode($response);

      // Let's send an email to other party
//      sendActivationEmail($tenantEmail, $landlordName, $propertyAddress );

      exit;
  } else {

    $response = array(
      'status' => 'error',
      'message' => 'Error: ' . $stmt->error);

       // Send the JSON response
       header('Content-Type: application/json');
       echo json_encode($response);
  }
  

  // Close the statement
  $stmt->close();
}


?>


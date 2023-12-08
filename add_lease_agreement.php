<?php
// Start output buffering to prevent header-related issues.
ob_start();

session_start();

// Include the database credentials file
require_once 'db_credentials.php';

// Connect to the database using constants from db_credentials.php
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $landlordName = $_POST["landlord_name"];
    $landlordAddress = $_POST["landlord_address"];
    $tenantName = $_POST["tenant_name"];
    $tenantAddress = $_POST["tenant_address"];
    $landlordEmail = $_POST["landlord_email"];
    $landlordPhone = $_POST["landlord_phone"];
    $tenantEmail = $_POST["tenant_email"];
    $tenantPhone = $_POST["tenant_phone"];
    $propertyAddress = $_POST["property_address"];
    $propertyPostcode = $_POST["property_postcode"];
    $termStartDate = $_POST["term_startdate"];
    $termEndDate = $_POST["term_enddate"];
    $totalRent = $_POST["total_rent"];
    $rentPaidOnDay = $_POST["rent_paid_on_day"];
    $installmentAmount = $_POST["installment_amount"];
    $landlordSign = $_POST["landlord_sign"];
    $landlordSignDate = $_POST["landlord_sign_date"];
    $tenantSign = $_POST["tenant_sign"];
    $tenantSignDate = $_POST["tenant_sign_date"];
    $createdBy = $_SESSION['user_id']; // Assuming you have stored the user ID in the session variable

    // Insert data into the lease_agreement table
    $stmt = $conn->prepare("INSERT INTO lease_agreement (
        landlord_name, landlord_address, tenant_name, tenanat_address, landlord_email, landlord_phone,
        tenant_email, tenant_phone, property_address, property_postcode, term_startdate, term_enddate,
        total_rent, rent_paid_on_day, installment_amount, landlord_sign, landlord_sign_date,
        tenant_sign, tenant_sign_date, created_by
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Check if the prepare statement is successful
    if (!$stmt) {
        // Output the error message
        die('Error in preparing the statement: ' . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssssdidsssssi",
        $landlordName, $landlordAddress, $tenantName, $tenantAddress, $landlordEmail, $landlordPhone,
        $tenantEmail, $tenantPhone, $propertyAddress, $propertyPostcode, $termStartDate, $termEndDate,
        $totalRent, $rentPaidOnDay, $installmentAmount, $landlordSign, $landlordSignDate,
        $tenantSign, $tenantSignDate, $createdBy
    );

    if ($stmt->execute()) {
        // Data inserted successfully
        // You can redirect to a success page or display a success message here
        echo "<p>Lease Agreement submitted successfully!</p>";
        echo "<p><a href='dashboard.php'>Click here to go back to dashboard</a></p>";
        exit;
    } else {
        // Error occurred while inserting data
        // You can redirect to an error page or display an error message here
        echo "---Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

?>

<!-- Your HTML form goes here -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lease Agreement Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
            overflow-y: auto;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
            font-size: 16px;
            cursor: pointer;
            appearance: none;
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"],
        input[type="reset"] {
            width: 49%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-top: 5px;
        }

        .success {
            color: green;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Lease Agreement Form</h1>

    <form method="post" action="add_lease_agreement.php" enctype="multipart/form-data">
        <!-- Lease agreement form fields -->
        <label>Landlord Name*</label>
        <input type="text" name="landlord_name" value="landlord_name" required>

        <label>Landlord Address*</label>
        <textarea name="landlord_address"  required>
        landlord_address
        </textarea>

        <label>Tenant Name*</label>
        <input type="text" name="tenant_name" value="tenant_name" required>

        <label>Tenant Address*</label>
        <textarea name="tenant_address"  required>
        tenant_address
        </textarea>

        <label>Landlord Email*</label>
        <input type="email" name="landlord_email" value="landlord_email@test.com" required>

        <label>Landlord Phone*</label>
        <input type="tel" name="landlord_phone" value="landlord_phone" required>

        <label>Tenant Email*</label>
        <input type="email" name="tenant_email" value="tenant_email@test.com" required>

        <label>Tenant Phone*</label>
        <input type="tel" name="tenant_phone" value="123456789" required>

        <label>Property Address*</label>
        <textarea name="property_address"  required>
        property_address
        </textarea>

        <label>Property Postcode*</label>
        <input type="text" name="property_postcode" value="property_postcode" required>

        <label>Term Start Date*</label>
        <input type="date" name="term_startdate" value="2023-01-01" required>

        <label>Term End Date*</label>
        <input type="date" name="term_enddate" value="2024-01-01" required>

        <label>Total Rent*</label>
        <input type="number" name="total_rent" value="6000" required>

        <label>Rent Paid On Day*</label>
        <input type="number" name="rent_paid_on_day" value="2" required>

        <label>Installment Amount*</label>
        <input type="number" name="installment_amount" value="500" required>

        <label>Landlord Signature*</label>
        <input type="text" name="landlord_sign" value="landlord_sign" required>

        <label>Landlord Signature Date*</label>
        <input type="date" name="landlord_sign_date" value="2023-01-01" required>

        <label>Tenant Signature*</label>
        <input type="text" name="tenant_sign" value="tenant_sign" required>

        <label>Tenant Signature Date*</label>
        <input type="date" name="tenant_sign_date" value="2023-01-02" required>
    

        <input type="submit" value="Submit">
        <input type="reset" value="Reset">
        <br />
    </form>
</body>
</html>

<?php
// Clear the output buffer and send the content to the browser.
ob_end_flush();
?>

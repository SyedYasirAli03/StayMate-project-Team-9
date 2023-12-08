<?php 
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		
		echo "Hurray this is a post";
		// Retrieve form data
		$data = $_POST["txtData"];
		echo $data;
	}
	else
	{
		echo "This is a GET";
	}
?>

<html>
<head>
</head>
<body>
	<p> This is a test page on form submission </p>
	
	<p> User has entered : <?php echo $data; ?>	 </p>
	
</body>
</html>
<html>
<head>
</head>
<script>
	function show()
	{
		var data = document.getElementById("txtData").value;
		alert(data);
	}
</script>
<body>
	<p> This is a test page </p>
	
	<form id="form1" method="post" action="formsubmit.php">
	
	<p> Enter your name: <input id="txtData" name="txtData" type="text" value="" />  </p>
	<p> <input type="button" value="Show" onclick="show()" /> </p>
	
	<input type="submit" value="Submit" />
	
	</form>
	
</body>
</html>
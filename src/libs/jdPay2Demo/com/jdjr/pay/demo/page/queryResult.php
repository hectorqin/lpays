<?php 
error_reporting(0);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>京东支付</title>
<link rel="stylesheet" type="text/css"
	href="../../../../../css/main.css">
</head>
<body>

	<!DOCTYPE html>

<html>
<head runat="server">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>京东支付</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
    <form id="form1">
    <div class="content" align="center">

		
		<?php echo $_SESSION['subhtml']?>
		

	</div>
	<textarea rows="20" cols="100" ><?php echo $_SESSION['jsonStr']?></textarea>
    </form>
</body>
</html>

</body>
</html>
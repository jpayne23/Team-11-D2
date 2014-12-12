<html>
	<head>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<title>Login Page</title>
		<script type ="text/javascript">
			/*$(document).ready(function(){
				$("#submit").click(function(){
					$("#content").load("mainPage.php");
				});//D1 Prototype Code.php
			});*/
		</script>
	</head>
	<body>
		<div id = "content">
			<form action="mainPage.php" method="POST">
			Username:
			<input id="username" name="username" type="text" placeholder="Username"></br>
			Password:
			<input id="password" name ="password" type="password" placeholder="Password"></br>
			<input id="submit" type="submit" value="Submit">
			</form>
		</div>
	</body>
</html>
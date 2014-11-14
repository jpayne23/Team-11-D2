<html >
	<head>
		<script type="text/javascript">
			function ajaxFunction()
			{
				var ajaxRequest;  // The variable that makes Ajax possible!
				try
				{
					// Opera 8.0+, Firefox, Safari
					ajaxRequest = new XMLHttpRequest();
				} 
				catch (e)
				{
					alert("Your browser broke!");
				}
				// Create a function that will receive data sent from the server
				ajaxRequest.onreadystatechange = function()
				{
					if(ajaxRequest.readyState == 4)
					{
						var ajaxDisplay = document.getElementById('res');
						ajaxDisplay.innerHTML = ajaxRequest.responseText;
						//set the allocated space on the webpage to the results from the php
					}
				}
				var park = document.getElementById('park').value;
				var capacity = document.getElementById('capacity').value;
				var sort = document.getElementById('sort').checked;
				var queryString = "?park=" + park + "&capacity=" + capacity + "&sort=" + sort;
				//building the query string
				ajaxRequest.open("GET", "roomIdRes.php" + queryString, true);
				ajaxRequest.send(null);
			}
		</script>
		
		<title>Room Finder</title>
		<style type="text/css">
		body {
			font-family: "Apple Chancery", Times, serif;
			background-color: #D6D6D6;
		}
		.center {
			text-align:center;
		}
		body,td,th {
			color: #06F; 
		}
		.larger {
			font-size:larger;
			text-align:right;
		}
		table {
			margin-left:auto;
			margin-right:auto;
		}
		</style>
	</head>
	<body>
		<h1 class="center">Room Finder</h1>
		<form>
			<table border="1">
				<tr>
					<th scope="col">Key</th>
					<th scope="col">Value</th>
				</tr>
				<tr>
					<td><label for="park">Park (park)</label></td>
					<td>
					<select name="park" id="park" class="larger" onchange="ajaxFunction();">
						<option>Any</option>
						<option>East</option>
						<option>Central</option>
						<option>West</option>
					</select>
					</td>
				</tr>
				<tr>
					<td><label for="capacity">Minimum Capacity (capacity)</label></td>
					<td>
						0<input type="range" name="slider" size="12" id="slider" min=0 max=400 step=5 value=150 oninput="capacity.value=value; ajaxFunction();"/>400
						<input name="capacity" readonly class="larger" id="capacity" value="150" size="3"/>
					</td>
				</tr>
				<tr>
					<td><label for="park">Sort (sort)</label>
					<td><input type="checkbox" id="sort" onchange="ajaxFunction();" /></td>
				</tr>
				<tr>
					<td>Submit</td>
					<td><input type="button" name="submit" id="submit" value="Submit" class="larger" onclick="ajaxFunction()" /></td>
				</tr>
			</table>
			</form>
			<div id="res">
			results go here.
			</div>
	</body>
</html>

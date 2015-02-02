<!DOCTYPE html>
<html>
<head>
	<title>Advanced Requests</title>
	<meta charset="UTF-8">
	
	<link rel="stylesheet" href="css/style.css" type="text/css" />
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" />
	
	<script src="javascript/jquery-2.1.1.js"></script>
	<script src="javascript/jquery-ui.js"></script>
	<script src="javascript/script.js"></script>
	<meta name="viewport" content="width-device-width, initial-scale=1.0"> <!-- Always needed if making a responsive website -->
</head>
<body>
	<input type="button" value="Close me!" onclick='closePopupRequestDiv();'></input>
	<table class='imageTable' style='padding-left:10px;' id='locationInputTable'>					
		<tr>
			<table class ='imageTable' id="multiRoomTable" border='1' style='margin-left:10px; font-family:arial; font-size:16px; color:#FFFFFF;'>
				<tr>
					<td></td>                                                                                                                                                                                                                                                                                                                                                                  
					<td>
						<label for="park" >Park</label>
					</td>
					<td>
						<label for="building" >Building</label>
					</td>
					<td>
						<label for="room" >Room</label>
					</td>
				</tr>							
				<tr>
					<td>
						<label for="multiRoom" >Room</label>
					</td>
					<td>
						<div id='parkDiv'>
							<select>
								<option>Any</option>
							</select>
						</div>
					</td>
					<td>
						<div id='buildingDiv'>
							<select>
								<option>Any</option>
							</select>
						</div>
					</td>
					<td>
						<div id='roomDiv'>
							<select>
								<option>Any</option>
							</select>										
						</div>
					</td>
				</tr>	
				<!--<tr>
					<td colspan='5' style='text-align:center'>
						<input class="rButtons" type='button' id='addRoomButton' value='Click here to add another room' onclick='addRoom();'></input>
					</td>
				</tr>-->
			</table>
			<br />
			<input id='bleh' type='button' value='Search'></input>	
		</tr>
	</table>
</body>
</html>
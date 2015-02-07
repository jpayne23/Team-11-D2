<!DOCTYPE html>
<html>
	<head>
		<title>Advanced Requests</title>
		<meta charset="UTF-8">
		
		<!--link rel="stylesheet" href="css/style.css" type="text/css" />-->
		<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" />
		<link rel="stylesheet" href="css/request.css" type="text/css" />
		<script src="javascript/jquery-2.1.1.js"></script>
		<script src="javascript/jquery-ui.js"></script>
		<script src="javascript/script.js"></script>
		<meta name="viewport" content="widtd-device-widtd, initial-scale=1.0"> <!-- Always needed if making a responsive website -->
	</head>
	<body id= 'body' class= 'requestbody'>
		<button class= 'closeAdvanced' onclick='closeAdvancedRequestDiv()'> x </button>
		<div id= 'whole' class= 'whole'>
		<div id='1st' class= 'rooms'>
			<div id= 'parktab' class='parktab'>
				<header class="parkHeader"> 
					<nav>
						<ul >
							<li><a href='#' id= 'parkeast' class='parkeast' onclick='clearParkContent(); clearBuildingContent(); hideParkContent(); clearRoomContent(); updateAdvancedBuilding(this.id)'>East Park</a></li>
							<li><a href='#' id='parkcentral' class= 'parkcentral' onclick='clearParkContent(); clearBuildingContent(); hideParkContent(); clearRoomContent(); updateAdvancedBuilding(this.id)'>Central Park</a></li>
							<li><a href='#' id= 'parkwest' class= 'parkwest' onclick='clearParkContent(); clearBuildingContent(); hideParkContent(); clearRoomContent(); updateAdvancedBuilding(this.id)'>West Park</a></li>
						</ul>
					</nav>
				</header>
			</div>

			<div class= 'requestcontent'>
				<div id= 'parkcontent' class= 'parkcontent' name= 'parkcontent'>
					<div id= 'east' class= 'parkrooms'>
						<tr> <td class= 'rooms'> Room Name: LDS.0.17 Capacity 148 <button id= 'eastButton' data-divID= 'eastinfo' class= 'parkroom' onclick='showEastContent()'> More Info </button></td></tr>
						<tr> <td class= 'rooms'> Room Name: zzzzz Capacity 100 <button id= 'eastButton' data-divID= 'eastinfo' class= 'parkroom' onclick='showEastContent()'> More Info </button></td></tr>
					</div>
					<div id= 'central' class= 'parkrooms'>
						<tr> <td class= 'rooms'> Room Name: A.0.01 Capacity 102 <button data-divID= 'centralinfo' class= 'parkroom' onclick='showCentralContent()'> More Info </button></td></tr>
						<tr> <td class= 'rooms'> Room Name: zzzzz Capacity 100 <button data-divID= 'centralinfo' class= 'parkroom' onclick='showCentralContent()'> More Info </button></td></tr>
					</div>
					<div id= 'west' class= 'parkrooms'>
						<tr> <td class= 'rooms'> Room Name: W.0.01 Capacity 124 <button data-divID= 'westinfo' class= 'parkroom' onclick='showWestContent()'> More Info </button></td></tr>
						<tr> <td class= 'rooms'> Room Name: zzzzz Capacity 100 <button data-divID= 'westinfo' class= 'parkroom' onclick='showWestContent()'> More Info </button></td></tr>
					</div>
				</div>
				<div id= 'buildingcontent' class= 'buildingcontent'>
					<div id= 'eastinfo' class= 'central' >
						room info
					</div>
					<div id= 'centralinfo' class= 'central'>
						room info
					</div>
					<div id= 'westinfo' class= 'central'>
							room info
					</div>
				</div>
				<div id= 'roominfo' class= 'roominfo'>
					<div id= 'eastinfo' class= 'central' >
						room info
					</div>
					<div id= 'centralinfo' class= 'central'>
						room info
					</div>
					<div id= 'westinfo' class= 'central'>
							room info
					</div>
				</div>
			</div>
		</div>

		<div id='2nd' class= 'time'>
				<table class='timetable'>
					<tr>
						<th class= 'title'> Days </th>
						<th class= 'title' id='p1'> 09.00-10.00 </br> Period 1 </th>
						<th class= 'title' id='p2'> 10.00-11.00 </br> Period 2 </th>
						<th class= 'title' id='p3'> 11.00-12.00 </br> Period 3 </th>
						<th class= 'title' id='p4'> 12.00-13.00 </br> Period 4 </th>
						<th class= 'title' id='p5'> 13.00-14.00 </br> Period 5 </th>
						<th class= 'title' id='p6'> 14.00-15.00 </br> Period 6 </th>
						<th class= 'title' id='p7'> 15.00-16.00 </br> Period 7 </th>
						<th class= 'title' id='p8'> 16.00-17.00 </br> Period 8 </th>
						<th class= 'title' id='p9'> 17.00-18.00 </br> Period 9 </th>
						<tr> <th class= 'title' id='d1'> Monday </th></tr>
						<tr> <th class= 'title' id='d2'> Tuesday </th></tr>
						<tr> <th class= 'title' id='d3'> Wednesday </th></tr>
						<tr> <th class= 'title' id='d4'> Thursday </th></tr>
						<tr> <th class= 'title' id='d5'> Friday </th></tr>					
					</tr>

				</table>
		</div>
	</body>
</html>
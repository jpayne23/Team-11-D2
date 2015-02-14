<!DOCTYPE html> 
<html>
	<head>
		<title>Advanced Requests</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="widtd-device-widtd, initial-scale=1.0"> <!-- Always needed if making a responsive website -->
	</head>
	<body id= 'body' class= 'requestbody'>
		<button class= 'closeAdvanced' onclick='closeDiv("popupRequestDiv");'> x </button>
		<div id= 'whole' class= 'requestMainDiv'>
			<div id='1st' class= 'rooms'>
				<div id= 'parktab' class='parktab'>
					<header class="parkHeader"> 
						<nav>
							<ul >
								<li><a href='#' id= 'parkeast' class='parkeast' onclick='clearParkContent(); clearBuildingContent(); updateAdvancedBuilding(this.id)'>East Park</a></li>
								<li><a href='#' id='parkcentral' class= 'parkcentral' onclick='clearParkContent(); clearBuildingContent(); updateAdvancedBuilding(this.id)'>Central Park</a></li>
								<li><a href='#' id= 'parkwest' class= 'parkwest' onclick='clearParkContent(); clearBuildingContent(); updateAdvancedBuilding(this.id)'>West Park</a></li>
								<li>
									<select id='popupDay'>
										<option>Monday</option>
										<option>Tuesday</option>
										<option>Wednesday</option>
										<option>Thursday</option>
										<option>Friday</option>
									</select>
								</li>
								<li>
									<select id='popupTime'>
										<option>09:00</option>
										<option>10:00</option>
										<option>11:00</option>
										<option>12:00</option>
										<option>13:00</option>
										<option>14:00</option>
										<option>15:00</option>
										<option>16:00</option>
										<option>17:00</option>
									</select>
								</li>
								<li>
									<a>Module Size</a>
									<input type='text' readonly id='maxGroupSize'/>
								</li>
								<li>
									<a>Group Size</a>
									<input type='range' id='groupSize' oninput='groupSizeVal.value=value;'/>
									<input id='groupSizeVal'/>
								</li>							
							</ul>
						</nav>
					</header>
				</div>

				<div class= 'requestcontent'>
					<div id= 'parkcontent' class= 'parkcontent' name= 'parkcontent'>
					</div>

					<div id= 'roomcontainer' class= 'roomcontainer'>
						<div id= 'buildingcontent' class= 'buildingcontent'>
						</div>
						<div id= 'selectedcontainer' class= 'selectedcontainer'>
							<div id= 'selectedrooms' class= 'selectedrooms'>
							</div>
							<div id= 'compared' class= 'compared'>
							</div>
						</div>
					</div>
					<div id= 'roominfo' class= 'roominfo'>

					
				</div>
			</div>

			<div id='2nd' class= 'time'>
					<table id='timetable' class='timetable'>
						<tr>
							<th class= 'ttheader'> Days/Times </th>
							<th class= 'ttheader' id='p1'> 09.00-10.00 </br> Period 1 </th>
							<th class= 'ttheader' id='p2'> 10.00-11.00 </br> Period 2 </th>
							<th class= 'ttheader' id='p3'> 11.00-12.00 </br> Period 3 </th>
							<th class= 'ttheader' id='p4'> 12.00-13.00 </br> Period 4 </th>
							<th class= 'ttheader' id='p5'> 13.00-14.00 </br> Period 5 </th>
							<th class= 'ttheader' id='p6'> 14.00-15.00 </br> Period 6 </th>
							<th class= 'ttheader' id='p7'> 15.00-16.00 </br> Period 7 </th>
							<th class= 'ttheader' id='p8'> 16.00-17.00 </br> Period 8 </th>
							<th class= 'ttheader' id='p9'> 17.00-18.00 </br> Period 9 </th>
							<tr> <th class= 'ttheader' id='d1'> Monday </th></tr>
							<tr> <th class= 'ttheader' id='d2'> Tuesday </th></tr>
							<tr> <th class= 'ttheader' id='d3'> Wednesday </th></tr>
							<tr> <th class= 'ttheader' id='d4'> Thursday </th></tr>
							<tr> <th class= 'ttheader' id='d5'> Friday </th></tr>					
						</tr>
					</table>
			</div>
		</div>
	</body>
</html>
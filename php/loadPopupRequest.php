<!DOCTYPE html> 
<!--
This page is the contents that is loaded as a pop-up of the popupRequestDiv in both the Adoc and Homepage.
It is initiated by btnAdvancedRequest, which then runs its associated click function.
With this page the user can view information on specific rooms. 
The information includes the facilities associated to the selected room and its capacity, room type and image.
The user can view the weeks available for a set of rooms by selecting the Add to Compare List button in the roominfo dialog.
The user can also select a room by selecting the Select Room button in the roominfo dialog.
By selecting the findroom button, the user can then go onto select multiple facilities, state the group size and then click on the roomSearchSubmit button, to view a list of rooms that match the chosen requirements.

Contribution by Bhavnit, Daniel, Prakash and Jason
-->
<html>
	<head>
		<title>Advanced Requests</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="widtd-device-widtd, initial-scale=1.0"> <!-- Always needed if making a responsive website -->
	</head>
	<body id= 'body' class= 'requestbody'>
			<button class= 'closeRequestDiv' onclick='closeDiv("popupRequestDiv");'> x </button>
		<div id= 'whole' class= 'requestMainDiv'>
			<div id='1st' class= 'rooms'>
				<div id= 'parktab' class='parktab'>
					<header class="parkHeader"> 
						<nav>
							<ul >
								<li><a href='#' id= 'parkeast' class='parkeast' onclick='clearParkContent(); clearBuildingContent(); updateAdvancedBuilding(this.id)'>East</a></li>
								<li><a href='#' id='parkcentral' class= 'parkcentral' onclick='clearParkContent(); clearBuildingContent(); updateAdvancedBuilding(this.id)'>Central</a></li>
								<li><a href='#' id= 'parkwest' class= 'parkwest' onclick='clearParkContent(); clearBuildingContent(); updateAdvancedBuilding(this.id)'>West</a></li>
								<li>
									<a href='#' id= 'findroom' onclick ='findRoomOpen();'>Find Room</a>
								</li>	
								<li>
									<select class= "optionResize" id='popupDay'>
										<option>Monday</option>
										<option>Tuesday</option>
										<option>Wednesday</option>
										<option>Thursday</option>
										<option>Friday</option>
									</select>
								</li>
								<li>
									<select class= "optionResize" id='popupTime'>
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
									<input class= "optionResize" id='maxGroupSize' size = 3 readonly/>
								</li>
								<li>
									<a>Group Size</a>
									<input class= "optionResize" id='groupSize' type='range' oninput='groupSizeVal.value=value;'/>
									<input class= "optionResize" id='groupSizeVal' size = 3 oninput='groupSize.value=value;' readonly/>
								</li>		
							</ul>
						</nav>
					</header>
				</div>

				<div class= 'requestcontent'>
					<div id= 'parkcontent' class= 'parkcontent' name= 'parkcontent'>
					</div>
					<div id= 'findroomDiv' class ='findRoomPopupDiv' style='visibility: hidden;'>	
						<div id= 'searchResultsDiv'></div>
						<div id= 'matchedRoomsDiv'></div>
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
						<tr id='mon'> <th class= 'ttheader' id='d1'> Monday </th></tr>
						<tr id='tue'> <th class= 'ttheader' id='d2'> Tuesday </th></tr>
						<tr id='wed'> <th class= 'ttheader' id='d3'> Wednesday </th></tr>
						<tr id='thu'> <th class= 'ttheader' id='d4'> Thursday </th></tr>
						<tr id='fri'> <th class= 'ttheader' id='d5'> Friday </th></tr>					
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
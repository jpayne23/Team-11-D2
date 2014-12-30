<html>
	<head>
		<title>Website Prototype</title>
		<link rel="Shortcut Icon" type="image/png" href="icon.png" />
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
		<link href="mystyle.css" rel="stylesheet" type="text/css">
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
		<script src='script.js' type='text/javascript'></script>
	</head>
	<body style='background-color:#708090' onload='updateModCode();updateModName();'>
		<div id="whole" style='width:1680px; margin-left:auto; margin-right:auto;'>
			<div style='height:4%; margin-left:7.5%; margin-right:7.5%; width:85%;'>
				<form action="http://co-project.lboro.ac.uk/team11/WebsitePrototype.html" style='display:inline'>
					<input class='tabButtons' type='submit' value='Home' ></input>
				</form>
				<form action="http://co-project.lboro.ac.uk/team11/Adhoc.html" style='display:inline'>
					<input class='tabButtons' type='submit' value='Ad hoc' ></input>
				</form>
				<form action="http://co-project.lboro.ac.uk/team11/history.html" style='display:inline'>
					<input class='tabButtons' type='submit' value='History' ></input>
				</form>
				<input class='tabButtons' type='button' value='Pending Submissions' onclick='openPendingDiv();'></input>				
				<form action="http://co-project.lboro.ac.uk/team11/login.html" style='display:inline'>
					<input class='tabButtons' type='submit' value='Log Out' ></input>
				</form>
			</div>	
			<!--<input type="button" <a href="Website Prototype1.html" style='font-family:arial; font-size:3.5mm; font-color:#333333; text-align:center; margin-left'>
			<th style='padding:4px 8px; border-right:1.5px solid #ccc;'> <a href="history.html" style='font-family:arial; font-size:3.5mm; font-color:#333333; text-align:center; margin-left'> History </a> </th>-->
			<div style= 'float:left; margin-left:7.5%; width:85%; height:70%;' >
				<div style= 'background-color:#2DABE0; overflow:auto; float:left; width:33%; height:100%; border-style:double; border-color:#FFFFFF; border-radius:10px 10px;'>
					<h3 style='text-align:left; padding-left:10px; font-family:arial; font-size:20pt; color:#FFFFFF'>General Information</h3>
					<table style='height:50%'>
						<tr>
							<td>								
								<label for="deptCode" style='font-family:arial; font-size:16px; color:#FFFFFF;'>Department Code</label>
							</td>
							<td>
								<select name="deptCode" id="deptCode" class="larger" onchange="updateModCode();">
									<option>CO</option>
									<option>MM</option>
									<option>EA</option>  
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="part" style='font-family:arial; font-size:16px; color:#FFFFFF;'> Part</label>
							</td>
							<td>
								<select name="part" id="part" class="larger" onchange="updateModCode();">
									<option>A</option>
									<option>B</option>
									<option>C</option>
									<option>D</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="modCode" style='font-family:arial; font-size:16px; color:#FFFFFF;'>Module Code</label>
							</td>
							<td>
								<select name="modCode" id="modCode" class="larger" onchange="updateModName();">
									<option></option>
								</select>
							</td>
							<td>
								<select name="modName" id="modName" class="larger" onchange="updateModCodeFromName()">
									<option></option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="weeks" style='font-family:arial; font-size:16px; color:#FFFFFF;'>Pick weeks</label>								
							</td>
							<td colspan='2'>
								<ol id="popupWeeks">
									<li class="ui-state-default">1</li>
									<li class="ui-state-default">2</li>
									<li class="ui-state-default">3</li>
									<li class="ui-state-default">4</li>
									<li class="ui-state-default">5</li>
									<li class="ui-state-default">6</li>
									<li class="ui-state-default">7</li>
									<li class="ui-state-default">8</li>
									<li class="ui-state-default">9</li>
									<li class="ui-state-default">10</li>
									<li class="ui-state-default">11</li>
									<li class="ui-state-default">12</li>
									<li class="ui-state-default">13</li>
									<li class="ui-state-default">14</li>
									<li class="ui-state-default">15</li>
								</ol>
								<p id='weeksSelected'>You have selected weeks: </p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="seshType" style='font-family:arial; font-size:16px; color:#FFFFFF;'>Session Type</label>
							</td>
							<td>
								<select name="seshType" id="seshType" class="larger" onchange="update();">
									<option>Feedback</option>
									<option>Lecture</option>
									<option>Practical</option>
									<option>Seminar</option>
									<option>Tutorial</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="seshLength" style='font-family:arial; font-size:16px; color:#FFFFFF;'> Session Length </label>
							</td>
							<td>
								<select name="seshLength" id="seshLength" class="larger" onchange="update();">
									<option>1 Hour</option>
									<option>2 Hours</option>
									<option>3 Hours</option>
									<option>4 Hours</option>
									<option>5 Hours</option>
								</select>
							</td>
						</tr>
					</table>
					<br />
				</div>
				<div style='background-color:#2DABE0; overflow:auto; float:left; margin-left:1%; width:31%; height: 100%; border-style:double; border-color:#FFFFFF; border-radius:10px 10px'>
					<table id='fac' style='width:95%; margin-left:15px;'>
						<h3 style='text-align:left; padding-left:10px; font-family:arial; font-size:20pt; color:#FFFFFF'>Facilities</h3>
						<tr>
							<td id='checkboxes' style='font-family:arial; font-size:16px; color:#FFFFFF; padding:0px;'>							
								<div id='facilitiesDiv'>
								</div>
							</td>
						</tr>
					</table>
					<!--<p><a href="Rooms_Timetable_Proto.html" onclick="newPopup(this.href, 'myWindow', '1500', '1300', 'no');return false"> open rooms pop-up </a></p>-->
					<button onclick='openAdvancedSearchDiv()'>Advanced Search</button>
					<input type='button' id='getCheckedFacilities' value = 'Get Selected Facilities'/>
					<div id="checkedFacilitiesDiv"></div>
					<input type='button' id='getMatchingRooms' value='Find rooms for these facilities'/>
					<div id="matchedRoomsdiv"></div>
				</div>
				<div  id='requests' style='background-color:#2DABE0; overflow:auto; float:left; width:32.74%; height:100%; margin-left:1%; border-style:double; border-color:#FFFFFF; border-radius:10px 10px;'>
					<h4 style='text-align:left; padding-left:10px; font-family:arial; font-size:18pt; color:#FFFFFF;'>Advanced Search</h4>
					<!--<input class='buttons' type='button' id='showHide' onclick='showhide();' value='Show' style='font-size: 14px; margin-left:15px;'>-->
					<table class='imageTable' style='padding-left:10px;' id='locationInputTable'>					
						<tr>
							<td style='font-family:arial; font-size:16px; color:#FFFFFF;'>Search Type</td>
							<td>
								<select name="priority" id="priority" class="larger" onchange="updateSearch(this.selectedIndex);">
									<option selected='selected'>Room Info</option>
									<option>Day + Time</option>
									<option>Facilities</option>
									<option>Capacity</option>
								</select>
							</td>
						</tr>
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
											<select id='chosenRoom'>
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
							<input id='btnGetFacilities' type='button' value='Get Facilities'></input>
						</tr>
					</table>
					
					<div id="dialog" title="Facilities of this room"></div>
				</div>
			</div>
			<div style='float:left; margin-left:7.5%; margin-right:7.5%; width:100%; margin-top:0.5%;'>
				<input class='tabButtons' type='button'  value='Submit' onclick='submitRequest();'></input>
				<input class='tabButtons' type='button' value='Next Round' onclick='nextRound();'></input>
			</div>	
		</div>
		
		<div id='popupPendingDiv' style='visibility: hidden;'>
			<input type="button" value="closeme" onclick='closePendingDiv();'></input>
			<div id='submissions' style='background-color:#2D9BE0; overflow:auto; margin-left: 2.5%; width:95%; height:100%; border-style:double; border-color:#FFFFFF; border-radius:10px; text-align:center; margin-top:1%; margin-bottom:1%;'> <!--border-top:5px; border-bottom:0px; border-right:0px; border-left:0px;--> 
				<h3  id='pendingTitle' style='text-align:left; padding-left:10px; font-family:arial; font-size:20pt; color:#FFFFFF'> Pending submissions for Round 1 </h3>
				<table border='1' id='submissionsTable' style='width:100%; margin-left:auto; margin-right:auto; font-family:arial; font-size:16px; color:#FFFFFF;'>
					<th>Request ID</th>
					<th>Module Code</th>
					<th>Start Week</th>
					<th>End Week</th>
					<th>Location (Park/Building/Room)</th>
					<th>Session Type</th>
					<th>Session Length</th>
					<th>Priority</th>
					<th>Facilities</th>
					<th>Edit</th>
					<th>Cancel</th>
				</table>
			</div>
		</div>
		<div id='popupAdvancedSearchDiv' style='visibility: hidden'>
			<div class= 'rooms'>
				<button class= 'close' onclick='closeAdvancedSearchDiv()'> x </button>
				<table class= 'park'>
					<tr class= 'park'>
						<th id= 'parkeast' class= 'park' onclick='showEast(); hideParkContent()'> East Park </th>
						<th id= 'parkcentral' class= 'park' onclick='showCentral(); hideParkContent()'> Central Park </th>
						<th id= 'parkwest' class= 'park' onclick='showWest(); hideParkContent()'> West Park </th>
					</tr>
				</table>
				<table id='east' class= 'east'>
					<th>
						<tr> <th class= 'rooms'> Room Name: LDS.0.17 Capacity 148 <button onclick='showEastContent()'> More Info </button></th></tr>
						<tr> <th class= 'rooms'> Room Name: zzzzz Capacity 100 <button onclick='showEastContent()'> More Info </button></th></tr>
					</th>
					<th id='eastinfo' class='central'> 
						room info
					</th>
				</table>
				<table id='central' class= 'central'>
					<th>
						<tr> <th class= 'rooms'> Room Name: A.0.01 Capacity 102 <button onclick='showCentralContent()'> More Info </button></th></tr>
						<tr> <th class= 'rooms'> Room Name: zzzzz Capacity 100 <button onclick='showCentralContent()'> More Info </button></th></tr>
					</th>
					<th id='centralinfo' class='central'> 
						room info
					</th>
				</table>
				<table id='west' class= 'west'>
					<th>
						<tr> <th class= 'rooms'> Room Name: W.0.01 Capacity 124 <button onclick='showWestContent()'> More Info </button></th></tr>
						<tr> <th class= 'rooms'> Room Name: zzzzz Capacity 100 <button onclick='showWestContent()'> More Info </button></th></tr>
					</th>
					<th id='westinfo' class='central'> 
						room info
						<!--<table id='westinfo' class='central'>
							<tr>
								<th> room info</th>
							</tr>
						</table>-->
					</th>
				</table>
			</div>
			<div class= 'timetable'>
				<table class= 'timetable'>
					<tr>
						<th class= 'times'> </th>
						<th class= 'times'> 09.00-10.00 </br> Period 1</th>
						<th class= 'times'> 10.00-11.00 </br> Period 2 </th>
						<th class= 'times'> 11.00-12.00 </br> Period 3 </th>
						<th class= 'times'> 12.00-13.00 </br> Period 4 </th>
						<th class= 'times'> 13.00-14.00 </br> Period 5 </th>
						<th class= 'times'> 14.00-15.00 </br> Period 6 </th>
						<th class= 'times'> 15.00-16.00 </br> Period 7 </th>
						<th class= 'times'> 16.00-17.00 </br> Period 8 </th>
						<th class= 'times'> 17.00-18.00 </br> Period 9 </th>
					</tr>
					<tr>
						<th class= 'days'> Monday </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
					</tr>
					<tr>
						<th class= 'days'> Tuesday </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
					</tr>
					<tr>
						<th class= 'days'> Wednesday </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
					</tr>
					<tr>
						<th class= 'days'> Thursday </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
					</tr>
					<tr>
						<th class= 'days'> Friday </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
						<th class= 'content'> </th>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>

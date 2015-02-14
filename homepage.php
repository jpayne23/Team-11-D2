<!DOCTYPE html>
<html>
<head>
	<title>Timetabling System</title>
	<meta charset="UTF-8">
	
	<link rel="stylesheet" href="css/style.css" type="text/css" />
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" />
	<link rel="stylesheet" href="css/request.css" type="text/css" />
	<script src="javascript/jquery-2.1.1.js"></script>
	<script src="javascript/jquery-ui.js"></script>
	<script src="javascript/script.js"></script>
	<script src="javascript/requestscript.js"></script>
	<meta name="viewport" content="width-device-width, initial-scale=1.0"> <!-- Always needed if making a responsive website -->
</head>

<body class="body">
	<div id="textsize" data-counter="0">
		<img id="minustext" alt="Decrease text size" src="img/smaller.png">
		<img id="plustext"  alt="Increase text size" src="img/bigger.png">
	</div>
	<header class="mainHeader"> 
		<img src="img/logo.png">
		<nav>
			<ul>
				<li class="active"><a href="#">Home</a></li>
				<li><a href="#" id='pendingButton'>Pending</a></li>
				<li><a href="adhoc.php" id='adhocButton'>Ad Hoc</a></li>
				<li><a href="#" id='historyButton'>History</a></li>
				<li><a href="#" id='lastYearButton'>View Last Year's Requests</a></li>
				<li><a href="php/logout_script.php">Log Out</a></li>
			</ul>
		</nav>	
	</header>
	<div class="mainContent">
		<div class="content">
			<article class="topcontent">
				<header>
					<h2 id='genInfo'> General Information</h2>
				</header>
				<content>
					<table>
						<tr>
							<td>								
								<label>Department Code</label>
							</td>
							<td>
									<?php
										session_start();
										if (isset($_SESSION["deptCode"]))
										{
											require_once 'MDB2.php';			
											include "/disks/diskh/teams/team11/passwords/password.php";
											$dsn = "mysql://$username:$password@$host/$dbName"; 
											$db =& MDB2::connect($dsn); 
											if(PEAR::isError($db)){ 
												die($db->getMessage());
											}
											$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
											
											$deptCode = $_SESSION["deptCode"];
											$sql = "SELECT DeptCode, DeptName FROM DeptNames WHERE DeptCode = '".$deptCode."';";
											$res =& $db->query($sql);
											
											if(PEAR::isError($res))
											{
												die($res->getMessage());
											}
											while ($row = $res->fetchRow())
											{
												echo '<div id="deptCodeDiv" title="'.$row["deptcode"].'">'.$row["deptcode"].' - '.$row["deptname"].'</div>';
											}
										}
										else
										{
											header("Location: login.html");
										}
									?>
							</td>
						</tr>
						<tr>
							<td>
								<label>Part</label>
							</td>
							<td>
								<div id="partDiv">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<label>Module Code</label>
							</td>
							<td>
								<div id="modCodeDiv">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<label>Pick weeks</label>								
							</td>
							<td colspan='2'>
								<ol id="weekSelector">
									<li class="ui-state-default ui-selected">1</li>
									<li class="ui-state-default ui-selected">2</li>
									<li class="ui-state-default ui-selected">3</li>
									<li class="ui-state-default ui-selected">4</li>
									<li class="ui-state-default ui-selected">5</li>
									<li class="ui-state-default ui-selected">6</li>
									<li class="ui-state-default ui-selected">7</li>
									<li class="ui-state-default ui-selected">8</li>
									<li class="ui-state-default ui-selected">9</li>
									<li class="ui-state-default ui-selected">10</li>
									<li class="ui-state-default ui-selected">11</li>
									<li class="ui-state-default ui-selected">12</li>
									<li class="ui-state-default">13</li>
									<li class="ui-state-default">14</li>
									<li class="ui-state-default">15</li>
								</ol>								
								</br>
							</td>
						</tr>
						<tr>
							<td>
								<label>Session Type</label>
							</td>
							<td>
								<select id="seshType">
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
								<label> Session Length </label>
							</td>
							<td>
								<select id="seshLength">
									<option>1 Hour</option>
									<option>2 Hours</option>
									<option>3 Hours</option>
									<option>4 Hours</option>
									<option>5 Hours</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label> Day </label>
							</td>
							<td>
								<select id="day">
									<option>Monday</option>
									<option>Tuesday</option>
									<option>Wednesday</option>
									<option>Thursday</option>
									<option>Friday</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label> Start Time </label>
							</td>
							<td>
								<select id="time">
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
							</td>
						</tr>
						<tr>
							<td>
								Special Requirements:
							</td>
							<td>
								<textarea id="specialReq" cols="40" rows="3"></textarea>
							</td>
						</tr>
						<tr>
							<td colspan='2'>
								<input type="checkbox" id='priorityCheckbox' checked="checked" disabled="disabled"> Priority Request
							</td>
						</tr>
						<!-- need this in ad-hoc
						<tr>
							<td>
								<label> Pick Date </label>
							</td>
							<td>
								<input type='text' id='date'>
							</td>
						</tr>
						-->	
					</table>
				</content>
				<input type="submit" class="homeButtons none" id="submit" value="Submit" />
				<input type='button' class="homeButtons" id='btnAdvancedRequest' value='Room Request'/>
				<input type='button' class="homeButtons" id='reset' value='Reset All Fields'/>
				<input type='button' class="homeButtons" name = '' id='round' value='Next Round'/>
			</article>
			<article class="bottomcontent">
				<header>
					<h2> Facilities </h2>
				</header>
				<content>
					<table id='facilitiesTable'>
						<tr>
							<td id='checkboxes'>							
								<div id='facilitiesDiv'></div>
							</td>
						</tr>
						<tr>
							<td>
								<input class="homeButtons" type='button' id='getMatchingRooms' value='Find rooms for these facilities'/>
								<div id="matchedRoomsdiv"></div>
							</td>
						</tr>
					</table>
				</content>
			</article>
			<article class="bottomcontent">
				<header>
					<h2> Chosen Rooms </h2>
				</header>
				<table id='chosenRooms' data-norooms=0 data-maxcap>				
				</table>
			</article>
		</div>
		<div id='popupPendingDiv'  class='popupDiv' style='visibility: hidden;'> <!--this div needs to be moved to a new webpage for pending submissions-->
			<input class='closeDiv' type="button" value="x" onclick='closeDiv("popupPendingDiv");closeDiv("filterDiv");'></input>
			<input class='pendingButton' type="button" value="Filter Requests..." onclick='openDiv("filterDiv");filterMenu("Pending")'></input>
			<div class='filterDiv' id='filterDiv' style='visibility: hidden;'>
			</div>
			<input class='pendingButton' type="button" id = 'submitRequests' value="Submit all requests" ></input>
			<div id='submissions'>				
			</div>
		</div>
		<div id='popupHistoryDiv'  class='popupDiv' style='visibility: hidden;'> <!--this div needs to be moved to a new webpage for history submissions-->
			<input type="button" class= 'closeDiv' value="x" onclick='closeDiv("popupHistoryDiv");closeDiv("filterDivHist")'></input>
			<input type="button" class='pendingButton' value="Filter Results" onclick='openDiv("filterDivHist");filterMenu("History");'></input>
			<div class='filterDiv' id='filterDivHist' style='visibility: hidden;'>
			</div>
			<div id='history'></div>
		</div>
		<div id='popupLastYear'  class='popupDiv' style='visibility: hidden;'>
			<input type="button" class= 'closeDiv' value="x" onclick='closeDiv("popupLastYear");'></input>
			<input type="button" class='pendingButton'  id='submitCheckedButton' value="Submit All Checked Requests"></input>
			<div id='lastYear'></div>
		</div>
		<div id='popupRequestDiv' class='popupDiv' style='visibility: hidden;'> <!--this div needs to be moved to a new webpage for pending submissions-->
		</div>
		<div id="dialog" title="Facilities of this room"></div>
	</div>
	<footer class="mainFooter">
		<p>Copyright &copy 2015: Team11
	</footer>
</body>
</html>
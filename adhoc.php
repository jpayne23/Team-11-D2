<!DOCTYPE html>
<html>
<head>
	<title>Timetabling System</title>
	<meta charset="UTF-8">
	
	<link rel="stylesheet" href="css/style.css" type="text/css" />
	<link rel="stylesheet" href="css/request.css" type="text/css" />	
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" />
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
				<li><a href="homepage.php">Home</a></li>
				<li class="active"><a href="#" id='adhocButton'>Ad Hoc</a></li>
				<li><a href="#" id='pastButton'>Past Requests</a></li>
				<li><a href="php/logout_script.php">Log Out</a></li>
			</ul>
		</nav>	
	</header>
	<div class="mainContent">
		<div class="content">
			<article class="topcontent">
				<header>
					<h2> General Information - Ad Hoc</h2>
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
								<label>Semester</label>
							</td>
							<td>
								<ol id='semesterSelector'>
									<li class="ui-state-default ui-selected">1</li>
									<li class="ui-state-default">2</li>
								</ol>
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
								<label>Pick week</label>								
							</td>
							<td colspan='2'>
								<ol id="adhocWeekSelector">
									<li class="ui-state-default ui-selected">1</li>
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
								</br>
							</td>
						</tr>
						<tr>
							<td>
								<label> Pick Date </label>
							</td>
							<td>
								<input type='text' id='date'>
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
								Special Requirements:
							</td>
							<td>
								<textarea id="specialReq" cols="40" rows="3"></textarea>
							</td>
						</tr>
					</table>					
				</content>
				<input type="submit" class="homeButtons none" id="submitAdhoc" value="Submit" />
				<input type='button' class="homeButtons" id='btnAdvancedRequest' value='Room Request'/>
				<input type='button' class="homeButtons" id='reset' value='Reset All Fields'/>
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
								<input type='button' id='getMatchingRooms' value='Find rooms for these facilities'/>
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
		<div id='popupPastDiv' style='visibility: hidden;'> <!--this div needs to be moved to a new webpage for pending submissions-->

			<input class='closeDiv' type="button" value="x" onclick='closeDiv("popupPastDiv");closeDiv("pastFilterDiv");'></input>
			<input class='pendingButton' type="button" value="Filter Requests..." onclick='openDiv("pastFilterDiv");filterMenu("Adhoc")'></input>
			<div class = 'filterDiv' id='pastFilterDiv' style='visibility: hidden;'>
			</div>
			<div id='past'>		
			</div>
		</div>
		<div id='popupRequestDiv' class = 'popupDiv' style='visibility: hidden;'> <!--this div needs to be moved to a new webpage for pending submissions-->
		</div>
		<div id="dialog" title="Facilities of this room"></div>
	</div>
	<footer class="mainFooter">
		<p>Copyright &copy 2015: Team11
	</footer>
</body>
</html>
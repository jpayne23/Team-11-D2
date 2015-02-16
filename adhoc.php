<!--  
This is file for the adhoc page in the system. The majority of the structure for this page is similar to homepage.php with a few differences.
These differences are a data input for the user to specify the semester the adhoc request is for and depending on what semester the user
chooses, the week selector will adjust to accommodate for different number of weeks in each semester. Also the user has the option to see 
any submitted adhoc requests and there status, with the option to edit unsuccessful requests.

As with homepage.php, contribution was from Jason, Dan, Jack, Prakash, Bhav and Joe
-->

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
		<?php 
		session_start();
		if ($_SESSION["access"] == "yes")
		{
			echo "<input id='btnAccessHome' class='homeButtons' type='button' value='Accessibility Mode - Off'/>";
		}
		else
		{
			echo "<input id='btnAccessHome' class='homeButtons' type='button' value='Accessibility Mode - Off'/>";
		}
		?>
		>	</div>
	<header class="mainHeader"> 
		<img src="img/logo.png">
		<nav>
			<ul>
				<li><a href="homepage.php">Home</a></li>
				<li class="active"><a href="#" id='adhocButton'>Ad Hoc</a></li>
				<li><a href="#" id='pastButton'>Past Requests</a></li>
				<li class='headerright'><a href="php/logout_script.php" id='logoutButton'>Log Out</a></li>
				<li class='headerright'><a href='#' id='AddModuleButton' onclick='AddNewModule();'>Add Module</a></li>
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
									<!-- Get the department code from user login to only show modules in that department -->
									<?php
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
								<select title='Day of the week' class= "optionResize" id="day">
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
								<label> Start Time </label>
							</td>
							<td>
								<select title='Time of the day' class= "optionResize" id="time">
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
								<select title='Session Length' class= "optionResize" id="seshLength">
									<option>1 Hour</option>
									<option>2 Hours</option>
									<option>3 Hours</option>
									<option>4 Hours</option>
									<option>5 Hours</option>
									<option>6 Hours</option>
									<option>7 Hours</option>
									<option>8 Hours</option>
									<option>9 Hours</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label>Session Type</label>
							</td>
							<td>
								<select title='Session Type' class= "optionResize" id="seshType">
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
								<textarea title='Special Requirements' class= "optionResize" id="specialReq" cols="40" rows="3" placeholder="e.g. Dimmer Lights"></textarea>
							</td>
						</tr>
					</table>					
				</content>
				<!-- Buttons -->
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
		<!-- Pop up that contains the past adhoc requests -->
		<div id='popupPastDiv' style='visibility: hidden;'>

			<input class='closeDiv' type="button" value="x" onclick='closeDiv("popupPastDiv");closeDiv("pastFilterDiv");'></input>
			<input class='pendingButton' type="button" value="Filter Requests..." onclick='openDiv("pastFilterDiv");filterMenu("Adhoc")'></input>
			<!-- Pop up for filter -->
			<div class = 'filterDiv' id='pastFilterDiv' style='visibility: hidden;'>
			</div>
			<!-- Table containing past adhoc requests -->
			<div id='past'>		
			</div>
		</div>
		<div id='popupRequestDiv' class = 'popupDiv' style='visibility: hidden;'>
		</div>
		<div id='popupAlertDiv' class='popupDiv' style='visibility: hidden'>	
			<input type="button" class="closeDiv" value="x" onclick='closeDiv("popupAlertDiv");'></input>
			<div id='alertDiv' class='successPopupDiv'>
			</div>
		</div>
		<div id="dialog" title="Facilities of this room"></div>
		<div id='newModuleDialog' title='Add a New Module'></div>
	</div>
	<footer class="mainFooter">
		<p>Copyright &copy 2015: Team11
	</footer>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<title>Timetabling System</title>
	<meta charset="UTF-8">
	
	<link rel="stylesheet" href="css/style.css" type="text/css" />
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" />
	
	<script src="javascript/jquery-2.1.1.js"></script>
	<script src="javascript/jquery-ui.js"></script>
	<script src="javascript/script.js"></script>
	<meta name="viewport" content="width-device-width, initial-scale=1.0"> <!-- Always needed if making a responsive website -->
</head>

<body class="body">
	<header class="mainHeader"> 
		<img src="img/logo.png">
		<nav>
			<ul>
				<li class="active"><a href="#">Home</a></li>
				<li><a href="#" id='pendingButton'>Pending</a></li>
				<li><a href="#">Ad Hoc</a></li>
				<li><a href="#" id='historyButton'>History</a></li>
				<li><a href="#">Log Out</a></li>
			</ul>
		</nav>
	</header>
	<div class="mainContent">
		<div class="content">
			<article class="topcontent">
				<header>
					<h2> General Information</h2>
				</header>
				<content>
					<table>
						<tr>
							<td>								
								<label>Department Code</label>
							</td>
							<td>
									<?php
										require_once 'MDB2.php';			
										include "/disks/diskh/teams/team11/passwords/password.php";
										$dsn = "mysql://$username:$password@$host/$dbName"; 
										$db =& MDB2::connect($dsn); 
										if(PEAR::isError($db)){ 
											die($db->getMessage());
										}
										$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
										
										$deptCode = $_POST["deptCode"];
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
								<label> Pick Date </label>
							</td>
							<td>
								<input type='text' id='date'>
							</td>
						</tr>
					</table>					
				</content>
			</article>
			<article class="bottomcontent">
				<header>
					<h2> Facilities </h2>
				</header>
				<content>
					<table id='facilitiesTable'>
						<tr>
							<td id='checkboxes'>							
								<div id='facilitiesDiv'>
								</div>
							</td>
						</tr>
					</table>
				</content>
		</article>
		</div>
		<div id='popupPendingDiv' style='visibility: hidden;'> <!--this div needs to be moved to a new webpage for pending submissions-->
			<input type="button" value="Close me!" onclick='closeDiv("popupPendingDiv");'></input>
			<div id='submissions'>				
			</div>
		</div>
		<div id='popupHistoryDiv' style='visibility: hidden;'> <!--this div needs to be moved to a new webpage for history submissions-->
			<input type="button" value="Close me!" onclick='closeDiv("popupHistoryDiv");'></input>
			<div id='history'>
			</div>
		</div>
	</div>
	<footer class="mainFooter">
		<p>Copyright &copy 2015: Team11
	</footer>
</body>
</html>
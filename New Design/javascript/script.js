var submissions = [];			
//stored in	this variable: all submissions that have been submitted and all submissions that have been added. ie concat of hist+pending
var submissionIDCounter = 0;
var subCounter = 0;
var round = 1;	
var numRooms = 1;

// Any JQUERY
//---------------------
$(document).ready(function()		// Execute all of this on load 
{	
	// Add week selector	
	var selectedItems = [];		// Holds all selectable elements which are already selected
	var numSelecting = 0;		// Keep count of how many elements we have selected during selecting event
	var selectAll = false;
	var removeAll = false;
	
	$("#popupWeeks").bind("mousedown", function(e) {				
		e.metaKey = true;		// Simulates holding down control to select non-adjacent elements
	}).selectable(
	{	
		selecting: function(e, ui)
		{
			var clickedText = ui.selecting.innerHTML;
			
			if (numSelecting == 0)		// If this is the first element being selected
			{
				// If element is not already selected, we now add all other elements we later select
				if (selectedItems.indexOf(clickedText) == -1)
				{
					selectAll = true;
					removeAll = false;
				}
				else	// If element is already selected, then we remove all other elements we later select
				{
					selectAll = false;
					removeAll = true;
				}
			}						
			
			// If element is not selected, add it to array
			if (selectAll)
			{
				selectedItems.push(clickedText);
			}
			
			if (removeAll)	// If element is already selected, then remove it from the array and remove it's class
			{
				selectedItems.splice(selectedItems.indexOf(clickedText), 1);
				ui.selecting.className = "ui-state-default";
			}

			// Move on to the next element
			numSelecting++;
		},
		
		stop: function()
		{
			// Reset selectedItems and numSelecting to 0
			selectedItems.length = 0;
			numSelecting = 0;
			var selectAll = false;
			var removeAll = false;
			
			// For every selected element, add it to the array
			$(".ui-selected", this).each(function() 
			{
				selectedItems.push(this.innerHTML);
			});
		},
		
		distance: 1			// This is so we can register normal mouse click events
	});
	
	// Since the distance on the selectable is now greater than zero, clicks do not work if the mouse does not move
	// Simulate mouse click like the selector would normally
	$("#popupWeeks li").click(function()
	{
		var clickedText = $(this).text();
		
		// If element is not selected, add it to array and add selected class
		if (selectedItems.indexOf(clickedText) == -1)
		{						
			$(this).addClass('ui-selected');
			selectedItems.push(clickedText);
		}
		else 	// If element is already selected, then remove it from the array and remove selected class
		{
			$(this).removeClass('ui-selected');
			selectedItems.splice(selectedItems.indexOf(clickedText), 1);
		}
	});
	
	// Add park selector
	$.get("updatePark.php", function(data)
	{
		$('#parkDiv').html(data);
	});
	
	// Load facilities
	$.get("loadFacilities.php", function(data)
	{
		$('#facilitiesDiv').html(data);
	});
	
	// Load Parts
	$(function()
	{
		var deptCode = "deptCode=" + document.getElementById("deptCodeDiv").title;
		$.get("loadPart.php?" + deptCode, function(data)
		{
			$('#partDiv').html(data);
		});
	});
	
	// Load Module Codes
	$(function()
	{
		var deptCode = "deptCode=" + document.getElementById("deptCodeDiv").title;
		var part = "&part=any";
		$.get("loadModCodes.php?" + deptCode + part, function(data)
		{
			$('#modCodeDiv').html(data);
		});
	});
	
});	

function updateModCode()
{
	var deptCode = "deptCode=" + document.getElementById("deptCodeDiv").title;
	var part = "&part=" + document.getElementById("part")[document.getElementById("part").selectedIndex].id;
	$.get("loadModCodes.php?" + deptCode + part, function(data)
	{
		$('#modCodeDiv').html(data);
	});
}

function updateBuilding()
{
	var string = $("#park1").serialize();
	$.get("updateBuilding.php?" + string, function(data)
	{
		$('#buildingDiv').html(data);
	}).done(function()		// Does this when the request is finished
	{
		updateRoom();
	});			
}

function updateRoom()
{
	var building = document.getElementById('building1');
	var string = "building=" + building[building.selectedIndex].id;
	
	$.get("updateRoom.php?" + string, function(data)
	{
		$('#roomDiv').html(data);
	});	
}		

function updateBackground()
{
	var parkChoice = document.getElementById('park1').value;
	var buildingChoice = document.getElementById('building1').value;
	var roomChoice = document.getElementById('room1').value;

	if (parkChoice == 'East' && buildingChoice == 'Design School'  && roomChoice == 'LDS.0.17')
	{
		document.getElementById('requests').style.backgroundImage = "url('http://www.lboro.ac.uk/media/wwwlboroacuk/content/facilitiesmanagement/pagephotos/lds017.jpg')";
		document.getElementById('requests').style.backgroundRepeat = "no-repeat";
		document.getElementById('requests').style.backgroundSize = "50% 30%";
		document.getElementById('requests').style.backgroundPosition = "left bottom";
	}
	else
	{
		document.getElementById('requests').style.backgroundImage = "";
		document.getElementById('requests').style.opacity = "1";
	}
		
			
}

function addRequest()
{
	var html = '';

	var park = [];
	var building = [];
	var room = [];
	
	for (var i = 1; i <= numRooms; i++)
	{
		park.push(document.getElementById('park'+i).value);
		building.push(document.getElementById('building'+i).value);
		room.push(document.getElementById('room'+i).value);
	}

	var deptCode = document.getElementById('deptCode').value;
	var part = document.getElementById('part').value;
	var moduleCode = document.getElementById('modCode').value;
	var sType = document.getElementById('seshType').value;
	var sLength = document.getElementById('seshLength').value;
	var priority = document.getElementById('priority').value;
	
	var parkIndex = [];				
	var buildingIndex = [];
	var roomIndex = [];

	for (var i = 1; i <= numRooms; i++)
	{
		parkIndex.push(document.getElementById('park'+i).selectedIndex);
		buildingIndex.push(document.getElementById('building'+i).selectedIndex);
		roomIndex.push(document.getElementById('room'+i).selectedIndex);
	}
	
	var deptCodeIndex = document.getElementById('deptCode').selectedIndex;
	var partIndex = document.getElementById('part').selectedIndex;
	var moduleCodeIndex = document.getElementById('modCode').selectedIndex;
	var sTypeIndex = document.getElementById('seshType').selectedIndex;
	var sLengthIndex = document.getElementById('seshLength').selectedIndex;
	var priorityIndex = document.getElementById('priority').selectedIndex;
	
	var checked = [];
	var checkedBool = [];
	$('#checkboxes input:checked').each(function() { 
		checked.push($(this).attr('value'));
	});
	
	$('#checkboxes input').each(function() { 
		checkedBool.push($(this).is(":checked"));
	});
	
	var temp = [submissionIDCounter, deptCodeIndex, partIndex, moduleCodeIndex, sTypeIndex, sLengthIndex, priorityIndex, parkIndex, buildingIndex, roomIndex, checkedBool];
	submissions.push(temp);
}

function submitRequest()
{
	addRequest();
	
	var html = '';
	var k = subCounter;
	for (var i = k; i < submissions.length; i++)
	{
		var reqID = submissions[i][0];
		var deptCode = document.getElementById('deptCode')[submissions[i][1]].value;
		var part = document.getElementById('part')[submissions[i][2]].value;
		var moduleCode = document.getElementById('modCode')[submissions[i][3]].value;
		var sType = document.getElementById('seshType')[submissions[i][4]].value;
		var sLength = document.getElementById('seshLength')[submissions[i][5]].value;
		var priority = document.getElementById('priority')[submissions[i][6]].value;

		var park = [];
		var building = [];
		var room = [];
		
		for (var j = 0; j < numRooms; j++)
		{
			park.push(document.getElementById('park1')[submissions[i][7][j]].value);				
			building.push(document.getElementById('building1')[submissions[i][8][j]].value);
			room.push(document.getElementById('room1')[submissions[i][9][j]].value);
		}
		
		var facilities = [];
		for (var j = 0; j < 9; j++)
		{
			if (submissions[i][10][j] == true)
				facilities.push(document.getElementById('c'+j).value);
		}
		
		var table = document.getElementById('submissionsTable');
		var row = table.insertRow(subCounter + 1);
		row.id = submissionIDCounter;
		var cellID = row.insertCell(0);
		var cell0 = row.insertCell(1);
		var cell1 = row.insertCell(2);
		var cell2 = row.insertCell(3);
		var cell3 = row.insertCell(4);
		var cell4 = row.insertCell(5);
		var cell5 = row.insertCell(6);
		var cell6 = row.insertCell(7);
		var cell7 = row.insertCell(8);
		var cell8 = row.insertCell(9);
		var cell9 = row.insertCell(10);
		
		cellID.innerHTML = reqID;
		cell0.innerHTML = moduleCode;
		
		for (var j = 0; j < park.length; j++)
		{
			cell3.innerHTML += park[j] + " - " + building[j] + " - " + room[j];
			cell3.innerHTML += "<br />";
		}
		
		cell4.innerHTML = sType
		cell5.innerHTML = sLength
		cell6.innerHTML = priority
		cell7.innerHTML = "";
		if (facilities.length == 0)
		{
			cell7.innerHTML = "None";
		}
		else
		{
			for (var j = 0; j < facilities.length; j++)
			{
				cell7.innerHTML += facilities[j];
				if (j < facilities.length -1) cell7.innerHTML += ", ";
			}
		}
		
		cell8.innerHTML = "<input class='pbuttons' type='button' value='Edit' onclick='editRow(this);'></input>";
		cell9.innerHTML = "<input class='pbuttons' type='button' value='Cancel' onclick='cancelRow(this);'></input>";

		subCounter++;
		submissionIDCounter++;
	}
}

function cancelRow(r)
{
	// Delete row in pending submissions
	var rowIndex = r.parentNode.parentNode.rowIndex;
	var rowID = r.parentNode.parentNode.id;
	document.getElementById('submissionsTable').deleteRow(rowIndex);
	
	// Update the status in history of submissions
	//document.getElementById('s'+rowID).innerHTML = 'Cancelled';
	for (var i = 0; i < submissions.length; i++)
	{
		if (submissions[i][0] == rowID)
		{
			submissions.splice(i, 1);
		}
	}
	
	subCounter--;
}

function editRow(r)
{
	var rowIndex = r.parentNode.parentNode.rowIndex;
	var rowID = r.parentNode.parentNode.id;
	
	// Loop through submissions to find the correct row to remove and edit
	for (var i = 0; i < submissions.length; i++)
	{
		if (rowID == submissions[i][0])
		{
			document.getElementById('deptCode').selectedIndex = submissions[i][1];
			document.getElementById('part').selectedIndex = submissions[i][2];
			document.getElementById('modCode').selectedIndex = submissions[i][3];
			document.getElementById('sWeek').selectedIndex = submissions[i][4];
			document.getElementById('lWeek').selectedIndex = submissions[i][5];
			document.getElementById('seshType').selectedIndex = submissions[i][6];
			document.getElementById('seshLength').selectedIndex = submissions[i][7];
			document.getElementById('priority').selectedIndex = submissions[i][8];
			document.getElementById('park1').selectedIndex = submissions[i][9];		
			document.getElementById('building1').selectedIndex = submissions[i][10];
			document.getElementById('room1').selectedIndex = submissions[i][11];
			
			for (var j=0; j<9; j++)
			{
				document.getElementById('c'+j).checked = submissions[i][12][j];
			}
			
			cancelRow(r);
		}
	}				
}

function nextRound()
{	
	round ++; 
	subCounter = 0;
	submissionIDCounter = 0;
	submissions = [];
	
	// Reflect round number in title
	document.getElementById('pendingTitle').innerHTML = "Pending submissions for Round " + round;
	
	// Clear pending submissions when switching round
	document.getElementById("submissionsTable").innerHTML = "<th>Request ID</th>"
	+   "<th>Module Code</th>"
	+	"<th>Location (Park/Building/Room)</th>"
	+	"<th>Session Type</th>"
	+	"<th>Session Length</th>"
	+	"<th>Priority</th>"
	+	"<th>Facilities</th>"
	+	"<th>Edit</th>"
	+	"<th>Cancel</th>";
	if (round == 2) document.getElementById('priority').disabled=false;
}	

function showhide()
{
	var value = document.getElementById('showHide').value;
	if (value == 'Show')
	{
		document.getElementById('showHide').value = 'Hide';
		document.getElementById('collapsible').style.visibility = 'visible';
	}
	else 
	{			
		document.getElementById('showHide').value = 'Show';
		document.getElementById('collapsible').style.visibility = 'collapse';
	}
}	

function addRoom()
{
	numRooms = document.getElementById('multiRoomTable').rows.length - 1;
	if (numRooms < 4)
	{
		var row = document.getElementById('multiRoomTable').insertRow(numRooms);
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);
		
		cell1.innerHTML = 'Room ' + (numRooms);
		cell1.style.color = "#FFFFFF";
		cell1.style.font = "16px arial";
		cell2.innerHTML =  '<select name="park" id="park' + numRooms + '" class="larger" onchange="updateBuilding(this);">' +
								'<option>Any</option>' +
								'<option>East</option>' +
								'<option>Central</option>' +
								'<option>West</option>' +
							'</select>';
		cell3.innerHTML = 	'<select name="building" id="building' + numRooms + '" class="larger" onchange="updateRoom(this);">' +
								'<option>Any</option>' +
							'</select>';
		cell4.innerHTML = 	'<select name="room" id="room' + numRooms + '" class="larger">' +
								'<option>Any</option>' + 
							'</select>';
		cell5.innerHTML = '<input class="rButtons" type="button" value="Remove" onclick="removeRoom(this);"></input>';
	}
	if (numRooms == 4)
	{
		var row = document.getElementById('multiRoomTable').insertRow(numRooms);
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);
		
		cell1.innerHTML = "Room " + (numRooms);
		cell1.id = "r"+ numRooms
		cell1.style.color = "#FFFFFF";
		cell1.style.font = "16px arial";
		cell2.innerHTML =  '<select name="park" id="park' + numRooms + '" class="larger" onchange="updateBuilding(this);">' +
								'<option>Any</option>' +
								'<option>East</option>' +
								'<option>Central</option>' +
								'<option>West</option>' +
							'</select>';
		cell3.innerHTML = 	'<select name="building" id="building' + numRooms + '" class="larger" onchange="updateRoom(this);">' +
								'<option>Any</option>' +
							'</select>';
		cell4.innerHTML = 	'<select name="room" id="room' + numRooms + '" class="larger">' +
								'<option>Any</option>' + 
							'</select>';	
		cell5.innerHTML = '<input class="rButtons" type="button" value="Remove" onclick="removeRoom(this);"></input>';
		var cell6 = document.getElementById('addRoomButton').parentNode;
		cell6.innerHTML = "Maximum number of rooms reached.";
		cell6.colSpan = "5";
		cell6.id = "maxRoomsLabel";
		cell6.style.color = "#FFFFFF";
		cell6.style.font = "16px arial";
	}			
}

function removeRoom(r)
{
	if (numRooms >= 4)
	{
		document.getElementById('maxRoomsLabel').innerHTML = "<input type='button' class='pbuttons' id='addRoomButton' value='Click here to add another room' onclick='addRoom();'></input>";
	}
	
	var rowIndex = r.parentNode.parentNode.rowIndex;
	document.getElementById('multiRoomTable').deleteRow(rowIndex);
	numRooms--;
	
	for (var i = 2; i <= numRooms; i++)
	{
		var rows = document.getElementById('multiRoomTable').rows[i];
		rows.cells[0].innerHTML = "Room " + i;
		rows.cells[1].childNodes[0].id = "park" + i;
		rows.cells[2].childNodes[0].id = "building" + i;
		rows.cells[3].childNodes[0].id = "room" + i;					
	}
}

function updateSearch(index)
{
	var html = '';
	
	document.getElementById('imageTable').innerHTML = '';	
	
	switch(index){
		case 0:
			html = '<tr><td></td><td><label for="park" >Park</label></td><td><label for="building" >Building</label></td><td>' + 
					'<label for="room" >Room</label></td></tr><tr><td><label for="multiRoom" >Room</label>' +
					'</td><td><select name="park" id="park1" class="larger" onchange="updateBuilding(this);">' +
					'<option>Any</option><option>East</option><option>Central</option><option>West</option>'
					'</select></td><td><select name="building" id="building1" class="larger" onchange="updateRoom(this);">' +
					'<option>Any</option></select></td><td><select name="room" id="room1" class="larger" onchange="updateBackground();">' +
					'<option>Any</option></select></td></tr>';
	}
}

// Open and close popups for pending submissions div
function openPendingDiv()
{
	document.getElementById('popupPendingDiv').style.visibility = 'visible';
}

function closePendingDiv()
{
	document.getElementById('popupPendingDiv').style.visibility = 'hidden';
}

// Open and close popups for selecting weeks div
function openWeeksDiv()
{
	document.getElementById('popupWeeksDiv').style.visibility = 'visible';
}

function closeWeeksDiv()
{
	document.getElementById('popupWeeksDiv').style.visibility = 'hidden';
}

/*function newPopup(url, winName, w, h, scroll) 
{
	var LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
	var TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
	var settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable';
	var popupWindow = window.open(url, winName, settings)
			
}*/
function openAdvancedSearchDiv()
{
	document.getElementById('popupAdvancedSearchDiv').style.visibility = 'visible';
}
function closeAdvancedSearchDiv()
{
	document.getElementById('popupAdvancedSearchDiv').style.visibility = 'hidden';
	document.getElementById('east').style.visibility= 'hidden'
	document.getElementById('eastinfo').style.visibility= 'hidden';
	document.getElementById('central').style.visibility= 'hidden';
	document.getElementById('centralinfo').style.visibility= 'hidden';
	document.getElementById('west').style.visibility= 'hidden';
	document.getElementById('westinfo').style.visibility= 'hidden';
}
function showEast()
{
	if(document.getElementById('parkcentral').style.backgroundColor= '#000000')
	{
		document.getElementById('parkcentral').style.backgroundColor= '#FFFFFF'
		document.getElementById('parkcentral').style.color= '#000000'
	}
	if(document.getElementById('parkwest').style.backgroundColor= '#000000')
	{
		document.getElementById('parkwest').style.backgroundColor= '#FFFFFF'
		document.getElementById('parkwest').style.color= '#000000'
	}
	document.getElementById('parkeast').style.backgroundColor= '#000000'
	document.getElementById('parkeast').style.color= '#FFFFFF'
	
	if(document.getElementById('central').style.visibility= 'visible')
	{
		document.getElementById('central').style.visibility= 'hidden'
	}
	if(document.getElementById('west').style.visibility= 'visible')
	{
		document.getElementById('west').style.visibility= 'hidden'
	}
	document.getElementById('east').style.visibility= 'visible'
}

function showEastContent()
{
	if(document.getElementById('parkeast').style.height= '40')
	{	
		document.getElementById('parkeast').style.height= '20%'
	}
	if(document.getElementById('parkeast').style.height= '20%')
	{	
		document.getElementById('parkeast').style.height= '40%'
	}
	if(document.getElementById('centralinfo').style.visibility= 'visible')
	{
		document.getElementById('centralinfo').style.visibility= 'hidden'
	}
	if(document.getElementById('westinfo').style.visibility= 'visible')
	{
		document.getElementById('westinfo').style.visibility= 'hidden'
	}
	document.getElementById('eastinfo').style.visibility= 'visible'
}

function showCentral()
{	
	if(document.getElementById('parkeast').style.backgroundColor= '#000000')
	{
		document.getElementById('parkeast').style.backgroundColor= '#FFFFFF'
		document.getElementById('parkeast').style.color= '#000000'
	}
	if(document.getElementById('parkwest').style.backgroundColor= '#000000')
	{
		document.getElementById('parkwest').style.backgroundColor= '#FFFFFF'
		document.getElementById('parkwest').style.color= '#000000'
	}
	document.getElementById('parkcentral').style.backgroundColor= '#000000'
	document.getElementById('parkcentral').style.color= '#FFFFFF'
	
	if(document.getElementById('east').style.visibility= 'visible')
	{
		document.getElementById('east').style.visibility= 'hidden'
	}	
	if(document.getElementById('west').style.visibility= 'visible')
	{
		document.getElementById('west').style.visibility= 'hidden'
	}
	document.getElementById('central').style.visibility= 'visible'
}

function showCentralContent()
{
	if(document.getElementById('eastinfo').style.visibility= 'visible')
	{
		document.getElementById('eastinfo').style.visibility= 'hidden'
	}
	if(document.getElementById('westinfo').style.visibility= 'visible')
	{
		document.getElementById('westinfo').style.visibility= 'hidden'
	}
	document.getElementById('centralinfo').style.visibility= 'visible'
}

function showWest()
{
	if(document.getElementById('parkeast').style.backgroundColor= '#000000')
	{
		document.getElementById('parkeast').style.backgroundColor= '#FFFFFF'
		document.getElementById('parkeast').style.color= '#000000'
	}
	if(document.getElementById('parkcentral').style.backgroundColor= '#000000')
	{
		document.getElementById('parkcentral').style.backgroundColor= '#FFFFFF'
		document.getElementById('parkcentral').style.color= '#000000'
	}
	document.getElementById('parkwest').style.backgroundColor= '#000000'
	document.getElementById('parkwest').style.color= '#FFFFFF'
	if(document.getElementById('east').style.visibility= 'visible')
	{
		document.getElementById('east').style.visibility= 'hidden'
	}
	if(document.getElementById('central').style.visibility= 'visible')
	{
		document.getElementById('central').style.visibility= 'hidden'
	}
	document.getElementById('west').style.visibility= 'visible'
}

function showWestContent()
{
	if(document.getElementById('eastinfo').style.visibility= 'visible')
	{
		document.getElementById('eastinfo').style.visibility= 'hidden'
	}
	if(document.getElementById('centralinfo').style.visibility= 'visible')
	{
		document.getElementById('centralinfo').style.visibility= 'hidden'
	}
	document.getElementById('westinfo').style.visibility= 'visible'
}

function hideParkContent()
{
	
	document.getElementById('eastinfo').style.visibility= 'hidden'
	document.getElementById('centralinfo').style.visibility= 'hidden'
	document.getElementById('westinfo').style.visibility= 'hidden'
}

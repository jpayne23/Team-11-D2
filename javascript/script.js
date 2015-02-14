var submissions = [];			
//stored in	this variable: all submissions that have been submitted and all submissions that have been added. ie concat of hist+pending
var submissionIDCounter = 0;
var subCounter = 0;
var round = 1;	
var listOfFac = [];

var selectedItems = ['1','2','3','4','5','6','7','8','9','10','11','12'];		// Holds all selectable elements which are already selected

// Any JQUERY
//---------------------
$(document).ready(function()		// Execute all of this on load 
{	
	// Add week selector		
	var numSelecting = 0;		// Keep count of how many elements we have selected during selecting event
	var selectAll = false;
	var removeAll = false;
	
	$("#weekSelector").bind("mousedown", function(e) {				
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
			updateSelectedWeeks(selectedItems);
		},
		
		distance: 1			// This is so we can register normal mouse click events
	});
	
	// Since the distance on the selectable is now greater than zero, clicks do not work if the mouse does not move
	// Simulate mouse click like the selector would normally
	$("#weekSelector li").click(function()
	{
		var clickedText = $(this).text();
		
		// If element is not selected, add it to array and add selected class
		if (selectedItems.indexOf(clickedText) == -1)
		{						
			$(this).addClass('ui-selected');
		}
		else 	// If element is already selected, then remove it from the array and remove selected class
		{
			$(this).removeClass('ui-selected');
		}
		
		// Reset selected items
		selectedItems.length = 0;
		
		// For every selected element, add it to the array
		$(".ui-selected").each(function() 
		{
			selectedItems.push($(this).text());
		});				
		
		updateSelectedWeeks(selectedItems);
	});
	
	// Add park selector
	$.get("php/updatePark.php", function(data)
	{
		$('#parkDiv').html(data);
	});
	
	// Load facilities
	$.get("php/loadFacilities.php", function(data)
	{
		//bring in string and turn into array
		data = data.substr(1,data.length - 2);
		listOfFac = data.split(',');
		//remove quotes from each element of the array
		for(var i = 0; i<13; i++)
		{
			listOfFac[i] = listOfFac[i].substr(1,listOfFac[i].length - 2);
			listOfFac[i] = listOfFac[i].replace(/\\/g, '');
		}
		//$('#facilitiesDiv').html(listOfFac);
	}).done(function(){
		createAutoCompleteFacList();
	});
	
	// Load Parts
	$(function()
	{
		var deptCode = "deptCode=" + document.getElementById("deptCodeDiv").title;
		$.get("php/loadPart.php?" + deptCode, function(data)
		{
			$('#partDiv').html(data);
		});
	});
	
	// Load Module Codes
	$(function()
	{
		var deptCode = "deptCode=" + document.getElementById("deptCodeDiv").title;
		var part = "&part=any";
		$.get("php/loadModCodes.php?" + deptCode + part, function(data)
		{
			$('#modCodeDiv').html(data);
		});
	});
	
	$('#date').datepicker({minDate:0, beforeShowDay: $.datepicker.noWeekends, firstDay: 1});
	
	// Load pending submissions
	$('#pendingButton').click(function()
	{
		reloadPendingTable("down", "RequestID");		
		openDiv("popupPendingDiv");
	});
	
	// Down arrow pending click event
	$('#submissions').on('click', "#downArrow", function() 
	{
		var sortColumn = this.name;
		reloadPendingTable("up", sortColumn);
	});
	
	// Up arrow pending click event
	$('#submissions').on('click', "#upArrow", function() 
	{		
		var sortColumn = this.name;
		reloadPendingTable("down", sortColumn);
	});	
	
	// Edit button click event
	$('#submissions').on('click', "#editIcon", function() 
	{
		closeDiv("popupPendingDiv");
		
		var requestID = "requestID=" + this.name.substr(8);
		
		// Load the form with data from database
		$.get("php/fetchEditData.php?" + requestID, function(data)
		{						
			var requestID = JSON.parse(data)[0];
			var modCode = JSON.parse(data)[1];
			var modtitle = JSON.parse(data)[2];
			var part = JSON.parse(data)[3]
			var sessionType = JSON.parse(data)[4];
			var sessionLength = JSON.parse(data)[5];
			var day = JSON.parse(data)[6];			
			var period = JSON.parse(data)[7];
			var specialReq = JSON.parse(data)[8];
			var priorityReq = JSON.parse(data)[9];
			var weeks = JSON.parse(data)[10];		
			var facilities = JSON.parse(data)[11];
			var rooms = JSON.parse(data)[12];
			var groupSizes = JSON.parse(data)[13];
			
			document.getElementById('part').value = part;
			updateModCode();
			document.getElementById('modCodes').value = modCode + " - " + modtitle;
			document.getElementById('seshType').value = sessionType;
			if (sessionLength == 1)
			{
				document.getElementById('seshLength').value = sessionLength + " Hour";
			}
			else
			{
				document.getElementById('seshLength').value = sessionLength + " Hours";
			}	
			document.getElementById('day').value = day;
			document.getElementById('time').value = period;
			document.getElementById('specialReq').value = specialReq;	

			if (priorityReq == 1)
			{
				document.getElementById("priorityCheckbox").checked = true;
			}
			else
			{
				document.getElementById("priorityCheckbox").checked = false;
			}
			
			setSelectedWeeks(weeks);
			
			if (facilities.length > 0)
			{
				document.getElementById("sortable").innerHTML = "";
				
				for (var i = 0; i < facilities.length; i++)
				{
					setFacilities(facilities[i]);
				}
			}
			else 
			{
				document.getElementById("sortable").innerHTML = "";
			}	
			
			if (rooms.length > 0)
			{
				$('#chosenRooms').attr('data-norooms', '0');
				document.getElementById("chosenRooms").innerHTML = "";
				
				for (var i = 0; i < rooms.length; i++)
				{
					var id = rooms[i];
					var groupSize = groupSizes[i];
					setSelectedRooms(id, groupSize);
				}
			}
			else 
			{
				$('#chosenRooms').attr('data-norooms', '0');
				document.getElementById("chosenRooms").innerHTML = "";
			}	
			
			$('#submit').val("Edit");	
			$('#submit').removeClass("none");
			$('#submit').addClass(requestID);
			$('#submit').addClass("homeButtons");
		});
	});
	
	// Delete button click event on pending
	$('#submissions').on('click', "#deleteIcon", function() 
	{
		var requestID = this.name.substr(10);
		$.post("php/deletePendingRequest.php", 
		{
			requestID: requestID
		},
		function(data)
		{
			reloadPendingTable("down", "RequestID");
		});
	});
	
	// Delete button click event on history
	$('#history').on('click', "#deleteIcon", function() 
	{
		var requestID = this.name.substr(10);
		$.post("php/deletePendingRequest.php", 
		{
			requestID: requestID
		},
		function(data)
		{
			reloadHistoryTable("down", "RequestID");
		});
	});
	
	// Load history page
	$('#historyButton').click(function()
	{
		var sortDirection = "sortDirection=down";
		var sortColumn = "&sortColumn=RequestID";
		var flag = "&flag=0";
		$.get("php/loadHistorySubmissions.php?" + sortDirection + sortColumn + flag, function(data)
		{
			$('#history').html(data);
		});
		
		openDiv("popupHistoryDiv");
	});	
	
	// Down arrow history click event
	$('#history').on('click', "#downArrow", function() 
	{
		var sortColumn = this.name;
		reloadHistoryTable("up", sortColumn);
	});
	
	// Up arrow history click event
	$('#history').on('click', "#upArrow", function() 
	{		
		var sortColumn = this.name;
		reloadHistoryTable("down", sortColumn);
	});
	
	// Load past requets page in adhoc
	$('#pastButton').click(function()
	{
		var sortDirection = "sortDirection=down";
		var sortColumn = "&sortColumn=RequestID";
		var flag = "&flag=0";
		$.get("php/loadAdhocSubmissions.php?" + sortDirection + sortColumn + flag, function(data)
		{
			$('#past').html(data);
		});
		
		openDiv("popupPastDiv");
	});	
	
	//get Facilities of a given room (room1 only)
	$('#btnGetInfo').on('click', function()
	{
		var room = document.getElementById("room1").value;
		var roomNo = "roomNo=" + room;
		if(room == "Any")
			return;
		$.get("php/roomFacility.php?" + roomNo, function(data)
		{
			$("#dialog").html(data);
			document.getElementById("dialog").title = "Room Info ";
			$('#dialog').dialog({
			      show: {
			        effect: "fadeIn",
			        duration: 500
			      }
			}); //end dialog
		}); //end $.get		
	}); //end click function
	
	//Find rooms matching given facilities
	$("#getMatchingRooms").on('click',function(){
		var f = [];
		var valid = false; //to check if any facilities are selected
		/*for(var i = 0;i<45;i=i+2) //+2 because it skips the <br> tags in between
		{
			if($('#facilitiesDiv').children().eq(i).is(':checked')){
				//append the facility name to a array to send to server
				f[f.length]= Number($('#facilitiesDiv').children().eq(i).attr('id').substr(1,2))+1;
				valid = true;
			}
		}*/
		var facDiv = document.getElementById("facilitiesDiv").children;
		for(var i = 2; i <facDiv.length; i++)
		{
			f.push(facDiv[i].getAttribute('name'));
		}
		
		if (f.length != 0)
		{
			valid = true;
		}
		if(valid == false){
			  $('#getMatchingRooms').tooltip({ items: "#getMatchingRooms", content: "You didn't select any facilities"});
			  $('#getMatchingRooms').tooltip("open");
			    $( "#getMatchingRooms" ).mouseout(function(){
			         $('#getMatchingRooms').tooltip("disable");
			    });
			return;
		}
		f = JSON.stringify(f);
		$.get("php/getMatchedRooms.php?f=" + f, function(data)		
		{
			$("#matchedRoomsdiv").html(data);
			$('#matchedRoomsdiv').dialog({
			      show: {
			        effect: "fadeIn",
			        duration: 500
			      }
			}); //end dialog
			
		}); //end $_get
		
	}); //end click function
	
	// Send request to database as pending
	$("#submit").click(function()
	{		
		// Get all values from form
		var modCode = document.getElementById('modCodes').value.substr(0, 8);
		var rooms = getSelectedRooms();
		var groupSizes = getGroupSizes();
		var selectedWeeks = updateSelectedWeeks(selectedItems);
		var facilities = getCheckedFacilities();
		var sessionType = document.getElementById('seshType').value;
		var sessionLength = document.getElementById('seshLength').value.substr(0, 1);
		var specialReq = document.getElementById('specialReq').value;
		var day = document.getElementById('day').selectedIndex + 1;
		var time = document.getElementById('time').selectedIndex + 1;
		var round = document.getElementById('round').getAttribute('name');
		var adhoc = 0;
		var semester = 1;

		if ($("#priorityCheckbox").is(":checked"))
		{
			var priority = 1;
		}
		else 
		{
			var priority = 0;
		}
		
		// Error check
		if (selectedWeeks.length != 0)
		{	
			if ($('#submit').hasClass('none'))
			{
				$.post("php/addPendingRequest.php",
				{	
					// Data to send
					modCode: modCode,
					rooms: rooms,
					groupSizes: groupSizes,
					selectedWeeks: selectedWeeks,
					facilities: facilities,
					sessionType: sessionType,
					sessionLength: sessionLength,
					specialReq: specialReq,
					day: day,
					time: time,
					round: round,
					priority: priority,
					adhoc: adhoc,
					semester: semester
				},
				function(data, status){
					// Function to do things with the data
					alert(data);
				});
			}
			else
			{
				var requestID = $('#submit').attr('class');
				$.post("php/editPendingRequest.php",
				{	
					// Data to send
					requestID: requestID,
					modCode: modCode,
					rooms: rooms,
					groupSizes: groupSizes,
					selectedWeeks: selectedWeeks,
					facilities: facilities,
					sessionType: sessionType,
					sessionLength: sessionLength,
					specialReq: specialReq,
					day: day,
					time: time,
					priority: priority
				},
				function(data, status){
					// Function to do things with the data
					alert(data);
				});
				
				$('#submit').val("Submit");	
				$('#submit').removeClass();
				$('#submit').addClass('none');
				$('#submit').addClass("homeButtons");
			}
		}
		else
		{
			alert("Please enter what weeks you want to book the module for");
		}
	});
	
	// Send adhoc request to database
	$("#submitAdhoc").click(function()
	{		
		// Get all values from form
		var modCode = document.getElementById('modCodes').value.substr(0, 8);
		var rooms = getSelectedRooms();
		var groupSizes = getGroupSizes();
		var selectedWeeks = updateSelectedWeeks(selectedItems);
		var facilities = getCheckedFacilities();
		var sessionType = document.getElementById('seshType').value;
		var sessionLength = document.getElementById('seshLength').value.substr(0, 1);
		var specialReq = document.getElementById('specialReq').value;
		var day = document.getElementById('day').selectedIndex + 1;
		var time = document.getElementById('time').selectedIndex + 1;
		var semester = document.getElementById('semester').selectedIndex+1;
		var round = 0;
		var adhoc = 1;
		var priority = 1;
		
		// Error check
		if (selectedWeeks.length != 0)
		{	
			$.post("php/addPendingRequest.php",
			{	
				// Data to send
				modCode: modCode,
				rooms: rooms,
				groupSizes: groupSizes,
				selectedWeeks: selectedWeeks,
				facilities: facilities,
				sessionType: sessionType,
				sessionLength: sessionLength,
				specialReq: specialReq,
				day: day,
				time: time,
				round: round,
				priority: priority,
				adhoc: adhoc,
				semester: semester
			},
			function(data, status){
				// Function to do things with the data
				alert(data);
			});
		}
		else
		{
			alert("Please enter what weeks you want to book the module for");
		}
	});
	
	// Make all pending requests submitted ones
	$("#submitRequests").click(function()
	{
		$.post("php/addSubmittedRequest.php");
		
		// Reload pending submissions
		reloadPendingTable("down", "RequestID");	
	});
	
	$('#reset').click(function() //reset all default values
	{
		$("select#part")[0].selectedIndex = 1;
		updateModCode();
		$("#modCodes")[0].selectedIndex = 0;
		$("select#seshType")[0].selectedIndex = 0;
		$("select#seshLength")[0].selectedIndex = 0;
		$("select#day")[0].selectedIndex = 0;
		$("select#time")[0].selectedIndex = 0;
		$("#specialReq").val("");
		$("#chosenRooms").html("");
		document.getElementById("priorityCheckbox").checked = true;
		document.getElementById("sortable").innerHTML = "";
		// Set the week selector to all the correct highlighted values
		selectedItems = ['1','2','3','4','5','6','7','8','9','10','11','12'];
		
		$("#weekSelector").children().each(function()
		{
			for (var k = 0; k < selectedItems.length; k++)
			{
				if (this.innerHTML == selectedItems[k])
				{
					$(this).addClass('ui-selected');
					break;				// Exit current loop cycle once match is found
				}
				else
				{
					$(this).removeClass('ui-selected');
				}
			}
		});		
	});
	
	//Open Advanced Requests
	$('#btnAdvancedRequest').click(function()
	{
		$.get("php/loadPopupRequest.php", function(data)
		{
			$('#popupRequestDiv').html(data)
		}).done(function()
		{
			addTitles();
			openDiv("popupRequestDiv");
			loadGroupSize();
			populateTimetable(); //function to add the tiles to the timetable
			
			//These functions load and update the day and time chosen between the main page and the popup room.
			$('#popupDay').val($('#day').val());
			$('#popupDay').change(function(){
				var day = $('#popupDay').val();
				$('#day').val(day);
			});
			$('#popupTime').val($('#time').val());
			$('#popupTime').change(function(){
				var day = $('#popupTime').val();
				$('#time').val(day);
			});	
			updateAdvancedBuilding("parkeast");
		});
		openDiv("popupRequestDiv");
	});

	populateTimetable();
	
	//These functions load and update the day and time chosen between the main page and the popup room.
	$('#popupDay').val($('#day').val());
	$('#popupTime').val($('#time').val());
	$('#popupDay').change(function(){
		var day = $('#popupDay').val();
		$('#day').val(day);
	
	});
	$('#popupTime').change(function(){
		var day = $('#popupTime').val();
		$('#time').val(day);
	
	});
	
	$('#round').click(function() //reset all default values
	{
		alert("pending submissions might be lost");
		$.post("php/simulateRound.php");
		var newRound = parseInt(this.getAttribute('name'))+1;
		this.name = newRound;
		document.getElementById('genInfo').innerHTML = 'General Information - Round ' + newRound;
		$("#priorityCheckbox").removeAttr("disabled");
	});
	
	$(function()
	{
		$.get("php/loadRound.php", function(data)
		{
			document.getElementById('round').name = data;
			document.getElementById('genInfo').innerHTML = 'General Information - Round ' + data;
			
			// If the round is higher than 1, let the priority checkbox be clickable
			if (data > 1)
			{
				$("#priorityCheckbox").removeAttr("disabled");
			}
		});
	});
	
	$('#submissions').on('click', '#pendingRow', function() //show more info if row clicked
	{ //need to add pointer css for pendingRow 
		var requestID = this.getAttribute('name');
		$.post("php/getRequestInfo.php", 
		{
			requestID: requestID
		},
		function(data)
		{
			alert(data);
		});
	});	
	
	$('#history').on('click', '#historyRow', function() //show more info if row clicked
	{ //need to add pointer css for pendingRow 
		var requestID = this.getAttribute('name');
		$.post("php/getRequestInfo.php", 
		{
			requestID: requestID
		},
		function(data)
		{
			alert(data);
		});
	});		

	// Load last year's requests
	$('#lastYearButton').click(function()
	{
		$.get("php/loadLastYear.php", function(data)
		{
			reloadLastYearTable("down", "RequestID");
		});
		
		openDiv("popupLastYear");
	});
	
	// Down arrow last years click event
	$('#lastYear').on('click', "#downArrow", function() 
	{
		var sortColumn = this.name;
		reloadLastYearTable("up", sortColumn);
	});
	
	// Up arrow last years click event
	$('#lastYear').on('click', "#upArrow", function() 
	{		
		var sortColumn = this.name;
		reloadLastYearTable("down", sortColumn);
	});	
	
	// Last years master checkbox click event
	$('#lastYear').on('click', "#requestCheckboxMaster", function()
	{
		if (document.getElementById('requestCheckboxMaster').checked == true)
		{
			var checkboxes = $('#lastYearTable').find(':checkbox');
			for (var i = 1; i < checkboxes.length; i++)
			{
				checkboxes[i].checked = true;
			}
		}
		else
		{
			var checkboxes = $('#lastYearTable').find(':checkbox');
			for (var i = 1; i < checkboxes.length; i++)
			{
				checkboxes[i].checked = false;
			}
		}
	});
	
	// Submit all checked requests
	$('#submitCheckedButton').click(function()
	{
		// Get all ID's of all checked boxes
		var checked = $('#lastYearTable').find(':checked');
		var requestIDHist = [];
		var round = document.getElementById('round').name
		
		if (document.getElementById('requestCheckboxMaster').checked == true)
		{			
			for (var i = 1; i < checked.length; i++)
			{
				requestIDHist.push(checked[i].id.substr(15));
			}
		}
		else
		{
			for (var i = 0; i < checked.length; i++)
			{
				requestIDHist.push(checked[i].id.substr(15));
			}
		}
		console.log(requestIDHist);
		// Error check
		if (requestIDHist.length != 0)
		{	
			$.post("php/addCheckedRequests.php",
			{	
				// Data to send
				requestIDHist: requestIDHist,
				round: round
			},
			function(data, status){
				// Function to do things with the data
				console.log(data);
			});
		}
		else
		{
			alert("No requests selected");
		}
	});
});

function resetSelectedRooms()
{
	document.getElementById('chosenRooms').innerHTML = "";
	$("#chosenRooms").attr('data-norooms', '0');
}

function getSelectedRooms()
{
	var rooms = [];
	var numRooms = $('#chosenRooms').children().children().length;
	
	$('#chosenRooms tr').each(function()
	{
		rooms.push($(this).find("td").eq(2).html());
	});	
	
	if (rooms.length == 0)
	{
		return "null";	// Sends an null string to the php file so as not to cause an error
						// Instead of sending an empty array
	}
	else
	{
		return rooms;
	}
}

function getGroupSizes()
{
	var groupSizes = [];
	var numGroups = $('#chosenRooms').children().children().length;
	
	$('#chosenRooms tr').each(function()
	{
		groupSizes.push($(this).find("td").eq(0).html());
	});	
	
	if (groupSizes.length == 0)
	{
		return "null";	// Sends an null string to the php file so as not to cause an error
						// Instead of sending an empty array
	}
	else
	{
		return groupSizes;
	}
}

function setSelectedRooms(id, groupSize)
{
	newid = id.replace(/\./g, '');
	var x = $('#chosenRooms').attr('data-norooms'); //get the no of rooms added already
	x = parseInt(x); 
	x++;
	var xStr = x.toString();
	var cap = groupSize;
	$('#chosenRooms').attr('data-norooms',''+xStr+''); //change the no of rooms added
	var html= "<tr id="+("rm" + newid)+"><td>"+groupSize+"</td><td> Students in room </td><td>"+id+"</td><td id='del"+ ("rm" + newid) +"' onclick='deleteRoom(this.id);'><img src='img/delete.png' height='15' width='15'><td></tr>";
	document.getElementById('chosenRooms').innerHTML += html;
}
//functions for advanced request-------------------------------------

function populateTimetable()
{
	var id;
	for(var d = 1; d<6; d++)
	{
		for(var p = 10; p > 1; p--) //go backwards as we are inserting the tiles in reverse order
		{
			id = "d" + d + "p" + p;
			$('#d'+d+'').after("<td class= 'test' id="+id+"> Content </td>");
		}
	}
}
function updateAdvancedBuilding(value)
{
	var string = value.substr(4, 1).toUpperCase();
	//string = string.toUpperCase();
	//alert(string);

	$.get("php/updateAdvancedBuilding.php?" + 'park=' + string, function(data)
	{
		//alert(data);
		//data = "'" + data + "'";
		//content = '<a id="prev" title="Previous Slide">Previous Slide</a><a id="next" title="Next Slide">Next Slide</a>'
		//alert(content);
		$("#parkcontent").append(data);
	});
}

function clearRoomContent()
{
	$('#roominfo').html("");
}

function openPopupRequestDiv()
{
	document.getElementById('popupRequestDiv').style.visibility = 'visible';
}

//--------------------------------------------------------------------------	

function reloadPendingTable(sortDirection, sortColumn)
{
	// Reload table based on sort direction and column
	sortDirection = "sortDirection=" + sortDirection;
	sortColumn = "&sortColumn=" + sortColumn;
	var flag = "&flag=0";
	$.get("php/loadPendingSubmissions.php?" + sortDirection + flag + sortColumn, function(data)
	{
		$('#submissions').html(data);
	});
}

function reloadHistoryTable(sortDirection, sortColumn)
{
	// Reload table based on sort direction and column
	sortDirection = "sortDirection=" + sortDirection;
	sortColumn = "&sortColumn=" + sortColumn;
	var flag = "&flag=0";
	$.get("php/loadHistorySubmissions.php?" + sortDirection + flag + sortColumn, function(data)
	{
		$('#history').html(data);
	});
}

function reloadLastYearTable(sortDirection, sortColumn)
{
	// Reload table based on sort direction and column
	sortDirection = "sortDirection=" + sortDirection;
	sortColumn = "&sortColumn=" + sortColumn;
	var flag = "&flag=0";
	$.get("php/loadLastYear.php?" + sortDirection + flag + sortColumn, function(data)
	{
		$('#lastYear').html(data);
	});
}

function createAutoCompleteFacList()
{
	//Create dropdown list for search suggestions
	html = "<div class='ui-widget'>";
	html+= "<select id='comboFac'>";
	
	for(var i = 0; i < listOfFac.length;i++)
	{
		html+= "<option>" + listOfFac[i] + "</option>";
	}
	html+= "</select></div>";
	$('#facilitiesDiv').html(html);
	
	html= "<table id='sortable'></table>";
	$("#facilitiesDiv").append(html);
	
	//
  (function( $ ) { //This function has been taken off the Jquery website and tweaked for our use
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
		this._createAddButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
		  .attr("id","facDropDown")
          //.addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() { //create the show all drop down button
        var input = this.input,
          wasOpen = false;
 
        
		$('<button>Show All Facilities</button>')
		.attr( "tabIndex", -1 )
			.attr("id",'btnShowAllItems')
			.attr('class','homeButtons')
			.tooltip()
			.appendTo( this.wrapper )


		//$('#facDropDown').after(btn); //append this button to the screen
		
		
		.mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
		

		
		/*
		$( "<a>" )
          
          .button({

           // icons: {
             // primary: "ui-icon-triangle-1-s"
            //},
            text: true
          })
          .removeClass( "ui-corner-all" )
         // .addClass( "custom-combobox-toggle ui-corner-right" )
         // .mousedown(function() {
           // wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          //})*/
      },
	  
	  //Created my own function to add a 'add' button to add the chosen facility to the list
	  _createAddButton: function(){
		var btn = $('<button class="homeButtons">Add</button>').click(function () 
		{
			var fac = $('#facDropDown').val(); //get the value of the selected facility
			if (fac != "")
				addFacToList(fac); //call function to add the facility to a list underneath
		});

		$('#btnShowAllItems').after(btn); //append this button to the screen
		
	  },
 
      _source: function( request, response ) { //this function is for matching the user's entry with the results using RegExp
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) ){
			return {
              label: text,
              value: text,
              option: this
            };
		  }
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
		
	

        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
		
 		
		

		
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );
 
  $(function() { //functions to create the elements onto the screen
    $( "#comboFac" ).combobox();
    $( "#toggle" ).click(function() {
      $( "#combobox" ).toggle();
    });
  });
  
  function addFacToList(fac){ //add selected facility to list
		var facid = fac.replace(/ /g,''); //remove spaces so ID works
		//check facilitiy isnt already on the list
		facid = facid.replace(/\//g, ''); //remove forward slash so ID works
		if($("#" + facid).length > 0) {
			//it doesn't exist
			alert("already exists");
			return;
		}
		//create the elements and append them to the list, adding the appropriate information
		html = "<tr id='"+facid+"' name='"+fac+"'><td>" + fac + "</td><td id='del"+facid+"' onclick='deleteFac(this.id);'><img src='img/delete.png' height='15' width='15'></td></tr>";
		document.getElementById("sortable").innerHTML += html;
	}

}	

// Same as addFacToList but slightly different as it's used for editing so it's in scope
function setFacilities(fac)
{
	var facid = fac.replace(/ /g,''); //remove spaces so ID works
	//check facilitiy isnt already on the list
	facid = facid.replace(/\//g, ''); //remove forward slash so ID works
	if($("#" + facid).length > 0) {
		//it doesn't exist
		alert("already exists");
		return;
	}
	//create the elements and append them to the list, adding the appropriate information
	html = "<tr id='"+facid+"' name='"+fac+"'><td>" + fac + "</td><td id='del"+facid+"' onclick='deleteFac(this.id);'><img src='img/delete.png' height='15' width='15'></td></tr>";
	document.getElementById("sortable").innerHTML += html;
}

function deleteFac(id) //function to delete the facility by searching for its id
{
	id = id.substr(3,id.length);
	$( '#'+id ).remove();
}

function filterMenu(source)
{
	if(source == "Pending"){
		$.get("php/loadFilter.php?source=Pending", function(data)
		{
			$('#filterDiv').html(data);
		});
	}
	else{
		$.get("php/loadFilter.php?source=History", function(data)
		{
			$('#filterDivHist').html(data);
		});
	}
};

function filterTable(source)
{
	
	var modCode = "modCode=" + document.getElementById("modCodesFilter")[document.getElementById("modCodesFilter").selectedIndex].id;
	var sessionType = "&sessionType=" + document.getElementById("sessionTypeFilter")[document.getElementById("sessionTypeFilter").selectedIndex].id;
	var flag = "&flag=1";
	var sortDirection = "&sortDirection=down";
	var sortColumn = "&sortColumn=RequestID";
	var day = "&day=" + document.getElementById("dayFilter")[document.getElementById("dayFilter").selectedIndex].id;
	var facility = "&facility=" + document.getElementById("facilitiesFilter")[document.getElementById("facilitiesFilter").selectedIndex].id;
	
	if(source == "History")
	{
		var status = "&status=" + document.getElementById("statusFilter")[document.getElementById("statusFilter").selectedIndex].id;
		$.get("php/loadHistorySubmissions.php?" + modCode + sessionType + facility + day + status + sortDirection + sortColumn + flag, function(data)
		{
			$('#history').html(data);
		});
		closeDiv('filterDivHist');
	}
	else{
		$.get("php/loadPendingSubmissions.php?" + modCode + sessionType + facility + day + sortDirection + sortColumn + flag, function(data)
		{
			$('#submissions').html(data);
		});
		closeDiv('filterDiv');
	}
}


function getCheckedFacilities()
{
	var checkFacilities = [];
	var facDiv = $('#sortable').children().children();
	for(var i = 0; i <facDiv.length; i++)
	{
		checkFacilities.push(facDiv[i].getAttribute('name'));
	}
	
	if (checkFacilities.length == 0)
	{
		return "null";	// Sends an null string to the php file so as not to cause an error
						// Instead of sending an empty array
	}
	else
	{
		return checkFacilities;
	}
}

function updateModCode()
{
	var deptCode = "deptCode=" + document.getElementById("deptCodeDiv").title;
	var part = "&part=" + document.getElementById("part")[document.getElementById("part").selectedIndex].id;
	$.get("php/loadModCodes.php?" + deptCode + part, function(data)
	{
		$('#modCodeDiv').html(data);
	});
}

function updateBuilding()
{
	var string = $("#park1").serialize();
	$.get("php/updateBuilding.php?" + string, function(data)
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
	
	$.get("php/updateRoom.php?" + string, function(data)
	{
		$('#roomDiv').html(data);
	});	
}		

function updateSelectedWeeks(selectedItems)
{		
	var length = selectedItems.length;
	var output = [];
	var i, j;
	
	for (i = 0; i < length; i = j + 1)
	{
		// Beginning of range or single
		output.push(selectedItems[i]);
		
		// Find end of range
		for (var j = i + 1; j < length && parseInt(selectedItems[j]) == parseInt(selectedItems[j-1]) + 1; j++);
		j--;
		
		if (i == j) 
		{
			// single number
			output.push(",");
		} 
		else 
		{
			if (i == j)
			{
				// two numbers
				output.push(",", selectedItems[j], ",");
			}
			else 
			{ 
				// range
				output.push("-", selectedItems[j], ",");
			}		
		} 		
	}
	
	output.pop(); // remove trailing comma
	return output.join("");
}

function setSelectedWeeks(weeksArray)
{
	var output = [];
	
	for (var i = 0; i < weeksArray.length; i++)
	{
		var dashPos = weeksArray[i].indexOf("-");
		
		if (dashPos > 0)
		{
			var leftSide = weeksArray[i].substring(0, dashPos);
			var rightSide = weeksArray[i].substring(dashPos + 1, weeksArray[i].length);
			
			for (var j = parseInt(leftSide); j <= parseInt(rightSide); j++)
			{
				output.push(String(j));
			}
		}
		else
		{
			output.push(String(weeksArray[i]));
		}			
	}
	
	// Set the week selector to all the correct highlighted values
	selectedItems = output;
	
	$("#weekSelector").children().each(function()
	{
		for (var k = 0; k < output.length; k++)
		{
			if (this.innerHTML == output[k])
			{
				$(this).addClass('ui-selected');
				break;				// Exit current loop cycle once match is found
			}
			else
			{
				$(this).removeClass('ui-selected');
			}
		}
	});		
}

function openDiv(id)
{
	document.getElementById(id).style.visibility = 'visible';
	$('#'+id).fadeIn();
}

function closeDiv(id)
{
	//document.getElementById(id).style.visibility = 'hidden';
	$('#'+id).fadeOut();
}

function resizeText(multiplier) 
{
	if (document.body.style.fontSize == "") 
	{
		document.body.style.fontSize = "1.0em";
	}
	document.body.style.fontSize = parseFloat(document.body.style.fontSize) + (multiplier * 0.2) + "em";
}
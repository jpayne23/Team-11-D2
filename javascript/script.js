var submissions = [];			
//stored in	this variable: all submissions that have been submitted and all submissions that have been added. ie concat of hist+pending
var submissionIDCounter = 0;
var subCounter = 0;
var round = 1;	
var numRooms = 1;
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
	
	// Down arrow click event
	$('#submissions').on('click', "#downArrow", function() 
	{
		var sortColumn = this.name;
		reloadPendingTable("up", sortColumn);
	});
	
	// Up arrow click event
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
			var weeks = JSON.parse(data)[9];			
			
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
			setSelectedWeeks(weeks);
			
			$('#submit').val("Edit");	
			$('#submit').removeClass("none");
			$('#submit').addClass(requestID);
		});
	});
	
	// Delete button click event
	$('#submissions').on('click', "#deleteIcon", function() 
	{
		$.post("php/deletePendingRequest.php", 
		{
			
		},
		function(data)
		{
			
		});
	});
	
	// Load history page
	$('#historyButton').click(function()
	{
		$.get("php/loadHistorySubmissions.php", function(data)
		{
			$('#history').html(data);
		});
		
		openDiv("popupHistoryDiv");
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
		var selectedWeeks = updateSelectedWeeks(selectedItems);
		//var facilities = getCheckedFacilities();
		var sessionType = document.getElementById('seshType').value;
		var sessionLength = document.getElementById('seshLength').value.substr(0, 1);
		var specialReq = document.getElementById('specialReq').value;
		var day = document.getElementById('day').selectedIndex + 1;
		var time = document.getElementById('time').selectedIndex + 1;
		
		// Error check
		if (selectedWeeks.length != 0)
		{	
			if ($('#submit').hasClass('none'))
			{
				$.post("php/addPendingRequest.php",
				{	
					// Data to send
					modCode: modCode,
					selectedWeeks: selectedWeeks,
					//facilities: facilities,
					sessionType: sessionType,
					sessionLength: sessionLength,
					specialReq: specialReq,
					day: day,
					time: time
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
					selectedWeeks: selectedWeeks,
					sessionType: sessionType,
					sessionLength: sessionLength,
					specialReq: specialReq
				},
				function(data, status){
					// Function to do things with the data
					alert(data);
				});
				
				$('#submit').val("Submit");	
				$('#submit').removeClass();
				$('#submit').addClass('none');
			}
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
		
	});
	
});

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
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
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
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
		  .attr("id",'btnShowAllItems')
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
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
      },
	  
	  //Created my own function to add a 'add' button to add the chosen facility to the list
	  _createAddButton: function(){
		var btn = $('<button>Add</button>').click(function () 
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
			$( "#sortable" ).after(html);
		}

}	

function deleteFac(id) //function to delete the facility by searching for its id
{
	id = id.substr(3,id.length);
	$( '#'+id ).remove();
}

function filterMenu()
{
	$.get("php/loadFilter.php?", function(data)
	{
		$('#filterDiv').html(data);
	});
};

function filterTable()
{
	var modCode = "modCode=" + document.getElementById("modCodesFilter")[document.getElementById("modCodesFilter").selectedIndex].id;
	var sessionType = "&sessionType=" + document.getElementById("sessionTypeFilter")[document.getElementById("sessionTypeFilter").selectedIndex].id;
	var flag = "&flag=1";
	var sortDirection = "&sortDirection=down"
	$.get("php/loadPendingSubmissions.php?" + modCode + sessionType + sortDirection + flag, function(data)
	{
		$('#submissions').html(data);
	});
	
	closeDiv('filterDiv');
}


function getCheckedFacilities()
{
	var checkFacilities = [];
	for (var i = 0; i < 13; i++)
	{
		if (document.getElementById('c' + i).checked)
		{
			checkFacilities.push(document.getElementById('c' + i).value);
		}
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
			
			for (var j = leftSide; j <= rightSide; j++)
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
}

function closeDiv(id)
{
	document.getElementById(id).style.visibility = 'hidden';
}

function resizeText(multiplier) 
{
	if (document.body.style.fontSize == "") 
	{
		document.body.style.fontSize = "1.0em";
	}
	document.body.style.fontSize = parseFloat(document.body.style.fontSize) + (multiplier * 0.2) + "em";
}
/*this may be useful for multirooms
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
*/
/*
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
}	*/

/*
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
*/
// Open and close popups for divs

/*function newPopup(url, winName, w, h, scroll) 
{
	var LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
	var TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
	var settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable';
	var popupWindow = window.open(url, winName, settings)
			
}*/
/*
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
}*/
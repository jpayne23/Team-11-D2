var times;
var newav = [];
$(document).ready(function()		// Execute all of this on load 
{
	
}); //end document ready

function loadGroupSize()//load the group size based on the module
{
	x = $('#chosenRooms').attr('data-norooms');
	if (x == "0")
 	{
 		var modCode = "modCode=" + $('#modCodes').val().substr(0,8);
 		$.get("php/loadGroupSize.php?" + modCode, function(data)
 		{
 			$('#maxGroupSize').val(data);
 			document.getElementById("groupSize").max = data;
			document.getElementById("groupSizeVal").value = data;
 			document.getElementById("groupSize").min = 1;
 			$('#groupSize').val(data);
 		});
 	}
	else
 	{
 		$('#maxGroupSize').val($('#chosenRooms').attr('data-maxcap'));
 		$('#groupSize').val($('#chosenRooms').attr('data-maxcap'));
 		document.getElementById("groupSize").max = $('#chosenRooms').attr('data-maxcap');
		document.getElementById("groupSizeVal").value = $('#chosenRooms').attr('data-maxcap');
 		document.getElementById("groupSize").min = 1;
		var rooms = getSelectedRooms();
		var groupSizes = getGroupSizes();
		var html= "";
		for (var i = 0; i < rooms.length; i++)
		{
			html += "<tr id="+("rm" + rooms[i].replace(/\./g, ''))+"><td>"+groupSizes[i]+"</td><td> Students in room </td><td>"+rooms[i]+"</td><td id='del"+ ("rm" + rooms[i].replace(/\./g, '')) +"' onclick='deleteRoom(this.id);'><img src='img/delete.png' height='15' width='15'><td></tr>";
		}
		clearBuildingContent();
		$("#buildingcontent").append(html);
	}
}

function populateTimetable() //function to populate the timeslots of the timetable
{
	var id;
	for(var d = 1; d<6; d++)
	{
		for(var p = 10; p > 1; p--) //go backwards as we are inserting the tiles in reverse order
		{
			id = "d" + d + "p" + (p-1);
			$('#d'+d+'').after("<td class= 'timeslot' data-selected='0' data-available='' data-display='[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]' id="+id+">Fully Available </td>");
		}
	}
}

function addTitles() //function to refresh the main contents, and re add the titles
{
	$("#parkcontent").html('<b><a class= "buildingcontenttitle">  Buildings </a></b><a> </br> </a> ');
	$("#buildingcontent").html('<b><a class= "roomcontenttitle"> Rooms </a></b><a> </br> </a> ');
	
	if($('#selectedrooms').children().length < 3) //if a room isnt in the list
		$("#selectedrooms").html('<b><a class= "selectedcontenttitle"> Rooms Selected </a></b><a> </br> </a> ');
	
	if ($('#comparedtable').children().children().length == 0) //if the chosen room list is empty
		$("#compared").html('<b><a class= "selectedcontenttitle"> Rooms Compared </a></b></br><table id="comparedtable"></table> ');
}

function addRoomTitle()
{
	$("#buildingcontent").html('<b><a class= "roomcontenttitle"> Rooms </a></b><a> </br> </a> ');
}

function clearParkContent(){ //Clear the buildings listed
	$("#parkcontent").html("");
}

function clearBuildingContent(){ //clear the rooms listed
	$("#buildingcontent").html("");
}

function clearRoomContent() //Clears the room info pop up
{
	$('#roominfo').html("");
}

function updateAdvancedBuilding(value) //function to display the list of buildings from the user park choice
{
	var string = value.substr(4, 1).toUpperCase();
	var length;
	var lastRow;
	
	//change background colours of sections
	$('#parkeast').css("background-color", "#CC0066");
	$('#parkcentral').css("background-color", "#CC0066");
	$('#parkwest').css("background-color", "#CC0066");
	$('#'+value).css("background-color", "#330066");
	
	$.get("php/updateAdvancedBuilding.php?" + 'park=' + string, function(data)
	{
		addTitles();
		$("#parkcontent").append(data);
		var buildingName =  value.substr(4,1).toUpperCase() + value.substring(5,value.length);
		//add the option of any building
		$('#parkcontent').children().eq(1).after('<table class= "anycontenttable"> <tr id="choice'+value+'" class="anycontentrows" onclick="addAnyPark(this.id); clearBuildingContent(); addRoomTitle()"><td>Any '+buildingName+' Building</td></tr><tr><td></br></td></tr></table>');
		length = $('#parkcontent').children().length -1;
		lastRow = $('#parkcontent').children().eq(length);
		lastRow.attr('style', 'border-bottom: 0'); //add a bottom border to the last row
	});
}
function changeSelected() //function to change the clicked row to a selected colour scheme
{
   $('#parkcontent').on('click', 'tr', function()
   {
	   //Change the background colors of the selected row in the table (i.e. showing selected)
		$('.anycontenttable tr').css('background-color', '#330066');
		$('.contenttable tr').css('background-color', '#330066');
		$(this).css('background-color',"#CC0066");
   });
}
    
function updateAdvancedRoomFacility(value) //function to display the information of a room
	{	var str = value.replace(/\./g, '');
		$("#timetable").css("opacity", "1");// make the timetable visible
		//get the information about chosen room, and show as popup
		$.get("php/updateAdvancedRoomFacility.php?" + 'roomNo=' + value, function(data)
		{
			//alert(data);

			data+= "<input class='homeButtons' type='button' id='"+value+"' value='Select Room' onclick='addRoomToList(this.id);'>";
			
			//find the id of the 'Select Room' button and add a 'compare' button
			var x = $('#comparedtable').find('#com'+ str);
			if(x.length == 0){
				data+= "<input class='homeButtons' type='button' id='com"+value+"' value='Add to Compare List' onclick='addRoomToCompareList(this.id);'>";

			}

			$("#roominfo").html(data); //Add the html data to the popup
			document.getElementById("roominfo").title = value;
			$('#roominfo').dialog({ //display the popup
				dialogClass:"dialogClass",
				  show: {
					effect: "fadeIn",
					duration: 500
				  }
			}).prev(".ui-dialog-titlebar").css("background", "#CC0066"); //end dialog
		}).done(function(){
			//check if the room has already been added to the compare table
			
			
			
			
		
			
			
		}); //end $.get
	}
	
	
	
function fillTimetable(value)
	{
		
		//Fill in the timetable
		$.get("php/loadTimesOfRoom.php?" + 'roomNo=' + value, function(data)
		{
			var id, length, j, str;
	
	
			times = JSON.parse(data); //JSON.parse turns php string into javascript object	
			
			for(var i =0; i < times.length;i++) //loop through each timeslot and add it to the timetable
			{
				j = 1;
				//bring in array data from php to each timeslot attribute
				id = times[i]['id'];
				length = times[i]['data-length'];
				var weeks = times[i]['data-weeks'];
				$('#'+id).attr('id',id);
				$('#'+id).attr('data-length',length);
				$('#'+id).attr('data-selected','1');
				$('#'+id).attr('data-weeks', weeks);
				$('#'+id).attr('class','timeslotbooked');
				$('#'+id).html('Partly Available');
				displayAvailableWeeks(id, value);
				while(j < (length))
				{
					//Loop through the length of a session and change the timeslot attributes
					str = $('#'+id).attr('id').substr(3,1);
					var n = parseInt(str);
					n = n + j;
					var newid = $('#'+id).attr('id').substr(0,3) + n;

					$('#'+newid).attr('data-length',length);
					$('#'+newid).attr('data-selected','1');
					$('#'+newid).attr('data-weeks', weeks);
					$('#'+newid).attr('class','timeslotbooked');
					$('#'+newid).html('Partly Available');
					displayAvailableWeeks(newid, value); //function to show the user the weeks this timeslot is available
					j++;
				}
			}
		}); // end $.get
		
}

function displayAvailableWeeks(id, value) //function to show the user the weeks this timeslot is available
{
		var strweeks = $('#'+id).attr("data-weeks");
		var av = []; //array of available weeks
		var weeks = [] //array of chosen weeks
		var temp = [];
		var len, match, w;
		var str = "";
	
		weeks = strweeks.split(":"); //turn the weeks into an array to loop through
		
		for(var i = 0;i<weeks.length;i++){ //loop through each set of weeks chosen
			
			len = weeks[i].length;
			switch(len){ //check the length of the week string
				case 5: //i.e.[1,2]
					//append to a string the list of booked weeks
					str += returnBookedWeeks(weeks[i].substr(1,1), weeks[i].substr(3,1));
					break;
				case 6://i.e.[1,12] or [12,1]
					if(weeks[i].substr(2,1) == ","){
						//append to a string the list of booked weeks
						str += returnBookedWeeks(weeks[i].substr(1,1), weeks[i].substr(3,2));
					} else{
						//append to a string the list of booked weeks
						str += returnBookedWeeks(weeks[i].substr(1,2), weeks[i].substr(4,1));
					}
					break;
				case 7: //i.e.[11,12]
				//append to a string the list of booked weeks
					str += returnBookedWeeks(weeks[i].substr(1,2), weeks[i].substr(4,2));
					break;
			} //end switch
		}
		
		temp = str.split(","); //temp is a array of booked weeks
		
		match = false;
		avweeks(id, temp); //output the available weeks
}

function avweeks(id, chosen) //function to change the available weeks of a time slot
{
	var match, str;
	var av = [];
	var str = $('#'+id).attr('data-display'); //get currently available weeks for this slot
	var display = str.substr(1,str.length-2);
	display = display.split(",");	//turn attribute into array
	
	
	var str = "";	
	//get the available weeks from an array of chosen/booked weeks
	var availWeeks = getAvailableWeeksAsArray(chosen);


	av = [];
	/*Match the currently displayed available weeks and intersect it with the available weeks
	/and output the new array of available weeks for the rooms chosen */
	for(var i = 0;i<display.length;i++)
	{
		match = false;
		for(var j=0;j<availWeeks.length;j++)
		{
			if(display[i] == availWeeks[j])
				match = true; //there is a available room that is also displayed available
		}
		if(match == true) //write the commonly available room into a new array
			av[av.length] = display[i];
	}
	var s = "";
	//turn array into a concatenated strings
	for(var i =0;i<av.length;i++)
	{
		
		s+= av[i] + ",";
	}
	
	//Assign the attributes and hover function
	$('#' + id).attr('data-display',s);
	
	$('#'+id).hover(function(){// make hover function to show the available weeks
		var str = $('#'+id).attr("data-display");
		$('#'+id).html(s);
	}, function(){
		$('#'+id).html("Partly Available");
	});
		

}

function getAvailableWeeksAsArray(chosen, id) //take a array of chosen weeks and output the array of available weeks
{
	var match;
	var av=[];
	var s="";
	for(i=0;i<16;i++)
	{
		match = false;
		for(j=0;j<chosen.length;j++)
		{
			if(i == chosen[j])
				match = true;
		}
		if(match == false){ //if a match hasnt been found i.e. the week is available
			av.push(i);
			s+= i + ","; //used for testing purposes
		}
	}
	
	return av;
}

function returnBookedWeeks(start, end) //takes two numbers and return the inclusive string of numbers
{
	var str = "";
	var intstart = parseInt(start); //convert given numbers into integers
	var intend = parseInt(end);
	
	for(var i = intstart; i <= intend; i++){
		str += i + ",";
	}
	return str;
}

function addRoomToCompareList(id) //function to add the chosen room to a list
{
	var newid = id.replace(/\./g, ''); //remove the slashes and dots so ID works
	var name = id.substr(3,id.length)
	$('#comparedtable').append('<tr id='+newid+' value='+name+'><td>'+name+ '</td><td id=del'+newid+' onclick="deleteRoomFromCompareList(this.id);"><img src="img/delete.png" height="15" width="15"> </td></tr>');
	recreateTimetable();
	$('#roominfo').slideUp(function(){
		$('#roominfo').dialog('close'); //animation to slide up and close the popup
	});
}

function deleteRoomFromCompareList(id) //function to delete a room from the compare list
{
	id = id.substr(3,id.length); //get the ID from the string
	$('#'+id).remove(); //remove the room from the list
	recreateTimetable(); //recreate the timetable of available weeks from the new set of rooms
}


function recreateTimetable() //function to update the weeks available of the selected rooms (i.e. if one was deleted)
{
	//recreate blank timetable
	$('#mon').html("<th class= 'ttheader' id='d1'> Monday </th>");
	$('#tue').html("<th class= 'ttheader' id='d2'> Tuesday </th>");
	$('#wed').html("<th class= 'ttheader' id='d3'> Wednesday </th>");
	$('#thu').html("<th class= 'ttheader' id='d4'> Thursday </th>");
	$('#fri').html("<th class= 'ttheader' id='d5'> Friday </th>");
	populateTimetable();

	//loop through each chosen room add intersect the available weeks
	var len = $('#comparedtable').children().children().length;
	for(var i = 0;i<len;i++)
	{
		var id = $('#comparedtable').children().children().eq(i).attr('value'); //get id of each room in the list
		fillTimetable(id); //take the room and add it to the timetable
		
	}
	
}

function updateAdvancedRoom(value) //Function to update the list of rooms from the chosen building
{
	var length;
	
	//call script to return the html of a list of rooms for a building
	$.get("php/updateAdvancedRoom.php?" + 'building=' + value, function(data)
	{
		$("#buildingcontent").html('<a class= "roomcontenttitle">  Rooms </a><a> </br> </a> ');
		$("#buildingcontent").append(data);
		$('#buildingcontent').children().eq(2).before('</br></br></br>'); //Add some spaces
	});
}

function timeOpacity(){ //When the user chooses a room, it makes the timetable opaque
	$("#timetable").css("opacity", "1");
}

function addRoomToList(id) //function to take a ID of a room and add it to the 'selected rooms' list
{
	newid = id.replace(/\./g, ''); //remove dots and slashes for ID validity
	if ($('#rm'+newid).length > 0 ) { //search for id existence
        alert('room already added');
		return;
    }
	
	var x = $('#chosenRooms').attr('data-norooms'); //get the no of rooms added already
	x = parseInt(x); 
	if(x>=3){ //check the no of rooms already chosen
		alert("You cannot choose more than 3 rooms");
		return;
	}
		
	//get caps on the group sizes of rooms, user given sizes and module size
	maxCap = parseInt(document.getElementById("maxGroupSize").value);
	reqCap = parseInt(document.getElementById("groupSize").value);
	roomCap = parseInt(document.getElementById("roomCapacity").innerHTML);
	if (roomCap >= reqCap)
	{
		if (reqCap>0) //if the groupsize is valid
		{
			maxCap = maxCap-reqCap;
			x++;
			var xStr = x.toString();
			var maxCapStr = maxCap.toString();
			$('#chosenRooms').attr('data-norooms',''+xStr+''); //change the no of rooms added
			$('#chosenRooms').attr('data-maxcap',''+maxCapStr+'');
			var html= "<tr id="+("rm" + newid)+"><td id ='cap"+newid+"'>"+reqCap+"</td><td> Students in room </td><td>"+id+"</td><td id='del"+ ("rm" + newid) +"' onclick='deleteRoom(this.id);'><img src='img/delete.png' height='15' width='15'><td></tr>";
			document.getElementById('chosenRooms').innerHTML += html;
			$("#selectedrooms").append(html);
			reqCap = maxCap;
			applyAccess(document.getElementById("btnAccessHome")); //add accessibility class if required
		}
		else
		{
			alert("Cannot book a room for 0 students!")
		}
	}
	else
	{
		alert("Room not big enough!");
	}
	document.getElementById("maxGroupSize").value = maxCap;
	if (maxCap == 0)
	{
		document.getElementById("groupSize").min = 0;
	}
	document.getElementById("groupSize").value = reqCap;
	document.getElementById("groupSize").max = maxCap;
	document.getElementById("groupSizeVal").value = maxCap;
	if (maxCap == 0)
	{
		document.getElementById("groupSize").min = 0;
	}
}

function deleteRoom(id) //need to implement group capacity, increment when room is deleted
{
	var roomid = id.substr(3,id.length);
	var x = $('#chosenRooms').attr('data-norooms'); //get the no of rooms added already
	if(x>0)
		x--;
	var str = x.toString();
	$('#chosenRooms').attr('data-norooms',''+str+''); //alter the value of the rooms added
	var groupSize = $('#chosenRooms').attr('data-maxcap');
	groupSize = parseInt(groupSize);
	var justroomid = roomid.substr(2,id.length);
	var capChange = document.getElementById('cap'+justroomid).innerHTML;
	capChange = parseInt(capChange);
	groupSize = groupSize+capChange;
	var groupSizeStr = groupSize.toString();
	$('#chosenRooms').attr('data-maxcap',''+groupSizeStr+'');
	while ($( '#'+roomid ).length > 0)
	{
		$( '#'+roomid ).remove();
	}
	loadGroupSize();
}

function addAnyPark(id) //function to add the option of 'Any building' in a chosen park
{
	maxCap = parseInt(document.getElementById("maxGroupSize").value);
	reqCap = parseInt(document.getElementById("groupSize").value);

	var str = 'rm'+id; //get the id
	var len = $('#chosenRooms').children().children().length;
	//loop through chosen rooms and check that the passed in room doesnt already exist
	for(i=0;i<len;i++){
		var parkname = $('#chosenRooms').children().children().eq(i).attr('id');
		if(str == parkname)
			return; //end function because the room has already been added
		
	}
	
	if(reqCap > 0){
		
		var parkname = id.substr(6); 
		parkname = parkname.substr(4).toUpperCase(); //Get the parkname character as uppercase
		//create html of the parkname
		var html= "<tr id="+("rm" + id)+"><td id ='cap"+id+"'>"+reqCap+"</td><td> Students in park </td><td>"+parkname+"</td><td id='del"+ ("rm" + id) +"' onclick='deleteRoom(this.id);'><img src='img/delete.png' height='15' width='15'><td></tr>";
		$("#selectedrooms").append(html); //add it to the selected rooms list
		document.getElementById('chosenRooms').innerHTML += html; //add it to the homepage list
	}
}

function findRoomOpen() //function to open the popup which lets the user find a room with given facilities and group size
{
	openDiv('findroomDiv');
	if ($('#findroomDiv').children().children().length <= 31) //31 is the last facility tag
	{
		//Add a close button at the top of the popup
		$("#findroomDiv").prepend('<input id="closefindroomDiv" class="closeDiv" type="button" value="x"></input><b><a class= "buildingcontenttitle"> Find Rooms </a></b><a> </br> </a> ');
		//Add a onclick attribute to close the popups
		$("#closefindroomDiv").attr("onclick", "closeDiv('findroomDiv'); closeDiv('matchedRoomsDiv')")
	}
	this.count = "1"; //reset the count
};

	


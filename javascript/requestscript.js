var times;
$(document).ready(function()		// Execute all of this on load 
{

}); //end document ready

//$('#timetable').children().eq(0).children().eq(2).children().eq(2).attr('id')
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

function populateTimetable()
{
	var id;
	for(var d = 1; d<6; d++)
	{
		for(var p = 10; p > 1; p--) //go backwards as we are inserting the tiles in reverse order
		{
			id = "d" + d + "p" + (p-1);
			$('#d'+d+'').after("<td class= 'timeslot' data-selected='0' data-available='' data-display='' id="+id+">Fully Available </td>");
		}
	}
}

function clearParkContent(){
	$("#parkcontent").html("");
}

function clearBuildingContent(){
	$("#buildingcontent").html("");
}

function clearRoomContent()
{
	$('#roominfo').html("");
}

function updateAdvancedBuilding(value)
{
	//alert(value);
	var string = value.substr(4, 1).toUpperCase();
	//string = string.toUpperCase();
	//alert(string);
	//document.getElementById("parkcontent").style.background-color= "#CC0066";
	
	/*$('#parkeast').css("background-color", "#CC0066");
	$('#parkcentral').css("background-color", "#CC0066");
	$('#parkwest').css("background-color", "#CC0066");
	$('#'+value).css("background-color", "#330066");
	*/
	$.get("php/updateAdvancedBuilding.php?" + 'park=' + string, function(data)
	{
		$("#parkcontent").html(data);
	});
}


function updateAdvancedRoomFacility(value)
	{
		//get the information about chosen room, and show as popup
		$.get("php/updateAdvancedRoomFacility.php?" + 'roomNo=' + value, function(data)
		{
			//alert(data);
			data+= "<input type='button' id='"+value+"' value='Select Room' onclick='addRoomToList(this.id);'>";
			$("#roominfo").html(data);
			document.getElementById("roominfo").title = value;
			$('#roominfo').dialog({
				dialogClass:"dialogClass",
				  show: {
					effect: "fadeIn",
					duration: 500
				  }
			}).prev(".ui-dialog-titlebar").css("background", "#CC0066"); //end dialog
		}); //end $.get
		
		//Fill in the timetable
		$.get("php/loadTimesOfRoom.php?" + 'roomNo=' + value, function(data)
		{
			var id, length, j, str;
			//var times = data.split(',,');
			times = JSON.parse(data); //JSON.parse turns php string into javascript object	
			
			for(var i =0; i < times.length;i++)
			{
				j = 1;
				id = times[i]['id'];
				length = times[i]['data-length'];
				var weeks = times[i]['data-weeks'];
				var display = times[i]['data-display'];
				$('#'+id).attr('id',id);
				$('#'+id).attr('data-length',length);
				$('#'+id).attr('data-selected','1');
				$('#'+id).attr('data-weeks', weeks);
				$('#'+id).attr('data-display', display);
				$('#'+id).attr('class','timeslotbooked');
				$('#'+id).html('Available');
				displayAvailableWeeks(id, value);
				while(j < (length))
				{
					//str = id.substr(3,1);
					str = $('#'+id).attr('id').substr(3,1);
					var n = parseInt(str);
					n = n + j;
					var newid = $('#'+id).attr('id').substr(0,3) + n;
					//var newTime = times[i].replace(id,newid);
					//$('#'+newid).replaceWith(newTime);
					$('#'+newid).attr('data-length',length);
					$('#'+newid).attr('data-selected','1');
					$('#'+newid).attr('data-weeks', weeks);
					$('#'+newid).attr('class','timeslotbooked');
					$('#'+newid).attr('data-display', display);
					$('#'+newid).html('Available');
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
		//weeks = weeks.replace(/[\[\]']+/g,'')
	
		weeks = strweeks.split(":");
		
		for(var i = 1;i<weeks.length;i++){ //loop through each set of weeks chosen
			
			len = weeks[i].length;
			switch(len){
				case 5:
					w = weeks[i].substr(1,1) + weeks[i].substr(3,1);
					//alert(w);
					str += returnBookedWeeks(weeks[i].substr(1,1), weeks[i].substr(3,1));
					break;
				case 6:
					if(weeks[i].substr(2,1) == ","){
						w = weeks[i].substr(1,1) + weeks[i].substr(3,2);
						//alert(w);
						str += returnBookedWeeks(weeks[i].substr(1,1), weeks[i].substr(3,2));
					} else{
						w = weeks[i].substr(1,2) + weeks[i].substr(4,1);
						//alert(w);
						str += returnBookedWeeks(weeks[i].substr(1,2), weeks[i].substr(4,1));
					}
					break;
				case 7:
					w = weeks[i].substr(1,2) + weeks[i].substr(3,2);
					//alert(w);
					str += returnBookedWeeks(weeks[i].substr(1,2), weeks[i].substr(3,2));
					break;
			} //end switch
		}
		
		temp = str.split(",");
		match = false;
		/*
		for(var i = 1 ;i < 16;i++){
			for(var j = 0; j < temp.length; j++){
				if(temp[j] == i){
					chosen = true;
					//break;
				}
			}
			
			if(chosen == false){
				av[av.length] = i;
			}
			chosen = false;
		
		}
		*/
		
		avweeks(id, temp);
}

function avweeks(id, chosen) //function to change the available weeks of a time slot
{
	var match, str;
	var av = [];
	//a201 = 6,11,12,13,14,15
	//cc011 = 6,13,14,15
		var str = $('#'+id).attr('data-display'); //get currently available weeks for this slot
		var display = str.substr(1,str.length-2);
		display = display.split(",");	//turn attribute into array
		var str = "";
		for(var i = 0; i< display.length; i++){ //loop to push available weeks into an array
			match = false;
			for(var j = 0; j< chosen.length;j++){
				if(chosen[j] == display[i]){
					match = true;
				}
			}
			if(match == false){
				av[av.length] = display[i];
			}
		}
		str= "";
		
		str = av.join(',');
		
		//str = "[" + str + "]";
		$('#' + id).attr('data-display',str);
		
		$('#'+id).hover(function(){// make hover function to show the available weeks
			var str = $('#'+id).attr("data-display");
			$('#'+id).html(str);
		}, function(){
			$('#'+id).html("Available");
		});
	
}


function returnBookedWeeks(start, end)
{
	var str = "";
	var intstart = parseInt(start); //convert given numbers into integers
	var intend = parseInt(end);
	for(var i = intstart; i <= intend; i++){
		str += i + ",";
	}
	return str;
}
	
function updateAdvancedRoom(value)
{
	
	$.get("php/updateAdvancedRoom.php?" + 'building=' + value, function(data)
	{
		$("#buildingcontent").append(data);
	});
}

function addRoomToList(id)
{
	newid = id.replace(/\./g, '');
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

	maxCap = parseInt(document.getElementById("maxGroupSize").value);
	reqCap = parseInt(document.getElementById("groupSize").value);
	roomCap = parseInt(document.getElementById("roomCapacity").innerHTML);
	if (roomCap >= reqCap)
	{
		if (reqCap>0)
		{
			maxCap = maxCap-reqCap;
			x++;
			var xStr = x.toString();
			var maxCapStr = maxCap.toString();
			$('#chosenRooms').attr('data-norooms',''+xStr+''); //change the no of rooms added
			$('#chosenRooms').attr('data-maxcap',''+maxCapStr+'');
			var html= "<tr id="+("rm" + newid)+"><td id ='cap"+newid+"'>"+reqCap+"</td><td> Students in room </td><td>"+id+"</td><td id='del"+ ("rm" + newid) +"' onclick='deleteRoom(this.id);'><img src='img/delete.png' height='15' width='15'><td></tr>";
			document.getElementById('chosenRooms').innerHTML += html;
			$("#buildingcontent").append(html);
			reqCap = maxCap;
			alert("Added room to request!")
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
	document.getElementById("groupSize").value = reqCap;
	document.getElementById("groupSize").max = maxCap;
	document.getElementById("groupSizeVal").value = maxCap;
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
var maxSize = 0;
var times;
$(document).ready(function()		// Execute all of this on load 
{
	loadGroupSize();
	populateTimetable(); //function to add the tiles to the timetable

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
	$('#groupSize').change(function(){
		alert(maxSize);
	});
	
	
}); //end document ready

//$('#timetable').children().eq(0).children().eq(2).children().eq(2).attr('id')



function loadGroupSize()//load the group size based on the module
{
	var modCode = "modCode=" + $('#modCodes').val().substr(0,8);
		$.get("php/loadGroupSize.php?" + modCode, function(data)
		{
			$('#groupSize').val(data);
		});
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

function closeAdvancedRequestDiv()
{
	document.getElementById('popupRequestDiv').style.visibility = 'hidden';
	document.getElementById('east').style.visibility= 'hidden'
	document.getElementById('eastinfo').style.visibility= 'hidden';
	document.getElementById('central').style.visibility= 'hidden';
	document.getElementById('centralinfo').style.visibility= 'hidden';
	document.getElementById('west').style.visibility= 'hidden';
	document.getElementById('westinfo').style.visibility= 'hidden';
}

function clearParkContent(){
	$("#parkcontent").html("");
}

function clearBuildingContent(){
	$("#buildingcontent").html("");
}

function hideParkContent()
{
	return;
	document.getElementById('eastinfo').style.visibility= 'hidden'
	document.getElementById('centralinfo').style.visibility= 'hidden'
	document.getElementById('westinfo').style.visibility= 'hidden'
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
	$('#parkeast').css("background-color", "#CC0066");
	$('#parkcentral').css("background-color", "#CC0066");
	$('#parkwest').css("background-color", "#CC0066");
	$('#'+value).css("background-color", "#330066");

	$.get("php/updateAdvancedBuilding.php?" + 'park=' + string, function(data)
	{
		//alert(data);
		//data = "'" + data + "'";
		//content = '<a id="prev" title="Previous Slide">Previous Slide</a><a id="next" title="Next Slide">Next Slide</a>'
		//alert(content);
		$("#parkcontent").append(data);
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
			
				//id = times[i].substr(8,4);
				id = times[i]['id'];
				//length = times[i].substr(45,1);
				length = times[i]['data-length'];
				var weeks = times[i]['data-weeks'];
				$('#'+id).attr('id',id);
				$('#'+id).attr('data-length',length);
				$('#'+id).attr('data-selected','1');
				$('#'+id).attr('data-weeks', weeks);
				$('#'+id).attr('class','timeslotbooked');
				$('#'+id).html('Available');
				//$('#'+id).replaceWith(times[i]);
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
		var len, chosen, w;
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
		chosen = false;
		str = "";
		
		
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
		
		var str = $('#'+id).attr("data-available");
		str += ":" + av.toString();
		$('#'+id).attr("data-available", str);
		$('#'+id).hover(function(){
			var str = $('#'+id).attr("data-available");
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
	x++;
	var str = x.toString();
	$('#chosenRooms').attr('data-norooms',''+str+''); //change the no of rooms added
	var html= "<tr id="+("rm" + newid)+"><td>"+id+"</td><td id='del"+ ("rm" + newid) +"' onclick='deleteRoom(this.id);'><img src='img/delete.png' height='15' width='15'><td></tr>";
	$('#chosenRooms').after(html);
}

function deleteRoom(id)
{
	var roomid = id.substr(3,id.length);
	$( '#'+roomid ).remove();
	var x = $('#chosenRooms').attr('data-norooms'); //get the no of rooms added already
	if(x>0)
		x--;
	var str = x.toString();
	$('#chosenRooms').attr('data-norooms',''+str+''); //alter the value of the rooms added
}

function openAdvancedSearchDiv()
{
	document.getElementById('popupAdvancedSearchDiv').style.visibility = 'visible';
}
function closeAdvancedRequestDiv()
{
	document.getElementById('popupRequestDiv').style.visibility = 'hidden';
	document.getElementById('east').style.visibility= 'hidden'
	document.getElementById('eastinfo').style.visibility= 'hidden';
	document.getElementById('central').style.visibility= 'hidden';
	document.getElementById('centralinfo').style.visibility= 'hidden';
	document.getElementById('west').style.visibility= 'hidden';
	document.getElementById('westinfo').style.visibility= 'hidden';
}

function showEast()
{
	if(document.getElementById('parkcentral').style.backgroundColor= '##330066')
	{
		document.getElementById('parkcentral').style.backgroundColor= '#CC0066'
	}
	if(document.getElementById('parkwest').style.backgroundColor= '#330066')
	{
		document.getElementById('parkwest').style.backgroundColor= '#CC0066'
	}
	document.getElementById('parkeast').style.backgroundColor= '#330066'
	
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
	if(document.getElementById('parkeast').style.backgroundColor= '#330066')
	{
		document.getElementById('parkeast').style.backgroundColor= '#CC0066'
	}
	if(document.getElementById('parkwest').style.backgroundColor= '#330066')
	{
		document.getElementById('parkwest').style.backgroundColor= '#CC0066'
	}
	document.getElementById('parkcentral').style.backgroundColor= '#330066'
	
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
	if(document.getElementById('parkeast').style.backgroundColor= '#330066')
	{
		document.getElementById('parkeast').style.backgroundColor= '#CC0066'
	}
	if(document.getElementById('parkcentral').style.backgroundColor= '#330066')
	{
		document.getElementById('parkcentral').style.backgroundColor= '#CC0066'
	}
	document.getElementById('parkwest').style.backgroundColor= '#330066'
	
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
	return;
	document.getElementById('eastinfo').style.visibility= 'hidden'
	document.getElementById('centralinfo').style.visibility= 'hidden'
	document.getElementById('westinfo').style.visibility= 'hidden'
}
function clearRoomContent()
{
	$('#roominfo').html("");
}

function openPendingDiv()
{
	document.getElementById('popupPendingDiv').style.visibility = 'visible';
}

function closePendingDiv()
{
	document.getElementById('popupPendingDiv').style.visibility = 'hidden';
}

function openPopupRequestDiv()
{
	document.getElementById('popupRequestDiv').style.visibility = 'visible';
}

function closePopupRequestDiv()
{
	document.getElementById('popupRequestDiv').style.visibility = 'hidden';
}
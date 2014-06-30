<html>

<head>

	<title>music player</title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0; mininum-scale=0.5; maximum-scale=1.0; user-scalable=no;" />

<style>

div 
{
display:block;
}
.List_Item
{
background-color:#eeeeee;
color:#111111;
margin:5 0;
padding:15 50; 
font-family:Helvetica Neue, Arial; 
display:block  
}
.List_Item:hover
{
background-color:#aaaaaa;
cursor:pointer;
}
#header{
top:0px;
left:0px;
width:100%;
}
#prev_btn
{
background-image:url('content/buttons.png');
background-size:116px;
background-position:0px, 0;
width:28px;
height:27px;
margin:10 0;
}
#play_btn
{
background-image:url('content/buttons.png');
background-size:175px;
background-position:87px, 0;
width:42px;
height:42px;
margin: 3;
}
#pause_btn
{
background-image:url('content/buttons.png');
background-size:175px;
background-position:43.5px, 0;
width:42px;
height:42px;
margin: 3;
}
#next_btn
{
background-image:url('content/buttons.png');
background-size:116px;
background-position:-29px, 0;
width:28px;
height:27px;
margin:10 0;
}

.player_btns
{
opacity:.8;
margin:auto;
padding:auto ;
display:block;
float:left;
}
.player_btns:hover
{
opacity:.6;
}



#footer_player_container
{
background-color:#000000;
position:fixed;
bottom:0px;
left:0px;
float:left;
width:100%;
height:50px;/*65*/
z-index:99;
}
#footer_player
{
	margin:5 auto 5  120; /*10 auto 5 120*/
}

.footer_control_btn
{
background-size:75px;/*100*/
position:fixed;
bottom:0px;
left:0px;
width:75px;/*100*/
height:75px;/*100*/
z-index:99;
}

#footer_play_btn
{
background-image:url('content/BPlaybtn.png');

}
#footer_pause_btn
{
background-image:url('content/BPausebtn.png');

}
.footer_control_btn:hover{
color:#ff00ff
}

#progress{
width:90%; 
height:10px; 
background-color:#222222;
margin:5 0; /*10 0*/
overflow:hidden;
border-radius:5;
}

#progress_loaded{
background-color:#555555;
width:0%;
height:5px;
z-index:5;
border-radius:0 0 5 5
}

#progress_position{
background-color:#cccccc;
width:0%;
height:5px;
z-index:10;
border-radius:5 5 0 0;
}

#now_playing
{
white-space:nowrap;
}

</style>

</head>


<body style="margin:0px;padding:0px;">
<div id="header" style="background-color:#000000; color:#f0f0f0; "  >
<table style="width:80%; margin:auto;">
	<tr style="width:100%">
		<td width="150">
			<div style="  color:#f0f0f0;  padding:10px 0px; font-size:24px; font-family:Helvetica Neue, Arial;  ">
				music player
			</div>
		</td>				
		<td>		
		</td>
		<td width="70" height="45" style="padding:0px; margin:0px; ">
			<div style="  color:#f0f0f0;  width:100%; height:100%; margin:0px; padding:0px; float:left; display:block; position:relative; vertical-align:middle; ">
						 <span id="prev_btn" class="player_btns" onclick="Prev_Item()"></span>
						 <!--<span id="play_btn" class="player_btns" onclick="Play_Pause()"></span> -->
						 <span id="next_btn" class="player_btns" onclick="Next_Item()"></span>
			</div>
		</td>
	</tr>
</table>
</div>

<div style="height:15px;"></div>

<div style="width:80%; margin:auto; margin-bottom:100px;">
	<div style="font-family:Arial; font-size:22px; margin:0 0 10 0">
		PlayList
	</div>
	<div id="Track_List" >
	<?php
	include 'pass.php';
$con=mysqli_connect("127.0.0.1","bcophm_music",$pass,"bcophm_music");
if (mysqli_connect_errno()) 
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{
$result = mysqli_query($con,"SELECT * FROM tracks WHERE adder_id=0 AND (file_type='audio/mpeg' OR file_type='audio/mp3')");
mysqli_close($con);
$count = 0;
 while($row = mysqli_fetch_array($result)) {
 		echo "<div class='List_Item' id='" . $count . "' sid='" . $row['file_name'] . "' onclick='Play_This(id)'>";
		echo $row['author_name'] . " - " . $row['track_name'] . "</div>";
		$count++;	

}
}
?>	
</div>	


<!--<button onclick="Prev_Item()"><<|</button>
<button onclick="Play_Pause()">Play/Pause</button>
<button onclick="Next_Item()">|>></button>-->


	
<audio id="player_element" src="" onended="Next_Item()" ontimeupdate="Progressbar()" onprogress="Buffer_Progressbar()"  preload="auto" ></audio>
	
<div id="footer_player_container" onclick="">
	<div id="footer_player">
		<div id="now_playing_container" style="width:100%; overflow-x:hidden; ">
			<div id="now_playing" style="color:#ffffff; font-family:Arial; line-height:20px; ">
				 - 
			</div>
		</div>
		<div id="progress" onclick="progress_Click(event)">
			<div id="progress_position"></div>
			<div id="progress_loaded"></div>			
		</div>
	</div>
</div>
<span id="footer_play_btn" class="footer_control_btn" onclick="Play_Pause()">
	
</span>	


<script>
//mobil ellenorzés
if (navigator.userAgent.match(/Android/i) ||
	 navigator.userAgent.match(/webOS/i) ||
	 navigator.userAgent.match(/iPhone/i) ||
	 navigator.userAgent.match(/iPad/i) ||
	 navigator.userAgent.match(/iPod/i) ||
	 navigator.userAgent.match(/BlackBerry/) || 
	 navigator.userAgent.match(/Windows Phone/i) || 
	 navigator.userAgent.match(/IEMobile/i) || 
	 navigator.userAgent.match(/Opera Mini/i)
	)
{
	document.getElementById("now_playing_container").style["overflow-x"]="scroll";
}
</script>

<script>

var Dir="./upload/uploads/"

var act_i=0;
var next_i=0;
var x=0;

var player =document.getElementById("player_element");


Play_Item();
Play_Pause();


//act_item.src=document.getElementById("0").getAttribute("sid");  ///getAttribute <<--

function Play_This(t_id)
{	
	next_i=t_id;
	Play_Item();
}

function Play_Pause()
{

	if(player.paused)
	{
		player.play();
		//document.getElementById("play_btn").id="pause_btn";
		document.getElementById("footer_play_btn").id="footer_pause_btn";
	}
	else
	{
		player.pause();
		//document.getElementById("pause_btn").id="play_btn";
		document.getElementById("footer_pause_btn").id="footer_play_btn";
	}
}
function Prev_Item()
{
if(act_i>0){
	next_i=Number(act_i)-1;
	Play_Item();
}
	
}
function Next_Item()
{
	next_i=Number(act_i)+1;
	if(document.getElementById(next_i)== null)
	{
		next_i=0; //KOVETKEZORE -- 0-elsore ugrik
		Play_Item();
		Play_Pause();
	}
	else
	{
		Play_Item();
	}
}
function progress_Click(event)
{
	 pos_x = event.offsetX?(event.offsetX):event.pageX-document.getElementById("progress").offsetLeft;
	 percent=(pos_x*100)/document.getElementById("progress").offsetWidth;
	 player.currentTime=((player.duration*percent)/100);
}
function Buffer_Progressbar()
{

	if(player.buffered.length>0)
	{
		pc=(player.buffered.end(0)/player.duration)*100+"%";
		document.getElementById("progress_loaded").style["width"]=pc;
	}
	
}
function Progressbar()
{
	p=(player.currentTime/player.duration)*100+"%";	
	document.getElementById("progress_position").style["width"]=p;
}

function Play_Item()
{
	player.pause()
	player.src="";
	player.load();
	
	player.src=Dir+""+document.getElementById(next_i).getAttribute("sid");
	player.load();
	player.play();
	document.getElementById("now_playing").innerHTML=document.getElementById(next_i).innerHTML;
	
	act_i=Number(next_i);  //Eddigi kovetkezo lesz az aktualis
	
	//document.getElementById("play_btn").id="pause_btn";
	document.getElementsByClassName("footer_control_btn")[0].id="footer_pause_btn";
}

//KEYDOWN
document.onkeydown = function(e)
{
	if(window.event) // IE
	{
		bill = e.keyCode;
	}
	else if(e.which) // Netscape/Firefox/Opera
	{
		bill = e.which;
	}
	if(bill==32)
	{
		Play_Pause();
	}
	if(bill==37)
	{
		Prev_Item();	
	}
	if(bill==39)
	{
		Next_Item();
	}
	

}

</script>

</body>
</html>

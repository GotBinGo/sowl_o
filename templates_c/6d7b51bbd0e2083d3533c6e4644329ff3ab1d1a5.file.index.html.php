<?php /* Smarty version Smarty-3.1.21-dev, created on 2014-12-04 17:15:46
         compiled from "tpl/index.html" */ ?>
<?php /*%%SmartyHeaderCode:45741780754808d4b076e86-90700669%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6d7b51bbd0e2083d3533c6e4644329ff3ab1d1a5' => 
    array (
      0 => 'tpl/index.html',
      1 => 1417712333,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '45741780754808d4b076e86-90700669',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54808d4b1f4a02_12623249',
  'variables' => 
  array (
    'username' => 0,
    'tracks' => 0,
    'toload' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54808d4b1f4a02_12623249')) {function content_54808d4b1f4a02_12623249($_smarty_tpl) {?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>music player</title>
		<meta name="viewport" content="width=device-width; initial-scale=1.0; mininum-scale=0.5; maximum-scale=1.0; user-scalable=no;" />
		<?php echo '<script'; ?>
>
	

function gohome() {	
	loadXMLDoc("search.php?type=track&term=",
			document.getElementById("content_main"),
			function(){hidePlaylist();},
			true);
}
window.onpopstate = function(event) {

	if(!ishidden())
	{
		loadXMLDoc(event.state.getU,
				document.getElementById(event.state.toC),
				function(){}, //eval(nextF),
				false);
	}
	else
		hidePlaylist();
}

function loadXMLDoc(location,to_container,next_function,logHistory){ //to_container -> hova(TAG) tölti be  next_function - a kovetkezo fuggveny	

	logHistory = typeof logHistory !== 'undefined' ? logHistory : false;		

	//new Function(argument, functionBody);

	document.getElementById("loading_bar").style["display"]="block";//Loading bar megjeleintese
	var geturl=location;  //pl search.php?type=all&term=(term)

	if(logHistory){
		var stateObj = { getU: geturl, toC: to_container.id };	//, nextF: next_function.toString()
		history.pushState(stateObj, "page 2", "?" +geturl); //A # utani reszt a php nem kapja meg
	}

	var xmlhttp;
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			var respTxt = xmlhttp.responseText;//DOMParser().parseFromString()
			to_container.innerHTML="";
			to_container.innerHTML = respTxt;//betoltes

			next_function();
			document.getElementById("loading_bar").style["display"]="none";//Loading bar megjeleintese
		}
	}
	xmlhttp.open("GET",geturl,true);
	xmlhttp.send();	
}

function createPlaylist(plName) {
	var geturl="create_playlist.php?name="+plName; 
	bgXHReq(geturl);	
}
function playlistAdd(list_id) {
	var geturl="add_to_playlist.php?track_id=" + list_id.parentNode.parentNode.id + "&list_id=" + list_id.value; 
	bgXHReq(geturl);	
}
function bgXHReq(geturl){	
	document.getElementById("loading_bar").style["display"]="block"; //Loading bar megjeleintese
	var xmlhttp;
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){		
			document.getElementById("loading_bar").style["display"]="none";//Loading bar megjeleintese
		}
	}
	xmlhttp.open("GET",geturl,true);
	xmlhttp.send();
}


	  
		<?php echo '</script'; ?>
>
		<link rel="stylesheet" type="text/css" href="tpl/style.css">
	</head>
	<body style="margin:0px; padding:0px; background-color:#d4eeee;">
		<?php echo '<script'; ?>
>

var mobile_device=0;///0- nem mobil   1- mobil
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
   ){
	mobile_device=1;
}		
	  
		<?php echo '</script'; ?>
>
		<div id="header" style=" "  >
			<div id="fejlec" style="">            
				<div id="fejlec_cim"  style=" float:left;">
					<label onclick="gohome()">music player</label>
				</div>
				<!--gombok-->
				<?php if ($_smarty_tpl->tpl_vars['username']->value=='') {?>
				<div id="login_button" onclick="loginClick()" style="float:right; width:40px; height:100%;">
				</div>					
				<?php } else { ?>
				
				<div id="logout_button" onclick="location.href='logout.php'" style="float:right;  width:40px; height:100%;">
				</div>					
				<div id="profil_button" onclick="loadXMLDoc('user.php',
			    document.getElementById('content_main'),
			    function(){hidePlaylist();},
			    true);" style="float:right;  width:40px; height:100%;">
				</div> 	
				<div id="upload_button" onclick="uploadClick()" style="float:right;  width:40px; height:100%;">
				</div>
													
				<?php }?>
				<!--kereso-->

				<div id="search_button" onclick="keresesClick()" style="float:right; width:40px; height:100%;">
				</div>		
				<input type="text" id="search_box" name="term" value="" class="inactive" placeholder="Kereses" style="float:right; height:100%; ">

			</div>			 
		</div>
		<?php if ($_smarty_tpl->tpl_vars['username']->value=='') {?>
		<!--Login box-->
		<div id="login_box" style="display:none;">
			<form name='login' action='login_process.php' method='post'>
				<input id="login_username" type='text' name='username' placeholder="Username" maxlength='20' tabindex="1" autofocus />		
				<input id="login_password" type='password' name='password' placeholder="Password" tabindex="1" />			
				<input id="login_submit" type='submit' value='Login' style="" />
			</form>		
			<iframe src="test.php" width="200" height="50"></iframe>
		</div>	
		<!--Login box end-->
		<?php }?>
		<div style="width:90%; margin:auto; margin-bottom:80px; margin-top:20px;">
			<!--lejatszo kontener-->
			<div id="playlist_container" style="display:none; padding:10px;">
				<div style="font-family:Arial; font-size:22px; margin:0px 0px 10px 0px;">
					PlayList 				
					<div onclick="document.getElementById('Track_List').innerHTML=''" style="float:right; font-size:18px; cursor:pointer;">
						X
					</div>
				</div>
				<div id="Track_List" >	  
					<!--<?php echo $_smarty_tpl->tpl_vars['tracks']->value;?>
-->
				</div>
			</div>
			<!--tartalom kontener-->
			<div id="content_container" style="display:block;">
				<div id="content_main">
				</div>			
			</div>
		</div>
		<!--lejatszo  audio tag-->
		<audio id="player_element" src="x.mp3" onended="nextItem()" ontimeupdate="playerTimeupdate()" onprogress="bufferProgressbar()"  preload="auto" ></audio>
		<!--lejatszo vezerlok-->
		<div id="footer_player_container" onclick="Nothing()" style="display:none;" >
			<div id="footer_player" style="display:table; table-layout:fixed; width:100%;">
				<div id="footer_play_btn" class="footer_control_btn" onclick="togglePlayPause()" style="display:table-cell; width:50px; ">

				</div>
			<div id="song_current_time" style="display:table-cell;">
			</div>
			<div style="display:table-cell; padding:5px;">
				<div id="now_playing_container" style="overflow-x:hidden;">
					<!--kiiras-->
					<div id="now_playing" style="line-height:20px; ">
						-
					</div>
				</div>
				<!--tolto csik-->
				<div id="progress" onclick="progressClick(event)" style="float:left; position:relative;">
					<div id="progress_position" style="position:absolute;"></div>
					<div id="progress_loaded" style="position:absolute;"></div>
				</div>				
			</div>
			<div id="song_duration" style="display:table-cell; ">
			</div>
			<div id="playlist_button" onclick="showhidePlaylist()" style="display:table-cell; width:50px;">

			</div>
			</div>
		</div>
		<div id="loading_bar" >
			<div></div>
			<div></div>
			<div></div>
			<div></div>
		</div>
		<?php echo '<script'; ?>
>
	  
document.getElementById("search_box").addEventListener('keyup',function(e){
	e.stopPropagation();//nem kuldi tovabb az esemenytaz alattalevoknek
	if(window.event) // IE
	{
		bill = e.keyCode;
	}
	else if(e.which) // Netscape/Firefox/Opera
	{
		bill = e.which;
	}
	if(bill==13){
		//Kereses - Enter
		loadXMLDoc("search.php?type=all&term="+document.getElementById("search_box").value,
				document.getElementById("content_main"),
				function(){},
				true);
		hidePlaylist();
	}
});
	  
		<?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
>
	
function loginClick(){
	document.querySelector('#login_box').style['display'] =	(document.querySelector('#login_box').style['display']=='block') ? 'none' : 'block';
	document.querySelector('#login_box #login_username').focus();
}
function uploadClick(){
	document.getElementById('content_main').innerHTML="<iframe src='./up' style='width:100%; height:600px; border:0px;'></iframe>";
}  
function keresesClick(){
	//Kereso gomb - gombnyomás
	var kereso_doboz=document.getElementById("search_box");
	if(kereso_doboz.getAttribute("class")=="inactive"){
		kereso_doboz.setAttribute("class","active");
		kereso_doboz.focus();
	}
	else{ kereso_doboz.setAttribute("class","inactive");}
}
function Item_Click(e){
	//Item - kattintás
	if(e.parentNode.id=="Track_List"){playThis(e);}//Ha a playlistbe van
	else{
		switch(e.getAttribute("type"))
		{
			case "track":
				/*loadXMLDoc("search.php?type=track&term=" + getUrlVariable("term"),
				  document.getElementById("Track_List"),
				  function(){playAtThis(e.id);},
				  false);*/
				//togglePlayPause();
				document.getElementById("Track_List").innerHTML="";
				var tracks = document.querySelectorAll("#content_main div[type=track]")
					for(var i=0;i<tracks.length;i++){	
						document.getElementById("Track_List").appendChild(tracks[i].cloneNode(true));
					}
				playAtThis2(e.getAttribute("count"));
				break;
			case "list":
				loadXMLDoc("playlist.php?id=" + e.getAttribute("id"),
						document.getElementById("content_main"),
						function(){/*startPlaylist();*/},
						true);
				break;
			case "user":
				loadXMLDoc("user.php?name=" + e.getAttribute("name"),
						document.getElementById("content_main"),
						function(){},
						true);
				break;
		}
	}
}
function getUrlVariable(variableName){
	//Kiszedi az url-bol a term valtozo erteket
	var searchKeyWord="";
	var docUrl=window.location.search.split("?");
	var urlVars=docUrl[docUrl.length-1].split("&");
	for(var i=0;i<urlVars.length;i++) {
		var urlVariable = urlVars[i].split("=");
		if(urlVariable[0]==variableName){return urlVariable[1];}	
	}
}
	  
		<?php echo '</script'; ?>
>	
	<?php echo '<script'; ?>
>

//site-url betoltese
var toload = '<?php echo $_smarty_tpl->tpl_vars['toload']->value;?>
';
loadXMLDoc(toload+"",
		document.getElementById("content_main"),
		function(){},
		true);

	<?php echo '</script'; ?>
>

					<?php echo '<script'; ?>
>
	  
//mobil ellenorzés
if (mobile_device==1){	/*Ha mobil, mozgathato a lejatszo kiirasa*/
	document.getElementById("now_playing_container").style["overflow-x"] = "scroll";
}
	  
					<?php echo '</script'; ?>
>
					<?php echo '<script'; ?>
>
	  
var Dir = "./upload/uploads/";
var player = document.getElementById("player_element");

var act_i;
var next_i;       

function Nothing(){         

}   
function playThis(t){	
	next_i = t;
	playItem();
}
function playAtThis(t_id){
	next_i = document.getElementById("Track_List").querySelector("[id='"+t_id+"']");
	playItem();
}
function playAtThis2(count){
	next_i = document.getElementById("Track_List").querySelector("[count='"+count+"']");
	playItem();
}
function startPlaylist(){
	next_i = document.getElementById("Track_List").getElementsByTagName("div")[0];
	playItem();
}         
function togglePlayPause(){

	if(player.paused){
		play();
	}
	else{
		pause();
	}
}
function play(){
	player.play();
	//document.getElementById("play_btn").id="pause_btn";
	document.getElementById("footer_play_btn").id = "footer_pause_btn";
}
function pause(){
	player.pause();
	//document.getElementById("pause_btn").id="play_btn";
	document.getElementById("footer_pause_btn").id = "footer_play_btn";
}
function prevItem(){         	
	next_i = get_prevElement(act_i);//.getAttribute("type")=="track"?get_prevElement(act_i):act_i;
	playItem();       	
}
function nextItem(){
	next_i = get_nextElement(act_i);//.getAttribute("type")=="track"?get_nextElement(act_i):act_i;
	if(next_i==act_i){startPlaylist();togglePlayPause();}
	else{
		playItem();
	}
}
function playItem(){

	if(document.getElementById("footer_player_container").style["display"]=="none"){
		document.getElementById("footer_player_container").style["display"]="block"; //Lejatszo megjelenitese
	}

	player.pause()
		player.src="";
	player.load();

	player.src=Dir+""+next_i.getAttribute("sid");
	player.load();
	player.play();
	//document.getElementById("now_playing").innerHTML = next_i.innerHTML;
	document.getElementById("now_playing").innerHTML = next_i.getAttribute('title');

	act_i=next_i;  //Eddigi kovetkezo lesz az aktualis

	document.getElementsByClassName("footer_control_btn")[0].id = "footer_pause_btn";
}		 
function get_nextElement(n){
	x = n.nextSibling;
	while (x.nodeType!=1 && x.getAttribute('type')!='track')
	{
		x=x.nextSibling;
		if(x==null){return n;}//ha nincs akkor onmagaval ter vissza
	}
	return x;
}
function get_prevElement(n){
	x = n.previousSibling;
	while (x.nodeType!=1 && x.getAttribute('type')!='track')
	{
		x=x.previousSibling;
		if(x==null){return n;}//ha nincs akkor onmagaval ter vissza
	}
	return x;
}


function increaseVolume(){
	if(player.volume <= 0.9){player.volume+=0.1;}
}
function decreaseVolume(){
	if(player.volume >= 0.1){player.volume-=0.1;}
}

function progressClick(event){
	pos_x = event.offsetX ? (event.offsetX) : event.pageX-document.getElementById("progress").offsetLeft;
	percent = (pos_x*100)/document.getElementById("progress").offsetWidth;
	player.currentTime = ((player.duration*percent)/100);
}
function playerTimeupdate(){
	Progressbar();
	if(!isNaN(player.duration)){// ha a hossz nem NaN
		document.getElementById("song_current_time").innerHTML = SecToTime(player.currentTime);// idő
		document.getElementById("song_duration").innerHTML = SecToTime(player.duration);}//hossz
}
function SecToTime(sec){//Csak perc másodperc mm:ss 
	sec=Math.round(sec);
	return Math.floor(sec/60)+":"+((sec%60)>9?(sec%60):"0"+(sec%60));
}
function bufferProgressbar(){

	if(player.buffered.length>0)
	{
		pc = (player.buffered.end(0)/player.duration)*100+"%";
		document.getElementById("progress_loaded").style["width"] = pc;
	}

}
function Progressbar(){
	p = (player.currentTime/player.duration)*100+"%";	
	document.getElementById("progress_position").style["width"] = p;
}
function ishidden()
{
	var playl_c=document.getElementById("playlist_container");
	if(playl_c.style["display"]=="block")
		return true;
	else
		return false;
}
function showhidePlaylist(){
	var playl_c=document.getElementById("playlist_container");
	var cont_c=document.getElementById("content_container");
	if(playl_c.style["display"]=="block"){
		playl_c.style["display"]="none";
		cont_c.style["display"]="block";
	}
	else{
		playl_c.style["display"]="block";
		cont_c.style["display"]="none";
	}
}
function showPlaylist(){
	var playl_c=document.getElementById("playlist_container");
	var cont_c=document.getElementById("content_container");
	playl_c.style["display"]="block";
	cont_c.style["display"]="none";
}
function hidePlaylist(){
	var playl_c=document.getElementById("playlist_container");
	var cont_c=document.getElementById("content_container");
	playl_c.style["display"]="none";
	cont_c.style["display"]="block";
}


	  		 
					<?php echo '</script'; ?>
>
					<?php echo '<script'; ?>
>

//KEYDOWN
document.onkeyup = function(e){
	if(window.event) // IE
	{
		bill = e.keyCode;
	}
	else if(e.which) // Netscape/Firefox/Opera
	{
		bill = e.which;
	}

	if(bill==32){
		e.preventDefault();
		togglePlayPause();
	}
	if(bill==37){
		e.preventDefault();
		prevItem();	
	}
	if(bill==39){
		e.preventDefault();
		nextItem();
	}
	if(bill==38){
		e.preventDefault();
		increaseVolume();	
	}
	if(bill==40){
		e.preventDefault();
		decreaseVolume();
	}	

}
	  		 
					<?php echo '</script'; ?>
>
	</body>
</html>
<?php }} ?>

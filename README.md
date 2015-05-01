requires  
facebook php - api ./Facebook/  
smarty - ./libs/

spec
insall
	passwords
	
reg
	mail
	handle
	display name(same as handle by def)
	pw
	
login
	request cookie, store cookie
	send all the requests with given cookie, if no cooke display public page
	
layout
	head line
	body
	bottom player floating
function 
	refresh everything with a global xmlhttp function, and some helper functions
	on each request check if user is still logged in	
	
player
	pure js, with one audio tag	
	load playlist, with song to play
	play next, play prev, stop, sound?
	3 repeat mode, 
		play songs til end of list
		play and repeat list
		repeat one song
	"play next" option, 
	able to return unique id of currently palying song

var tile = { h: 278,
				 l: 476.5,
				 hdest: 60,
				 ldest: 120};
var map = new Map("premiere", tile);

window.requestAnimFrame = (function(){
    return window.requestAnimationFrame       || // La forme standardisée
           window.webkitRequestAnimationFrame || // Pour Chrome et Safari
           window.mozRequestAnimationFrame    || // Pour Firefox
           window.oRequestAnimationFrame      || // Pour Opera
           window.msRequestAnimationFrame     || // Pour Internet Explorer
           function(callback){                   // Pour les élèves du dernier rang
               window.setTimeout(callback, 1000 / 60);
           };
})();

document.onkeydown = function(event) {
     var key_pressed; 
     if(event == null){
          key_pressed = window.event.keyCode; 
     }
     else {
          key_pressed = event.keyCode; 
     }
     switch(key_pressed){
          case 37:
               left=true;
               break; 
          case 38:
               up=true;
               break; 
          case 39:
               right=true;
               break;
          case 40:
               down=true;
               break; 
		  case 109:
               moin=true;
			   break;
		  case 107:
               plus=true;
			   break;	
     } 
}
 
document.onkeyup = function(event) {
     var key_pressed; 
     if(event == null){
          key_pressed = window.event.keyCode; 
     }
     else {
          key_pressed = event.keyCode; 
     }
     switch(key_pressed){
          case 37:
               left=false;
               break; 
          case 38:
               up=false;
               break; 
          case 39:
               right=false;
               break;
          case 40:
               down=false;
               break; 
		  case 109:
               moin=false;
			   break;
		  case 107:
               plus=false;
			   break;			   
		
     } 
}

var left=false;
var right=false;
var up=false;
var down=false;
var moin=false;
var plus=false;

window.onload = function() {
var scroll = {x: 0,
              y: 245};
function game(){
	var canvas = document.getElementById('canvas');
	var ctx = canvas.getContext('2d');	
	
	canvas.width  = 800;
	canvas.height = 600;
	
	if(left){
          scroll.x += 5;
     }
     if(right){
          scroll.x -= 5;
     }
     if(up){
          scroll.y += 5;
     }
     if(down){
          scroll.y -= 5;
     }
	 if(plus){
         tile.hdest += 3;
		 tile.ldest += 3;
     }
	 if(moin){
          tile.hdest -= 3;
		  tile.ldest -= 3;
     }
	
	map.dessinerMap(ctx, tile, scroll);
	window.requestAnimFrame(function(){game()});}
	game();
}
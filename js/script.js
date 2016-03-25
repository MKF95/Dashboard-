
function pie(ctx, w, h, datalist, colist){
  var radius = h / 2 - 5; 
  var centerx = w / 2;
  var centery = h / 2;
  var total = 0;
  
    for(x=0; x < datalist.length; x++) { total += datalist[x]; }; 
  var lastend=0;
  var offset = Math.PI / 2;
  for(x=0; x < datalist.length; x++){
      
    var thispart = datalist[x]; 
    ctx.beginPath();
    ctx.fillStyle = colist[x];
    ctx.moveTo(centerx,centery);
    var arcsector = Math.PI * (2 * thispart / total);
    ctx.arc(centerx, centery, radius, lastend - offset, lastend + arcsector - offset, false);
    ctx.lineTo(centerx, centery);
    ctx.fill();
    ctx.closePath();		
    lastend += arcsector;	
  }
}
            
function mon_cercle(datalist) {
var colist = new Array("#028eaf", "#5db56a", "#1e893f", "#ffbc00", "#e54b25", "#c01617");
var canvas = document.getElementById("id_canvas"); 
var ctx = canvas.getContext('2d');
pie(ctx, canvas.width, canvas.height, datalist, colist); 
    
}

function total_tickets(total_ticket){
    document.getElementById("id_ticket").innerHTML="Tickets ["+total_ticket+"]";
}
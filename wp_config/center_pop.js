<!--//
var x,y;
if (self.innerHeight)
{
	x = (screen.availWidth - self.innerWidth)/2;
	y = (screen.availHeight - self.innerHeight)/2;
}
else if (document.documentElement && document.documentElement.clientHeight)
{
	x = (screen.availWidth - document.documentElement.clientWidth)/2;
	y = (screen.availHeight - document.documentElement.clientHeight)/2;
}
else if (document.body)
{
	x = (screen.availWidth - document.body.clientWidth)/2;
	y = (screen.availHeight - document.body.clientHeight)/2;
}
window.moveTo(x,y);
//-->
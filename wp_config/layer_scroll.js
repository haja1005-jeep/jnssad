<!--//
var scroll_pixel, div_pixel, gtpos, gbpos, loop, moving_spd;

// 브라우저 상단과의 여백 Down //
var top_margin = 0;

// 브라우저 상단과의 여백 Up //
var top_margin2 = 0;

// 스크롤 속도 //
var speed = 20;

// setTimeout 속도 //
var speed2 = 15;

// 스크롤 로딩 on/off 설정(1:작동, 0:스톱 //
var moving_stat = 1;

// 레이어 스크롤 //
function Scroll_Move()
{
	scroll_pixel = document.body.scrollTop;
	gtpos = document.body.scrollTop+top_margin;
	gbpos = document.body.scrollTop+top_margin2;
	if(div_id.style.pixelTop < gtpos)
	{
		moving_spd = (gbpos-div_id.style.pixelTop)/speed;
		div_id.style.pixelTop += moving_spd;
	}
	if(div_id.style.pixelTop > gtpos)
	{
		moving_spd = (div_id.style.pixelTop-gtpos)/speed;
		div_id.style.pixelTop -= moving_spd;
	}
	loop = setTimeout("Scroll_Move()",speed2);
}

// 스크롤 컨트롤 //
function Scroll_Control()
{
	if(!moving_stat)
	{
		Scroll_Move(); moving_stat = 1;
	}
	else
	{
		clearTimeout(loop);
		moving_stat = 0;
		div_id.style.pixelTop = top_margin;
	}
}

// 스크롤 작동 //
Scroll_Move();
//-->
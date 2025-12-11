<!--//
var DP_PickerInput = null;

// HTML 이벤트 핸들러 //
function DP_PickerInput_blur()
{
	if(!DP_PickerHasMouse())
	{
		DP_DisablePicker();
	}
}

// Picker Callback //
function DP_GetPickedDate(date_str)
{
	if(DP_PickerInput != null)
		DP_PickerInput.value = date_str;
	DP_DisablePicker();
}

// Picker Method //
function DP_OpenPicker(input)
{
	var top = document.body.clientTop + DP_GetObjectTop(input);
	var left = document.body.clientLeft + DP_GetObjectLeft(input);
	DP_PickerInput = input;
	var DPContainer = document.all.DPContainer;
	DPContainer.style.pixelTop = top + input.offsetHeight;
	DPContainer.style.pixelLeft = left;
	DPContainer.style.display = '';
	DP_IFRAME.init(DP_ParseDate(DP_PickerInput.value));
}

function DP_DisablePicker()
{
	DP_PickerInput = null;
	var DPContainer = document.all.DPContainer;
	DPContainer.style.pixelTop = 0;
	DPContainer.style.pixelLeft = 0;
	DPContainer.style.display = 'none';
}

function DP_PickerHasMouse()
{
	var DPContainer = document.all.DPContainer;
	var top = document.body.clientTop + DP_GetObjectTop(DPContainer) - document.body.scrollTop;
	var left = document.body.clientLeft + DP_GetObjectLeft(DPContainer) - document.body.scrollLeft;
	var width = DPContainer.offsetWidth;
	var height = DPContainer.offsetHeight;
	return ((event.y >= top && event.y < top + height) && (event.x >= left && event.x < left + width))
}

function DP_InitPicker()
{
	document.write('<div id="DPContainer" style="position: absolute; display: none">');
	document.write('<iframe id="DP_IFRAME" width="100%" height="100%" src="../wp_object/calendar.html" scrolling="no" frameborder="no" border="0">');
	document.writeln('</iframe></div>');
	document.writeln('<script language="javascript" for="document" event="onclick">');
	document.writeln('var e = event.srcElement;');
	document.writeln('if (e.tagName == "INPUT" && e.className == "DateInput") {');
	document.writeln('	DP_OpenPicker(e);');
	document.writeln('}');
	document.writeln('</script>');
}

// HTML 개체용 유틸리티 함수 //
function DP_GetObjectTop(obj)
{
	if(obj.offsetParent == document.body)
	{
		return obj.offsetTop;
	}
	else
	{
		return obj.offsetTop + DP_GetObjectTop(obj.offsetParent);
	}
}

function DP_GetObjectLeft(obj)
{
	if(obj.offsetParent == document.body)
	{
		return obj.offsetLeft;
	}
	else
	{
		return obj.offsetLeft + DP_GetObjectLeft(obj.offsetParent);
	}
}

// DP_Calendar //
function DP_Calendar(year, month)
{
	if(year == null || month == null)
	{
		var d = new Date();
		this.year = d.getFullYear();
		this.month = d.getMonth();
	}
	else
	{
		this.year = year;
		this.month = month - 1;
	}
	this.first_week = new DP_Week(new Date(this.year, this.month, 1));
	this.last_date = new Date(this.year, this.month + 1, 1);
	this.next_week = this.first_week;
	this.reset = DP_Calendar_Reset;
	this.hasNextWeek = DP_Calendar_HasNextWeek;
	this.nextWeek = DP_Calendar_NextWeek;
}

function DP_Calendar_Reset()
{
	this.last_week = this.first_week;
}

function DP_Calendar_HasNextWeek()
{
	return DP_DateCompare(this.next_week.date, this.last_date) < 0;
}

function DP_Calendar_NextWeek()
{
	next_week = this.next_week;
	this.next_week = next_week.nextWeek();
	return next_week;
}

function DP_Week(date)
{
	this.hasNextDate = DP_Week_HasNextDate;
	this.nextDate = DP_Week_NextDate;
	this.nextWeek = DP_Week_NextWeek;
	this.getWeekFirstDate = DP_Week_GetWeekFirstDate;
	this.equals = DP_Week_Equals;
	this.compare = DP_Week_Compare;
	this.reset = DP_Week_Reset;

	this.index = 0;

	if(date == null)
	{
		date = new Date();
	}
	this.date = this.getWeekFirstDate(date);
}

function DP_Week_Reset()
{
	this.index = 0;
}

function DP_Week_GetWeekFirstDate(date)
{
	var d = new Date(date.valueOf());
	var wday = d.getDay();
	d.setDate(d.getDate() - wday);
	return d;
}

function DP_Week_HasNextDate()
{
	return (this.index < 7)
}

function DP_Week_NextDate()
{
	if(this.index >= 7)
	{
		return null;
	}
	var d = new Date(this.date.valueOf());
	d.setDate(d.getDate() + this.index);
	this.index ++;
	return d;
}

function DP_Week_NextWeek()
{
	var d = new Date(this.date.valueOf());
	d.setDate(d.getDate() + 7);
	return new DP_Week(d);
}

function DP_Week_Equals(week)
{
	return DP_DateEquals(this.date, week.date);
}

function DP_Week_Compare(week)
{
	return Date_Compare(this.date, week.date);
}

// 유틸리티 함수 //
function DP_FormatDate(d)
{
	var year = String(d.getFullYear());
	var month = String(d.getMonth() + 1);
	var mday = String(d.getDate());

	var s = year + "-";
	s += ((month.length < 2)? '0': '') + month;
	s += '-' + ((mday.length < 2)? '0': '') + mday;
	return s;
}

function DP_ParseDate(dstr)
{
	var str = String(dstr);
	if(str == null || str == '')
	{
		return null;
	}
	if(str.match(/(\d{4})-(\d{2})-(\d{2})/))
	{
		var year = Number(RegExp.$1);
		var month = Number(RegExp.$2) - 1;
		var mday = Number(RegExp.$3);
		return new Date(year, month, mday);
	}
	else
	{
		return null;
	}
}

function DP_DateEquals(d1, d2)
{
	return ((d1.getFullYear() == d2.getFullYear()) && (d1.getMonth() == d2.getMonth()) && (d1.getDate() == d2.getDate()))
}

function DP_DateCompare(d1, d2)
{
	if(d1.getFullYear() > d2.getFullYear())
	{
		return 1;
	}
	else if(d1.getFullYear() < d2.getFullYear())
	{
		return -1;
	}
	else if(d1.getMonth() > d2.getMonth())
	{
		return 1;
	}
	else if(d1.getMonth() < d2.getMonth())
	{
		return -1;
	}
	else if(d1.getDate() > d2.getDate())
	{
		return 1;
	}
	else if(d1.getDate() < d2.getDate())
	{
		return -1;
	}
	else
	{
		return 0;
	}
}
//-->
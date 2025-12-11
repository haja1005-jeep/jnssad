<!--//
//=================== 모든 사이트 공통사항 여기서부터 =======================
//=======================================================================

// 달력 출력 //
//DP_InitPicker();

//필수항목일 경우 배경색 설정
function setRequireBgColor(){
	var f_n = document.forms.length;
	if(f_n == 0) return;
	for(i=0; i < f_n; i++){
		var frm = document.forms[i];
		for (var k=0; k<frm.elements.length; k++) {
			var el = frm.elements[k];
			//필수항목 검색
			if(el.getAttribute("required") != null){
				el.style.background = "#FFEDFA";
				el.style.color="#0E24CB";
			}
		} //for
	} //for
}

//첫번째 form객체에 포커스 이동
function goFirstFocus(){
	var f_n = document.forms.length;
	if(f_n == 0) return;
	for(i=0; i < f_n; i++){
		var frm = document.forms[i];
		for (var k=0; k<frm.elements.length; k++) {
			var el = frm.elements[k];
			//tabindex ="1"인걸 검색
			var tabindex = el.getAttribute("tabindex");
			if(tabindex == 1){
				el.focus();
				return;
			}
		}//for
	}//for
}

//한글만 입력가능토록체크
function Input_Hangle()
{
	if(navigator.userAgent.indexOf("MSIE") != -1)
	{
		var keyCode = window.event.keyCode;
		if ( (keyCode < 12592) || (keyCode > 12687) )
		{
			event.returnValue=false;
		}
	}
	return;
}
//영어만 입력가능토록체크
function Input_English()
{

	if(navigator.userAgent.indexOf("MSIE") != -1)
	{
		var keyCode = window.event.keyCode;
		if ( (keyCode >= 65) && (keyCode <= 90) || (keyCode >= 97) && (keyCode <= 122) )
		{
			return;
		}else{
			event.returnValue=false;
		}
		event.returnValue=false;
	}
}

//영어와 숫자만만 입력가능토록체크
function Input_Eng_Num(obj)
{
	if(navigator.userAgent.indexOf("MSIE") != -1)
	{
		var keyCode = window.event.keyCode;
		if ( (keyCode >= 65) && (keyCode <= 90) || (keyCode >= 97) && (keyCode <= 122) )
		{
			return;
		}else if ( (keyCode >= 48) && (keyCode <= 57) ){
			return;
		}else{
			event.returnValue=false;
		}
		event.returnValue=false;
	}
	/*
	if ( obj.value == null ) return false ;
	if(!Check_Eng_Num(obj.value)){
		alert(obj.hname+'은(는) 영어와 숫자로만 입력해주세요!!');
		obj.focus();
	}
	*/
}
// 숫자만 입력가능 //
function Num_Check()
{
	// 숫자 입력 //
	if(navigator.userAgent.indexOf("MSIE") != -1)
	{
		var keyCode = window.event.keyCode;
		if ( (keyCode < 48) || (keyCode > 57) )
		{
			event.returnValue=false;
		}
	}
	return;
}


//영어로만 작성되어있는지 체크
function Check_EnglishOnly( englishChar ) {
	if ( englishChar == null ) return false ;
	for( var i=0; i < englishChar.length;i++){
		var c=englishChar.charCodeAt(i);
		if( !( (  0x61 <= c && c <= 0x7A ) || ( 0x41 <= c && c <= 0x5A ) ) ) {
			return false ;
		}
	}
	return true ;
}
//영어와숫자만로만되어있는지 체크
function Check_Eng_Num( str ) {
	if ( str == null ) return false ;

	for( var i=0; i < str.length;i++){
		var c=str.charCodeAt(i);
		if( !( ( 0x61 <= c && c <= 0x7A ) || ( 0x41 <= c && c <= 0x5A ) ) ) {
			if((c < 0x30 || c > 0x039 ))
				return false ;
		}
	}
	return true ;
}
// 숫자로만 되어있는지..체크 //
function Check_Number(NUM)
{
	for(var i=0 ; i < NUM.length ; i++)
	{
		achar = NUM.substring(i,i+1);
		if(achar < "0" || achar > "9")
		{
			return false;
		}
	}
	return true;
}
// 한글로만 되어있는지..체크 //
function Check_Hangul(str){
	for(i=0 ; i<str.length ; i++){
		if(!((str.charCodeAt(i) > 0x3130 && str.charCodeAt(i) < 0x318F) || (str.charCodeAt(i) >= 0xAC00 && str.charCodeAt(i) <= 0xD7A3))){
			return false;
		}
	}
}

// 자동 콤마 //
function Auto_Comma(frm,val)
{
	if(navigator.userAgent.indexOf("MSIE") != -1)
	{
		var keyCode = window.event.keyCode;
		if(((keyCode>=48) && (keyCode <= 105)) || (keyCode==8) || (keyCode==13) || (keyCode==35) || (keyCode==46))
		{
			// 0(48)~숫자키패드9(105), enter(13), bakspace(8), delete(46), end(35) key 일 때만 처리 //
			// 숫자만 가져옴 //
			var str = "" + Get_Number(val.value);
			if((str != null) && (str != "") && (str != "0"))
			{
				// 콤마삽입 //
				val.value = Add_Comma(str);
			}
			else
			{
				val.value = "0";
			}
		}
	}
	return;
}
// 숫자 얻음 //
function Get_Number(val)
{
	var str = ""+val;
	var temp = "";
	var num = "";
	for(var i=0; i<str.length; i++)
	{
		temp = str.charAt(i);
		if(temp >= "0" && temp <= "9")
		{
			num += temp;
		}
	}
	if((num != null) && (num != "") && (num != "0"))
	{
		// 십진수로 변환하여 리턴 //
		return parseInt(num,10);
	}
	else
	{
		return "0";
	}
}
// 콤마 추가 //
function Add_Comma(val)
{
	var num = val;
	if(num.length <= 3)
	{
		return num;
	}

	var loop = Math.ceil(num.length / 3);
	var offset = num.length % 3;
	if(offset==0)
	{
		offset = 3;
	}

	var str = num.substring(0, offset);
	for(i=1;i<loop;i++)
	{
		str += "," + num.substring(offset, offset+3);
		offset += 3;
	}
	return str;
}
// 날짜자동포맷설정 //
function Auto_date(obj)
{
	if(navigator.userAgent.indexOf("MSIE") != -1)
	{
		var keyCode = window.event.keyCode;
		if((keyCode==8) || (keyCode==13) || (keyCode==35) || (keyCode==46))
		{
			return;
		}//if
	}//if
	// 0(48)~숫자키패드9(105), enter(13), bakspace(8), delete(46), end(35) key 일 때만 처리 //
	var num = obj.value;
	num = num.replace(/[^0-9]/g,'');
	var len = num.length;
	var returnV = "";
	for(i=0;i<len;i++)
	{
		if(i == 3 || i == 5)
			returnV += ""+num.substring(i, i+1)+"-";
		else
			returnV += ""+num.substring(i, i+1);
	}
	obj.value=returnV;
}

//체크박스 전체 선택/비선택
var selectVal = true;
function selectAll(frm, objN){
	objN = objN+"[]";
	// 전체 선택/삭제
	selectVal = selectVal ? false : true;
	var cObj = frm.elements[objN];
	//체크할 checkbox가 없을때
	if(typeof(cObj) === "undefined"){
		return false;
	}
	var  len= cObj.length;
	//체크할 checkbox가 하나 있을때
	if(typeof(len) === "undefined"){
      		if(selectVal){
			cObj.checked = false;
			selectVal = true;
      		}else{
			cObj.checked = true;
			selectVal = false;
		}
	}else{
	   	for(var k=0; k < len; k++){
	      		if(selectVal){
				cObj[k].checked = false;
				selectVal = true;
	      		}else{
				cObj[k].checked = true;
				selectVal = false;
			}
		}
	}
}

// 레이어 체크 //
function MM_showHideLayers()
{
	var i,p,v,obj,args=MM_showHideLayers.arguments;
	for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null)
	{
		v=args[i+2];
		if (obj.style)
		{
			obj=obj.style;
			v=(v=='show')?'visible':(v='hide')?'hidden':v;
		}
		obj.visibility=v;
	}
}
// 레이어 검색 //
function MM_findObj(n, d)
{
	var p,i,x;
	if(!d) d=document;
	if((p=n.indexOf("?"))>0&&parent.frames.length)
	{
		d=parent.frames[n.substring(p+1)].document;
		n=n.substring(0,p);
	}
	if(!(x=d[n])&&d.all)
	{
		x=d.all[n];
	}
	for (i=0;!x&&i<d.forms.length;i++)
	{
		x=d.forms[i][n];
	}
	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	if(!x && d.getElementById) x=d.getElementById(n);
	return x;
}
//레이어 표시
function MM_showHideDisplay(obj){
	document.getElementById(obj).style.display = "block";
}
// 콤보박스 링크 //
function Select_Goto(link,target)
{
	var index = link.selectedIndex;
	if(link.options[index].value != '')
	{
		if(target == 'blank')
		{
			window.open(link.options[index].value, 'win1');
		}
		else
		{
			var frameobj;
			if(target == '')
			{
				targetr = 'self';
			}
			if((frameobj = eval(target)) != null)
			frameobj.location = link.options[index].value;
		}
	}
}


// 전체 선택 //
function All_Check(frm)
{
	for(var i = 0 ; i < frm.elements.length ; i++)
	{
		var check = frm.elements[i];
		check.checked = true;
	}
	return;
}

// 선택 해제 ///
function Un_Check(frm)
{
	for( var i = 0 ; i < frm.elements.length ; i++)
	{
		var check = frm.elements[i];
		check.checked = false;
	}
	return;
}

// 체크 선택 //
function Check_Check(frm)
{
	if(frm.chk_all.checked)
	{
		All_Check(frm);
	}
	else
	{
		Un_Check(frm);
	}
}

// 일괄 변경 //
function Check_Print(link_get)
{
	var form = document.signForm;
	var cnt = 0;
	var check_nums = document.signForm.elements.length;
	for(var i = 0 ; i < check_nums ; i++)
	{
		var checkbox_obj = eval("document.signForm.elements[" + i + "]");
		if(checkbox_obj.checked == true)
		{
			cnt++;
		}
	}
	if(cnt <= 0)
	{
		alert("1개 이상을 선택하여 주시겠습니까?");
		return false;
	}
	//window.open('excel.html','prize','width=690,height=975');
	signForm.action = "excel.html?" + link_get;
	signForm.submit();
}

//=================== 공통사항은 여기까지 =======================
//=======================================================================

//=================== 각사이트별로 추가사항은 여기서부터 =======================
//=======================================================================
/*
// 스포츠등급 검색 - 선수 등록시 사용
function Sports_Trouble(frm)
{
	var spo_code = frm.spo_code.value;
	var tro_code = frm.tro_code.value;
	/*
	if(spo_code == ""){
		alert('경기종목을 선택해주세요');
		frm.spo_code.focus();
		return false;
	}
	if(tro_code == ""){
		alert('장애유형을 선택해주세요');
		frm.tro_code.focus();
		return false;
	}
	*/
	//sports_level.src = "../wp_object/search_sports_trouble.html?spo_code=" + spo_code+"&tro_code="+tro_code;
//}


// 중분류 검색 //
/*
function Sports_Code(frm)
{
	var spo_code = frm.spo_code.value;
	var tro_code = frm.tro_code.value;
	/*
	if(spo_code == ""){
		alert('경기종목을 선택해주세요');
		frm.spo_code.focus();
		return false;
	}
	if(tro_code == ""){
		alert('장애유형을 선택해주세요');
		frm.tro_code.focus();
		return false;
	}
	*/
//	sports_level.src = "../wp_object/search_sports_code.html?spo_code=" + spo_code+"&tro_code="+tro_code;
//}


// 경기 종목에 해당하는 2차 경기 종목을  검색 //
function Sports_Sec_Code(frm)
{
	var spo_code = frm.spo_code.value;
	sports_sec_code.src = "../wp_object/search_sports_sec_code.html?spo_code=" + spo_code;
}

// 경기종목에 해당하는 장애유형 검색//
function Trouble_Code(frm)
{
	var spo_code = frm.spo_code.value;
	var spo_sec_code = frm.spo_sec_code.value;


	trouble_code.src = "../wp_object/search_trouble_code.html?spo_code=" + spo_code+"&spo_sec_code=" + spo_sec_code;
}

// 경기종목 ,장애유형,성별에 따른 스포츠 등급 검색과 세부종목 검색
function Sports_Detail3(frm,kind,display)
{
	var gam_uid = frm.gam_uid.value;
	var spo_code = frm.spo_code.value;
	var spo_sec_code = frm.spo_sec_code.value;
	var tro_code = frm.tro_code.value;
	var sex = frm.sex.value;
	//kind == 1 이면 스포츠 등급 검색
	if(kind == "1")
	{
		sports_code_detail1.src = "../wp_object/search_sports_detail_code2.html?gam_uid="+gam_uid+"&spo_sec_code=" + spo_sec_code +"&spo_code=" + spo_code + "&tro_code=" + tro_code + "&sex=" + sex + "&tro_level_code=" + tro_level_code+ "&kind=" + kind;
	}
	//kind == 2 이면 세부종목 검색
	var tro_level_code = frm.tro_level_code.value;
	if(kind == "2")
	{
		sports_code_detail2.src = "../wp_object/search_sports_detail_code2.html?gam_uid="+gam_uid+"&spo_sec_code=" + spo_sec_code+"&spo_code=" + spo_code + "&tro_code=" + tro_code + "&sex=" + sex + "&tro_level_code=" + tro_level_code+ "&kind=" + kind + "&display=" + display;
	}
}

// 다른 select박스를 초기화함.
function Init_Select(frm, obj1, obj2, obj3, obj4)
{
	if(obj1)
	{
		var obj = frm[obj1];
		obj[0].selected = true;
	}
	if(obj2)
	{
		var obj = frm[obj2];
		obj[0].selected = true;
	}
	if(obj3)
	{
		var obj = frm[obj3];
		obj[0].selected = true;
	}
	if(obj4)
	{
		var obj = frm[obj4];
		obj[0].selected = true;
	}
	/*
	if(obj1)
	{
		document.getElementById(obj1)[0].selected = true;
	}
	if(obj2)
	{
		document.getElementById(obj2)[0].selected = true;
	}
	if(obj3)
	{
		document.getElementById(obj3)[0].selected = true;
	}
	if(obj4)
	{
		document.getElementById(obj4)[0].selected = true;
	}
	*/
}

/*
// 스포츠 세부종목 검색 //
function Sports_Detail(frm,kind)
{
	var gam_uid = frm.gam_uid.value;
	var spo_code = frm.spo_code.value;
	var tro_code = frm.tro_code.value;
	var sex = frm.sex.value;

	if(kind == "1"){
		sports_code_detail1.src = "../wp_object/search_sports_detail_code.html?gam_uid="+gam_uid+"&spo_code=" + spo_code + "&tro_code=" + tro_code + "&sex=" + sex + "&tro_level_code=" + tro_level_code+ "&kind=" + kind;
	}
	var tro_level_code = frm.tro_level_code.value;
	if(kind == "2"){
		sports_code_detail2.src = "../wp_object/search_sports_detail_code.html?gam_uid="+gam_uid+"&spo_code=" + spo_code + "&tro_code=" + tro_code + "&sex=" + sex + "&tro_level_code=" + tro_level_code+ "&kind=" + kind;
	}
}
*/
//리로딩시 초기값셋팅 - 스포츠 상세 코드값셋팅
function preloadForm() {
	var frm  = document.sForm;
	Sports_Detail(frm,1);
	//코드값 셋팅
	setTimeout('setTro_level_code()',30);
	setTimeout('getSports_Detail()',50);
	//필수항목 배경색 설정
	setRequireBgColor();
	//커서이동
	goFirstFocus();
}
//리로딩시 초기값셋팅 - 스포츠 상세 코드값셋팅- 대회참가에서 사용
function preloadForm3() {
	var frm  = document.sForm;
	Sports_Sec_Code(frm,1);
	//경기2차종목 셋팅
	setTimeout('setSpo_sec_code()',50);
	//장애유형 셋팅
	setTimeout('getTrouble_Code()',50);
	//스포츠등급, 세부종목 셋팅
	setTimeout('getSports_Detail3()',100);
	//코드값 셋팅
	setTimeout('setTro_level_code()',180);
	setTimeout('ccc()',250);
	//필수항목 배경색 설정
	setRequireBgColor();
	//커서이동
	goFirstFocus();
}
function getSports_Detail3(){
	var frm  = document.sForm;
	Sports_Detail3(frm,1);
	//코드값 셋팅
	setTimeout('setSpo_code_detail()',30);
}
function getSports_Detail(){
	var frm  = document.sForm;
	Sports_Detail(frm,2);
	//코드값 셋팅
	setTimeout('setSpo_code_detail()',30);
}
function ccc(){
	var frm  = document.sForm;
	Sports_Detail3(frm,2);
	//코드값 셋팅
	setTimeout('setSpo_code_detail()',30);
}
function getTrouble_Code(){
	var frm  = document.sForm;
	Trouble_Code(frm);
	//코드값 셋팅
	setTimeout('setTro_Code()',30);
}
//리로딩시 초기값셋팅 - 스포츠코드값셋팅
function preloadForm2() {
	var frm  = document.sForm;
	Sports_Code(frm);
	//코드값 셋팅
	setTimeout('setTro_level_code()',300);
	//필수항목 배경색 설정
	setRequireBgColor();
	//커서이동
	goFirstFocus();
}

function setSpo_code_detail(){
	var frm  = document.sForm;
	var h_spo_code_detail = frm.h_spo_code_detail.value;
	var grabEl =  frm.spo_code_detail;
	for (var itemIndex = 0; itemIndex < grabEl.length; itemIndex++)
	{
		if (grabEl[itemIndex].value == h_spo_code_detail) {
			frm.spo_code_detail[itemIndex].selected = true;
		}
	}
}
function setTro_Code(){
	var frm  = document.sForm;
	var h_tro_code = frm.h_tro_code.value;
	var grabEl =  frm.tro_code;
	for (var itemIndex = 0; itemIndex < grabEl.length; itemIndex++)
	{
		if (grabEl[itemIndex].value == h_tro_code) {
			frm.tro_code[itemIndex].selected = true;
		}
	}
}
function setTro_level_code(){
	var frm  = document.sForm;
	var h_tro_level_code = frm.h_tro_level_code.value;
	var grabEl =  frm.tro_level_code;
	for (var itemIndex = 0; itemIndex < grabEl.length; itemIndex++)
	{
		if (grabEl[itemIndex].value == h_tro_level_code) {
			frm.tro_level_code[itemIndex].selected = true;
		}
	}
}
function setSpo_sec_code(){
	var frm  = document.sForm;
	var h_spo_sec_code = frm.h_spo_sec_code.value;
	var grabEl =  frm.spo_sec_code;
	for (var itemIndex = 0; itemIndex < grabEl.length; itemIndex++)
	{
		if (grabEl[itemIndex].value == h_spo_sec_code) {
			frm.spo_sec_code[itemIndex].selected = true;
		}
	}
}
//승인/삭제요청
function func_status(uid,code,str){
	if(confirm(str+"하시겠습니까?")){
		hidframe.location.href = "./status.php?code=" + code + "&uid=" + uid;
	}
	window.location.reload();
}

//대회관리 종목설정이벤트
function funcSetSports(frm){
	frm.action = "index.html?mode=input#list";
	frm.submit();
}

//경기실적입력하기
function funcRecordInsert(frm){
	//validation체크
	var bl = validate(frm);
	if(bl == false)
		return bl;
//	if(frm.type.value=="real")
//		frm.action = "input_r.php";
//	else frm.action = "input.php";
	frm.action = "input.php";
	frm.submit();
}
//대회실적 간단입력 리로딩
function getResult(val){
	location.href="index.html?mode=list&gam_uid=" + val+"#list";
}

//레포트 출력하기
function print_player(frm, objN){
	objN = objN+"[]";
	var cnt = 0;
	var cObj = frm.elements[objN];
	//체크할 checkbox가 없을때
	if(typeof(cObj) === "undefined"){
		cnt = 0;
	}
	var  len= cObj.length;
	//체크할 checkbox가 하나 있을때
	if(typeof(len) === "undefined"){
		if(cObj.checked == true) cnt++;
	}else{
	   	for(var k=0; k < len; k++){
			if(cObj[k].checked == true) cnt++;
		}
	}

	if(cnt < 1){
		alert('최소 한건 이상 선택해주세요 !!');
		return false;
	}
//	frm.target="_blank";
//	frm.action="excel.html?gam_uid="+frm.gam_uid.value;
//	frm.submit();
}
//레포트 출력하기
function print_player2(objN){
	var frm = document.sForm;
	objN = objN+"[]";
	var cnt = 0;
	var cObj = frm.elements[objN];
	var chk_ids = "";
	//체크할 checkbox가 없을때
	if(typeof(cObj) === "undefined"){
		cnt = 0;
	}
	var  len= cObj.length;
	//체크할 checkbox가 하나 있을때
	if(typeof(len) === "undefined"){
		if(cObj.checked == true){
			cnt++;
			chk_ids = cObj.value;
		}
	}else{
	   	for(var k=0; k < len; k++){
			if(cObj[k].checked == true){
				cnt++;
				chk_ids += cObj[k].value+"/";
			}
		}
	}

	if(cnt < 1){
		alert('최소 한건 이상 선택해주세요 !!');
		return false;
	}
	document.excelForm.chk_ids.value = chk_ids;
	document.excelForm.clu_code.value = document.sForm.clu_code.value;
	//frm.target="_blank";
	excelForm.submit();
}
//주민번호 입력후 생년월일자동채우기
function autoFillBirth(frm, str, num){
	// 2000년 이후 주민번호일시 //
	if(num == 2)
	{
		if(str.length == 1) // 첫번째자리판별 //
		{
			if(str == 3 || str == 4)
			{
				frm.birthday_y.value = "20"+frm.birthday_y.value.substr(2,2);
			}
		}
	}
	else
	{
		if(str.length < 3)
		{
			frm.birthday_y.value = "19"+str;
		}
		else if(str.length < 5)
		{
			frm.birthday_m.value = str.substr(2,2);
		}
		else
		{
			frm.birthday_d.value = str.substr(4,2);
		}
	}
}
//주민번호 입력후 성별 자동채우기
function autoFillSex(frm, obj){
	if(obj != "" && obj.length == 7){
		if (obj.substr(0,1) == "1" || obj.substr(0,1) == "3")
			frm.sex[0].checked = "true";
		else if (obj.substr(0,1) == "2" || obj.substr(0,1) == "4")
			frm.sex[1].checked = "true";
	}
}
//필수 항목 체크 처리
function chgFieldAttr(frm, obj){
	if(obj == "9"){ //선수이면
		//frm.tro_code.style.background = "#FFEDFA";
		//frm.tro_code.style.color="#0E24CB";
	}
	else if(obj == "6"||obj == "7"||obj == "8"){ //종목인솔, 감독, 코치자이면
		//frm.tro_code.style.background = "#fffff";
		//frm.trouble_level.style.background = "#fffff";
		//frm.tro_code.style.color="#000000";
	} else {
		//frm.tro_code.style.background = "#FFEDFA";
		//frm.tro_code.style.color="#0E24CB";
	}
}
//필수 항목 체크 처리
function chgFieldAttr2(frm, obj){
	if(obj == "9"){ //선수이면
		id_01_01.style.display = "";
		id_01_02.style.display = "";
		id_01_03.style.display = "";
		id_02_01.style.display = "none";
	}
	else if(obj == "6"||obj == "7"||obj == "8"){ //종목인솔, 감독, 코치자이면
		id_01_01.style.display = "none";
		id_01_02.style.display = "none";
		id_01_03.style.display = "none";
		id_02_01.style.display = "";
	} else {
		id_01_01.style.display = "none";
		id_01_02.style.display = "none";
		id_01_03.style.display = "none";
		id_02_01.style.display = "none";
	}
}
//선수정보 수정시 해당 항목들 리로드
function loadField(kind){
	var frm  = document.sForm;
	if(kind == "2"){
		chgFieldAttr2(frm, frm.kubun.value);
	}else{
		chgFieldAttr(frm, frm.kubun.value);
	}
}
//=================== 각사이트별로 추가사항은 여기까지 =======================
//=======================================================================

// 대진표 작성화면으로 이동
function Match_Input(frm)
{
	document.miForm.game_kind.value = frm.s_game_kind.value;
	document.miForm.game_code.value = frm.s_game_code.value;
	document.miForm.spo_sec_code.value = frm.spo_sec_code.value;
	document.miForm.submit();
}

// 경기실적입력을 위해 페이지를 리로딩한다.
function reloadTournament(frm,str)
{
	if(str == "0")
	{
		frm.sch_val.value = "";
		//frm.spo_sec_code[0].checked = true;
		frm.game_code[0].checked = true;
		//frm.level[0].checked = true;
	}
	else if(str == "1")
	{
		frm.sch_val.value = "";
		frm.game_code[0].checked = true;
		//frm.level[0].checked = true;
	}
	else if(str == "2")
	{
		frm.sch_val.value = "";
		//frm.level[0].checked = true;
	}
	else if(str == "3")
	{
		frm.sch_val.value = "sch";
	}
	frm.submit();
}

// 2차종목을 선택가능하도록 설정
function Show_SecCode(frm)
{
	if(frm.spo_code.value == "ATHIETICS")
	{
		eval("secDiv").style.display = "";
	}
	else
	{
		frm.spo_sec_code[0].selected = true;
		eval("secDiv").style.display = "none";
	}
}

// 상장을 입력한다.
function Insert_Prize(frm)
{
	frm.action = "input.php";
	frm.submit();
}

// 메달
function medal_close(str)
{
	if(str == "open")
	{
		for(i = 1; i <= 22; i++)
		{
			eval("b_"+i).style.display = "";
		}
	}
	else if(str == "close")
	{
		for(i = 1; i <= 22; i++)
		{
			eval("b_"+i).style.display = "none";
		}
	}
}

function Check_Print_2(link_get)
{
	var form = document.schForm;
	var cnt = 0;
	var check_nums = document.schForm.elements.length;
	for(var i = 0 ; i < check_nums ; i++)
	{
		var checkbox_obj = eval("document.schForm.elements[" + i + "]");
		if(checkbox_obj.checked == true)
		{
			cnt++;
		}
	}
	if(cnt <= 0)
	{
		alert("1개 이상을 선택하여 주시겠습니까?");
		return false;
	}
	//window.open('excel.html','prize','width=690,height=975');
	schForm.action = "excel.html?" + link_get;
	schForm.submit();
}

// 라디오 버튼 입력 박스 //
function Etc_Input(str)
{
	if(str == "1" || str == "2" || str == "3")
	{
		select_a.style.display = "none";
		select_b.style.display = "none";
		select_c.style.display = "none";
		select_d.style.display = "none";
		select_e.style.display = "none";
		select_f.style.display = "none";
	}
	if(str == "4")
	{
		select_a.style.display = "";
		select_b.style.display = "none";
		select_c.style.display = "none";
		select_d.style.display = "none";
		select_e.style.display = "none";
		select_f.style.display = "none";
	}
	if(str == "5")
	{
		select_a.style.display = "none";
		select_b.style.display = "";
		select_c.style.display = "";
		select_d.style.display = "";
		select_e.style.display = "";
		select_f.style.display = "";
	}
}


// 득점자 리스트 보기
function Score_List(gam_uid,clu_code,spo_code)
{
	var url = "pop_score_list.html?sc_gam_uid="+gam_uid+"&clu_code="+clu_code+"&spo_code="+spo_code;
	var sl = window.open(url,'sl','width=500,height=500,scrollbars=yes');
	sl.focus();
}
//-->
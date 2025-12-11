<!--//
//====================================================================
//========= 공통 체크 부분 시작
//======================================================================
//============  smartEditor를 사용하기 위한 공통 변수
var oEditors = [];

//============================= 자바스크립트 에러 메세지 출력함수 ============
onerror=handleErr;
var errTxt="";
function handleErr(msg,url,l)
{
	errTxt="처리중 다음과 같은 에러가 발생했습니다.\n\n";
	errTxt+="Error: " + msg + "\n";
	errTxt+="URL: " + url + "\n";
	errTxt+="Line: " + l + "\n\n";
	alert(errTxt);
	return false;
}
//=================  자바스크립트 에러메세지 출력 함수 여기까지 =====================

//입력필드 체크
function validate(frm){
	for (var i=0; i<frm.elements.length; i++) {
		var el = frm.elements[i];
		//form객체만을 체크
		if(el.type==null) continue;
		if(el instanceof Object) continue;

		try{
			//텍스트 에어리어 값 셋팅
			if(el.type.toLowerCase() == "textarea"){
				var attr = el.getAttribute("attr");
				if(attr == "editor"){
					try{
						oEditors.getById[el.name].exec("UPDATE_IR_FIELD", []);
					}catch(err){
						alert(err.description );
						return false;
					}
				}
			}
			//필수항목 체크
			if(el.getAttribute('required')!=null){
				if(el.type.toLowerCase() == "radio")
			{
				var elCheck = frm.elements[el.name];
				var elChecked = false;
				if(typeof elCheck.length != "undefined")
				{
					for(var j = 0 ; j < elCheck.length ; j++)
					{
						if(elCheck[j].checked == true)
						{
							elChecked = true;
						}
					}
				}
				else
				{
					if(elCheck.checked == true)
					{
						elChecked = true;
					}
				}
				if(elChecked == false)
				{
				 	alert(el.getAttribute("hname") + "을(를) 선택하여 주시겠습니까?");
				 	el.focus();
				 	return false;
				}
				else
				{
					if(elCheck[1].checked == true)
					{
						var otherCheck = "on";
					}
				}
			}else if(el.type.toLowerCase() == "textarea"){
					var attr = el.getAttribute("attr");
					if(attr == "editor"){
						 if(document.getElementById(el.name).value == "") {
						 	alert(el.hname+"은 필수입력입니다");
						 	return false;
						}
					}else{
						 if(el.value == "") {
						 	alert(el.hname+"은 필수입력입니다");
						 	el.focus();
						 	return false;
						}
					}
				}else{
					 if(el.value == "") {
					 	alert(el.hname+"은 필수입력입니다");
					 	el.focus();
					 	return false;
					}
				}
			}
			//비밀번호체크
			if(el.type.toLowerCase() == "password"){
				if(el.value.length != 0){
					//영어와숫자체크
					if(!Check_Eng_Num(el.value)){
						alert(el.hname+"의 비밀번호는 영문과 숫자로만 입력하여주세요!");
					 	el.focus();
					 	return false;
					}
					//자리수 체크
					if(!Check_Num(el.value)){
						alert(el.hname+"은(는) 5~12자리이내로 입력하여주세요!!");
					 	el.focus();
					 	return false;
					}
				}
			}
			//연관된 필드와 일치하는지 체크 (새 비밀번호)
			var matching = el.getAttribute('matching');
			if (matching != null && (el.value != frm.elements[matching].value)){
				alert(el.hname+"과 "+frm.elements[matching].hname+"이(가) 일치하지 않습니다.");
				el.focus();
				return false;

			}
			//메일체크
			var opt = el.getAttribute("opt");
			if(opt=="email"){
				if(el.value.length != 0){
					if(!Check_Email(el.value)){
						alert("메일주소 형식에 맞지 않습니다.");
						el.focus();
						return false;
					}
				}
			}
			//chksize를 설정하면 maxlength에 설정된 값과 일치하지 않으면 에러
			var chksize = el.getAttribute("chksize");
			if (chksize != null){
				if(el.value.length != 0){
					var maxlength = el.getAttribute("maxlength");
					if(el.value.length != maxlength){
						alert(el.hname+"은 "+maxlength+"자로 입력하여주세요!");
						el.focus();
						return false;
					}
				}
			}
			//주민번호체크
			if(opt=="jumin"){
				if(el.value.length != 0){
					var opt = el.getAttribute("with");
					var opt_v = frm.elements[opt].value;
					if(!Check_Jumin(opt_v+"-"+el.value)){
						alert("형식에 맞는 주민번호를 입력하여주세요!!");
						el.focus();
						return false;
					}
				}
			}
			//length에 설정된 범위의 값을 체크
			var range = el.getAttribute("range");
			if (range != null){
				if(el.value.length != 0){
					var len = range.split(":");
					var min = Number(len[0]);
					var max = Number(len[1]);
					if(el.value.length < min || el.value.length > max){
						alert(el.hname+"은 "+min+"자 ~ "+max+"자 이내로 입력하여주세요!");
						el.focus();
						return false;
					}
				}
			}
			//파일확장자 체크
			var chkfile = el.getAttribute("chkfile");
			if (chkfile != null){
				if(el.value.length != 0){
					var fname = el.value;
					var ext= fname.split(".");
					if((ext[1] == "" || ext[1] == null) || !Check_File_Ext(chkfile,ext[1])){
						alert(el.hname+"은(는) 업로드 불가능한 파일입니다. 다시 확인해주세요!");
						el.focus();
						return false;
					}
				}
			}

		}catch(err){
			alert(err.description );
			throw(err);
			return false;
		}
	}
//	if(errTxt != "")
		return true;
//	else return false;
}

// 자릿수 체크 //
function Check_Num(str)
{
	if(str.length < 5 || str.length > 30 )
	{
		return false;
	}
	for(var i = 0; i < str.length; i++)
	{
		var chr = str.substr(i,1);
		if((chr < '0' || chr > '9') && (chr < 'a' || chr > 'z'))
		{
			return false;
		}
	}
	return true;
}
//메일주소형식
function Check_Email(value) {
	var value = value ? value : el.value;
	var pattern = /^[_a-zA-Z0-9-\.]+@[\.a-zA-Z0-9-]+\.[a-zA-Z]+$/;
	return (pattern.test(value)) ? true : false;
}
//주민번호체크
function Check_Jumin(str) {
/*
	var pattern = /^([0-9]{6})-?([0-9]{7})$/;
    	if (!pattern.test(str)) return false;
    	num = RegExp.$1 + RegExp.$2;
	var sum = 0;
	var last = num.charCodeAt(12) - 0x30;
	var bases = "234567892345";
	for (var i=0; i<12; i++) {
		if (isNaN(num.substring(i,i+1))) return false;
		sum += (num.charCodeAt(i) - 0x30) * (bases.charCodeAt(i) - 0x30);
	}
	var mod = sum % 11;
	return ((11 - mod) % 10 == last) ? true : false;
*/
	/* 상위 계산방식에 걸리는 주민등록번호가 있을 경우에 아래와 같이 처리*/
	str = str.replace(/[^0-9]/g,'');
	for(var i=0 ; i < str.length ; i++)
	{
		achar = str.substring(i,i+1);
		if(achar < "0" || achar > "9")
		{
			return false;
		}
	}
	return true;

}
//입력가능한 확장자 체크
function Check_File_Ext(chkfile,ext){
	var img = new Array('gif','jpeg','jpg','png');
	var normal = new Array("doc","hwp","xls","txt","zip","ppt","pdf","alz","jpg","gif","psd");
	if(chkfile == "img"){
		for (idx in img){
			if(img[idx]==ext.toLowerCase()) return true;
		}
	}
	if(chkfile == "normal"){
		for (idx in normal){
			if(normal[idx]==ext.toLowerCase()) return true;
		}
	}
	return false;
}

//====================================================================
//========= 공통 체크 부분 끝
//======================================================================


//====================================================================
//========= 각 사이트별 또는 각 폼 별 개별적인 체크는 여기 이하에 추가 작성
//======================================================================
//선수등록정보 체크
function chkPlayerData(frm){
	if(frm.clu_code.value == ""){
		alert(frm.clu_code.hname+'은 필수입니다.');
		frm.clu_code.focus();
		return false;
	}

	if(frm.kubun.value == ""){
		alert(frm.kubun.hname+'은 필수입니다.');
		frm.kubun.focus();
		return false;
	}
	if(frm.kubun.value == "9"){ //선수이면
		if(frm.tro_code.value == ""){
			alert(frm.tro_code.hname+'은 필수입니다.');
			frm.tro_code.focus();
			return false;
		}
		if(frm.trouble_level.value == ""){
			alert(frm.trouble_level.hname+'은 필수입니다.');
			frm.trouble_level.focus();
			return false;
		}
	}
	if(frm.name.value == ""){
		alert(frm.name.hname+'은 필수입니다.');
		frm.name.focus();
		return false;
	}
	if(frm.jumin1.value == ""){
		alert(frm.jumin1.hname+'은 필수입니다.');
		frm.jumin1.focus();
		return false;
	}
	if(frm.jumin1.value.length != 6){
		alert(frm.jumin1.hname+'앞자리는 6자리로 입력하여주세요!.');
		frm.jumin1.focus();
		return false;
	}
	if(frm.jumin2.value == ""){
		alert(frm.jumin2.hname+'은 필수입니다.');
		frm.jumin2.focus();
		return false;
	}
	if(frm.jumin2.value.length != 7){
		alert(frm.jumin2.hname+'뒷자리는 7자리로 입력하여주세요!.');
		frm.jumin2.focus();
		return false;
	}
	if(frm.birthday_y.value == ""){
		alert(frm.birthday_y.hname+'은 필수입니다.');
		frm.birthday_y.focus();
		return false;
	}
	if(frm.birthday_m.value == ""){
		alert(frm.birthday_m.hname+'은 필수입니다.');
		frm.birthday_m.focus();
		return false;
	}
	if(frm.birthday_d.value == ""){
		alert(frm.birthday_d.hname+'은 필수입니다.');
		frm.birthday_d.focus();
		return false;
	}

	if(frm.picture.value == ""){
		alert(frm.picture.hname+'은 필수입니다.');
		frm.picture.focus();
		return false;
	}
	//파일확장자 체크
	var chkfile = frm.picture.getAttribute("chkfile");
	var fname = frm.picture.value;
	var ext= fname.split(".");
	if((ext[1] == "" || ext[1] == null) || !Check_File_Ext(chkfile,ext[1])){
		alert(frm.picture.hname+"은(는) 업로드 불가능한 파일입니다. 다시 확인해주세요!");
		frm.picture.focus();
		return false;
	}
	if(frm.kubun.value == "9" && frm.tro_code.value != "NOTROU"){ //선수이면 복지카드 필수
		if(frm.welfare_card.value == ""){
			alert(frm.welfare_card.hname+'은 필수입니다.');
			frm.welfare_card.focus();
			return false;
		}

		//파일확장자 체크
		var chkfile = frm.welfare_card.getAttribute("chkfile");
		var fname = frm.welfare_card.value;
		var ext= fname.split(".");
		if((ext[1] == "" || ext[1] == null) || !Check_File_Ext(chkfile,ext[1])){
			alert(frm.welfare_card.hname+"은(는) 업로드 불가능한 파일입니다. 다시 확인해주세요!");
			frm.welfare_card.focus();
			return false;
		}
	}
	if(frm.check_name.value=="0"){
		alert('실명인증을 거치지 않았습니다. 성명,주민번호를 입력후 실명인증을 하여주십시요.');
		frm.ck_name.focus();
		return false;
	}
	return true;
}
//선수정보변경 체크
function modPlayerData(frm){
	if(frm.clu_code.value == ""){
		alert(frm.clu_code.hname+'은 필수입니다.');
		frm.clu_code.focus();
		return false;
	}

	if(frm.kubun.value == ""){
		alert(frm.kubun.hname+'은 필수입니다.');
		frm.kubun.focus();
		return false;
	}
	if(frm.kubun.value == "9"){ //선수이면
		if(frm.tro_code.value == ""){
			alert(frm.tro_code.hname+'은 필수입니다.');
			frm.tro_code.focus();
			return false;
		}
		if(frm.trouble_level.value == ""){
			alert(frm.trouble_level.hname+'은 필수입니다.');
			frm.trouble_level.focus();
			return false;
		}
	}
	if(frm.name.value == ""){
		alert(frm.name.hname+'은 필수입니다.');
		frm.name.focus();
		return false;
	}
	if(frm.jumin1.value == ""){
		alert(frm.jumin1.hname+'은 필수입니다.');
		frm.jumin1.focus();
		return false;
	}
	if(frm.jumin1.value.length != 6){
		alert(frm.jumin1.hname+'앞자리는 6자리로 입력하여주세요!.');
		frm.jumin1.focus();
		return false;
	}
	if(frm.jumin2.value == ""){
		alert(frm.jumin2.hname+'은 필수입니다.');
		frm.jumin2.focus();
		return false;
	}
	if(frm.jumin2.value.length != 7){
		alert(frm.jumin2.hname+'뒷자리는 7자리로 입력하여주세요!.');
		frm.jumin2.focus();
		return false;
	}
	if(frm.birthday_y.value == ""){
		alert(frm.birthday_y.hname+'은 필수입니다.');
		frm.birthday_y.focus();
		return false;
	}
	if(frm.birthday_m.value == ""){
		alert(frm.birthday_m.hname+'은 필수입니다.');
		frm.birthday_m.focus();
		return false;
	}
	if(frm.birthday_d.value == ""){
		alert(frm.birthday_d.hname+'은 필수입니다.');
		frm.birthday_d.focus();
		return false;
	}
	if(frm.tel1.value == ""){
		alert(frm.tel1.hname+'은 필수입니다.');
		frm.tel1.focus();
		return false;
	}
	if(frm.tel2.value == ""){
		alert(frm.tel2.hname+'은 필수입니다.');
		frm.tel2.focus();
		return false;
	}
	if(frm.tel3.value == ""){
		alert(frm.tel3.hname+'은 필수입니다.');
		frm.tel3.focus();
		return false;
	}
	if(frm.zip1.value == ""){
		alert(frm.zip1.hname+'은 필수입니다.');
		frm.zip1.focus();
		return false;
	}
	if(frm.zip2.value == ""){
		alert(frm.zip2.hname+'은 필수입니다.');
		frm.zip2.focus();
		return false;
	}
	if(frm.address1.value == ""){
		alert(frm.address1.hname+'은 필수입니다.');
		frm.address1.focus();
		return false;
	}
	if(frm.address2.value == ""){
		alert(frm.address2.hname+'은 필수입니다.');
		frm.address2.focus();
		return false;
	}

	if(frm.picture.value != ""){
		//파일확장자 체크
		var chkfile = frm.picture.getAttribute("chkfile");
		var fname = frm.picture.value;
		var ext= fname.split(".");
		if((ext[1] == "" || ext[1] == null) || !Check_File_Ext(chkfile,ext[1])){
			alert(frm.picture.hname+"은(는) 업로드 불가능한 파일입니다. 다시 확인해주세요!");
			frm.picture.focus();
			return false;
		}
	}

	if(frm.welfare_card.value != ""){
		//파일확장자 체크
		var chkfile = frm.welfare_card.getAttribute("chkfile");
		var fname = frm.welfare_card.value;
		var ext= fname.split(".");
		if((ext[1] == "" || ext[1] == null) || !Check_File_Ext(chkfile,ext[1])){
			alert(frm.welfare_card.hname+"은(는) 업로드 불가능한 파일입니다. 다시 확인해주세요!");
			frm.welfare_card.focus();
			return false;
		}
	}
	return true;
}

//대회참가시 체크
function Check_GPlayer(frm)
{
	if(frm.kubun.value == "9")
	{
		if(frm.is_welfare.value != "1")
		{
			if(frm.p_tro.value != "NOTROU")
			{
				alert("대회 참가시 선수는 복지카드를 먼저 입력하여주세요.!");
				return false;
			}
		}
		if((frm.spo_code.value == "BADMINTON" && frm.tro_code.value == "IWAS")
		||(frm.spo_code.value == "BOCCIA" && frm.tro_code.value == "CP-ISRA")
		||(frm.spo_code.value == "SWIMMING" && (frm.tro_code.value == "CP-ISRA" || frm.tro_code.value == "IWAS" || frm.tro_code.value == "IWAS_2"))
		||(frm.spo_code.value == "ATHIETICS" && (frm.tro_code.value == "CP-ISRA" || frm.tro_code.value == "IWAS" || frm.tro_code.value == "IWAS_2"))
		||(frm.spo_code.value == "VOLLEYBALL" && frm.tro_code.value == "IWAS")
		||(frm.spo_code.value == "TABLETENNIS" && (frm.tro_code.value == "CP-ISRA" || frm.tro_code.value == "IWAS" || frm.tro_code.value == "IWAS_2")))
		{
			if(frm.is_sport_card.value != "1")
			{
				alert("등급분류카드를 먼저 입력하여주세요.!");
				return false;
			}
		}
	}
}

//대회 참가 선수 체크
function chkPlayer(frm){
	var cnt = 0;
	var cObj = frm.elements['pla_id[]'];
	//체크할 checkbox가 없을때
	if(typeof(cObj) === "undefined"){
		return false;
	}
	var  len= cObj.length;
	//체크할 checkbox가 하나 있을때
	if(typeof(len) === "undefined"){
		if(cObj.checked == true){
			cnt++;
		}
	}else{
		for(var k = 0 ; k < len ; k++)
		{
			if(cObj[k].checked == true){
				cnt++;
			}
		}
	}
	if(cnt <1) {
		alert("최소 한명 이상의 선수를 선택해주세요!!");
		if(typeof(len) === "undefined")
			cObj.focus();
		else	cObj[0].focus();
		return false;
	}
	return true;
}
/*
//기타 개별적인 체크
function Check_Etc(frm){
	var cnt = 0;
	var cObj = frm.elements['lang[]'];
	var  len= cObj.length;
	for(var k = 0 ; k < len ; k++)
	{
		if(cObj[k].checked == true){
			cnt++;
		}
	}
	if(cnt <2) {
		alert("합동체크는 최소 두개이상 선택해주세요!!");
		cObj[0].focus();
		return false;
	}
	return true;
}
//대회신청전 입력필드 체크하기
function funcChkField(frm){
	if(frm.gam_uid.value == ""){
		alert(frm.gam_uid.hname+'은 필수입니다.');
		frm.gam_uid.focus();
		return false;
	}
	if(frm.kubun.value == ""){
		alert(frm.kubun.hname+'은 필수입니다.');
		frm.kubun.focus();
		return false;
	}
	if(frm.kubun.value == "9"){ //선수이면
		if(frm.spo_code.value == ""){
			alert(frm.spo_code.hname+'은 필수입니다.');
			frm.spo_code.focus();
			return false;
		}
		if(frm.tro_code.value == ""){
			alert(frm.tro_code.hname+'은 필수입니다.');
			frm.tro_code.focus();
			return false;
		}
		if(frm.sex.value == ""){
			alert(frm.sex.hname+'은 필수입니다.');
			frm.sex.focus();
			return false;
		}
		if(frm.tro_level_code.value == ""){
			alert(frm.tro_level_code.hname+'은 필수입니다.');
			frm.tro_level_code.focus();
			return false;
		}
		if(frm.spo_code_detail.value == ""){
			alert(frm.spo_code_detail.hname+'은 필수입니다.');
			frm.spo_code_detail.focus();
			return false;
		}
	}
	if(frm.kubun.value == "6"||frm.kubun.value == "7"||frm.kubun.value == "8"){ //종목인솔자이면
		if(frm.spo_code1.value == ""){
			alert(frm.spo_code1.hname+'은 필수입니다.');
			frm.spo_code1.focus();
			return false;
		}
	}
}
//대회 참가하기
function funcGameInsert(frm){
	//validation체크
	var bl = funcChkField(frm);
	if(bl == false)
		return bl;
	//등록할 선수 체크
	bl = chkPlayer(frm)
	if(bl == false)
		return bl;
	frm.action = "input.php";
	frm.submit();
}
*/
//대회에서 삭제하기
function funcPlayerDelete(frm){
	//validation체크
	var bl = validate(frm);
	if(bl == false)
		return bl;
	//등록할 선수 체크
	bl = chkPlayer(frm)
	if(bl == false)
		return bl;
	frm.action = "delete.php";
	frm.submit();
}

//대진표에 입력하기
function funcGameEach(frm){
	//validation체크
	var bl = validate(frm);
	if(bl == false)
		return bl;
	//등록할 선수 체크
	bl = chkPlayer(frm)
	if(bl == false)
		return bl;
	frm.action = "input.php";
	frm.submit();
}
/*
//대회에서 일괄수정하기
function funcPlayerModify(frm){
	//validation체크
	var bl = validate(frm);
	if(bl == false)
		return bl;
	frm.action = "modify.php";
	frm.submit();
}
*/
//실명인증 체크
function ncCheck(frm){
	if(frm.name.value == ""){
		alert(frm.name.hname+'은 필수입니다.');
		frm.name.focus();
		return false;
	}
	if(frm.jumin1.value == ""){
		alert(frm.jumin1.hname+'은 필수입니다.');
		frm.jumin1.focus();
		return false;
	}
	if(frm.jumin1.value.length != 6){
		alert(frm.jumin1.hname+'앞자리는 6자리로 입력하여주세요!.');
		frm.jumin1.focus();
		return false;
	}
	if(frm.jumin2.value == ""){
		alert(frm.jumin2.hname+'은 필수입니다.');
		frm.jumin2.focus();
		return false;
	}
	if(frm.jumin2.value.length != 7){
		alert(frm.jumin2.hname+'뒷자리는 7자리로 입력하여주세요!.');
		frm.jumin2.focus();
		return false;
	}
	var name = frm.name.value;
	var jumin1 = frm.jumin1.value;
	var jumin2 = frm.jumin2.value;
	var URL = "../wp_object/nc_check.php?name=" + name+ "&jumin1=" + jumin1 + "&jumin2="+jumin2;
	var status = 'toolbar=no,directories=no,scrollbars=no,resizable=no,status=no,menubar=no, width= 300, height= 300, top=100,left=100';
	window.open(URL,'_blank',status);
}

// 상장관리 등록체크 //
/*
function chkPrize01(frm)
{
	if(frm.clu_code.value == "")
	{
		alert( '시/군 선택은 필수입니다.');
		frm.clu_code.focus();
		return false;
	}
	if(frm.rank.value == "")
	{
		alert( '순위 선택은 필수입니다.');
		frm.rank.focus();
		return false;
	}
}
function chkPrize02(frm)
{
	if(frm.clu_code.value == "")
	{
		alert( '시/군 선택은 필수입니다.');
		frm.clu_code.focus();
		return false;
	}
}
function chkPrize03(frm)
{
	if(frm.clu_code.value == "")
	{
		alert( '시/군 선택은 필수입니다.');
		frm.clu_code.focus();
		return false;
	}
}
function chkPrize04(frm)
{
	if(frm.clu_code.value == "")
	{
		alert( '시/군 선택은 필수입니다.');
		frm.clu_code.focus();
		return false;
	}
}
function chkPrize05(frm)
{
	if(frm.clu_code.value == "")
	{
		alert( '시/군 선택은 필수입니다.');
		frm.clu_code.focus();
		return false;
	}
	if(frm.spo_code.value == "")
	{
		alert( '종목 선택은  필수입니다.');
		frm.spo_code.focus();
		return false;
	}
	if(frm.spo_name.value == "")
	{
		alert( '세부종목 입력은  필수입니다.');
		frm.spo_name.focus();
		return false;
	}
	if(frm.rank.value == "")
	{
		alert( '순위 선택은 필수입니다.');
		frm.rank.focus();
		return false;
	}
	if(frm.prz_pla_name.value == "")
	{
		alert( '참가선수 입력은 필수입니다.');
		frm.prz_pla_name.focus();
		return false;
	}
}
function chkPrize06(frm)
{
	if(frm.clu_code.value == "")
	{
		alert( '시/군 선택은 필수입니다.');
		frm.clu_code.focus();
		return false;
	}
	if(frm.prz_pla_name.value == "")
	{
		alert( '참가선수 입력은 필수입니다.');
		frm.prz_pla_name.focus();
		return false;
	}
}
function chkPrize07(frm)
{
	if(frm.clu_code.value == "")
	{
		alert( '시/군 선택은 필수입니다.');
		frm.clu_code.focus();
		return false;
	}
	if(frm.spo_code.value == "")
	{
		alert( '종목 선택은  필수입니다.');
		frm.spo_code.focus();
		return false;
	}
	if(frm.spo_name.value == "")
	{
		alert( '세부종목 입력은  필수입니다.');
		frm.spo_name.focus();
		return false;
	}
	if(frm.rank.value == "")
	{
		alert( '순위 선택은 필수입니다.');
		frm.rank.focus();
		return false;
	}
	if(frm.prz_pla_name.value == "")
	{
		alert( '참가선수 입력은 필수입니다.');
		frm.prz_pla_name.focus();
		return false;
	}
}
*/

// 입력 전송 //
function Frm_Submit(frm)
{
	var frm_check = validate(document.sForm);
	if(frm_check == false)
	{
		// validate 체크 오류 발생시 오류 반환 //
		return false;
	}
	else
	{
		document.sForm.submit();
	}
}
//-->


/* ID 카드출력  2017.05.07 **************************************************************/

function funcIdCardPrint(frm){
	
    var cnt = 0;
	var cObj = frm.elements['pla_id[]'];
    var pla_idArray = new Array();


	//체크할 checkbox가 없을때
	if(typeof(cObj) === "undefined"){
		return false;
	}
	var  len= cObj.length;

	//체크할 checkbox가 하나 있을때
	if(typeof(len) === "undefined"){
		if(cObj.checked == true){
			cnt++;
		}
	}else{
		for(var k = 0 ; k < len ; k++)
		{
			if(cObj[k].checked == true){
				//pla_idArray[k] = cObj[k].value;
				//alert(pla_idArray[k]);
				cnt++;
			}
		}
	}
	if(cnt <1) {
		alert("최소 한명 이상의 선수를 선택해주세요!!");
		if(typeof(len) === "undefined")
			cObj.focus();
		else	cObj[0].focus();
		return false;
	}
	
	
   	var URL = "idcard_print_view.html";
	var status = 'toolbar=no,directories=no,scrollbars=yes,resizable=no,status=no,menubar=no, width= 900, height= 840, top=0,left=0';

	window.open('', "idcard_print", status);

	frm.action = URL;
	frm.target='idcard_print';
	frm.submit();

	
}
<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($kubun);

// 주민번호체크 //
//Jumin_Check($jumin1, $jumin2);

// 만 14세 체크 //
$player_birthday_date = mktime(0, 0, 0, $birthday_m, $birthday_d, $birthday_y);		// 생년월일
$player_game_date = mktime(23, 59, 59, 1, 1, 2005);		// 대회참가신청(2001년 03월 06일 이전) 2004년생 15세, 2003년생 참가신청 가능 2018->2004?
if($player_birthday_date >= $player_game_date)
{
	echo ("<script>
		alert('만 14세 이상만 참가 할 수 있습니다.');
		history.back(-1);
		</script>
		");
}

//같은 주민번호로 이미 등록된 선수가 있으면 오류
$que = "SELECT * FROM wp_player_ctrl WHERE isuse='0' AND jumin1='$jumin1' AND jumin2='$jumin2'";
$res = mysql_query($que);
if(!$res){
	Error("QUERY_ERROR");
	exit;
}
if(mysql_num_rows($res) > 0){
	Error("ALREADY_INPUT_ERROR");
	exit;
}

$f_field = array(); //첨부파일 필드명
$f_chg_field = array(); //변환파일 필드명
$r_name = array(); //첨부파일 실제파일명
$c_name = array(); //첨부파일 변환파일명
foreach ($_FILES as $idx => $val) {
	$error = $_FILES[$idx]["error"];
	if ($error == UPLOAD_ERR_OK) {
		unset($Upload);
		unset($file);
		unset($file_size);
		unset($file_name);
		//파일생성
		$file = $_FILES[$idx]["tmp_name"];
		$file_size = $_FILES[$idx]["size"];
		$file_name = $_FILES[$idx]["name"];
		//업로드 클래스 생성
		$Upload = new Upload;
		// 초기화 //
		$Upload->Init($file,$photo_path,$file_size,$file_name);

		// 용량 체크 //
		if(!$Upload->LimitSize($upload_file_size_8M))
		{
			Error("SIZE_ERROR","8");
			exit;
		}

		// 확장자 체크 //
		if(!$Upload->LimitExp("img"))
		{
			Error("UPLOAD_ERROR");
			exit;
		}

		// 파일명 //
		$sName = $Upload->GetName();

		// 파일 저장 //
		$Upload->FileSave($sName);

		// 저장 경로 //
		$img_path = $photo_path.$sName;
		// 이미지 크기 변환 //
		$save_name = "c_".$sName;
		if($idx == "picture"){ //증명사진이면
			$max_width = 120;
			$max_height = 160;
		}
		if($idx == "welfare_card"){ //복 지 카 드이면
			$max_width = 335;
			$max_height = 217;
		}
		if($idx == "sport_card"){ //등 급 카 드이면
			$max_width = 640;
			$max_height = 1021;
		}
		// 썸네일 //
		Thumnail($img_path,$save_name,$photo_path,$max_width,$max_height);


		//파일내용 배열에 등록
		array_push($f_field,$idx);
		array_push($f_chg_field,$idx."_chg");
		array_push($r_name,$file_name);
		array_push($c_name,$sName);
	}

}
// 비밀번호 설정 //
$user_pw = Encrypt($jumin2);
//아이디
$wp_id=$jumin1.$jumin2;
//생일
$birthday = date("Y-m-d", mktime(0,0,0,$birthday_m,$birthday_d,$birthday_y));
//결혼기념일
if($mar_date_y && $mar_date_m && $mar_date_d)
	$mar_date = date("Y-m-d", mktime(0,0,0,$mar_date_m,$mar_date_d,$mar_date_y));
//선수번호 : 시/도코드 + 주민번호
$player_no = $clu_code.$jumin1.$jumin2;
//선수코드가
if($kubun == $wp_kubun_종목인솔자 || $kubun == $wp_kubun_감독 || $kubun == $wp_kubun_코치){//감독/코치/인솔자
	$spo_code = $spo_code;
	$tro_code = $tro_code;
	$trouble_level = $trouble_level;
	$tro_level_code = "";
	$wheel = "1";
} else if($kubun != $wp_kubun_선수){//선수가 아니면
	$spo_code = "";
	$tro_code = "";
	$trouble_level = "";
	$tro_level_code = "";
	$wheel = "1";
}

//등록일
$year = date("Y");

// TB 필드명 //
$fields = array("wp_id","wp_passwd","name","kubun","wheel","trouble_level","name_hanja","name_eng","jumin1","jumin2","birthday","sex","married",
			"mar_date","player_no","isplayer","year","clu_code","tea_code","tro_code",
			"tall","blood","weight","eye1","eye2","tel1","tel2","tel3","hp1","hp2","hp3","email","zip1","zip2",
			"address1","address2","orig_address","reason","bigo1","bigo2","motive","special","friends",
			"tro_detail","equip","sponsor","history","signdate","moddate");

// TB 필드값 //
$values = array("$wp_id","$user_pw","$name","$kubun","$wheel","$trouble_level","$name_hanja","$name_eng","$jumin1","$jumin2","$birthday","$sex","$married",
			"$mar_date","$player_no","0","$year","$clu_code","$tea_code","$tro_code",
			"$tall","$blood","$weight","$eye1","$eye2","$tel1","$tel2","$tel3","$hp1","$hp2","$hp3","$email","$zip1","$zip2",
			"$address1","$address2","$orig_address","$reason","$bigo1","$bigo2","$motive","$special","$friends",
			"$tro_detail","$equip","$sponsor","$history","$signdate","");

//첨부파일 입력
for($i=0;$i<count($f_field); $i++){
	array_push($fields,$f_field[$i]);
	array_push($fields,$f_chg_field[$i]);
	array_push($values,$r_name[$i]);
	array_push($values,$c_name[$i]);
}
// DB 저장 //
$insert = $Mysql->Insert(wp_player_ctrl,$fields,$values);

// 페이지 이동 //
if($type == "pop"){
	echo ("<script>
		alert('정상적으로 선수등록이 완료되었습니다. 대회참가 신청을 하여주세요');
		opener.location.href='../wp_game_player/index.html?mode=input&pla_id=$wp_id';
		self.close();
		</script>
		");
}else{
	echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
}
?>
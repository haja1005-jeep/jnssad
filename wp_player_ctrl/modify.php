<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($kubun);

// 주민번호체크 //
//Jumin_Check($jumin1, $jumin2);

//해당 시/군 클럽 소속의 선수인지 체크
$query = "SELECT * FROM wp_player_ctrl WHERE uid = '$uid'";
$result = mysql_query($query);
if (!$result) {
   	Error("DB_ERROR");
   	exit;
}
if(mysql_num_rows($result) <= 0){
   	Error("NOT_DATA");
	exit;
}
$row = mysql_fetch_object($result);

if($Admin_auth != "top" && $Admin_code != $row->clu_code){
	Error("AUTH_ERROR");
	exit;
}


//체크 선택한 파일 삭제
$d_field = array(); //삭제할 필드
$d_value = array();//삭제할 데이터
for($i = 0; $i < count($file_change); $i++) {
	$del_file = split(":",$file_change[$i]);
	array_push($d_field,$del_file[0]);
	array_push($d_field,$del_file[0]."_chg");
	array_push($d_value,"");
	array_push($d_value,"");
	$d_f = $photo_path.$del_file[1];
	$d_f2 = $photo_path."c_".$del_file[1];
	unlink($d_f);
	unlink($d_f2);
}
if(count($file_change) > 0){
	$insert = $Mysql->Update(wp_player_ctrl,$d_field,$d_value," where uid='$uid'");
}

//첨부된 파일 등록
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

		//기존등록된 파일 삭제
		unset($is_del);
		$is_del = $idx."_n";

		if($$is_del != "n"){  //새로등록이 아닌경우만 삭제
			$d_file = $$is_del;
			$d_f = $photo_path.$d_file;
			$d_f2 = $photo_path."c_".$d_file;
			unlink($d_f);
			unlink($d_f2);
		}
		//기존 등록된 파일 삭제 끝..

		// 저장 경로 //
		$img_path = $photo_path.$sName;
		// 이미지 크기 변환 //
		$save_name = "c_".$sName;
		if($idx == "picture"){ //증명사진이면
			$max_width = 118;
			$max_height = 157;
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

//echo $tro_level_code.$uid;
//echo $tro_code;
//exit;
// TB 필드명 //
$fields = array("wp_id","wp_passwd","name","kubun","wheel","trouble_level","name_hanja","name_eng","jumin1","jumin2","birthday","sex","married",
			"mar_date","player_no","isplayer","clu_code","tea_code","tro_code",
			"tall","blood","weight","eye1","eye2","tel1","tel2","tel3","hp1","hp2","hp3","email","zip1","zip2",
			"address1","address2","orig_address","reason","bigo1","bigo2","motive","special","friends",
			"tro_detail","equip","sponsor","history","isuse","moddate");

// TB 필드값 //
$values = array("$wp_id","$user_pw","$name","$kubun","$wheel","$trouble_level","$name_hanja","$name_eng","$jumin1","$jumin2","$birthday","$sex","$married",
			"$mar_date","$player_no","0","$clu_code","$tea_code","$tro_code",
			"$tall","$blood","$weight","$eye1","$eye2","$tel1","$tel2","$tel3","$hp1","$hp2","$hp3","$email","$zip1","$zip2",
			"$address1","$address2","$orig_address","$reason","$bigo1","$bigo2","$motive","$special","$friends",
			"$tro_detail","$equip","$sponsor","$history","$isuse","$signdate");

//첨부파일 입력
for($i=0;$i<count($f_field); $i++){
	array_push($fields,$f_field[$i]);
	array_push($fields,$f_chg_field[$i]);
	array_push($values,$r_name[$i]);
	array_push($values,$c_name[$i]);
}

// DB 저장 //
$insert = $Mysql->Update(wp_player_ctrl,$fields,$values," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>
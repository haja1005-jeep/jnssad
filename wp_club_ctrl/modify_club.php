<?
// Session 시작 //
require_once "../wp_library/head.php";


// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($code);

// 빈칸 체크 //
Input_Check($code);

// TB 필드명 //
$fields = array("name","comment");

// TB 필드값 //
$values = array("$name","$comment");

if($wp_passwd){
	// 비밀번호 설정 //
	$user_pw = Encrypt($wp_passwd);
	array_push($fields,"wp_passwd");
	array_push($values,"$user_pw");
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
		$sName = $Upload->GetName2($clu_code);

		// 파일 저장 //
		$Upload->FileSave($sName);

		// 저장 경로 //
		$img_path = $photo_path.$sName;
		
		// 이미지 크기 변환 //
		$save_name = "ci_".$sName;
		if($idx == "cipicture"){ //증명사진이면
			$max_width = 190;
			$max_height = 55;
		}

		// 썸네일 //
		Thumnail($img_path,$save_name,$photo_path,$max_width,$max_height);


	}

}




// DB 저장 //
$insert = $Mysql->Update(wp_club_ctrl,$fields,$values," where wp_id='$Site_Admin'");

// 페이지 이동 //
echo ("
	<script>
		alert('정보가 정상적으로 수정되었습니다.');
	</script>
	<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=modify_club'>
	");
?>
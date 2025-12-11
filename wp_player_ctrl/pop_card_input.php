<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($pla_id);
Null_Check($type);

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
// TB 필드명 //
$fields = array();

// TB 필드값 //
$values = array();

//첨부파일 입력
for($i=0;$i<count($f_field); $i++){
	array_push($fields,$f_field[$i]);
	array_push($fields,$f_chg_field[$i]);
	array_push($values,$r_name[$i]);
	array_push($values,$c_name[$i]);
}
// DB 저장 //
$update = $Mysql->Update(wp_player_ctrl,$fields,$values," WHERE wp_id = '$pla_id'");

if($update){
	if($type == "welfare_card"){
	// 페이지 이동 //
	echo ("<script>
		alert('정상적으로 첨부되었습니다.');
		opener.sForm.is_welfare.value='1';
		self.close();
		</script>
	");
	}
	if($type == "sport_card"){
		// 페이지 이동 //
		echo ("<script>
			alert('정상적으로 첨부되었습니다.');
			opener.sForm.is_sport_card.value='1';
			self.close();
			</script>
		");
	}
}else{
	// 페이지 이동 //
	echo ("<script>
		alert('파일 첨부를 실피하였습니다. 다시 등록아혀주세요!.');
		history.go(-1);
		</script>
	");
}
?>
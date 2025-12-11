<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($clu_code);

// 빈칸 체크 //
Input_Check($clu_code);

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
	unlink($d_f);
}
if(count($file_change) > 0){
	$insert = $Mysql->Update(wp_confirm,$d_field,$d_value," where uid='$uid'");
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
			unlink("$photo_path$d_file");
		}
		//기존 등록된 파일 삭제 끝..

		//파일내용 배열에 등록
		array_push($f_field,$idx);
		array_push($f_chg_field,$idx."_chg");
		array_push($r_name,$file_name);
		array_push($c_name,$sName);
	}

}
// TB 필드명 //
$fields = array("clu_code","name","signdate");

// TB 필드값 //
$values = array("$clu_code","$name","$signdate");

//첨부파일 입력
for($i=0;$i<count($f_field); $i++){
	array_push($fields,$f_field[$i]);
	array_push($fields,$f_chg_field[$i]);
	array_push($values,$r_name[$i]);
	array_push($values,$c_name[$i]);
}
// DB 저장 //
$insert = $Mysql->Update(wp_confirm,$fields,$values," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=modify'>");
?>
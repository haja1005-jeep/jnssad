<?
// Session 시작 //
require_once "../wp_library/head_admin.php";


exit;
// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($code);
Null_Check($writer);

// 빈칸 체크 //
Input_Check($subject);
Input_Check($comment);

// 성명 체크 //
//Name_Check($writer);

// 전자우편 체크 //
Mail_Check($email);
// HTML 체크 //
$subject = Html_Check($subject);
$comment = Html_Check($comment);

// 단어 체크 //
while(list($str_val, $str_lab) = each($no_string))
{
	if(eregi($str_lab,$subject))
	{
		Error("INFO_ERROR");
		exit;
	}
}
//비밀글 설정
if($secret)
	$secret = $secret;
else $secret = "0";
// 안내 체크 //
if($wkind)
{
	$wkind = $wkind;
}
else
{
	$wkind = 0;
}

//선택한 파일 등록
foreach ($_FILES["files"]["error"] as $idx => $error) {
	// 파일 체크 //
   if ($error == UPLOAD_ERR_OK) {
		unset($Upload);
		unset($file);
		unset($file_size);
		unset($file_name);

		//기존등록된 파일 삭제
		if($f_uid[$idx] != "n"){  //새로등록이 아닌경우만 삭제
			if($d_files[$idx])
			{
				unlink("$file_path$d_files[$idx]");
				// TB 필드명 //
				$fields = array("real_file","change_file","down");
				// TB 필드값 //
				$values = array("","",0);
				// DB 수정 //
				$update = $Mysql->Delete(wp_file,"WHERE uid='$f_uid[$idx]'");
			}
		}
		//기존 등록된 파일 삭제 끝..

		//파일생성
		$file = $_FILES["files"]["tmp_name"][$idx];
		$file_size = $_FILES["files"]["size"][$idx];
		$file_name = $_FILES["files"]["name"][$idx];
		//업로드 클래스 생성
		$Upload = new Upload;
		// 초기화 //
		$Upload->Init($file,$file_path,$file_size,$file_name);

		// 용량 체크 //
		if(!$Upload->LimitSize($auth_class->fileSIze($code)))
		{
			Error("SIZE_ERROR",$auth_class->fileSIzeM($code));
			exit;
		}

		// 확장자 체크 //
		if(!$Upload->LimitExp($auth_class->fileType($code)))
		{
			Error("UPLOAD_ERROR");
			exit;
		}

		// 파일명 //
		$sName = $Upload->GetName();

		// 파일 저장 //
		$Upload->FileSave($sName);

		unset($fields);
		unset($vaules);
		// TB 필드명 //
		$fields = array("t_name","t_uid","real_file","change_file","down");
		// TB 필드값 //
		$values = array("$code","$uid","$file_name","$sName",0);
		// DB 저장 //
		$insert = $Mysql->Insert(wp_file,$fields,$values);
	}
}
//체크된 파일 삭제
if($file_change){
	foreach($file_change as $idx => $value){

		$tmp = split(",",$value);
		$d_uid = $tmp[0];
		$d_f = $tmp[1];
		// 파일 삭제 //
		if($d_f)
		{
			//if(file_exists("$file_path$d_f")){
				unlink("$file_path$d_f");
			//}
		}

		// TB 필드명 //
		$fields = array("real_file","change_file","down");

		// TB 필드값 //
		$values = array("","",0);

		// DB 수정 //
		$update = $Mysql->Delete(wp_file,"WHERE uid='$d_uid'");
	}
}

// <P>태그 삭제 //
$comment = stripTags("P",$comment);

// TB 필드명 //
$fields = array("name","email","subject","comment","html","wkind","secret");

// TB 필드값 //
$values = array("$writer","$email","$subject","$comment","$html","$wkind","$secret");

// 비밀번호 변경 //
if($passwd)
{
	// 비밀번호 설정 //
	$user_pw = Encrypt($passwd);
	array_push($fields,"passwd");
	array_push($values,$user_pw);
}

// DB 수정 //
$update = $Mysql->Update(wp_board,$fields,$values,"WHERE uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=admin.html?$basic_get$add_get&mode=list'>");

?>
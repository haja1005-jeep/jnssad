<?
// 클래스 //
require_once "../wp_library/head_admin.php";

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

// 비밀번호 체크 //
Pw_Check($passwd);

// 전자우편 체크 //
Mail_Check($email);

// HTML 체크 //
$subject = Html_Check($subject);
$comment = Html_Check($comment);

// 스팸 체크 //
/*
if($name)
{
	Error("INFO_ERROR");
	exit;
}
*/
// 인증 번호 //
$today_date = date("Y:m:d");
$input_auth = Encrypt($today_date);

// 인증 확인 //
if(strcmp($write_date,$input_auth))
{
	Error("INFO_ERROR");
	exit;
}

// 단어 체크 //
while(list($str_val, $str_lab) = each($no_string))
{
	if(eregi($str_lab,$subject))
	{
		Error("INFO_ERROR");
		exit;
	}
}

// 번호 체크 //
$query = "SELECT max(uid), max(fid) FROM wp_board";
$values = $Mysql->ArrayQuery($query);
if($values[0])
{
	$new_uid = $values[0] + 1;
}
else
{
	$new_uid = 1;
}
if($values[1])
{
	$new_fid = $values[1] + 1;
}
else
{
	$new_fid = 1;
}


// 비밀번호 설정 //
$user_pw = Encrypt($passwd);

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

foreach ($_FILES["files"]["error"] as $idx => $error) {
	// 파일 체크 //
   if ($error == UPLOAD_ERR_OK) {
		unset($Upload);
		unset($file);
		unset($file_size);
		unset($file_name);
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
		$values = array("$code","$new_uid","$file_name","$sName",0);
		// DB 저장 //
		$insert = $Mysql->Insert(wp_file,$fields,$values);
	}
}
// <P>태그 삭제 //
$comment = stripTags("P",$comment);
// TB 필드명 //
$fields = array("uid","fid","code","name","passwd","email","subject","comment","thread","html","ref","signdate","id","ip","wkind","secret");

// TB 필드값 //
$values = array("$new_uid","$new_fid","$code","$writer","$user_pw","$email","$subject","$comment","A","$html",0,$signdate,"$id","$ip","$wkind","$secret");

// DB 저장 //
$insert = $Mysql->Insert(wp_board,$fields,$values);

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=admin.html?$basic_get$add_get&mode=list'>");
?>
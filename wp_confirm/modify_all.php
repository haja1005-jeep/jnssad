<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($clu_code);

// 빈칸 체크 //
Input_Check($clu_code);

foreach($clu_cods as $keys => $vals){
	$code_name_t = "name_".$vals;
	$code_name = $$code_name_t;
	// TB 필드명 //
	$fields = array("name");
	// TB 필드값 //
	$values = array("$code_name");
	// DB 저장 //
	$update = $Mysql->Update(wp_confirm,$fields,$values," where clu_code='$vals'");
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=modify'>");
?>
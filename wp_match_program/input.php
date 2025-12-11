<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

//권한 체크 - 관리자, 주최관리자만 볼 수 있음
if(!$site_auth->isConn($cd_player,$Admin_auth)){
	Error("AUTH_ERROR");
	exit;
}

$bye_txt = ":";
for($i=1; $i<= $person_cnt; $i++){
	$p_t = "p_".$i;
	if($$p_t == 1) $bye_txt .= $i.":";
}

// TB 필드명 //
$fields = array("match_person","bye");

// TB 필드값 //
$values = array("$match_person","$bye_txt");

// DB 저장 //
$insert = $Mysql->DupInsert(wp_match_program,$fields,$values);

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?mode=list&match_person=$match_person'>");
?>
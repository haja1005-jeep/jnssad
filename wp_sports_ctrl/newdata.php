<?
// Session 시작 //
require_once "../wp_library/head.php";

// 경로 체크 //
Referer_Check($admin_domain);

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 기존 종목 정보 삭제 //
$delete = $Mysql->Delete(wp_sports_event," where gam_uid='$tar_game'");

// 경기 종목 가져오기 //
$query = "SELECT * FROM wp_sports_event WHERE gam_uid = '$org_game' ORDER BY orderby ASC";
$result = mysql_query($query);
while($obj = mysql_fetch_object($result))
{
	// TB 필드명 //
	$fields = array("gam_uid","code","gam_type","gam_kind","name","method","detail","isuse","orderby");

	// TB 필드값 //
	$values = array("$tar_game","$obj->code","$obj->gam_type","$obj->gam_kind","$obj->name","$obj->method","$obj->detail","$obj->isuse","$obj->orderby");

	// DB 저장 //
	$insert = $Mysql->Insert(wp_sports_event,$fields,$values);
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html'>");
?>
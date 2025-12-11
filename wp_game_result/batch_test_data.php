<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($s_gam_uid);

// 빈칸 체크 //
Input_Check($s_gam_uid);

//=== 대회 정보 체크 시작
//대회정보검색
$query = "SELECT * FROM wp_game_ctrl where uid='$s_gam_uid'";

$result = mysql_query($query);
if (!$result) {
   	Error("QUERY_ERROR");
}
$obj = mysql_fetch_array($result,MYSQL_BOTH);

//진행중인 대회인지 확인
if($obj[status] != "2" || $obj[isuse] != "0"){ //승인된 대회가 아니면 에러
	Error("END_GAME_ERROR");
	exit;
}
//해당대회에 대한 권한 체크
if($Admin_auth != "top" && $Admin_code != $obj[clu_code]){
	Error("AUTH_ERROR");
	exit;
}
//=== 대회 정보 체크 끝
//기존 기록정보 삭제
mysql_query ("DELETE FROM wp_game_record2 where gam_uid='$s_gam_uid'");
$que = "SELECT * FROM wp_game_record WHERE gam_uid = '$s_gam_uid' ORDER BY uid ASC";
$res = mysql_query($que);
while($obj = mysql_fetch_object($res)){
	unset($a_que);
	unset($a_res);
	unset($a_obj);

	$fields = array("gam_uid","game_code","game_kind","level","id","name","clu_code",
				"spo_code","spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","lane_no","back_no",
				"group_no","rank","rank_tot","record_01","record_02","record_03","record_04","record_05","record_tot",
				"record_best","record_prz","win","other_group_no","other_lane_no","other_clu_code","unearnedwin","test_game");
	// TB 필드값 //
	$values = array("$obj->gam_uid","$obj->game_code","$obj->game_kind","$obj->level","$obj->id","$obj->name","$obj->clu_code",
				"$obj->spo_code","$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->lane_no","$obj->back_no",
				"$obj->group_no","$obj->rank","$obj->rank_tot","$obj->record_01","$obj->record_02","$obj->record_03","$obj->record_04","$obj->record_05","$obj->record_tot",
				"$obj->record_best","$obj->record_prz","$obj->win","$obj->other_group_no","$obj->other_lane_no","$obj->other_clu_code","$obj->unearnedwin","$obj->test_game");
	// DB 저장 //
	$insert = $Mysql->Insert(wp_game_record2,$fields,$values);
}
// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?gam_uid=$s_gam_uid&mode=test_data'>");
?>
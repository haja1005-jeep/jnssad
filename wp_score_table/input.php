<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);


//권한 체크
if(!$site_auth->isConn($cd_game,$Admin_auth)){
	Error("AUTH_ERROR");
	exit;
}

//기존데이터 삭제
$delete = $Mysql->Delete(wp_score_table," WHERE gam_uid='$s_gam_uid'");

$sports_kind = array_keys($wp_sports_code);
array_push($sports_kind,"BILLIARDS_2");
array_push($sports_kind,"BILLIARDS_5");
array_push($sports_kind,"LAWNBOWL_2");
array_push($sports_kind,"LAWNBOWL_3");
array_push($sports_kind,"LAWNBOWL_4");
array_push($sports_kind,"BADMINTON_2");
array_push($sports_kind,"BOCCIA_2");
array_push($sports_kind,"BOCCIA_3");
array_push($sports_kind,"SWIMMING_2");
array_push($sports_kind,"BOWLING_2");
array_push($sports_kind,"BOWLING_4");
array_push($sports_kind,"ATHIETICS_2");
array_push($sports_kind,"ROWING_2");
array_push($sports_kind,"ROWING_3");
array_push($sports_kind,"FOOTSAL_2");
array_push($sports_kind,"FOOTSAL_3");
array_push($sports_kind,"TABLETENNIS_2");
array_push($sports_kind,"TABLETENNIS_3");
array_push($sports_kind,"TABLETENNIS_4");
array_push($sports_kind,"TENNIS_2");
array_push($sports_kind,"GOLF_2");
array_push($sports_kind,"GOLF_3");
array_push($sports_kind,"BADUK_2");
array_push($sports_kind,"DANCE_2");
array_push($sports_kind,"ARCHERY_3");

foreach($sports_kind as $keys => $vals)
{
	$spo_code = $_POST[$vals];
	
	// TB 필드명 //
	$fields = array("gam_uid","spo_code","game_kind","player_cnt","score_1","score_2","score_3","score_4","score_5","score_6","score_7","score_8","sort","note");

	// TB 필드값 //
	$values = array("$s_gam_uid","$spo_code[spo_code]","$spo_code[game_kind]","$spo_code[player_cnt]","$spo_code[1]","$spo_code[2]","$spo_code[3]","$spo_code[4]",
				"$spo_code[5]","$spo_code[6]","$spo_code[7]","$spo_code[8]","$spo_code[sort]","$spo_code[note]");
	
	// DB 저장 //
	$insert = $Mysql->Insert(wp_score_table,$fields,$values);

}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=input'>");
?>
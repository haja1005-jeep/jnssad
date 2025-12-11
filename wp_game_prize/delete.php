<?
// Session 시작 //
require_once "../wp_library/head.php";

// DB 삭제 //
$insert = $Mysql->Delete(wp_game_prize," where uid='$uid'");
if($insert) {
	//상벌 삭제
	$Mysql->Delete(wp_prize_count," where prz_uid='$uid'");
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>
<?
// Session 시작 //
require_once "../wp_library/head.php";

//해당 시/군 클럽 소속의 선수인지 체크
$query = "SELECT * FROM wp_player_ctrl WHERE uid = '$uid'";
$result = mysql_query($query);
if (!$result) {
   	Error("DB_ERROR");
   	exit;
}
if(mysql_num_rows($result) <= 0){
   	Error("NOT_DATA");
	exit;
}
$row = mysql_fetch_object($result);

if($Admin_auth != "top" && $Admin_code != $row->clu_code){
	Error("AUTH_ERROR");
	exit;
}

//파일 삭제
if($row->jumin_card) unlink($photo_path.$row->jumin_card_chg);
if($row->picture) unlink($photo_path.$row->picture_chg);
if($row->welfare_card) unlink($photo_path.$row->welfare_card_chg);
if($row->sport_card) unlink($photo_path.$row->sport_card_chg);

// DB 삭제 //
$insert = $Mysql->Delete(wp_player_ctrl," where uid='$uid'");
if($insert) {
	//상벌 삭제
	$Mysql->Delete(wp_player_prize," where id='$row->wp_id'");
	//학벌 삭제
	$Mysql->Delete(wp_player_school," where id='$row->wp_id'");
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>
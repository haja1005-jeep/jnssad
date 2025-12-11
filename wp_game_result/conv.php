<?
// Session 시작 //
require_once "../wp_library/head.php";

//$que  = "SELECT * FROM wp_game_serial WHERE gam_uid = '$gam_uid' ORDER BY spo_code ASC, spo_sec_code ASC";
$que  = "SELECT * FROM wp_game_record WHERE gam_uid = '$gam_uid' ORDER BY spo_code ASC, spo_sec_code ASC";
$res = mysql_query($que);
while($obj = mysql_fetch_object($res))
{
	$tmp_game_code = split("-",$obj->game_code);
	$new_game_code = $tmp_game_code[0]."-".str_pad($tmp_game_code[1],2,"0",STR_PAD_LEFT);

	echo $obj->game_code." : ".$new_game_code."<br>";
	// TB 필드명 //
	$fields = array("game_code");

	// TB 필드값 //
	$values = array("$new_game_code");

	// DB 저장 //
	//$update = $Mysql->Update(wp_game_serial,$fields,$values, " WHERE uid = '$obj->uid'");
	$update = $Mysql->Update(wp_game_record,$fields,$values, " WHERE uid = '$obj->uid'");


}
// 페이지 이동 //
//echo ("<meta http-equiv='Refresh' content='0; URL=index.html?s_gam_uid=$s_gam_uid&spo_code=$spo_code&spo_sec_code=$spo_sec_code&mode=serial'>");
?>
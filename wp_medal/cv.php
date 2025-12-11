<?
// Session 시작 //
require_once "../wp_library/head.php";

$que = "SELECT * FROM wp_game_serial WHERE gam_uid = 8";
$res = mysql_query($que);
while($obj = mysql_fetch_object($res))
{
	$c_que = "SELECT * FROM wp_game_record WHERE gam_uid = 8 AND game_code = '$obj->game_code'";
	$c_res = mysql_query($c_que);
	while($c_obj = mysql_fetch_object($c_res))
	{
		// TB 필드 //
		$fields = array("test_game");

		// TB 필드값 //
		$values = array("$obj->test_game");

		// DB 저장 //
		$insert = $Mysql->Update(wp_game_record,$fields,$values," WHERE uid = '$c_obj->uid'");
	}

}
echo "완ㄹ";
?>

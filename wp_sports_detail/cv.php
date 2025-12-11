<?
// Session 시작 //
require_once "../wp_library/head.php";

$query = "SELECT * FROM wp_sports_event_detail WHERE gam_uid = 8";
$result = mysql_query($query);
while($obj = mysql_fetch_object($result))
{
	$serial_que = "SELECT * FROM wp_game_serial WHERE gam_uid = 8 AND spo_sec_code = '$obj->code'";
	$serial_res = mysql_query($serial_que);
	while($serial_obj = mysql_fetch_object($serial_res))
	{
		// TB 필드명 //
		$fields = array("game_kind");

		// TB 필드값 //
		$values = array("$obj->game_kind");

		// DB 저장 //
		$update = $Mysql->Update(wp_game_serial,$fields,$values," where uid='$serial_obj->uid'");

		$c_que = "SELECT * FROM wp_game_record WHERE gam_uid = 8 AND game_code = '$serial_obj->game_code'";
		$c_res = mysql_query($c_que);
		while($c_obj = mysql_fetch_object($c_res))
		{
			// TB 필드 //
			$fields = array("game_kind");

			// TB 필드값 //
			$values = array("$serial_obj->game_kind");

			// DB 저장 //
			$insert = $Mysql->Update(wp_game_record,$fields,$values," WHERE uid = '$c_obj->uid'");
		}

	}
}
echo "완료";
?>
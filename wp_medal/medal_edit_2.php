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

// 2014-09-24 박민철 추가 - 종목별로 점수 집계 가능하도록 추가

//기존데이터 삭제
if($spo_code) $del_where .= " AND spo_code = '$spo_code'";
$insert = $Mysql->Delete(wp_medal," WHERE gam_uid='$s_gam_uid' $del_where");

$medal_array = array();
if($spo_code)
{
	$medal_array[$spo_code] = $wp_sports_code[$spo_code];
}
else
{
	$medal_array = $wp_sports_code;
}

foreach($medal_array as $keys => $values){
	//생활체육은 메달집계에서 제외
	//if(array_key_exists($keys, $wp_life_game)) continue;
	//체험종목 메달집계에서 제외
	if(array_key_exists($keys, $wp_exp_game)) continue;
	unset($query);
	unset($result);
	unset($obj);
	
	//메달 집계
	if($keys == "POWERLIFTING")
	{
		// 역도는 한 경기에 3개의 메달이 주어진다.
		$query = "
			SELECT clu_code, sum(gold) gold, sum(silver) silver, sum(bronze) bronze FROM (
				SELECT clu_code, 1 gold, 0 silver, 0 bronze FROM wp_game_record WHERE gam_uid = '$s_gam_uid'  AND rank_tot='1' AND game_code in (SELECT game_code FROM wp_game_serial WHERE gam_uid = '$s_gam_uid' AND spo_code='$keys' AND test_game <> '1') GROUP BY game_code, spo_sec_code
					UNION ALL
				SELECT clu_code, 0 gold, 1 silver, 0 bronze FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND rank_tot='2' AND game_code in (SELECT game_code FROM wp_game_serial WHERE gam_uid = '$s_gam_uid' AND spo_code='$keys' AND test_game <> '1') GROUP BY game_code, spo_sec_code
					UNION ALL
				SELECT clu_code, 0 gold, 0 silver, 1 bronze FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND rank_tot='3'  AND game_code in (SELECT game_code FROM wp_game_serial WHERE gam_uid = '$s_gam_uid' AND spo_code='$keys' AND test_game <> '1') GROUP BY game_code, spo_sec_code
			) medal GROUP BY clu_code
		";
	}
	else
	{
		$query = "
			SELECT clu_code, sum(gold) gold, sum(silver) silver, sum(bronze) bronze FROM (
				SELECT clu_code, 1 gold, 0 silver, 0 bronze FROM wp_game_record WHERE gam_uid = '$s_gam_uid'  AND rank_tot='1' AND game_code in (SELECT game_code FROM wp_game_serial WHERE gam_uid = '$s_gam_uid' AND spo_code='$keys' AND test_game <> '1')  GROUP BY game_code
					UNION ALL
				SELECT clu_code, 0 gold, 1 silver, 0 bronze FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND rank_tot='2' AND game_code in (SELECT game_code FROM wp_game_serial WHERE gam_uid = '$s_gam_uid' AND spo_code='$keys' AND test_game <> '1') GROUP BY game_code
					UNION ALL
				SELECT clu_code, 0 gold, 0 silver, 1 bronze FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND rank_tot='3'  AND game_code in (SELECT game_code FROM wp_game_serial WHERE gam_uid = '$s_gam_uid' AND spo_code='$keys' AND test_game <> '1')  GROUP BY game_code
			) medal GROUP BY clu_code
		";
	}

	$result = mysql_query($query);
	while($obj = mysql_fetch_object($result)){
		// TB 필드 //
		$fields = array("gam_uid","clu_code","spo_code","gold","silver","bronze","total_point","signdate");
		// TB 필드값 //
		$values = array("$s_gam_uid","$obj->clu_code","$keys","$obj->gold","$obj->silver","$obj->bronze",0,"$signdate");
		// DB 저장 //
		$insert = $Mysql->Insert(wp_medal,$fields,$values);
	}

}

//페이지이동
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?s_gam_uid=$s_gam_uid&mode=medal&spo_code=$spo_code'>");
?>

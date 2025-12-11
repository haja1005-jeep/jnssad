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
//성립이 체크되어있거나 시범경기가 체크 되어있는데 경기번호가 없으면 에러
foreach($ok_match as $keys => $vals){
	$game_code = $$vals;
	if(!$game_code){
		ERROR("GAME_SERIAL_ERROR");
		exit;
	}
}

//기존 등록된 데이터 삭제
if($spo_code == "ATHIETICS")
{
	mysql_query(" DELETE FROM wp_game_serial WHERE gam_uid = '$gam_uid' AND spo_code = '$spo_code' AND spo_sec_code='$spo_sec_code'");
}
else
{
	mysql_query(" DELETE FROM wp_game_serial WHERE gam_uid = '$gam_uid' AND spo_code = '$spo_code'");
}

// TB 필드명 //
$fields = array("gam_uid","game_code","game_name","game_kind","spo_code","spo_sec_code","spo_code_detail","sex","tro_level_code","sort", "test_game");
$gam_number = 1;
if(is_array($ok_match)){
	foreach($ok_match as $keys => $vals){
		$spo_data = explode("/", $vals); // spo_code + spo_sec_code + sex + tro_level_code + spo_code_detail
		$spo_code = $spo_data[0];
		$spo_sec_code = $spo_data[1];
		$sex = $spo_data[2];
		$tro_level_code = $spo_data[3];
		$spo_code_detail = $spo_data[4];

		$game_code = $$vals;
		$tmp_sort = explode("-",$game_code);
		$sort = $tmp_sort[1];
		//경기명 셋팅
		unset($game_name);
		$game_name = $wp_game_code[$spo_code]." ";
		if($wp_sports_code[$spo_code] != getSpo_sec_code($spo_code,$spo_sec_code)){
			$game_name .= getSpo_sec_code($spo_code,$spo_sec_code)." ";
		}
		$tro_level_code = getTrouble_level_code($spo_code, $tro_level_code)."[$tro_level_code]";
		$game_name .= $tro_level_code." ".$wp_sex_code[$sex]." / ". $wp_sports_detail_code[$spo_code_detail];

		$test_game_txt = "0";
		if(!is_array($test_game)) $test_game = array();
		if(in_array($vals,$test_game)){ //시범경기이면
			$test_game_txt = "1";
		}

		$game_kind = $game_kind_code[$spo_code."-".$spo_sec_code."-".$spo_code_detail];

		// TB 필드값 //
		$values = array("$gam_uid","$game_code","$game_name","$game_kind","$spo_data[0]","$spo_data[1]","$spo_data[4]","$spo_data[2]","$spo_data[3]",$sort,"$test_game_txt");

		// DB 저장 //
		$insert = $Mysql->Insert(wp_game_serial,$fields,$values);

		$gam_number++;
	}
}

if(is_array($test_game)){
	foreach($test_game as $keys => $vals){
		if(!in_array($vals,$ok_match)){ //성립이 체크되어있지 않으면 등록
			$spo_data = explode("/", $vals);
			$spo_code = $spo_data[0];
			$spo_sec_code = $spo_data[1];
			$sex = $spo_data[2];
			$tro_level_code = $spo_data[3];
			$spo_code_detail = $spo_data[4];

			$game_code = $$vals;
			$tmp_sort = explode("-",$game_code);
			$sort = $tmp_sort[1];
			//경기명 셋팅
			unset($game_name);
			$game_name = $wp_game_code[$spo_code]." ";
			if($wp_sports_code[$spo_code] != getSpo_sec_code($spo_code,$spo_sec_code)){
				$game_name .= getSpo_sec_code($spo_code,$spo_sec_code)." ";
			}
			$tro_level_code = getTrouble_level_code($spo_code, $tro_level_code)."[$tro_level_code]";
			$game_name .= $tro_level_code." ".$wp_sex_code[$sex]." / ". $wp_sports_detail_code[$spo_code_detail];
			$game_kind = $game_kind_code[$spo_code."-".$spo_sec_code."-".$spo_code_detail];

			// TB 필드값 //
			$values = array("$gam_uid","$game_code","$game_name","$game_kind","$spo_data[0]","$spo_data[1]","$spo_data[4]","$spo_data[2]","$spo_data[3]",$sort,"1");

			// DB 저장 //
			$insert = $Mysql->Insert(wp_game_serial,$fields,$values);

			$gam_number++;
		}
	}
}
// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?s_gam_uid=$s_gam_uid&spo_code=$spo_code&spo_sec_code=$spo_sec_code&mode=serial'>");
?>
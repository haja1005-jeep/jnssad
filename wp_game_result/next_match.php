<?
// Session 시작 //
require_once "../wp_library/head.php";


// 경로 체크 //
Referer_Check($admin_domain);

// 공백 체크 //
Null_Check($gam_uid);
Null_Check($game_code);

// 빈칸 체크 //
Input_Check($gam_uid);
Input_Check($game_code);

//로그인 체크
if(!$site_auth->isLogin()){
	Error("ADMIN_ERROR");
	exit;
}

if($type == "real") $tbName = " wp_game_record ";
else $tbName = " wp_game_record2 ";

/*
if($spo_code == "DART")
{
	//기존에 입력한 데이터 삭제
	$Mysql->Delete($tbName," WHERE gam_uid = '$gam_uid' AND game_code = '$game_code' AND level = '1'");
	//등록할 데이터 검색 (1~9위까지)
	$query = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND game_code = '$game_code' AND level = '2' AND rank_tot >=1 AND rank_tot <= 9 ORDER BY rank_tot";
	$result = mysql_query($query);
	$n_lane_no = 1;
	while($obj = mysql_fetch_object($result)){

		// TB 필드 //
		$fields = array("gam_uid","game_code","game_kind","level","group_no","lane_no","id","name","clu_code","spo_code",
					"spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","back_no","test_game","unearnedwin");
		// TB 필드값 //
		$values = array("$obj->gam_uid","$obj->game_code","$obj->game_kind","$n_level","$obj->group_no","$n_lane_no","$obj->id","$obj->name","$obj->clu_code","$obj->spo_code",
					"$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->back_no","$obj->test_game","");
		// DB 저장 //
		$insert = $Mysql->Insert($tbName,$fields,$values);

		$n_lane_no++;
	}
}
else
{
*/
	//기존에 입력한 데이터 삭제
	$Mysql->Delete($tbName," WHERE gam_uid = '$gam_uid' AND game_code = '$game_code' AND level = '$n_level'");

	// 2014-09-26 박민철 추가 - 토너먼트 다음 경기 등록시 group_str 등록되도록 수정

	$query = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND game_code = '$game_code' AND level = '$level' AND win='W' GROUP BY lane_no ORDER BY lane_no ASC";
	$result = mysql_query($query);
	$n_lane_no = 1;
	while($obj = mysql_fetch_object($result)){

		// TB 필드 //
		$fields = array("gam_uid","game_code","game_kind","level","group_no","lane_no","id","name","clu_code","spo_code",
					"spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","back_no","test_game","unearnedwin","group_str");
		// TB 필드값 //
		$values = array("$obj->gam_uid","$obj->game_code","$obj->game_kind","$n_level","$obj->group_no","$n_lane_no","$obj->id","$obj->name","$obj->clu_code","$obj->spo_code",
					"$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->back_no","$obj->test_game","","$obj->group_str");
		// DB 저장 //
		$insert = $Mysql->Insert($tbName,$fields,$values);

		// 단체전이면 같은 팀의 사람도 등록한다.
		if($game_kind == "2")
		{
			$o_query = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND game_code = '$game_code' AND level = '$level' AND win='W' AND lane_no = '$obj->lane_no' AND id not in ('$obj->id') ORDER BY uid ASC";
			$o_result = mysql_query($o_query);
			while($o_obj = mysql_fetch_object($o_result)){
				// TB 필드 //
				$o_fields = array("gam_uid","game_code","game_kind","level","group_no","lane_no","id","name","clu_code","spo_code",
							"spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","back_no","test_game","unearnedwin","group_str");
				// TB 필드값 //
				$o_values = array("$o_obj->gam_uid","$o_obj->game_code","$o_obj->game_kind","$n_level","$o_obj->group_no","$n_lane_no","$o_obj->id","$o_obj->name","$o_obj->clu_code","$o_obj->spo_code",
							"$o_obj->spo_sec_code","$o_obj->spo_code_detail","$o_obj->sex","$o_obj->tro_code","$o_obj->tro_level_code","$o_obj->back_no","$o_obj->test_game","","$o_obj->group_str");
				// DB 저장 //
				$insert = $Mysql->Insert($tbName,$o_fields,$o_values);
			}
		}
		$n_lane_no++;
	}

	if($n_level == "1"){ //결승전 생성이면 3.4위전도 생성한다.
		$n_level = "3";
		//기존에 입력한 데이터 삭제
		$Mysql->Delete($tbName," WHERE gam_uid = '$gam_uid' AND game_code = '$game_code' AND level = '$n_level'");
		$query = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND game_code = '$game_code' AND level = '$level' AND win='F' GROUP BY lane_no ORDER BY lane_no ASC";
		$result = mysql_query($query);
		$n_lane_no = 1;
		while($obj = mysql_fetch_object($result)){
			// TB 필드 //
			$fields = array("gam_uid","game_code","game_kind","level","group_no","lane_no","id","name","clu_code","spo_code",
						"spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","back_no","test_game","unearnedwin","group_str");
			// TB 필드값 //
			$values = array("$obj->gam_uid","$obj->game_code","$obj->game_kind","$n_level","$obj->group_no","$n_lane_no","$obj->id","$obj->name","$obj->clu_code","$obj->spo_code",
						"$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->back_no","$obj->test_game","","$obj->group_str");
			// DB 저장 //
			$insert = $Mysql->Insert($tbName,$fields,$values);

			// 단체전이면 같은 팀의 사람도 등록한다.
			if($game_kind == "2")
			{
				$o_query = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND game_code = '$game_code' AND level = '$level' AND win='F' AND lane_no = '$obj->lane_no' AND id not in ('$obj->id') ORDER BY uid ASC";
				$o_result = mysql_query($o_query);
				while($o_obj = mysql_fetch_object($o_result)){
					// TB 필드 //
					$o_fields = array("gam_uid","game_code","game_kind","level","group_no","lane_no","id","name","clu_code","spo_code",
								"spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","back_no","test_game","unearnedwin","group_str");
					// TB 필드값 //
					$o_values = array("$o_obj->gam_uid","$o_obj->game_code","$o_obj->game_kind","$n_level","$o_obj->group_no","$n_lane_no","$o_obj->id","$o_obj->name","$o_obj->clu_code","$o_obj->spo_code",
								"$o_obj->spo_sec_code","$o_obj->spo_code_detail","$o_obj->sex","$o_obj->tro_code","$o_obj->tro_level_code","$o_obj->back_no","$o_obj->test_game","","$obj->group_str");
					// DB 저장 //
					$insert = $Mysql->Insert($tbName,$o_fields,$o_values);
				}
			}

			$n_lane_no++;
		}
	}
//}

// 페이지 이동 //
$next_match_txt = $n_level."강전이";
if($n_level == 3)
{
	$next_match_txt = "준결승과 결승전이";
}
echo "
	<script>
		alert('$next_match_txt 생성되었습니다.');
	</script>
	<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=input&sch_val=$sch_val'>
";
?>
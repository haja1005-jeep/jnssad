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

/* 2014-09-19 박민철 수정
	현 페이지에 wp_game_record에 group_str 필드 추가로 인하여
	wp_game_player (참가선수테이블) -> wp_game_record (기록관리테이블) 로
	데이터 추가시 group_str도 데이터 삽입되도록 전체 수정
*/

// 기존에 등록된 기록관리를 위한 기초 데이터 삭제
$Mysql->Delete(wp_game_record," WHERE gam_uid = '$gam_uid' AND game_code = '$game_code'");

//테스트 데이터 삭제
$Mysql->Delete(wp_game_record2," WHERE gam_uid = '$gam_uid' AND game_code = '$game_code'");

if(in_array($spo_code,$wp_tot_game))
{
	// 토너먼트 경기일 경우 //
	for($i = 1; $i <= $match_cnt*2; $i++)
	{
		if($i <= $match_cnt)
		{
			$group_no = 1;
		}
		else
		{
			$group_no = 2;
		}
		$level = $match_cnt*2;
		$p_num = "p_{$i}";



		//시군전이면 시/군만 등록한다.
		if($game_kind == "3")
		{
			$clu_code = $$p_num;

			if(!$clu_code) continue;

            //2017.05.23일 탁구 단체전 경기 TT-A, TT-B 섞어지는 오류 AND tro_level_code = '$tro_level_code' 추가함.
			$que = "SELECT * FROM wp_game_player WHERE gam_uid='$gam_uid' AND spo_code='$spo_code' AND spo_code_detail = '$spo_code_detail' AND clu_code = '$clu_code' AND tro_level_code = '$tro_level_code' GROUP BY clu_code";

			$res = mysql_query($que);
			$obj = mysql_fetch_object($res);

			// TB 필드 //
			$fields = array("game_code","gam_uid","game_kind","group_no","lane_no","level","spo_code","spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","clu_code","test_game");

			// TB 필드값 //
			$values = array("$game_code","$gam_uid","$game_kind","$group_no","$i","$level","$obj->spo_code","$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game");


			// DB 저장 //
			$insert = $Mysql->Insert(wp_game_record,$fields,$values);

			// 테스트 DB 저장 //
			$insert = $Mysql->Insert(wp_game_record2,$fields,$values);
		}
		else
		{
			$pla_id = $$p_num;

			if(!$pla_id) continue;

			$que = "SELECT * FROM wp_game_player WHERE gam_uid='$gam_uid' AND spo_code='$spo_code'  AND spo_code_detail = '$spo_code_detail' AND pla_id = '$pla_id'";
			$res = mysql_query($que);
			$obj = mysql_fetch_object($res);

			// TB 필드 //
			$fields = array("game_code","gam_uid","game_kind","group_no","lane_no","level","id","name","spo_code","spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","clu_code","test_game","group_str");

			// TB 필드값 //
			/* 2014-09-19 박민철 수정
					   구례에서 단체전 수정 부분의 소스를 제거하고 group_str을 이용하여 소스 재작업
			if($game_kind == "2" && ($spo_code == "TENNIS" || $spo_code == "HANDLER")) // 구례에서 단체전 버그 잡기위해 임시적용
			{
				$values = array("$game_code","$gam_uid","$game_kind","$obj->group_str","$i","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game");
			}
			else
			{
				$values = array("$game_code","$gam_uid","$game_kind","$group_no","$i","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game");
			}
			*/
			$values = array("$game_code","$gam_uid","$game_kind","$group_no","$i","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game","$obj->group_str");

			// DB 저장 //
			$insert = $Mysql->Insert(wp_game_record,$fields,$values);

			// 테스트 DB 저장 //
			$insert = $Mysql->Insert(wp_game_record2,$fields,$values);

			//단체전일 경우 같은 편 선수 등록         /* 2019-04-29 일 테니스 복식이 개인전으로 보이는 현상 해결 */ || $game_code == "TENNIS_TS100-03" || $game_code == "TENNIS_TS100-04" || $game_code == "TENNIS_TS100-06 추가
			if($game_kind == "2" || $game_code == "TENNIS_TS100-03" || $game_code == "TENNIS_TS100-04" || $game_code == "TENNIS_TS100-06" || $game_code == "TENNIS_TS100-08")
			{
				$a_que = "SELECT * FROM wp_game_player WHERE gam_uid='$gam_uid' AND spo_code='$obj->spo_code' AND spo_code_detail = '$obj->spo_code_detail' AND sex='$obj->sex' AND tro_level_code='$obj->tro_level_code' AND clu_code = '$obj->clu_code' AND group_str='$obj->group_str' AND pla_id not in ('$obj->pla_id')";

				//$a_que = "SELECT * FROM wp_game_player WHERE gam_uid='$gam_uid' AND spo_code='$obj->spo_code' AND spo_sec_code='$obj->spo_sec_code' AND spo_code_detail = '$obj->spo_code_detail' AND sex='$obj->sex' AND tro_level_code='$obj->tro_level_code' AND clu_code = '$obj->clu_code' AND pla_id not in ('$obj->pla_id')";
				$a_res = mysql_query($a_que);
				while($a_obj = mysql_fetch_object($a_res))
				{
					// TB 필드 //
					$a_fields = array("game_code","gam_uid","game_kind","group_no","lane_no","level","id","name","spo_code","spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","clu_code","test_game","group_str");

					// TB 필드값 //
					/* 2014-09-19 박민철 수정
					   구례에서 단체전 수정 부분의 소스를 제거하고 group_str을 이용하여 소스 재작업
					if($spo_code == "TENNIS" || $spo_code == "HANDLER") // 구례에서 단체전 버그 잡기위해 임시적용
					{
						$a_values = array("$game_code","$gam_uid","$game_kind","$a_obj->group_str","$i","$level","$a_obj->pla_id","$a_obj->pla_name","$a_obj->spo_code","$a_obj->spo_sec_code","$a_obj->spo_code_detail","$a_obj->sex","$a_obj->tro_code","$a_obj->tro_level_code","$a_obj->clu_code","$test_game");
					}
					else
					{
						$a_values = array("$game_code","$gam_uid","$game_kind","$group_no","$i","$level","$a_obj->pla_id","$a_obj->pla_name","$a_obj->spo_code","$a_obj->spo_sec_code","$a_obj->spo_code_detail","$a_obj->sex","$a_obj->tro_code","$a_obj->tro_level_code","$a_obj->clu_code","$test_game");
					}
					*/
					$a_values = array("$game_code","$gam_uid","$game_kind","$group_no","$i","$level","$a_obj->pla_id","$a_obj->pla_name","$a_obj->spo_code","$a_obj->spo_sec_code","$a_obj->spo_code_detail","$a_obj->sex","$a_obj->tro_code","$a_obj->tro_level_code","$a_obj->clu_code","$test_game","$a_obj->group_str");

					// DB 저장 //
					$insert = $Mysql->Insert(wp_game_record,$a_fields,$a_values);

					// 테스트 DB 저장 //
					$insert = $Mysql->Insert(wp_game_record2,$fields,$values);
				}
			}
		}
	}
}
else if(in_array($spo_code,$wp_score_game))
{
	foreach($pla_id as $key_f => $val_f)
	{
		$tmp_lane =  "lane_".$val_f;
		$lane_no = $$tmp_lane;

		$tmp_group =  "group_".$val_f;
		$group_no = $$tmp_group;

		$tmp_back =  "back_".$val_f;
		$back_no = $$tmp_back;

		// 20회 역도 종목에 경우 세부 종목이 여럿 통합되었기 때문에 세부 종목 코드는 조건에서 제외한다. //
		if($spo_code == "POWERLIFTING")
		{
			$que = "SELECT * FROM wp_game_player WHERE gam_uid='$gam_uid' AND spo_code='$spo_code'  AND pla_id = '$val_f'";
		}
		else
		{
			$que = "SELECT * FROM wp_game_player WHERE gam_uid='$gam_uid' AND spo_code='$spo_code'  AND spo_code_detail = '$spo_code_detail' AND pla_id = '$val_f'";
		}

		//print_r($pla_id);
		//echo $que;
		$res = mysql_query($que);
		$obj = mysql_fetch_object($res);

		$level = "1";
		if($spo_code == "DART")
		{
			$level = "2";
		}
		// TB 필드 //
		$fields = array("game_code","gam_uid","game_kind","group_no","lane_no","back_no","level","id","name","spo_code","spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","clu_code","test_game","group_str");

		// TB 필드값 //
		$values = array("$game_code","$gam_uid","$game_kind","$group_no","$lane_no","$back_no","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game","$obj->group_str");

		// DB 저장 //
		$insert = $Mysql->Insert(wp_game_record,$fields,$values);

		// 테스트 DB 저장 //
		$insert = $Mysql->Insert(wp_game_record2,$fields,$values);

		//단체전일 경우 같은 편 선수 등록
		if($game_kind == "2")
		{
			/* 2014-09-19 박민철 수정
					   구례에서 단체전 수정 부분의 소스를 제거하고 group_str을 이용하여 소스 재작업
			if($spo_code == "BOWLING")
			{
				$a_que = "SELECT * FROM wp_game_player WHERE gam_uid='$gam_uid' AND spo_code='$obj->spo_code' AND spo_sec_code='$obj->spo_sec_code' AND spo_code_detail = '$obj->spo_code_detail' AND sex='$obj->sex' AND tro_level_code='$obj->tro_level_code' AND clu_code = '$obj->clu_code' AND group_str = '$group_no' AND pla_id not in ('$obj->pla_id')";
			}
			else
			{
				$a_que = "SELECT * FROM wp_game_player WHERE gam_uid='$gam_uid' AND spo_code='$obj->spo_code' AND spo_sec_code='$obj->spo_sec_code' AND spo_code_detail = '$obj->spo_code_detail' AND sex='$obj->sex' AND tro_level_code='$obj->tro_level_code' AND clu_code = '$obj->clu_code' AND pla_id not in ('$obj->pla_id')";
			}
			*/
			$a_que = "SELECT * FROM wp_game_player WHERE gam_uid='$gam_uid' AND spo_code='$obj->spo_code'  AND spo_code_detail = '$obj->spo_code_detail' AND sex='$obj->sex' AND tro_level_code='$obj->tro_level_code' AND clu_code = '$obj->clu_code' AND group_str = '$obj->group_str' AND pla_id not in ('$obj->pla_id')";

			$a_res = mysql_query($a_que);
			while($a_obj = mysql_fetch_object($a_res))
			{
				// TB 필드 //
				$a_fields = array("game_code","gam_uid","game_kind","group_no","lane_no","back_no","level","id","name","spo_code","spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","clu_code","test_game","group_str");

				// TB 필드값 //
				$a_values = array("$game_code","$gam_uid","$game_kind","$group_no","$lane_no","$back_no","$level","$a_obj->pla_id","$a_obj->pla_name","$a_obj->spo_code","$a_obj->spo_sec_code","$a_obj->spo_code_detail","$a_obj->sex","$a_obj->tro_code","$a_obj->tro_level_code","$a_obj->clu_code","$test_game","$a_obj->group_str");

				// DB 저장 //
				$insert = $Mysql->Insert(wp_game_record,$a_fields,$a_values);

				// 테스트 DB 저장 //
				$insert = $Mysql->Insert(wp_game_record2,$a_fields,$a_values);
			}
		}


		//역도일경우 종합으로 등록하면 하위 두경기를 등록한다.
		if($spo_code == "POWERLIFTING"){
			if($obj->spo_sec_code == "PL600"){ // 파워리프트 종합이면 스쿼드, 데드리프트 경기를 등록한다.

				//echo "파워리프트<br>";

				//스쿼트 경기 등록 (PL400)
				// TB 필드값 //
				$values = array("$game_code","$gam_uid","$game_kind","$group_no","$lane_no","$back_no","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","PL400","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game","group_str");
				// DB 저장 //
				$insert = $Mysql->Insert(wp_game_record,$fields,$values);

				//데드리프트 경기 등록 (PL500)
				// TB 필드값 //
				$values = array("$game_code","$gam_uid","$game_kind","$group_no","$lane_no","$back_no","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","PL500","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game","$obj->group_str");
				// DB 저장 //
				$insert = $Mysql->Insert(wp_game_record,$fields,$values);

				//테스트용에다도 등록
				// TB 필드값 //
				$values = array("$game_code","$gam_uid","$game_kind","$group_no","$lane_no","$back_no","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","PL400","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game","$obj->group_str");
				// DB 저장 //
				$insert = $Mysql->Insert(wp_game_record2,$fields,$values);

				// TB 필드값 //
				$values = array("$game_code","$gam_uid","$game_kind","$group_no","$lane_no","$back_no","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","PL500","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game","$obj->group_str");
				// DB 저장 //
				$insert = $Mysql->Insert(wp_game_record2,$fields,$values);
				//테스트용에다도 등록 끝

			}
			else if($obj->spo_sec_code == "PL300") // 벤치프레스 종합이면 파워리프팅 , 웨이트리프팅 경기를 등록한다.
			{
				

				//파워리프팅 경기 등록 (PL100)

				//echo "벤치프레스<br>";
				//echo "[".$lane_no."]<br>";

							echo $obj->spo_sec_code;

				// TB 필드값 //
				$values = array("$game_code","$gam_uid","$game_kind","$group_no","$lane_no","$back_no","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","PL100","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game","$obj->group_str");
				// DB 저장 //
				$insert = $Mysql->Insert(wp_game_record,$fields,$values);

				//웨이트리프팅 경기 등록 (PL200)
				// TB 필드값 //
				$values = array("$game_code","$gam_uid","$game_kind","$group_no","$lane_no","$back_no","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","PL200","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game","$obj->group_str");
				// DB 저장 //
				$insert = $Mysql->Insert(wp_game_record,$fields,$values);

				//테스트용에다도 등록
				// TB 필드값 //
				$values = array("$game_code","$gam_uid","$game_kind","$group_no","$lane_no","$back_no","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","PL100","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game","$obj->group_str");
				// DB 저장 //
				$insert = $Mysql->Insert(wp_game_record2,$fields,$values);

				// TB 필드값 //
				$values = array("$game_code","$gam_uid","$game_kind","$group_no","$lane_no","$back_no","$level","$obj->pla_id","$obj->pla_name","$obj->spo_code","PL200","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->clu_code","$test_game","$obj->group_str");
				// DB 저장 //
				$insert = $Mysql->Insert(wp_game_record2,$fields,$values);
				//테스트용에다도 등록 끝


			}
		}

	}
}
else if(in_array($spo_code,$wp_league_game))
{
	foreach($team as $key_f => $val_f)
	{
		$_laneno = "lane_".$val_f;
		$lane_no = $$_laneno;
		//대전팀 정보
		$_lteam = "lteam_".$val_f;
		$lteam = $$_lteam;
		$_rteam = "rteam_".$val_f;
		$rteam = $$_rteam;
		$level = 1;

		// TB 필드 //
		$fields = array("game_code","gam_uid","game_kind","lane_no","spo_code","spo_sec_code","spo_code_detail","level","clu_code","other_clu_code");

		// TB 필드값 //
		$values = array("$game_code","$gam_uid","$game_kind","$lane_no","$spo_code","$spo_sec_code","$spo_code_detail","$level","$lteam","$rteam");

		// DB 저장 //
		$insert = $Mysql->Insert(wp_game_record,$fields,$values);

		// 테스트 DB 저장 //
		$insert = $Mysql->Insert(wp_game_record2,$fields,$values);
	}
	//경기결과를 가지고 있는 팀수만큼 등록 $level=0으로 등록한다. 결과 및 등수를 확인시 사용한다.
	foreach($pla_club as $key_f => $val_f)
	{
		$level = 0;

		// TB 필드 //
		$fields = array("game_code","gam_uid","game_kind","lane_no","spo_code","spo_sec_code","spo_code_detail","level","record_tot","record_06","record_07","clu_code");

		// TB 필드값 //
		$values = array("$game_code","$gam_uid","$game_kind","0","$spo_code","$spo_sec_code","$spo_code_detail","0","0","0","0","$val_f");

		// DB 저장 //
		$insert = $Mysql->Insert(wp_game_record,$fields,$values);

		// 테스트 DB 저장 //
		$insert = $Mysql->Insert(wp_game_record2,$fields,$values);
	}
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=match_input.html?spo_code=$spo_code&spo_sec_code=$spo_sec_code&s_gam_uid=$gam_uid&game_code=$game_code&game_kind=$game_kind'>");
?>
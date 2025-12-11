<?
class Tournament
{
	// 각 경기의 토너먼트 갯수 및 출력인원을 생성한다. //
	function getTournaInfo($gam_uid, $game_code,$level)
	{
		// 시범경기 여부
		global $glo_test_game;
		// 경기 게임수
		global $glo_match_cnt;
		// 경기를 뛰는 선수
		global $glo_match_player;
		// BYE 위치정보
		global $glo_bye_num;
		// 경기 설정된 선수
		global $glo_al_player;

		global $game_kind_code;

		// DB 생성 //
		$Mysql = new Mysql;

		// DB 연결 //
		$Mysql->Connect();

		//echo "game_code : ".$game_code."<br>";
		//print_r($game_kind_code);
		//개인전(1), 단체전(2), 시/군대항전(3)
		$game_kind = $game_kind_code[$game_code];
		//echo $game_code;
		//echo $game_kind;

		// 해당 게임의 속하는 세부경기 종목을 쿼리
		$s_que = "SELECT *, group_concat(spo_code_detail order by spo_code_detail ASC separator '/') spo_code_details FROM wp_game_serial WHERE gam_uid='$gam_uid' AND game_code = '$game_code' GROUP BY game_code";
		$s_res = mysql_query($s_que);
		$s_obj = mysql_fetch_object($s_res);

		$game_kind = $s_obj->game_kind;


		$s_tmp = explode("/",$s_obj->spo_code_details);
		$my_spo_code_details = "'";
		foreach($s_tmp as $keys => $vals)
		{
			$my_spo_code_details .= $vals."','";
		}
		// 세부경기 종목
		$my_spo_code_details = substr($my_spo_code_details, 0 ,strlen($my_spo_code_details) - 2);

		//시범경기
		$glo_test_game = $s_obj->test_game;

		//종목별 토너먼트 갯수 쿼리 2017.04.18일 2차종목 삭제로 쿼리 수정함

			
		if($s_obj->spo_code == 'ATHIETICS') 
		   $where = " WHERE gam_uid='$gam_uid' AND spo_code='$s_obj->spo_code' AND spo_sec_code='$s_obj->spo_sec_code' AND sex='$s_obj->sex' AND tro_level_code = '$s_obj->tro_level_code' AND spo_code_detail in ($my_spo_code_details)";
		else 
		  $where = " WHERE gam_uid='$gam_uid' AND spo_code='$s_obj->spo_code' AND sex='$s_obj->sex' AND tro_level_code = '$s_obj->tro_level_code' AND spo_code_detail in ($my_spo_code_details)";		
		
		
		//$orderby = " ORDER BY clu_class_name asc, spo_code ASC, spo_sec_code ASC, spo_code_detail ASC, tro_code ASC, sex ASC, tro_level_code ASC, pla_name ASC";
		$orderby = " ORDER BY pla_name ASC";


		//echo $game_kind;
		// 시/군전이면
		if($game_kind == "3") // 여기부터 0518 //
		{
			$tot_que = "SELECT *,group_concat(pla_name order by spo_code_detail ASC, pla_name ASC separator ',') pla_name  FROM wp_game_player $where GROUP BY clu_code $orderby";
			//echo "AAAAAAAAAAAAAAAAAAAAAAAA";
		}
		// 단체전이면
		else if($game_kind == "2")
		{
			/* 2014-01-23 박민철 수정
				단체전을 group_str에 A팀B팀으로 구분하여 처리하도록 하므로 구례에서 임시 적용했던 부분을
				삭제하고 전체 적용으로 변경함.
			if($s_obj->spo_code == "TENNIS" || $s_obj->spo_code == "HANDLER" ) // 구례에서 단체전 버그 잡기위해 임시적용
			{
				$tot_que = "SELECT *,group_concat(pla_name order by spo_code_detail ASC, pla_name ASC separator ',') pla_name  FROM wp_game_player $where GROUP BY clu_code,group_str, spo_code_detail $orderby";
			echo $tot_que;
			}
			else
			{
				$tot_que = "SELECT *,group_concat(pla_name order by spo_code_detail ASC, pla_name ASC separator ',') pla_name  FROM wp_game_player $where GROUP BY clu_code, spo_code_detail $orderby";
			}
			*/
			$tot_que = "SELECT *,group_concat(pla_name order by spo_code_detail ASC, pla_name ASC separator ',') pla_name  FROM wp_game_player $where GROUP BY clu_code, spo_code_detail,group_str $orderby";
		}
		else
		{
			//개인전
			//2016년 이전 if($s_obj->spo_code == "GOALBALL" || $s_obj->spo_code == "DART" || $s_obj->spo_code == "GATEBALL" || $s_obj->spo_code == "SHUFFLEBOARD") DART Bye 안나오는 문제 해결
            if($s_obj->spo_code == "GOALBALL" || $s_obj->spo_code == "GATEBALL" || $s_obj->spo_code == "SHUFFLEBOARD")
			{
				$tot_que = "SELECT *,group_concat(pla_name order by spo_code_detail ASC, pla_name ASC separator ',') pla_name  FROM wp_game_player $where GROUP BY clu_code";
			}
			else
			{
				$tot_que = "SELECT * FROM wp_game_player $where GROUP BY clu_code,pla_id $orderby";
				//$tot_que = "SELECT *,group_concat(pla_name order by spo_code_detail ASC, pla_name ASC separator ',') pla_name  FROM wp_game_player $where GROUP BY clu_code $orderby";
				//echo "CCCCCCCCCCCCCCCCCCCCC";
			}

		}
		//echo $tot_que;

		/*
		if($game_code == "LAWNBOWIS_LB200-05")
		{
			$tot_que = "SELECT *,group_concat(pla_name order by spo_code_detail ASC, pla_name ASC separator ',') pla_name  FROM wp_game_player $where GROUP BY clu_code, spo_code_detail $orderby";
			echo "DDDDDDDDDDDDDDDDDDDDDD";
		}
		*/

		//echo $tot_que;
		$tot_res = mysql_query($tot_que);

		// 경기를 치뤄야할 인원, 또는 시/군
		$match_person = mysql_num_rows($tot_res);

		// 경기를 치르는 선수들 정보
		$glo_match_player = array();
		while($tot_obj = mysql_fetch_object($tot_res))
		{
			$glo_match_player[$tot_obj->pla_id] =$tot_obj->clu_class_name." ".$tot_obj->pla_name;
		}

		/* 2018-04-04 김용은 수정
			각 경기 인원수 강제 셋팅(탁구 14경, 론볼 2경기) */
		/*if($s_obj->game_code == "TABLETENNIS_TT200-14")
		{
			$match_person = 91;
		}*/
		
		if($s_obj->game_code == "BADUK_BD100-01")
		{
			$match_person = 93;
		}

		else if($s_obj->game_code == "BADUK_BD100-02")
		{
			$match_person = 4;
		}

		else if($s_obj->game_code == "BADUK_BD100-04")
		{
			$match_person = 4;
		}

		else if($s_obj->game_code == "GATEBALL_GB100-01")
		{
			$match_person = 8;
		}

		else if($s_obj->game_code == "VOLLEYBALL_VB100-01")
		{
			$match_person = 8;
		}
		
        else if($s_obj->game_code == 'BOCCIA_BC100-02')
		{
			$match_person = 6;
		}
		else if($s_obj->game_code == 'BOCCIA_BC100-03')
		{
			$match_person = 5;
		}

		else if($s_obj->game_code == 'BOCCIA_BC100-04')
		{
			$match_person = 4;
		}

		else if($s_obj->game_code == "TENNIS_TS100-03")
		{
			$match_person = 4;
		}
		/*
		else if($s_obj->game_code == "CUROLLING_CR100-01")
		{
			$match_person = 5;
		}
		else if($s_obj->game_code == "TARGET_TG100-01")
		{
			$match_person = 14;
		}
		else if($s_obj->game_code == "LAWNBOWIS_LB200-05")
		{
			$match_person = 37;
		}
		else if($s_obj->game_code == "TABLETENNIS_TT200-13" && $level == "4")
		{
			$match_person = 4;
		}
		*/

		if($match_person >=1 && $match_person <= 4) $glo_match_cnt = 2;
		if($match_person >=5 && $match_person <= 8) $glo_match_cnt = 4;
		if($match_person >=9 && $match_person <= 16) $glo_match_cnt = 8;
		if($match_person >=17 && $match_person <= 32) $glo_match_cnt = 16;
		if($match_person >=33 && $match_person <= 64) $glo_match_cnt = 32;
		if($match_person >=65 && $match_person <= 128) $glo_match_cnt = 64;
		//echo $match_person;
		//echo "++".$level."++";

		// 토너먼트 대진표의 부전승 위치 쿼리
		$glo_bye_num = array();
		$query = "SELECT * FROM wp_match_program WHERE match_person = '$match_person' limit 1";
		//echo $query;
		$result = mysql_query($query);
		if(mysql_num_rows($result) > 0){
			$obj = mysql_fetch_object($result);
			$glo_bye_num = explode(":", $obj->bye);
		}

		// 경기기록테이블에 등록된 데이터 검색
		$glo_al_player = array();

		// 단체전이면
		if($game_kind == "3")
		{
			$al_que = "SELECT * FROM wp_game_record WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level='$level' GROUP BY clu_code ORDER BY lane_no";
		}
		else if($game_kind == "2")
		{
			/* 2014-01-23 박민철 수정
				단체전을 group_str에 A팀B팀으로 구분하여 처리하도록 하므로 구례에서 임시 적용했던 부분을
				삭제하고 전체 적용으로 변경함.
			if($s_obj->spo_code == "TENNIS" || $s_obj->spo_code == "HANDLER") // 구례에서 단체전 버그 잡기위해 임시적용
			{
				$al_que = "SELECT *,group_concat(name order by spo_code_detail ASC, name ASC separator ',') name,count(*) cnt  FROM wp_game_record WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level='$level' GROUP BY clu_code, spo_code_detail, group_no ORDER BY lane_no";
			}
			else
			{
				$al_que = "SELECT *,group_concat(name order by spo_code_detail ASC, name ASC separator ',') name,count(*) cnt  FROM wp_game_record WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level='$level' GROUP BY clu_code, spo_code_detail ORDER BY lane_no";
			}
			*/
			$al_que = "SELECT *,group_concat(name order by spo_code_detail ASC, name ASC separator ',') name  FROM wp_game_record WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level='$level' GROUP BY clu_code, spo_code_detail,group_str ORDER BY lane_no";
		}
		else
		{
			$al_que = "SELECT * FROM wp_game_record WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level='$level' ORDER BY lane_no";
		}


        /* 2019-04-29 일 테니스 복식이 개인전으로 보이는 현상 해결 */
		if($game_code == "TENNIS_TS100-03" || $game_code == "TENNIS_TS100-04" || $game_code == "TENNIS_TS100-06" || $game_code == "TENNIS_TS100-08")
		{
			$al_que = "SELECT *,group_concat(name order by spo_code_detail ASC, name ASC separator ',') name  FROM wp_game_record WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level='$level' GROUP BY clu_code, spo_code_detail,group_str ORDER BY lane_no";
		}


		//echo $al_que;
		$al_res = mysql_query($al_que);
		while($al_obj = mysql_fetch_object($al_res))
		{
			$glo_al_player[$al_obj->lane_no] = $al_obj;
		}

		//print_r($glo_al_player);
	}
	// 각 경기의 토너먼트 갯수 및 출력인원을 생성한다. //
	function getTournaInfoTest($gam_uid, $game_code,$level)
	{
		// 시범경기 여부
		global $glo_test_game;
		// 경기 게임수
		global $glo_match_cnt;
		// 경기를 뛰는 선수
		global $glo_match_player;
		// BYE 위치정보
		global $glo_bye_num;
		// 경기 설정된 선수
		global $glo_al_player;

		global $game_kind_code;

		// DB 생성 //
		$Mysql = new Mysql;

		// DB 연결 //
		$Mysql->Connect();

		//개인전(1), 단체전(2), 시/군대항전(3)
		$game_kind = $game_kind_code[$game_code];

		// 해당 게임의 속하는 세부경기 종목을 쿼리
		$s_que = "SELECT *, group_concat(spo_code_detail order by spo_code_detail ASC separator '/') spo_code_details FROM wp_game_serial WHERE gam_uid='$gam_uid' AND game_code = '$game_code' GROUP BY game_code";
		$s_res = mysql_query($s_que);
		$s_obj = mysql_fetch_object($s_res);
		$s_tmp = explode("/",$s_obj->spo_code_details);
		$my_spo_code_details = "'";
		foreach($s_tmp as $keys => $vals)
		{
			$my_spo_code_details .= $vals."','";
		}
		// 세부경기 종목
		$my_spo_code_details = substr($my_spo_code_details, 0 ,strlen($my_spo_code_details) - 2);

		//시범경기
		$glo_test_game = $s_obj->test_game;

		//종목별 토너먼트 갯수 쿼리
		$where = " WHERE gam_uid='$gam_uid' AND spo_code='$s_obj->spo_code' AND spo_sec_code='$s_obj->spo_sec_code' AND sex='$s_obj->sex' AND tro_level_code = '$s_obj->tro_level_code' AND spo_code_detail in ($my_spo_code_details)";
		//$orderby = " ORDER BY clu_class_name asc, spo_code ASC, spo_sec_code ASC, spo_code_detail ASC, tro_code ASC, sex ASC, tro_level_code ASC, pla_name ASC";
		$orderby = " ORDER BY pla_name ASC";

		// 단체전이면
		if($game_kind == "3")
		{
			$tot_que = "SELECT *,group_concat(pla_name order by spo_code_detail ASC, pla_name ASC separator ',') pla_name  FROM wp_game_player $where GROUP BY clu_code $orderby";
		}
		else if($game_kind == "2")
		{
			$tot_que = "SELECT *,group_concat(pla_name order by spo_code_detail ASC, pla_name ASC separator ',') pla_name  FROM wp_game_player $where GROUP BY clu_code, spo_code_detail $orderby";
		}
		else
		{
			$tot_que = "SELECT * FROM wp_game_player $where GROUP BY clu_code,pla_id $orderby";
		}

		$tot_res = mysql_query($tot_que);

		// 경기를 치뤄야할 인원, 또는 시/군
		$match_person = mysql_num_rows($tot_res);

		// 경기를 치르는 선수들 정보
		$glo_match_player = array();
		while($tot_obj = mysql_fetch_object($tot_res))
		{
			$glo_match_player[$tot_obj->pla_id] =$tot_obj->clu_class_name." ".$tot_obj->pla_name;
		}

		if($match_person >=1 && $match_person <= 4) $glo_match_cnt = 2;
		if($match_person >=5 && $match_person <= 8) $glo_match_cnt = 4;
		if($match_person >=9 && $match_person <= 16) $glo_match_cnt = 8;
		if($match_person >=17 && $match_person <= 32) $glo_match_cnt = 16;
		if($match_person >=33 && $match_person <= 64) $glo_match_cnt = 32;
		if($match_person >=65 && $match_person <= 128) $glo_match_cnt = 64;

		// 토너먼트 대진표의 부전승 위치 쿼리
		$glo_bye_num = array();
		$query = "SELECT * FROM wp_match_program WHERE match_person = '$match_person' limit 1";
		$result = mysql_query($query);
		if(mysql_num_rows($result) > 0){
			$obj = mysql_fetch_object($result);
			$glo_bye_num = explode(":", $obj->bye);
		}
		// 경기기록테이블에 등록된 데이터 검색
		$glo_al_player = array();

		// 단체전이면
		if($game_kind == "3")
		{
			$al_que = "SELECT * FROM wp_game_record2 WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level='$level' GROUP BY clu_code ORDER BY lane_no";
		}
		else if($game_kind == "2")
		{
			$al_que = "SELECT *,group_concat(name order by spo_code_detail ASC, name ASC separator ',') name,count(*) cnt  FROM wp_game_record2 WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level='$level' GROUP BY clu_code, spo_code_detail ORDER BY lane_no";
		}
		else
		{
			$al_que = "SELECT * FROM wp_game_record2 WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level='$level' ORDER BY lane_no";
		}
		//echo $al_que;
		$al_res = mysql_query($al_que);
		while($al_obj = mysql_fetch_object($al_res))
		{
			$glo_al_player[$al_obj->lane_no] = $al_obj;
		}

		//print_r($glo_al_player);
	}
}
?>
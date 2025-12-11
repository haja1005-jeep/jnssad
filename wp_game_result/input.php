<?
// Session 시작 //
require_once "../wp_library/head.php";

// 공백 체크 //
Null_Check($gam_uid);
Null_Check($spo_code);
Null_Check($game_code);
//Null_Check($level);

// 빈칸 체크 //
Input_Check($gam_uid);
Input_Check($game_code);

if($done == "done")
{
	//2018.04.05. 상장번호 중복 방지를 위해 경기종료일 추가 함.
	$timestamp = time(); 
	
	// 경기 종료 업데이트 //
	mysql_query("UPDATE wp_game_serial set game_done = '1', signdate='$timestamp' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");

	// 페이지 이동 //
	echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=input&game_code=$game_code&type=$type&sch_val=$sch_val'>");
}

//=== 대회 정보 체크 시작
//대회정보검색
$query = "SELECT * FROM wp_game_ctrl where uid='$gam_uid'";

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
//	Error("AUTH_ERROR");
//	exit;
}
//=== 대회 정보 체크 끝


// 테이블 선택
if($type == "real") $tbName = " wp_game_record ";
else $tbName = " wp_game_record2 ";

//리그전 경기이면 점수 부분을 0으로 변경한다.
if(in_array($spo_code,$wp_league_game))
{
	mysql_query("UPDATE $tbName set record_tot=0, record_06=0,record_07=0 WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND level='0'");
}


//2018년 26회 여수대회 부터 육상 트랙 멀리뛰기 등수 산출 기존 필드와 똑깥이
if($game_code == 'ATHIETICS_AS100-46' || $game_code == 'ATHIETICS_AS100-47' || $game_code == 'ATHIETICS_AS100-48' || $game_code == 'ATHIETICS_AS100-49' || $game_code == 'ATHIETICS_AS100-50' || $game_code == 'ATHIETICS_AS100-51')
{
   $spo_sec_code = "AS200";
}
//--> 여기까지

foreach($pla_id as $tkeys => $tvalues){
	if(in_array($spo_code,$wp_tot_game)){ //토너먼트 게임이면

		// 해당선수의 승/패 여부
		$tmp_win = "win_".$tvalues;
		$win = $$tmp_win;

		// 해당 선수의 점수
		$tmp_record_01 = "s_01_".$tvalues;
		$tmp_record_02 = "s_02_".$tvalues;
		$tmp_record_03 = "s_03_".$tvalues;
		$tmp_record_04 = "s_04_".$tvalues;
		$tmp_record_05 = "s_05_".$tvalues;
		$record_01 = $$tmp_record_01;
		$record_02 = $$tmp_record_02;
		$record_03 = $$tmp_record_03;
		$record_04 = $$tmp_record_04;
		$record_05 = $$tmp_record_05;




		// 단셋트 경기이면
		if($spo_code == "LAWNBOWL" || $spo_code == "GOALBALL" || $spo_code == "BOCCIA" || $spo_code == "GATEBALL"
		 || $spo_code == "BILLIARDS" || $spo_code == "SHUFFLEBOARD" || $spo_code == "CUROLLING" || $spo_code == "TARGET" || $spo_code == "FENCING")
		{
			$record_tot = $record_01;
		}
		else if($spo_code == "VOLLEYBALL" || $spo_code == "BADMINTON" || $spo_code == "BOCCIA" || $spo_code == "HANDLER" || $spo_code == "DART")
		{
			$my_win = 0;
			$you_win = 0;
			// 1세트
			$my_score = explode(":", $record_01);
			if($my_score[0] > $my_score[1]) $my_win++;
			else $you_win++;
			$my_score = explode(":", $record_02);
			if($my_score[0] > $my_score[1]) $my_win++;
			else $you_win++;
			$my_score = explode(":", $record_03);
			if($my_score[0] > $my_score[1]) $my_win++;
			else $you_win++;
			$record_tot = $my_win.":".$you_win;
		}
		else if($spo_code == "TABLETENNIS")
		{
			$my_win = 0;
			$you_win = 0;
			// 1세트
			$my_score = explode(":", $record_01);
			if($my_score[0] > $my_score[1]) $my_win++;
			else $you_win++;
			$my_score = explode(":", $record_02);
			if($my_score[0] > $my_score[1]) $my_win++;
			else $you_win++;
			$my_score = explode(":", $record_03);
			if($my_score[0] > $my_score[1]) $my_win++;
			else $you_win++;
			$my_score = explode(":", $record_04);
			if($my_score[0] > $my_score[1]) $my_win++;
			else $you_win++;
			$my_score = explode(":", $record_05);
			if($my_score[0] > $my_score[1]) $my_win++;
			else $you_win++;
			$record_tot = $my_win.":".$you_win;
		}
		else
		{
			$record_tot = $record_01;
		}

		$my_lane_no = $lane_no[$tvalues];

		// 2013-06-20 점수집계시 사용하기 위하여 해당 소스를 추가한다.
		// 기권자/실격자는 점수 집계에서 처리하지 않기 위해 '-'값을 입력하면 기권처리로 DB에 셋팅한다.
		if($record_tot == "-") $win = "G"; //기권
		if($record_tot == "x") $win = "N"; //실격
		// 2013-06-20

		//나의 점수 UPDATE
		// TB 필드명 //
		$fields = array("record_01","record_02","record_03","record_04","record_05","record_06","record_07","record_08","record_09","record_10","record_tot","win");
		// TB 필드값 //
		$values = array("$record_01","$record_02","$record_03","$record_04","$record_05","$record_06","$record_07","$record_08","$record_09","$record_10","$record_tot","$win");
		//디비등록
		$update = $Mysql->Update($tbName, $fields, $values," WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND level='$level' AND lane_no = '$my_lane_no'");
	}
	else if(in_array($spo_code,$wp_league_game))
	{ //리그전 게임이면
		$pla_id = explode("/",$tvalues);
		$home = $pla_id[0]; // 홈팀
		$away = $pla_id[1]; //어웨이팀

		$_record_01 = "record_01_".$home;
		$record_01 = $$_record_01;

		$_tmp_r01 = explode(":",$record_01);
		$h_score = $_tmp_r01[0];
		$a_score = $_tmp_r01[1];
		if($h_score > $a_score)
		{
			$home_win = "W";
			$away_win = "F";
			$home_score = 3;
			$away_score = 0;
		}
		else if($h_score < $a_score)
		{
			$home_win = "F";
			$away_win = "W";
			$home_score = 0;
			$away_score = 3;
		}
		else
		{
			$home_win = "=";
			$away_win = "=";
			$home_score = 1;
			$away_score = 1;
		}

		// TB 필드명 //
		$fields = array("record_01","win");

		// TB 필드값 //
		$values = array("$record_01","$home_win");
		$update = $Mysql->Update($tbName, $fields, $values," WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND level='1' AND clu_code ='$home' AND other_clu_code='$away'");

		//각 시군에 승패에 따른 승점을 입력한다.
		mysql_query("UPDATE $tbName set record_tot=record_tot+$home_score, record_06=record_06+$h_score WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND level='0' AND clu_code ='$home'");
		mysql_query("UPDATE $tbName set record_tot=record_tot+$away_score, record_07=record_07-$h_score WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND level='0' AND clu_code ='$away'");

	} else if(in_array($spo_code,$wp_score_game)){ //기록경기이면
		$_groupno = "groupno_".$tvalues;
		$_laneno = "laneno_".$tvalues;
		$_backno = "backno_".$tvalues;
		if("weight_".$tvalues)
		{
			$_weight = "weight_".$tvalues;
		}

		$_record_01 = "record_01_".$tvalues;
		$_record_02 = "record_02_".$tvalues;
		$_record_03 = "record_03_".$tvalues;
		$_record_04 = "record_04_".$tvalues;
		$_record_05 = "record_05_".$tvalues;
		$_test_game = "test_game_".$tvalues;
		$_record_prz = "record_prz_".$tvalues;
		$_win = "win_".$tvalues;


        //댄스스포츠는 순위 직접입력
		$_rank_tot_01 =  "rank_tot_01_".$tvalues;
		$rank_tot     = $$_rank_tot_01;

		$group_no = $$_groupno;
		$lane_no = $$_laneno;
		$back_no = $$_backno;
		$weight = $$_weight;

		$record_01 = $$_record_01;
		$record_02 = $$_record_02;
		$record_03 = $$_record_03;
		$record_04 = $$_record_04;
		$record_05 = $$_record_05;
		$test_game = $$_test_game;
		$record_prz = $$_record_prz;
		$win = $$_win;


		if(($spo_code == "ATHIETICS" && $spo_sec_code == "AS100") || $spo_code == "SWIMMING"  || $spo_code == "CYCLING"  || $spo_code == "ROWING"){
			$_tmp = explode(":", $record_01);
			if(count($_tmp) > 1){ //분을 넘었으면 초로 변환
				$sec = $_tmp[0] * 60;
				$record_tot = $sec + $_tmp[1];
			}else{
				$record_tot = $_tmp[0];
			}
		}else{
			$record_tot = $record_01+ $record_02+ $record_03+ $record_04+ $record_05;
		}
		$record_best = $record_01;
		if($record_02 > $record_best) $record_best = $record_02;
		if($record_03 > $record_best) $record_best = $record_03;
		if($record_04 > $record_best) $record_best = $record_04;
		if($record_05 > $record_best) $record_best = $record_05;

		//볼링이면 최고기록에 평균점수를 넣는다.
		if($spo_code == "BOWLING"){
			$record_best = floor(($record_01+$record_02+$record_03) / 3);
		}

		if($win == "G" || $win == "N"){
			//기권이나 실격이면 점수처리하지 않음
			//$record_01 = "-";
			//$record_02 = "-";
			//$record_03 = "-";
			//$record_04 = "-";
			//$record_05 = "-";
			//$record_best = "-";
			//$record_tot = "-";
		}

		// TB 필드명 //
		/* 2017.05.16 시범경기(미성립경기) 정식경기로 변경되는 오류 수정 test_game updatting 안되게
		$fields = array("group_no","lane_no","back_no","weight","record_01","record_02","record_03","record_04","record_05","record_tot","record_best","record_prz","test_game","win");
		// TB 필드값 //
		$values = array("$group_no","$lane_no","$back_no","$weight","$record_01","$record_02","$record_03","$record_04","$record_05","$record_tot","$record_best", "$record_prz","$test_game","$win");*/


		$fields = array("group_no","lane_no","back_no","weight","record_01","record_02","record_03","record_04","record_05","record_tot","record_best","record_prz","win");
		// TB 필드값 //
		$values = array("$group_no","$lane_no","$back_no","$weight","$record_01","$record_02","$record_03","$record_04","$record_05","$record_tot","$record_best", "$record_prz","$win");


		
		// 2018.04 26회 여수대회 댄스 스포츠 순위 직접입력	2018.04 -->
		if($spo_code == "DANCE" && $game_kind == "2") { // 
	      $fields = array("group_no","lane_no","back_no","weight","rank_tot","record_01","record_02","record_03","record_04","record_05","record_tot","record_best","record_prz","win");
		  // TB 필드값 //
		  $values = array("$group_no","$lane_no","$back_no","$weight","$rank_tot","$record_01","$record_02","$record_03","$record_04","$record_05","$record_tot","$record_best", "$record_prz","$win");
		}
        //여기까지 추가 함-->

		$update = $Mysql->Update($tbName, $fields, $values," WHERE gam_uid='$gam_uid' AND uid='$tvalues' ");

		// 21회 경기 구례에서 임시추가
		if($spo_code == "ROWING" && $game_kind == "2") { // 실내조정 단체전이면 같은 팀정보 등록
			$update = $Mysql->Update($tbName, $fields, $values," WHERE gam_uid='$gam_uid' AND game_code = '$game_code' AND group_no='$group_no' AND lane_no = '$lane_no'");
		}

        




		// 역도일경우
		if($spo_code == "POWERLIFTING"){
			$_record_01 = "r_record_01_".$tvalues;
			$_record_02 = "r_record_02_".$tvalues;
			$_record_03 = "r_record_03_".$tvalues;
			$_record_04 = "r_record_04_".$tvalues;
			$_record_05 = "r_record_05_".$tvalues;
			$_record_prz = "r_record_prz_".$tvalues;
			$_r_uid = "r_uid_".$tvalues;
			$_r_uid2 = "r_uid2_".$tvalues;
			$_weight = "weight_".$tvalues;

			$r_record_01 = $$_record_01;
			$r_record_02 = $$_record_02;
			$r_record_03 = $$_record_03;
			$r_record_04 = $$_record_04;
			$r_record_05 = $$_record_05;
			$record_prz = $$_record_prz;
			$r_uid = $$_r_uid;
			$r_uid2 = $$_r_uid2;
			$r_weight = $$_weight;

			$r_record_tot = $r_record_01+ $r_record_02+ $r_record_03+ $r_record_04+ $r_record_05;
			$r_record_best = $r_record_01;
			if($r_record_02 > $r_record_01) $r_record_best = $r_record_02;
			if($r_record_03 > $r_record_02) $r_record_best = $r_record_03;
			if($r_record_04 > $r_record_03) $r_record_best = $r_record_04;
			if($r_record_05 > $r_record_04) $r_record_best = $r_record_05;

			if($win == "G" || $win == "N")
			{
				//기권이나 실격이면 점수처리하지 않음
				$r_record_01 = "-";
				$r_record_02 = "-";
				$r_record_03 = "-";
				$r_record_04 = "-";
				$r_record_05 = "-";
				$r_record_best = "-";
				$r_record_tot = "-";
			}

			// TB 필드명 //
			$fields = array("group_no","lane_no","back_no","record_01","record_02","record_03","record_04","record_05","record_tot","record_best","record_prz","win","weight");

			// TB 필드값 //
			$values = array("$group_no","$lane_no","$back_no","$r_record_01","$r_record_02","$r_record_03","$r_record_04","$r_record_05","$r_record_tot","$r_record_best", "$record_prz","$win","$r_weight");
			$update = $Mysql->Update($tbName, $fields, $values," WHERE gam_uid='$gam_uid' AND uid='$r_uid' ");

			//종합점수 입력
			$t_record_01 = $record_01 + $r_record_01;
			$t_record_02 = $record_02 + $r_record_02;
			$t_record_03 = $record_03 + $r_record_03;
			$t_record_04 = $record_04 + $r_record_04;
			$t_record_05 = $record_05 + $r_record_05;

			$l_record_best = $record_01;
			if($record_02 > $record_01) $l_record_best = $record_02;
			if($record_03 > $record_02) $l_record_best = $record_03;
			if($record_04 > $record_03) $l_record_best = $record_04;
			if($record_05 > $record_04) $l_record_best = $record_05;
			$r_record_best = $r_record_01;
			if($r_record_02 > $r_record_01) $r_record_best = $r_record_02;
			if($r_record_03 > $r_record_02) $r_record_best = $r_record_03;
			if($r_record_04 > $r_record_03) $r_record_best = $r_record_04;
			if($r_record_05 > $r_record_04) $r_record_best = $r_record_05;

			$t_record_tot = $l_record_best + $r_record_best; //총점
			//종합은 총점을 베스트에 넣는다.
			$t_record_best = $t_record_tot;
			if($win == "G" || $win == "N"){
				//기권이나 실격이면 점수처리하지 않음
				$t_record_01 = "-";
				$t_record_02 = "-";
				$t_record_03 = "-";
				$t_record_04 = "-";
				$t_record_05 = "-";
				$t_record_best = "-";
				$t_record_tot = "-";
			}
			// TB 필드값 //
			$values = array("$group_no","$lane_no","$back_no","$t_record_01","$t_record_02","$t_record_03","$t_record_04","$t_record_05","$t_record_tot","$t_record_best", "$record_prz","$win","$r_weight");
			$update = $Mysql->Update($tbName, $fields, $values," WHERE gam_uid='$gam_uid' AND uid='$r_uid2'");
		}
	}
} //foreach

if($update){
	//육상 트랙
	if($spo_code == "ATHIETICS"){
		if($spo_sec_code == "AS100"){ //육상트랙
			//기존 랭크순위 클리어
			mysql_query("UPDATE  $tbName set rank_tot = '',rank='' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");
			//전체 순위 결정
			$que = "SELECT uid, record_tot,id FROM  $tbName WHERE gam_uid = '$gam_uid' AND record_tot not in ('', '-','0') AND game_code='$game_code' ORDER BY record_tot ASC";
			$res = mysql_query($que);
			$rnk_arr = array();
			while($obj = mysql_fetch_object($res)){
				$rnk_arr[$obj->uid] = $obj->record_tot;
			}
			asort($rnk_arr); //순위별로 정렬
			$rnk = 1;
			foreach($rnk_arr as $k => $v){
				mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
				$rnk++;
			}
			//조별 순위 결정
			$que = "SELECT uid, group_no,record_best,id,record_tot FROM $tbName WHERE gam_uid = '$gam_uid' AND record_tot not in ('', '-','0') AND game_code='$game_code' ORDER BY group_no ASC, record_tot ASC";
			$res = mysql_query($que);

			$rnk_arr = array();
			$b_group_no = "";
			while($obj = mysql_fetch_object($res)){
				if($b_group_no != "" && $obj->group_no != $b_group_no){
					$rnk_arr[] = $_t_arr;
					$_t_arr = array();
				}
				$_t_arr[$obj->uid] = $obj->record_tot;
				$b_group_no = $obj->group_no;
			}
			$rnk_arr[] = $_t_arr;

			for($i=0; $i< count($rnk_arr); $i++){
				$r_arr = $rnk_arr[$i];
				asort($r_arr); //순위별로 정렬
				$rnk = 1;
				foreach($r_arr as $k => $v){
					mysql_query("UPDATE $tbName set rank = $rnk WHERE uid='$k'");
					$rnk++;
				}
			}
		}
		if($spo_sec_code == "AS200"){ //육상필드
			//기존 랭크순위 클리어
			mysql_query("UPDATE $tbName set rank_tot = '',rank='' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");
			//전체 순위 결정
			$que = "SELECT uid, record_best,id FROM  $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' ORDER BY record_best DESC";
			$res = mysql_query($que);
			$rnk_arr = array();
			while($obj = mysql_fetch_object($res)){
				$rnk_arr[$obj->uid] = $obj->record_best;
			}
			arsort($rnk_arr); //순위별로 정렬
			$rnk = 1;
			foreach($rnk_arr as $k => $v){
				if($b_tot == $v) { //공동순위
					$rnk = $rnk - 1;
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
					$rnk = $rnk+2;
				}else{
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
					$rnk++;
				}
				$b_tot = $v;
			}
			//조별 순위 결정
			$que = "SELECT uid, group_no,record_tot,id FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' ORDER BY group_no ASC, record_best DESC";
			$res = mysql_query($que);

			$rnk_arr = array();
			$b_group_no = "";
			while($obj = mysql_fetch_object($res)){
				if($b_group_no != "" && $obj->group_no != $b_group_no){
					$rnk_arr[] = $_t_arr;
					$_t_arr = array();
				}
				$_t_arr[$obj->uid] = $obj->record_best;
				$b_group_no = $obj->group_no;
			}
			$rnk_arr[] = $_t_arr;

			for($i=0; $i< count($rnk_arr); $i++){
				$r_arr = $rnk_arr[$i];
				arsort($r_arr); //순위별로 정렬
				$rnk = 1;
				foreach($r_arr as $k => $v){
					mysql_query("UPDATE $tbName set rank = $rnk WHERE uid='$k'");
					$rnk++;
				}
			}
		} //if
	}
	// 수영, 싸이클
	else if($spo_code == "SWIMMING" || $spo_code == "CYCLING"){
		//기존 랭크순위 클리어
		mysql_query("UPDATE $tbName set rank_tot = '',rank='' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");
		//전체 순위 결정
		$que = "SELECT uid, record_tot,id FROM $tbName WHERE gam_uid = '$gam_uid' AND record_tot not in ('', '-','0') AND game_code='$game_code' ORDER BY record_tot ASC";
		$res = mysql_query($que);
		$rnk_arr = array();
		while($obj = mysql_fetch_object($res)){
			$rnk_arr[$obj->uid] = $obj->record_tot;
		}
		asort($rnk_arr); //순위별로 정렬
		$rnk = 1;
		foreach($rnk_arr as $k => $v){
			mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
			$rnk++;
		}
		//조별 순위 결정
		$orderby = " ORDER BY group_no ASC, record_tot ASC";
		$que2 = "SELECT uid, group_no,record_tot,id,lane_no FROM $tbName WHERE gam_uid = '$gam_uid' AND record_tot not in ('', '-','0') AND game_code='$game_code' $orderby";
		$res2 = mysql_query($que2);
		$rnk_arr = array();
		$b_group_no = "";
		while($obj2 = mysql_fetch_object($res2)){
			if($b_group_no != "" && $obj2->group_no != $b_group_no){
				$rnk_arr[] = $_t_arr;
				$_t_arr = array();
			}
			$_t_arr[$obj2->uid] = $obj2->record_tot;
			$b_group_no = $obj2->group_no;
		}
		$rnk_arr[] = $_t_arr;

		for($i=0; $i< count($rnk_arr); $i++){
			$r_arr = $rnk_arr[$i];
			asort($r_arr); //순위별로 정렬
			$rnk = 1;
			foreach($r_arr as $k => $v){
				mysql_query("UPDATE $tbName set rank = $rnk WHERE uid='$k'");
				$rnk++;
			}
		}
	}
	// 2014-09-19 박민철 추가 - 실내조정경기를 별도로 분리하여 순위 책정
	else if($spo_code == "ROWING"){
		//기존 랭크순위 클리어
		mysql_query("UPDATE $tbName set rank_tot = '',rank='' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");
		//전체 순위 결정
		$que = "SELECT uid, record_tot,id FROM $tbName WHERE gam_uid = '$gam_uid' AND record_tot not in ('', '-','0') AND game_code='$game_code' ORDER BY record_tot ASC";
		$res = mysql_query($que);
		$rnk_arr = array();
		while($obj = mysql_fetch_object($res)){
			$rnk_arr[$obj->uid] = $obj->record_tot;
		}
		asort($rnk_arr); //순위별로 정렬
		$rnk = 1;
		foreach($rnk_arr as $k => $v){
			mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
			$rnk++;
		}

		//조별 순위 결정
		if($game_kind == "2")
		{
			$orderby = " GROUP BY lane_no ORDER BY group_no ASC, record_tot ASC";
		}
		else
		{
			$orderby = " ORDER BY group_no ASC, record_tot ASC";
		}
		$que2 = "SELECT uid, group_no,record_tot,id,lane_no FROM $tbName WHERE gam_uid = '$gam_uid' AND record_tot not in ('', '-','0') AND game_code='$game_code' $orderby";

		$res2 = mysql_query($que2);
		$rnk_arr = array();
		$b_group_no = "";
		while($obj2 = mysql_fetch_object($res2)){
			if($b_group_no != "" && $obj2->group_no != $b_group_no){
				$rnk_arr[] = $_t_arr;
				$_t_arr = array();
			}
			$_t_arr[$obj2->uid] = $obj2->record_tot;
			$b_group_no = $obj2->group_no;
		}
		$rnk_arr[] = $_t_arr;

		for($i=0; $i< count($rnk_arr); $i++){
			$r_arr = $rnk_arr[$i];
			asort($r_arr); //순위별로 정렬
			$rnk = 1;
			foreach($r_arr as $k => $v){
				mysql_query("UPDATE $tbName set rank = $rnk WHERE uid='$k'");
				if($game_kind == "2") { // 실내조정 단체전이면 같은 팀정보 등록
					$x_que = "SELECT * FROM $tbName WHERE uid = '$k'";
					$x_res = mysql_query($x_que);
					$x_obj = mysql_fetch_object($x_res);
					mysql_query("UPDATE $tbName set rank = $rnk, rank_tot=$rnk WHERE uid in (SELECT * FROM (SELECT uid FROM $tbName WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND group_no='$x_obj->group_no' AND lane_no = '$x_obj->lane_no') as tmp)");
				}
				$rnk++;
			}
		}
	}
	//사격
	else if($spo_code == "SHUOOTING"){
		//기존 랭크순위 클리어
		mysql_query("UPDATE $tbName set rank_tot = '',rank='' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");
		//전체 순위 결정
		$que = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND record_tot not in ('', '-','0') AND game_code='$game_code' ORDER BY record_tot DESC";
		$res = mysql_query($que);
		$rnk_arr = array();
		while($obj = mysql_fetch_object($res)){
			$rnk_arr[$obj->uid] = $obj->record_tot;
		}
		arsort($rnk_arr); //순위별로 정렬
		$rnk = 1;
		$b_tot = 0;
		foreach($rnk_arr as $k => $v){
			if($b_tot == $v) { //공동순위
				$rnk = $rnk - 1;
				mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
				$rnk = $rnk+2;
			}else{
				mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
				$rnk++;
			}
			$b_tot = $v;
		}
	}


	else if($spo_code == "DANCE"){
		

	}
	
	//역도
	else if($spo_code == "POWERLIFTING"){
		//기존 랭크순위 클리어
		mysql_query("UPDATE $tbName set rank_tot = '',rank='' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");

		//파워리프팅 순위 결정
		$que = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' AND spo_sec_code='PL100' ORDER BY record_best DESC, weight ASC";
		$res = mysql_query($que);
		$rnk_arr = array();
		while($obj = mysql_fetch_object($res)){
			$rnk_arr[$obj->uid] = $obj->record_best;
			$weg_arr[$obj->uid] = $obj->weight;
		}

		arsort($rnk_arr); //순위별로 정렬

		$rnk = 1;
		foreach($rnk_arr as $k => $v){

		//echo $rnk_arr[$k];
		//echo $weg_arr[$k];
		//echo "</br>";

			mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
			$rnk++;
		}
//exit;
	
		//웨이트리프팅 순위 결정
		$que = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' AND spo_sec_code='PL200' ORDER BY record_best DESC, weight ASC";
		$res = mysql_query($que);
		$rnk_arr = array();
		while($obj = mysql_fetch_object($res)){
			$rnk_arr[$obj->uid] = $obj->record_best;
		}
		arsort($rnk_arr); //순위별로 정렬
		$rnk = 1;
		foreach($rnk_arr as $k => $v){
			mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
			$rnk++;
		}

		//벤치프레스 종합 순위 결정
		$que = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' AND spo_sec_code='PL300' ORDER BY record_best DESC, weight ASC";
		$res = mysql_query($que);
		$rnk_arr = array();
		while($obj = mysql_fetch_object($res)){
			$rnk_arr[$obj->uid] = $obj->record_best;
		}
		arsort($rnk_arr); //순위별로 정렬
		$rnk = 1;
		foreach($rnk_arr as $k => $v){
			mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
			$rnk++;
		}
		//스쿼트 순위 결정
		$que = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' AND spo_sec_code='PL400' ORDER BY record_best DESC, weight ASC";
		$res = mysql_query($que);
		$rnk_arr = array();
		while($obj = mysql_fetch_object($res)){
			$rnk_arr[$obj->uid] = $obj->record_best;
		}
		arsort($rnk_arr); //순위별로 정렬
		$rnk = 1;
		foreach($rnk_arr as $k => $v){
			mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
			$rnk++;
		}

		//데드리프트 순위 결정
		$que = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' AND spo_sec_code='PL500' ORDER BY record_best DESC, weight * 1 DESC";
		$res = mysql_query($que);
		$rnk_arr = array();
		while($obj = mysql_fetch_object($res)){
			$rnk_arr[$obj->uid] = $obj->record_best;
		}
		arsort($rnk_arr); //순위별로 정렬
		$rnk = 1;
		foreach($rnk_arr as $k => $v){
			mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
			$rnk++;
		}
		//파워리프트종합 순위 결정
		$que = "SELECT * FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' AND spo_sec_code='PL600' ORDER BY record_best DESC, weight ASC";
		$res = mysql_query($que);
		$rnk_arr = array();
		while($obj = mysql_fetch_object($res)){
			$rnk_arr[$obj->uid] = $obj->record_best;
		}
		arsort($rnk_arr); //순위별로 정렬
		$rnk = 1;
		foreach($rnk_arr as $k => $v){
			mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$k'");
			$rnk++;
		}
	}
	//볼링
	else if($spo_code == "BOWLING")
	{
		//기존 랭크순위 클리어
		mysql_query("UPDATE $tbName set rank_tot = '',rank='' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");

		// 개인전이면
		if($game_kind == "1")
		{
			//전체 순위 결정
			$que = "SELECT id, group_no, lane_no, name, CAST(record_tot AS UNSIGNED) record_tot FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' ORDER BY record_tot DESC";
			$res = mysql_query($que);
			$rnk = 1;
			$b_tot = 0;
			while($obj = mysql_fetch_object($res)){
				if($b_tot == $obj->record_tot) { //공동순위
					$rnk = $rnk - 1;
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND id = '$obj->id'");
					$rnk = $rnk+2;
				}else{
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND id = '$obj->id'");
					$rnk++;
				}
				$b_tot = $obj->record_tot;
			}
		}
		else
		{
			//단체전이면
			//전체 순위 결정
			$que = "SELECT group_no,lane_no,name,sum(record_tot) tot FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' GROUP BY group_no,lane_no ORDER BY tot DESC";
			$res = mysql_query($que);
			$rnk = 1;
			$b_tot = 0;
			while($obj = mysql_fetch_object($res)){
				if($b_tot == $obj->tot) { //공동순위
					$rnk = $rnk - 1;
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND group_no='$obj->group_no' AND lane_no='$obj->lane_no'");
					$rnk = $rnk+2;
				}else{
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND group_no='$obj->group_no' AND lane_no='$obj->lane_no'");
					$rnk++;
				}
				$b_tot = $obj->tot;
			}
		}
	}

	//양궁
	else if($spo_code == "ARCHERY")
	{
		//기존 랭크순위 클리어
		mysql_query("UPDATE $tbName set rank_tot = '',rank='' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");

		// 개인전이면
		if($game_kind == "1")
		{
			//전체 순위 결정
			$que = "SELECT id, group_no, lane_no, name, CAST(record_tot AS UNSIGNED) record_tot FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' ORDER BY record_tot DESC";
			$res = mysql_query($que);
			$rnk = 1;
			$b_tot = 0;
			while($obj = mysql_fetch_object($res)){
				if($b_tot == $obj->record_tot) { //공동순위
					$rnk = $rnk - 1;
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND id = '$obj->id'");
					$rnk = $rnk+2;
				}else{
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND id = '$obj->id'");
					$rnk++;
				}
				$b_tot = $obj->record_tot;
			}
		}
		else
		{
			//단체전이면
			//전체 순위 결정
			$que = "SELECT group_no,lane_no,name,sum(record_tot) tot FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' GROUP BY group_no,lane_no ORDER BY tot DESC";
			$res = mysql_query($que);
			$rnk = 1;
			$b_tot = 0;
			while($obj = mysql_fetch_object($res)){
				if($b_tot == $obj->tot) { //공동순위
					$rnk = $rnk - 1;
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND group_no='$obj->group_no' AND lane_no='$obj->lane_no'");
					$rnk = $rnk+2;
				}else{
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND group_no='$obj->group_no' AND lane_no='$obj->lane_no'");
					$rnk++;
				}
				$b_tot = $obj->tot;
			}
		}
	}

	// 파크골프 //
	else if($spo_code == "GOLF")
	{
		//기존 랭크순위 클리어
		mysql_query("UPDATE $tbName set rank_tot = '',rank='' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");

		// 개인전이면
		if($game_kind == "1")
		{
			//전체 순위 결정
			$que = "SELECT id, group_no, lane_no, name, CAST(record_tot AS UNSIGNED) record_tot FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' ORDER BY record_tot ASC";
			$res = mysql_query($que);
			$rnk = 1;
			$b_tot = 0;
			while($obj = mysql_fetch_object($res))
			{
				if($b_tot == $obj->record_tot)
				{ //공동순위
					$rnk = $rnk - 1;
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND id = '$obj->id'");
					$rnk = $rnk+2;
				}
				else
				{
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND id = '$obj->id'");
					$rnk++;
				}
				$b_tot = $obj->record_tot;
			}
		}
		else
		{
			//단체전이면
			//전체 순위 결정
			$que = "SELECT group_no,lane_no,name,sum(record_tot) tot FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' GROUP BY group_no,lane_no ORDER BY tot ASC";
			$res = mysql_query($que);
			$rnk = 1;
			$b_tot = 0;
			while($obj = mysql_fetch_object($res))
			{
				if($b_tot == $obj->tot)
				{ //공동순위
					$rnk = $rnk - 1;
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND group_no='$obj->group_no' AND lane_no='$obj->lane_no'");
					$rnk = $rnk+2;
				}
				else
				{
					mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND group_no='$obj->group_no' AND lane_no='$obj->lane_no'");
					$rnk++;
				}
				$b_tot = $obj->tot;
			}
		}
	}
	// 승마 2014-09-19 박민철 승마 점수 입력 추가 //
	else if($spo_code == "HORSERIDING")
	{

		//기존 랭크순위 클리어
		mysql_query("UPDATE $tbName set rank_tot = '',rank='' WHERE gam_uid='$gam_uid' AND game_code='$game_code'");

		// 개인전이면
		if($game_kind == "1")
		{
			//전체 순위 결정
			$que = "SELECT id, group_no, lane_no, name, CAST(record_tot AS UNSIGNED) record_tot FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' ORDER BY record_tot DESC";
			$res = mysql_query($que);
			$rnk = 1;
			$b_tot = 0;
			while($obj = mysql_fetch_object($res))
			{
				if($b_tot == $obj->record_tot)
				{ //공동순위
					$rnk = $rnk - 1;
					mysql_query("UPDATE $tbName set rank_tot = $rnk, rank=$rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND id = '$obj->id'");
					$rnk = $rnk+2;
				}
				else
				{
					mysql_query("UPDATE $tbName set rank_tot = $rnk, rank=$rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND id = '$obj->id'");
					$rnk++;
				}
				$b_tot = $obj->record_tot;
			}
		}
		else
		{
			//단체전이면
			//전체 순위 결정
			$que = "SELECT group_no,lane_no,name,sum(record_tot) tot FROM $tbName WHERE gam_uid = '$gam_uid' AND record_best not in ('', '-','0') AND game_code='$game_code' GROUP BY group_no,lane_no ORDER BY tot DESC";
			$res = mysql_query($que);
			$rnk = 1;
			$b_tot = 0;
			while($obj = mysql_fetch_object($res))
			{
				if($b_tot == $obj->tot)
				{ //공동순위
					$rnk = $rnk - 1;
					mysql_query("UPDATE $tbName set rank_tot = $rnk, rank=$rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND group_no='$obj->group_no' AND lane_no='$obj->lane_no'");
					$rnk = $rnk+2;
				}
				else
				{
					mysql_query("UPDATE $tbName set rank_tot = $rnk, rank=$rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND group_no='$obj->group_no' AND lane_no='$obj->lane_no'");
					$rnk++;
				}
				$b_tot = $obj->tot;
			}
		}
	}

	//토너먼트 경기
	else if(in_array($spo_code,$wp_tot_game)){ //토너먼트 게임이면
		//전체 순위 결정
		$que = "SELECT uid, level,win FROM $tbName WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level in ('3','1') ORDER BY level ASC, win DESC";
		$res = mysql_query($que);
		while($obj = mysql_fetch_object($res))
		{
			if($obj->level == "1" && $obj->win == "W") $rnk = 1;
			if($obj->level == "1" && $obj->win == "F") $rnk = 2;
			if($obj->level == "3" && $obj->win == "W") $rnk = 3;
			if($obj->level == "3" && $obj->win == "F") $rnk = 4;
			mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE uid='$obj->uid'");
		}
	}
	else if(in_array($spo_code,$wp_league_game)){ //리그전이면
		//전체 참가팀
		$que = "SELECT clu_code, record_tot, record_06, record_07 FROM $tbName WHERE gam_uid = '$gam_uid' AND game_code='$game_code' AND level='0' ORDER BY record_tot DESC, record_06 DESC, record_07 ASC";
		$res = mysql_query($que);
		$rnk = 1;
		while($obj = mysql_fetch_object($res)){
			mysql_query("UPDATE $tbName set rank_tot = $rnk WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND level='0' AND clu_code='$obj->clu_code'");
			$rnk++;
		}
	}
}//if

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=input&game_code=$game_code&type=$type&sch_val=$sch_val'>");
?>
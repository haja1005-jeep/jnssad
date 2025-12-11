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
$delete = $Mysql->Delete(wp_score," WHERE gam_uid='$s_gam_uid' $del_where ");

// 경기에 참가하는 전체 시군 검색
$acity = "SELECT count(clu_code) clu_codes, clu_code FROM wp_game_player WHERE gam_uid = '$s_gam_uid'  GROUP BY clu_code";
$ac_res = mysql_query($acity);
$all_city = mysql_num_rows($ac_res); //경기에 참가하는 전체 시군..




// 1. 각 시군별 참가인원에 대한 참가점수를 부여한다. <-- 체험경기참가자까지 포함하는지 확인...
if(!$spo_code)
{
	foreach($wp_club_code as $ckeys => $cvals)
	{
		if($ckeys == "jnsad") continue;
		$a_que = "SELECT pla_id, count(pla_id) score FROM wp_game_player WHERE gam_uid = '$s_gam_uid' AND kubun = '$wp_kubun_선수' AND clu_code = '$ckeys' group by pla_id";
		$a_res = mysql_query($a_que);
		$a_score = mysql_num_rows($a_res);

		// TB 필드 //
		$fields = array("gam_uid","clu_code","spo_code","pla_name","score","note","signdate");
		// TB 필드값 //
		$values = array("$s_gam_uid","$ckeys","참가점수","base","$a_score","참가자점수","$signdate");
		// DB 저장 //
		$insert = $Mysql->Insert(wp_score,$fields,$values);
	}
}

// 2. 각 종목별 순위점수를 배점표 기준하여 부여한다.
//    배점 기준 : 종목별 순위(1위∼8위)점수×참가 시군 수/ 전체 시군 수×100   단, 『구기 단체종목(골볼, 좌식배구, 축구)은 100점(1위∼8위)추가』
//                - 8명(팀) 미만이 참가한 경우에는 별표 순위 점수를 적용함에 있어 8위부터 역순으로 순위를 부여하고 해당되는 점수를 적용한다.
// 경기종목 검색
//$spo_que = "SELECT * FROM wp_sports_event WHERE gam_uid = '$s_gam_uid' and code='BOWLING' ORDER BY gam_type ASC, isuse asc, orderby asc";
$spo_where = "WHERE gam_uid = '$s_gam_uid'";
if($spo_code) $spo_where .= " AND code = '$spo_code'"; //WHERE gam_uid = '12' AND code = 'GATEBALL'

$spo_que = "SELECT * FROM wp_sports_event  $spo_where ORDER BY gam_type ASC, isuse asc, orderby asc";


$spo_res = mysql_query($spo_que);
while($spo_obj = mysql_fetch_object($spo_res))
{
	if($spo_obj->gam_type == "3") continue; // 체험종목은 제외
//	echo $spo_obj->code."-".$spo_obj->gam_type."-".$spo_obj->name."<br>";

	// 종목별 진행경기를 검색한다.
//	$game_que = "SELECT * FROM wp_game_serial WHERE gam_uid = '$s_gam_uid' AND spo_code = '$spo_obj->code' and game_code='POWERLIFTING_PL600-10' AND test_game <> '1' ORDER BY game_code ASC";
	$game_que = "SELECT * FROM wp_game_serial WHERE gam_uid = '$s_gam_uid' AND spo_code = '$spo_obj->code' AND test_game <> '1' ORDER BY game_code ASC";
	$game_res = mysql_query($game_que);
	while($game_obj = mysql_fetch_object($game_res))
	{
		/* 2014-09-24 박민철 추가
			아직 경기를 치르지 않은 경기(즉 순위가 없는경기)는 점수 집계에서 제외한다.
		*/

		$a_que = "SELECT count(*) cnt FROM wp_game_record where gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' AND (rank_tot is not null OR rank_tot != '')";
		$a_res = mysql_query($a_que);
		$a_obj = mysql_fetch_object($a_res);
		if($a_obj->cnt == 0)
		{
			continue;
		}


		// 점수집계제외 끝..

		// 경기에 참가하는 인원수를 구한다.
		if($spo_obj->gam_kind == "score") // 기록경기이면
		{
			//역도경기는 별도관리
			if($game_obj->spo_code == "POWERLIFTING")
			{
				$cp_que = "SELECT * FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' GROUP BY id";
			}
			else
			{
				$cp_que = "SELECT * FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code'";
			}
		}
		else if ($spo_obj->gam_kind == "tot")// 토너먼트경기이면
		{
			if($game_obj->game_kind == "1") // 개인전이면
			{
				$cp_que = "SELECT count(clu_code) city FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' GROUP BY id";
			}
			if($game_obj->game_kind == "2") // 단체전이면
			{
				$cp_que = "SELECT count(clu_code) city FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' GROUP BY clu_code, group_no";
			}
			else if($game_obj->game_kind == "3") // 시/군대항전이면
			{
				$cp_que = "SELECT count(clu_code) city FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' GROUP BY clu_code";
			}
		}
		else if ($spo_obj->gam_kind == "league")// 리그전경기이면
		{
		}
	//	echo "경기참가 시군 Query : ".$cp_que."<br>";
		$cp_res = mysql_query($cp_que);
		$player_cnt = mysql_num_rows($cp_res); // 해당 경기에 참가하는 인원수
	//	echo "<br>참가인원 수[player_cnt] : $player_cnt <br>";




		// 참가시군을 구한다.
		if($spo_obj->gam_kind == "score") // 기록경기이면
		{
			$city_que = "SELECT clu_code FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' GROUP BY clu_code, group_str";
		}
		else
		{
			$city_que = "SELECT clu_code FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' GROUP BY clu_code,group_str";
		}
		$city_res = mysql_query($city_que);
		//echo "<br> $city_que";
		$city_count = mysql_num_rows($city_res); // 해당 경기에 참가하는 시군수
		//echo "<br> 참가시군 $city_count";

		

		// 중복을 제외한 계산식을 위한 시군수
		$real_city_arr = array();
		while($city_obj = mysql_fetch_object($city_res))
		{
			$real_city_arr[$city_obj->clu_code] = $city_obj->clu_code;
		}
//		$real_city_count = $city_count; // 중복을 제외한 계산식을 위한 시군수
		$real_city_count = count($real_city_arr); // 중복을 제외한 계산식을 위한 시군수

        
		
		//2018년 26회 여수대회때부터. 대진표 표준 맞지 않아서..
		if($game_obj->game_code == 'VOLLEYBALL_VB100-01') {
			$real_city_count = 8;
			$city_count = 8;
			$player_cnt = 8;
            
		}

		//2018년 26회 여수대회. 대진표 표준 맞지 않아서..       
		if($game_obj->game_code == 'GATEBALL_GB100-01') {
			$real_city_count = 20;
			$city_count = 20;
			$player_cnt = 20;
            
		}

		if($game_obj->game_code == 'BADUK_BD100-01') {
			$real_city_count = 10;
			$city_count = 10;
			$player_cnt = 10;
            
		}

		if($game_obj->game_code == 'BADUK_BD100-02') {
			$real_city_count = 7;
			$city_count = 7;
			$player_cnt = 7;
            
		}

		if($game_obj->game_code == 'BADUK_BD100-04') {
			$real_city_count = 6;
			$city_count = 6;
			$player_cnt = 7;
            
		}

		if($game_obj->game_code == 'BOWLING_BL300-25') {
			$real_city_count = 4;
			$city_count = 4;
			$player_cnt = 4;
            
		}

		// 참가시군이 8팀이상이면 8위까지만 수여하므로 8위로 셋팅한다.
		if($spo_obj->gam_kind == "score") // 기록경기이면
		{
			if($game_obj->game_kind == "1") // 개인전
			{
				$city_count = $player_cnt;
			}
			else
			{
				$city_count = $city_count;
			}
			if($city_count > 8) $city_count = 8;
		}
		else if($spo_obj->gam_kind == "tot")
		{
			/*
			if($game_obj->game_kind == "1") // 개인전
			{
				if($player_cnt > 8)
				{
					$city_count = 8;
				}
				else
				{
					$city_count = $player_cnt;
				}
			}
			else
			{
				if($city_count > 8) $city_count = 8;
			}
			*/
				if($player_cnt > 8)
				{
					$city_count = 8;
				}
				else
				{
					$city_count = $player_cnt;
				}
		}

       //2019테니스 조정



//		echo "<br>참가시군 [city_count] : $city_count ";
//		echo "<br>참가시군 [real_city_count] : $real_city_count <br>";

//		echo $spo_obj->gam_kind;
//      exit;

		$score_count = 1; //종목별 경기를 치르는 인원수 검색한다. 예) 개인전 : 1, 2인조 : 2, 5인조 : 5
		
		if($game_obj->spo_code == "ATHIETICS") {
		
		  $sc_que = "SELECT player_cnt FROM wp_sports_event_detail WHERE gam_uid='$s_gam_uid' AND spo_code = '$game_obj->spo_code' AND code = '$game_obj->spo_sec_code' AND sex = '$game_obj->sex' AND code_detail = '$game_obj->spo_code_detail'";
        
		} else {

		  $sc_que = "SELECT player_cnt FROM wp_sports_event_detail WHERE gam_uid='$s_gam_uid' AND spo_code = '$game_obj->spo_code'  AND sex = '$game_obj->sex' AND code_detail = '$game_obj->spo_code_detail'";
		
		}

		//echo $sc_que;
		//echo $game_obj->spo_sec_cod;
		//exit;



		$sc_res = mysql_query($sc_que);
		$sc_obj = mysql_fetch_object($sc_res);
		$score_count = $sc_obj->player_cnt;


		//echo "<br>경기에 참가하는 인원 : $score_count "; //세부종목관리의 '참가인원수'와 접수집계 테이블의 player_cnt가 동일해야 점수를 불러 온다.

		// 순위별 기준배점을 검색한다.
		$bc_que = "SELECT * FROM wp_score_table WHERE gam_uid = '$s_gam_uid' AND spo_code = '$game_obj->spo_code' AND player_cnt = $score_count ";
		

			
	//echo "<br>  $bc_que <br>";

		
		
		
		$bc_res = mysql_query($bc_que);
		$bc_obj = mysql_fetch_object($bc_res);
		$rc1 = 9-$city_count;
		$score_tb[1] = $bc_obj->{"score_".$rc1};
		$score_tb[2] = $bc_obj->{"score_".($rc1+1)};
		$score_tb[3] = $bc_obj->{"score_".($rc1+2)};
		$score_tb[4] = $bc_obj->{"score_".($rc1+3)};
		$score_tb[5] = $bc_obj->{"score_".($rc1+4)};
		$score_tb[6] = $bc_obj->{"score_".($rc1+5)};
		$score_tb[7] = $bc_obj->{"score_".($rc1+6)};
		$score_tb[8] = $bc_obj->{"score_".($rc1+7)};

		//echo $score_tb[1];
	

		// 참가시군이 8위미만일경우 참가시군 이상순위는 0으로 설정
		for($i = $city_count+1; $i <= 8; $i++)
		{
			$score_tb[$i] = 0;
		}

//			print_r($score_tb);
		//토너먼트이면서 참가팀이 8팀 미만일 경우 5~8위동점처리 : 5,6,7,8점수 합산후 / 참가팀수
		if ($spo_obj->gam_kind == "tot")// 토너먼트경기이면
		{
			if($city_count-4 == 0) $rank5678 = 0;
			else $rank5678 = ($score_tb[5] + $score_tb[6] + $score_tb[7] + $score_tb[8]) / ($city_count-4);
//			echo "<br> 5~8위점수 : ".$rank5678."<br>";


			$score_tb[5] = $rank5678;
			$score_tb[6] = $rank5678;
			$score_tb[7] = $rank5678;
			$score_tb[8] = $rank5678;
	
		
		
		}
//		     echo "<br>";
			//print_r($score_tb);
            //echo "<br><br><br>";

		// 순위를 검색한다.
		if($spo_obj->gam_kind == "score") // 기록경기이면
		{
			if($game_obj->game_kind == "1") // 개인전
			{
				$player_que = "SELECT *, name as names FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' ORDER BY id ASC, rank_tot ASC";
			}
			else
			{
				$player_que = "SELECT *, group_concat(name) names FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' GROUP BY clu_code,group_str ORDER BY rank_tot ASC";
			}
		}
		else if ($spo_obj->gam_kind == "tot")// 토너먼트경기이면
		{
			if($game_obj->game_kind == "1") // 개인전이면
			{
				$player_que = "SELECT *, name as names FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code'AND level <= 8 GROUP BY id ORDER BY rank_tot ASC";
			}
			if($game_obj->game_kind == "2") // 단체전이면
			{
				//$player_que = "SELECT *, group_concat(name) name FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code'AND level <= 8 GROUP BY clu_code, rank_tot ORDER BY rank_tot ASC";
				$player_que = "
				SELECT * FROM (
					SELECT *, group_concat(name) names FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' AND level <=3 GROUP BY clu_code,level
					union all
					SELECT *, group_concat(name) names FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' AND level <= 8 and id not in( SELECT id FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code' AND level <=3) GROUP BY clu_code,level
				) tb ORDER BY level asc, rank_tot ASC
				";
			}
			else if($game_obj->game_kind == "3") // 시/군대항전이면
			{
				$player_que = "SELECT * FROM wp_game_record WHERE gam_uid = '$s_gam_uid' AND game_code = '$game_obj->game_code'AND level <= 8 GROUP BY clu_code ORDER BY rank_tot ASC";
			}
		}
		else if ($spo_obj->gam_kind == "league")// 리그전경기이면
		{
		}

//		echo "<BR>".$player_que;
		$player_res = mysql_query($player_que);




		while($player_obj = mysql_fetch_object($player_res))
		{
			unset($total_score);

			// 기권자 및 실격자는 점수처리에서 제외한다.
			if($player_obj->win == "G" || $player_obj->win == "N") continue;

			// 토너먼트경기이면 5,6,7,8위는 5위로 동점처리한다.
			if ($spo_obj->gam_kind == "tot" && (!$player_obj->rank_tot || $player_obj->rank_tot == 0))
			{
				$player_obj->rank_tot = 5;
			}

			if($player_obj->rank_tot == 0)
			{
				$rank_score = 0;
			}
			else
			{
				$rank_score = $score_tb[$player_obj->rank_tot];
			}

			//echo "<br>순위에 따른 기준점수 : $player_obj->rank_tot == $rank_score ";

			//echo "<br> 계산식 $rank_score "."*".$real_city_count."/ ( ".$all_city." * 0.01 ) ";



			$total_score = round($rank_score*$real_city_count/($all_city*0.01)); // 합산점수 = 순위점수 * 참가시군 /  (전체시군*0.01)



  	        if($spo_obj->gam_type == "2") // 시범종목은 절반의 점수만 부여한다.
			{
				// 정식종목 외 종목은 배점을 0.5점단위로 배점
				$total_score = round($total_score/2);
			
			}


			// 구기단체종목(골볼, 좌식배구, 축구)는 가산점 100점 추가
			if($game_obj->spo_code == "GOALBALL" || $game_obj->spo_code == "VOLLEYBALL" || $game_obj->spo_code == "FOOTSAL")
			{
				$total_score += 100;
			}

			// 메달별 점수추가 2017.05.09
			if($player_obj->rank_tot == 1) {
                 //$total_score += 60;
				 $medal_score = 60;
            } else if($player_obj->rank_tot == 2) {
			
	             //$total_score += 40;
				 $medal_score = 40;				 
            } else if($player_obj->rank_tot == 3) {

				 //$total_score += 20;
				 $medal_score = 20;
			}




			echo "<br>시군 :[$player_obj->uid] $player_obj->clu_code , $player_obj->name 득점: $total_score <br>";

			// TB 필드 //
			$fields = array("gam_uid","clu_code","spo_code","game_code","game_kind","pla_id","pla_name","rank","score","medal_score","note","signdate");

			// TB 필드값 //
			$values = array("$s_gam_uid","$player_obj->clu_code","$player_obj->spo_code","$game_obj->game_code","$game_obj->game_kind","$player_obj->id","$player_obj->names","$player_obj->rank_tot","$total_score","$medal_score","$game_obj->game_code / {$player_obj->rank_tot}위 점수","$signdate");

			//2019년 5월 1일 점수 등록 안되게
			if($game_obj->game_code == 'BILLIARDS_BA200-07' && $game_obj->spo_code_detail == "BA206")
			{
		
			} else if($game_obj->game_code == 'BILLIARDS_BA200-07' && $game_obj->spo_code_detail == "BA207") {
						

			} else {
				
				
				// DB 저장 //
				$insert = $Mysql->Insert(wp_score,$fields,$values);

			}
			
			$medal_score = "";
            

		}

	}
}

/*
// 21회 대회는 데이터 맞추는 작업으로 점수를 강제로 추가한다.
if($s_gam_uid == "9")
{
	$add_arr = array();
	$_add = array();
	$_add[moksad] = 69;
	$_add[yssad] = 194;
	$_add[scsad] = 137;
	$_add[jdsad] = 136;
	$_add[mgsad] = 645;
	$_add[dysad] = 136;
	$add_arr[BILLIARDS] = $_add;


	$_add = array();
	$_add[moksad] = 1005;
	$_add[yssad] = 54;
	$_add[gysad] = -907;
	$_add[yasad] = 777;
	$_add[njsad] = 54;
	$_add[gssad] = 604;
	$_add[dysad] = 55;
	$add_arr[LAWNBOWIS] = $_add;

	$_add = array();
	$_add[sasad] = 263;
	$add_arr[BADMINTON] = $_add;

	$_add = array();
	$_add[moksad] = 273;
	$_add[yssad] = 205;
	$_add[hnsad] = -57;
	$_add[njsad] = 194;
	$add_arr[BOCCIA] = $_add;


	$_add = array();
	$_add[jdsad] = -9;
	$_add[hnsad] = -14;
	$_add[bssad] = -18;
	$add_arr[SWIMMING] = $_add;

	$_add = array();
	$_add[yssad] = 136;
	$_add[hnsad] = -91;
	$_add[mgsad] = 28;
	$_add[njsad] = 27;
	$_add[hpsad] = 23;
	$add_arr[BOWLING] = $_add;

	$_add = array();
	$_add[moksad] = 407;
	$_add[yssad] = 175;
	$_add[gysad] = 80;
	$_add[gjsad] = 91;
	$_add[jdsad] = 57;
	$_add[yasad] = 75;
	$_add[ygsad] = 59;
	$_add[bssad] = 18;
	$_add[wdsad] = 111;
	$_add[gssad] = 344;
	$_add[jhsad] = 13;
	$_add[grsad] = -28;
	$add_arr[POWERLIFTING] = $_add;

	$_add = array();
	$_add[moksad] = -118;
	$_add[yssad] = 37;
	$_add[gysad] = -473;
	$_add[scsad] = -336;
	$_add[gjsad] = 36;
	$_add[jdsad] = -67;
	$_add[bssad] = 8;
	$_add[njsad] = -19;
	$_add[jhsad] = 36;
	$_add[grsad] = 18;
	$_add[hpsad] = 23;
	$add_arr[ATHIETICS] = $_add;

	$_add = array();
	$_add[moksad] = -27;
	$_add[yssad] = 27;
	$add_arr[ROWING] = $_add;

	$_add = array();
	$_add[ygsad] = -82;
	$add_arr[TABLETENNIS] = $_add;

	$_add = array();
	$_add[yasad] = -455;
	$add_arr[GATEBALL] = $_add;

	$_add = array();
	$_add[scsad] = 8;
	$_add[njsad] = 17;
	$_add[sasad] = 27;
	$add_arr[BADUK] = $_add;

	$_add = array();
	$_add[moksad] = -29;
	$_add[scsad] = -15;
	$_add[yasad] = 150;
	$_add[mgsad] = -150;
	$_add[dysad] = -43;
	$add_arr[GOLF] = $_add;

	$_add = array();
	$_add[moksad] = 112;
	$_add[yssad] = 175;
	$_add[gysad] = 9;
	$_add[gjsad] = 85;
	$_add[njsad] = -166;
	$_add[grsad] = -83;
	$_add[hpsad] = -101;
	$add_arr[FENCING] = $_add;

	$_add = array();
	$_add[yssad] = -23;
	$_add[gysad] = 68;
	$_add[scsad] = 56;
	$_add[grsad] = -52;
	$_add[ghsad] = 11;
	$add_arr[TENNIS] = $_add;

	$_add = array();
	$_add[moksad] = 2;
	$_add[yssad] = 1;
	$_add[gjsad] = 1;
	$_add[jdsad] = 2;
	$_add[yasad] = 1;
	$_add[hnsad] = 3;
	$_add[ygsad] = 1;
	$_add[bssad] = 1;
	$_add[wdsad] = 1;
	$_add[mgsad] = 5;
	$_add[njsad] = 2;
	$_add[gssad] = 1;
	$_add[dysad] = 4;
	$_add[ghsad] = 2;
	$add_arr[참가점수] = $_add;

	foreach($add_arr as $keys => $vals)
	{
		foreach($vals as $akeys => $avals)
		{
			// TB 필드 //
			$fields = array("gam_uid","clu_code","spo_code","score","note","signdate");
			// TB 필드값 //
			$values = array("$s_gam_uid","$akeys","$keys",$avals,"강제추가점","$signdate");
			// DB 저장 //
			$insert = $Mysql->Insert(wp_score,$fields,$values);
		}
	}
}
*/

if(!$spo_code)
{
	// 3. 개최지 출전 종목 중 종합득점에 가산점(20%)를 부여한다. 메달점수 포함
	//$add_que = "SELECT sum(score) score FROM wp_score WHERE gam_uid='$s_gam_uid' AND clu_code = '$game_city_code' AND spo_code <> '참가점수'";
    $add_que = "SELECT sum(CASE WHEN medal_score THEN  score + medal_score ELSE score END) score FROM wp_score WHERE gam_uid='$s_gam_uid' AND clu_code = '$game_city_code' AND spo_code <> '참가점수'";
	$add_res = mysql_query($add_que);
	$add_obj = mysql_fetch_object($add_res);
	$add_score = round($add_obj->score * 0.2);



	// TB 필드 // 모바일 출력위해 개최지 필드 pla_name에 add_point 추가 2016-05-24
	$fields = array("gam_uid","clu_code","spo_code","score","pla_name","note","signdate");
	// TB 필드값 //
	$values = array("$s_gam_uid","$game_city_code","가산점","$add_score","add_point","주최시도 가산점 20%","$signdate");
	// DB 저장 //
	$insert = $Mysql->Insert(wp_score,$fields,$values);
}
//exit;

//페이지이동
echo ("<meta http-equiv='Refresh' content='2; URL=index.html?s_gam_uid=$s_gam_uid&mode=score&spo_code=$spo_code'>");
?>

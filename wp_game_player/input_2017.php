<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 권한 체크: 관리자, 주최관리자만 볼 수 있음
if(!$site_auth->isConn($cd_player,$Admin_auth))
{
	Error("AUTH_ERROR");
	exit;
}

// 공백 체크 //
Null_Check($gam_uid);
Null_Check($kubun);

// 빈칸 체크 //
Input_Check($gam_uid);


// 선수 및 기타 체크 //
if($kubun == $wp_kubun_선수)
{
	// 공백 체크 //
	Null_Check($spo_code);
	//Null_Check($spo_sec_code);
	Null_Check($tro_code);
	Null_Check($sex);
	Null_Check($tro_level_code);
	//Null_Check($spo_code_detail);

	// 빈칸 체크 //
	Input_Check($spo_code);
	//Input_Check($spo_sec_code);
	Input_Check($tro_code);
	Input_Check($sex);
	Input_Check($tro_level_code);
	//Input_Check($spo_code_detail);
}
else if($kubun == $wp_kubun_종목인솔자 || $kubun == $wp_kubun_감독 || $kubun == $wp_kubun_코치)
{
	// 공백 체크 //
	//Null_Check($tro_code1);
	// 빈칸 체크 //
	//Input_Check($tro_code1);
}

// 감독/코치/인솔자 체크
if(($kubun == $wp_kubun_종목인솔자 && $join_status != "5") || $kubun == $wp_kubun_감독 || $kubun == $wp_kubun_코치)
{
	$sex = $obj->sex;
	$spo_code = $spo_code1;
	$tro_code = $tro_code1;
	$spo_code_detail = "";
	$tro_level_code = "";
}
else if($join_status == "5")
{
	$spo_code = $spo_code;
	$spo_sec_code = $spo_sec_code;
	$tro_code = $tro_code;
	$sex = $sex;
	$tro_level_code = $tro_level_code;
	$spo_code_detail = $spo_code_detail;
}
else if($kubun != $wp_kubun_선수)
{
	// 선수가 아니면
	$sex = $obj->sex;
	$spo_code = "";
	$tro_code = "";
	$spo_code_detail = "";
	$tro_level_code = "";
}

// 대회정보 체크 //
$query = "SELECT * FROM wp_game_ctrl where uid='$gam_uid'";
$result = mysql_query($query);
if(!$result)
{
   	Error("QUERY_ERROR");
   	exit;
}
$obj = mysql_fetch_array($result,MYSQL_BOTH);
if($obj[status] != "2" || $obj[isuse] != "0")
{
	// 승인된 대회가 아니면 에러
	Error("END_GAME_ERROR");
	exit;
}

// 선택한 선수들이 해당 클럽의 선수들인지 체크 //
unset($query);
unset($result);
unset($obj);
$query = "SELECT * FROM wp_player_ctrl WHERE wp_id = '$player' AND isuse = '0'";
$result = mysql_query($query);
if(!$result)
{
	ERROR("QUERY_ERROR");
	exit;
}
if(mysql_num_rows($result) < 1)
{
	ERROR("NOT_DATA");
	exit;
}
$obj = mysql_fetch_object($result);

// 선수는 더이상 등록 못하도록 수정
/*
if($obj->kubun == $wp_kubun_선수 && $Admin_code != "jnsad" && $Admin_code != "test")
{
	Error("NOT_INPUT_PLAYER");
	exit;
}
*/

// 등록해제한 선수이면 에러
if($obj->isuse == "1"){
	Error("NO_CLUB_PLAYER");
	exit;
}

// 해당클럽 소속의 선수가 아니면 에러
if($Admin_auth != "top" && strcmp($obj->clu_code, $Admin_code) != 0)
{
	Error("NO_CLUB_PLAYER");
	exit;
}

// 등록하고자하는 선수 구분과 일치하지 않으면 에러
if($kubun != $obj->kubun)
{
	Error("NOT_EQUAL_KUBUN", $obj->name);
	exit;
}
unset($que);
unset($res);
unset($obj2);

// 전문체육(정식종목과 시범종목)에는 한종목만, 생활체육(체험종목)는 2종목까지 신청가능 체크 //
$r_que = "SELECT spo_code,kubun FROM wp_game_player WHERE gam_uid='$gam_uid' AND pla_id='$player' GROUP BY spo_code";
$r_res = mysql_query($r_que);
$life_game_cnt = 0;			// 생활체육참가 건수
$game_cnt = 0;				// 전문체육참가 건수
$al_game = array();			// 참가한 경기정보를 갖고 있는 배열
$life_game_keys = array_keys($wp_life_game);
$al_kubun = "";				// 이미등록된 선수 구분
while($r_obj = mysql_fetch_object($r_res))
{
	if(in_array($r_obj->spo_code, $life_game_keys))
	{
		$life_game_cnt++;
	}
	else
	{
		$al_kubun = $r_obj->kubun;
		$game_cnt++;
	}
	$al_game[] = $r_obj->spo_code;
}
$wp_life_game_keys = array_keys($wp_life_game);

// 생활체육이면 두종목까지 신청가능 //
if(in_array($spo_code, $wp_life_game_keys))
{
	if(!in_array($spo_code, $al_game) && $life_game_cnt >= 2)
	{
		Error("LIFE_SPORTS_OVER");
		exit;
	}
}
else
{
	// 전문체육이면 1종목만 신청가능(2014년 06월 12일 LJS : 아래 두줄 주석 품)
	/* 2015.03.31 KDW: 제 23회 참가요강 변경으로 주석 처리함. (과거 요구사항이 적용되어 있다.)
	if(!in_array($spo_code, $al_game) && $game_cnt >= 1)
	{
		Error("SPORTS_OVER");
		exit;
	}
	*/
}
// 한선수가 전문체육에는 한종목만, 생체는 2종목까지 신청가능 체크 끝

// 이미 등록되어있고 같은 종목이며 선수구분이 틀리면 에러
if($al_kubun && $al_kubun != $kubun)
{
	ERROR("ALREADY_JOIN",$obj->name);
	exit;
}

/*
//참가자격체크
if($kubun == $wp_kubun_종목인솔자 || $kubun == $wp_kubun_감독 || $kubun == $wp_kubun_코치){
	if($obj->spo_code != $spo_code1){
		ERROR("NOT_EQUAL_CAPACITY",$obj->name);
		exit;
	}

}
*/

// 이미 등록된 선수인지 체크
if($kubun == $wp_kubun_선수 )
{


//2019년 3월 긴급수정 다른종목 중복출전 방지.
if($al_game[0]) {
	if($al_game[0] != $spo_code) {

				ERROR("ALREADY_JOIN",$obj->name);
				exit;
    }
}
//--> 여기까지
	
	//선수이면
	foreach($spo_code_detail as $kesy => $vals)
	{

		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND pla_id = '$player' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);


		if($obj2->cnt != 0)
		{
			// 2015.03.31 KDW: 조정 단체전일 경우 1인 2경기까지 신청가능 하므로 체크하지 않는다. //
			if($spo_sec_code != "RW200")
			{
				ERROR("ALREADY_JOIN",$obj->name);
				exit;
			}
		}
	}
}
else if($kubun == $wp_kubun_종목인솔자 || $kubun == $wp_kubun_감독 || $kubun == $wp_kubun_코치 || $join_status == "5")
{

//2019년 3월 긴급수정 다른종목 중복출전 방지.
if($al_game[0]) {
	if($al_game[0] != $spo_code) {

				ERROR("ALREADY_JOIN",$obj->name);
				exit;
    }
}
//--> 여기까지

    //같은 종목 중복출전 방지
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND pla_id = '$player' AND spo_code = '$spo_code'"; // 2011/04/21
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);



	if($obj2->cnt != 0)
	{
		ERROR("ALREADY_JOIN",$obj->name);
		exit;
	}

	// 참가하고자하는 종목의 장애유형이 동일한지 체크한다.
	//if($obj->tro_code != $tro_code)
	//{
	//	ERROR("NOT_EQUAL_CAPACITY",$obj->name);
	//	exit;
	//}
}
else
{
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND pla_id = '$player'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt != 0)
	{
		ERROR("ALREADY_JOIN",$obj->name);
		exit;
	}
}

// 역도경기는 세종목중 한종목으로 만 신청하여도 신청된걸로 판단함.
if($spo_code == "POWERLIFTING")
{

}

// 이미 등록된 선수인지 체크 끝
if($kubun == $wp_kubun_선수)
{
	
	
   //댄스 스포츠 비장애인 장애유형 통과 2018년 임시. 대회 끝나고 조치 해야 함.	
    if($spo_code == "DANCE") 
    {

    }	else  {


	// 참가하고자하는 종목의 장애유형이 동일한지 체크한다.
	if($obj->tro_code != $tro_code)
	{
		ERROR("NOT_EQUAL_CAPACITY",$obj->name);
		exit;
	}

    }	
	

		
	
	// 참가하고자하는 종목의 혼성경기 이외에는 성별이 동일한가 체크
	if(($sex != "none" && $sex != "both") && $obj->sex != $sex)
	{
		ERROR("NOT_EQUAL_CAPACITY",$obj->name);
		exit;
	}
}

/*** 경기 종목별 체크 ***/

//남자_여자 성구분 1:남자, 2:여자 2016.03.14
$sex_kind = substr($obj->wp_id, 6, 1); 

// 골볼
if($spo_code == "GOALBALL")
{

 if($kubun == $wp_kubun_종목인솔자 || $kubun == $wp_kubun_감독 || $kubun == $wp_kubun_코치) {
 
 } else {
	// 감독,코치 트레이너 ,주무 외 선수는 6명 이내에서 참가신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND kubun = '9'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 6 || ($obj2->cnt + 1) > 6)
	{
		ERROR("FULL_JOIN",6);
		exit;
	}

 }

}

// 당구
if($spo_code == "BILLIARDS")
{
	
	
	
	
	/* 2011/04/21
	if(count($spo_code_detail) > 1)
	{
		ERROR("BD_ONE_JOIN");
		exit;
	}

	// 1. 이미 참가했으면 참가 불가
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND pla_id = '$player' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 1)
	{
		ERROR("BD_ONE_JOIN");
		exit;
	}
	*/

	// 1. 시군별 출전선수는 2명 2팀신청가능
	/*
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 4)
	{
		ERROR("FULL_JOIN",4);
		exit;
	}
	*/

	// 1. 시군별 세부종목 출전선수는 2명 까지만 신청가능함
	foreach($spo_code_detail as $kesy => $vals)
	{
		unset($que);
		unset($res);
		unset($obj2);
		//$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND sex = '$sex' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals'";

        //세부종목 2인까지 출전 AND kubun = '9' 선수만 카운팅 2018.02.19일 추가
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND sex = '$sex' AND  tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals' AND kubun = '$wp_kubun_선수'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}

		//단체전 출전선수는 5명 까지만 신청가능함.
		if($tro_level_code == "BA-A")
		{
			$billiards_team_ea = 5;		// 5명
		}
		else
		{
			$billiards_team_ea = 2;		// 2명
		}

		$obj2 = mysql_fetch_object($res);

		
		
		if($obj2->cnt >= $billiards_team_ea)
		{
			ERROR("FULL_JOIN",$billiards_team_ea);
			exit;
		}


      

/******** 여기서 부터 *************************************************************************** */
//BIS 개인전 남자는 3구, 원큐션 분류 출전함
if($tro_level_code == "BIS" && $sex_kind == '1')	
	{
	    if($spo_code_detail[0] == "BA101" && $spo_code_detail[1] == "BA102") {
		
		ERROR("BILLIARDS_BIS_MALE_ONE_JOIN");
 	    exit;

		}
		
	}




//혼성복식 세부종목 원큐션/스텐딩(BIS) 스텐딩 선수만 등록가능 2019.02월
if($vals == "BA203")
 {
    // 스텐딩 선수만 참가 가능 //
    if($obj->wheel == "0")
      {
	    ERROR("ONLY_USE_WHEEL"); //wp_library/check.php
 	    exit;
	  }

    // 휠체어 선수만 참가 가능 //
    /*if($obj->wheel !== "0")
      {
	    ERROR("NO_USE_WHEEL");
 	    exit;
	  }*/
 }


 //남_녀 1명씩 출전하는 혼성복식, 단체전 경기입니다. 2016.03.14
 if($vals == "BA203" && $sex_kind == '1') //혼성복식은 남_녀 1명씩 출전가능함.
	{
	
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals' AND sex_kind = '1'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	
	$obj2 = mysql_fetch_object($res);

	if($obj2->cnt >= 1)
	{
		ERROR("WOMAN_JOIN",1);
		exit;
	}

	}

 if($vals == "BA203" && $sex_kind == '2') //혼성복식은 남_녀 1명씩 출전가능함.
	{
	
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals' AND sex_kind = '2'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 1)
	{
		ERROR("FULL_JOIN",1);
		exit;
	}

	}

if($vals == "BA204" && $sex_kind == '1') //혼성복식은 남_녀 1명씩 출전가능함.
	{
	
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals' AND sex_kind = '1'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	
	$obj2 = mysql_fetch_object($res);

	if($obj2->cnt >= 1)
	{
		ERROR("WOMAN_JOIN",1);
		exit;
	}

	}

 if($vals == "BA204" && $sex_kind == '2') //혼성복식은 남_녀 1명씩 출전가능함.
	{
	
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals' AND sex_kind = '2'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 1)
	{
		ERROR("FULL_JOIN",1);
		exit;
	}

	}

if($vals == "BA205" && $sex_kind == '1') //혼성복식은 남_녀 1명씩 출전가능함.
	{
	
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals' AND sex_kind = '1'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	
	$obj2 = mysql_fetch_object($res);

	if($obj2->cnt >= 1)
	{
		ERROR("WOMAN_JOIN",1);
		exit;
	}

	}

 if($vals == "BA205" && $sex_kind == '2') //혼성복식은 남_녀 1명씩 출전가능함.
	{
	
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals' AND sex_kind = '2'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 1)
	{
		ERROR("FULL_JOIN",1);
		exit;
	}

	}

 if($tro_level_code == "BA-A") //혼성복식은 남_녀 1명씩 출전가능함.
	{
	
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 5)
	{

      //여성선수 1인 포함해야 됩니다.
      $que2 = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals' AND sex_kind ='2'";
	  $res2 = mysql_query($que2);
	  if(!$res2)
	   {
		  ERROR("QUERY_ERROR");
		  exit;
	   }
	   $obj3 = mysql_fetch_object($res2);	
	   if($obj3->cnt == 0)
	    {
		ERROR("WOMAN_JOIN",0);
		exit;
	    }
	 }

	}
/**** 여기까지 ************************************************************************************************************ /


		/* 2014.06.17 KDW : 코드 수정으로 주석 처리함.
		if($obj2->cnt >= 2)
		{
			ERROR("FULL_JOIN",2);
			exit;
		}
		*/
	}

/*
	// 2. 팀당 예비선수 포함하여 2명 신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$spo_code_detail' AND kubun = '$wp_kubun_선수'";


	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 2)
	{
		ERROR("FULL_JOIN",2);
		exit;
	}
*/


	// 휠체어 사용여부 판단 (BIW 휠체어사용)
	if($tro_level_code == "BIS,BIW")	
	{
		
		if($obj->wheel == "0")
		{
			// 해당 세부종목, 같은 시군의 휠체어 선수를 검색
			$wheel_que = "SELECT count(*) cnt FROM wp_game_player wgp JOIN wp_player_ctrl wpc ON wgp.pla_id = wpc.wp_id WHERE gam_uid = '$gam_uid' AND wgp.clu_code = '$obj->clu_code' AND wgp.spo_code = '$spo_code' AND wgp.spo_sec_code = '$spo_sec_code' AND wgp.tro_level_code = '$tro_level_code' AND wgp.spo_code_detail = '$spo_code_detail[0]' AND wpc.wheel = '0'";
			$wheel_res = mysql_query($wheel_que);
			$wheel_obj = mysql_fetch_object($wheel_res);
			
		}
		else
		{
			// 해당 세부종목, 같은 시군의 스탠딩 선수를 검색
			$n_wheel_que = "SELECT count(*) cnt FROM wp_game_player wgp JOIN wp_player_ctrl wpc ON wgp.pla_id = wpc.wp_id WHERE gam_uid = '$gam_uid' AND wgp.clu_code = '$obj->clu_code' AND wgp.spo_code = '$spo_code' AND wgp.spo_sec_code = '$spo_sec_code' AND wgp.tro_level_code = '$tro_level_code' AND wgp.spo_code_detail = '$spo_code_detail[0]' AND wpc.wheel = '1'";
			$n_wheel_res = mysql_query($n_wheel_que);
			$n_wheel_obj = mysql_fetch_object($n_wheel_res);
			/** 2014.06.24 KDW : 해당 세부종목에서 휠체어를 여러명 등록가능하게 에러 해제함.
			if($n_wheel_obj->cnt > 0)
			{
				ERROR("C_USE_WHEEL");
				exit;
			}
			**/
		}
	}
	else if($tro_level_code == "BIW")
	{
		// 휠체어 선수만 참가 가능 //
		if($obj->wheel != "0")
		{
			ERROR("NO_USE_WHEEL");
			exit;
		}
	}
	else
	{
		// 휠체어 사용 선수는 참가 불가 //
		//if($spo_code_detail[0] != "BA203")		// 2014.06.19 KDW : 단체전 1단 2복은 휠체어 사용자로 참가 가능하다. (세부종목이 1개 밖에 없으므로 배열을 [0]으로 지정한다.)
		//{
		//	if($obj->wheel == "0")
		//	{
		//		ERROR("ONLY_USE_WHEEL");
		//		exit;
		//	}
		//}
	}

	// 4. 각 시/군별로 출전선수 1명이 4개 세부종목까지만 신청가능(2014년 06월 12일 LJS : 4종목으로 수정)
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND pla_id = '$player' AND spo_code = '$spo_code'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 4 || ($obj2->cnt + count($spo_code_detail)) > 4)
	{
		ERROR("ONE_FULL_JOIN",4);
		exit;
	}
}

// 론볼
if($spo_code == "LAWNBOWL")
{
	// 1. 시군별 출전선수는 성별 관계없이 14까지만 신청 (2016년 03월 12일 LJS : 16명으로 수정-인솔자포함)//
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND kubun = '$wp_kubun_선수'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 16)
	{
		ERROR("FULL_JOIN",16);
		exit;
	}

	// 2. 단식에 참가 선수가 복식에 신청불가 //
	if($spo_sec_code == "LB200")
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid='$gam_uid' AND clu_code='$obj->clu_code' AND pla_id='$obj->wp_id' AND spo_code='$spo_code' AND spo_sec_code='LB100'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 1)
		{
			ERROR("LAWNBOING_JOIN");
			exit;
		}
	}

	// 3. 단식에 12명까지만 신청가능, 복식 (2인조)에 1팀 2명까지만 가능(2016년 03월 12일 KYE : 개인전은 8명) //
	if($spo_sec_code == "LB100")
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid='$gam_uid'  AND spo_code='$spo_code' AND spo_sec_code='LB100' AND clu_code = '$obj->clu_code'";	// 2014.06.16 KDW : 시군조건 추가
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 12)
		{
			ERROR("FULL_JOIN",12);
			exit;
		}
	}






	if($spo_sec_code == "LB200")
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = 'LB200'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 12)
		{
			ERROR("FULL_JOIN",12);
			exit;
		}
	}


	if($spo_code_detail[0] == "LB101" && $tro_level_code == 'LB-1') //남 개인전, 휄체어부
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = 'LB100' AND spo_code_detail = 'LB101' AND tro_level_code = 'LB-1'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 3)
		{
			ERROR("FULL_JOIN",3);
			exit;
		}
	}

	if($spo_code_detail[0] == "LB101" && $tro_level_code == 'LB-2') //남 개인전, 스텐딩부
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = 'LB100' AND spo_code_detail = 'LB101' AND tro_level_code = 'LB-2'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);

		echo $obj2->cnt;
		if($obj2->cnt >= 3)
		{
			ERROR("FULL_JOIN",3);
			exit;
		}
	}

	if($spo_code_detail[0] == "LB102"  && $tro_level_code == 'LB-1')//여 개인전, 휄체어부
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = 'LB200' AND spo_code_detail = 'LB102' AND tro_level_code = 'LB-1'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 3)
		{
			ERROR("FULL_JOIN",3);
			exit;
		}
	}

	if($spo_code_detail[0] == "LB102"  && $tro_level_code == 'LB-2')//여 개인전, 스텐딩부
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = 'LB200' AND spo_code_detail = 'LB102' AND tro_level_code = 'LB-2'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 3)
		{
			ERROR("FULL_JOIN",3);
			exit;
		}
	}

	if($spo_code_detail[0] == "LB201") //단체전 남녀구분없이 2명, 2017.03.13일 2차종목 제외로 $que에서 spo_code = '$spo_code'제외 함.
	{
		unset($que);
		unset($res);
		unset($obj2);
		//$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = 'LB200' AND spo_code_detail = 'LB201'";

		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_code_detail = 'LB201'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 2)
		{
			ERROR("FULL_JOIN",2);
			exit;
		}
	}
	if($spo_code_detail[0] == "LB204")
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = 'LB200' AND spo_code_detail = 'LB204'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 3)
		{
			ERROR("FULL_JOIN",3);
			exit;
		}
	}
}

// 배드민턴
if($spo_code == "BADMINTON")
{
	// 1. 시군별 세부종목 출전선수는 3명 까지만 신청가능함
	foreach($spo_code_detail as $kesy => $vals)
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND sex = '$sex' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);



		if($obj2->cnt >= 4)
		{
			ERROR("FULL_JOIN",4);
			exit;
		}
	
	}
}


// 보치아
if($spo_code == "BOCCIA")
{
	// 1. 시군별 세부종목 출전선수는 4명 까지만 신청가능함
	foreach($spo_code_detail as $kesy => $vals)
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 5)
		{
			ERROR("FULL_JOIN",5);
			exit;
		}

		// 아래 등급은 휠체어 사용선수만 가능
		if($tro_level_code == "BC1" || $tro_level_code == "BC2")
		{
			// 선수가 휠체어사용이 아니면 에러
			if($obj->wheel != "0")
			{
				ERROR("NO_USE_WHEEL");
				exit;
			}
		}

		// 단체전은 각등급별로 한명만 존재해야함.
		if($tro_level_code == "BC1~BC5")
		{
			/* 2015.03.31 KDW: 개인전 참가자도 단체전 참가도 가능해야 한다.
			unset($que);
			unset($res);
			unset($obj2);
			$que = "SELECT tro_level_code, pla_id FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code <> 'BC1~BC4'";
			$res = mysql_query($que);
			if(!$res)
			{
				ERROR("QUERY_ERROR");
				exit;
			}
			$not_bochia = false;
			while($obj2 = mysql_fetch_object($res))
			{
				if($player == $obj2->pla_id)
				{
					$not_bochia = true;
					break;
				}
			}
			if(!$not_bochia)
			{
				ERROR("NOT_BOCHIA_GROUP");
				exit;
			}
			*/

			/*
			unset($que);
			unset($res);
			unset($obj2);
			$que = "SELECT tro_level_code, pla_id FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_level_code = 'BC1~BC4'";
			$res = mysql_query($que);
			*/
		}
	}
}

// 볼링
if($spo_code == "BOWLING")
{
	foreach($spo_code_detail as $kesy => $vals)
	{
		// 개인전이면
		if($vals == "BL101")
		{
			// 1개 세부종목에 각 시군별 출전선수는 3명으로 제한
			unset($que);
			unset($res);
			unset($obj2);
			$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND sex = '$sex' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals'";
			$res = mysql_query($que);
			if(!$res)
			{
				ERROR("QUERY_ERROR");
				exit;
			}
			$obj2 = mysql_fetch_object($res);
			if($obj2->cnt >= 3)
			{
				ERROR("FULL_JOIN",3);
				exit;
			}
		}

		// 2인조이면
		if($vals == "BL201" || $vals == "BL202")
		{
			// 1개 세부종목에 각 시군별 출전선수는 2명으로 제한
			unset($que);
			unset($res);
			unset($obj2);
			$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND sex = '$sex' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals'";
			$res = mysql_query($que);
			if(!$res)
			{
				ERROR("QUERY_ERROR");
				exit;
			}
			$obj2 = mysql_fetch_object($res);
			if($obj2->cnt >= 2)
			{
				//ERROR("BL_TWO_FULL_JOIN",2);
				//exit;
			}

			// 등급별 각 시군별 출전선수는 총 3명으로 제한
			unset($que);
			unset($res);
			unset($obj2);
			$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND sex = '$sex' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals'";
			$res = mysql_query($que);
			if(!$res)
			{
				ERROR("QUERY_ERROR");
				exit;
			}
			$obj2 = mysql_fetch_object($res);
			if($obj2->cnt >= 3)
			{
				//ERROR("FULL_JOIN",3);
				//exit;
			}
		}

		// 휠체어장애
		if($tro_level_code == "TPB11")
		{
			// 선수가 휠체어사용이 아니면 에러
			if($obj->wheel != "0")
			{
				ERROR("NO_USE_WHEEL");
				exit;
			}
		}
	}
}

// 수영
if($spo_code == "SWIMMING")
{
	// 2. 각 시/군은 한 세부종목에 2명 이내 신청
	foreach($spo_code_detail as $kesy => $vals)
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_code = '$tro_code' AND sex = '$sex' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 2)
		{
			ERROR("FULL_JOIN",2);
			exit;
		}
	}

	// 3. 각 시/군별로 출전섢수 1명이 2개 세부종목까지만 신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND pla_id = '$player' AND spo_code = '$spo_code'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 2 || ($obj2->cnt + count($spo_code_detail)) > 2)
	{
		ERROR("ONE_FULL_JOIN",2);
		exit;
	}
}

// 역도
if($spo_code == "POWERLIFTING")
{
	// 1. 각 시/군은 세부 2종목 이내 신청
	foreach($spo_code_detail as $kesy => $vals)
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND sex = '$sex' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND spo_code_detail = '$vals'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 2)
		{
			ERROR("FULL_JOIN",2);
			exit;
		}
	}

	// 2. 각 종목별로 해당유형만 가능
	if($kubun == $wp_kubun_선수)
	{
		if($spo_sec_code == "PL100" || $spo_sec_code == "PL200" || $spo_sec_code == "PL300")
		{
			if($obj->tro_code != "IWAS" && $obj->tro_code != "IWAS_2" && $obj->tro_code != "CP-ISRA")
			{
				ERROR("NOT_EQUAL_CAPACITY");
				exit;
			}
		}
		else
		{
			if($obj->tro_code != "IBSA" && $obj->tro_code != "CISS" && $obj->tro_code != "INAS-FID")
			{
				ERROR("NOT_EQUAL_CAPACITY");
				exit;
			}
		}
	}
}

// 육상
if($spo_code == "ATHIETICS")
{
	// 1. 트랙과 필드는 함께 신청 불가
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT * FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND pla_id = '$player' AND spo_code = '$spo_code'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if(mysql_num_rows($res) > 0 && $obj2->spo_sec_code != $spo_sec_code)
	{
		ERROR("NO_ORTHER_GAME");
		exit;
	}

	// 2. 각 시/군은 한 세부종목에 2명 이내 신청
	foreach($spo_code_detail as $kesy => $vals)
	{
		unset($que);
		unset($res);
		unset($obj2);
		//$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND sex = '$sex' AND spo_code_detail = '$vals'";


		$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND sex = '$sex' AND spo_code_detail = '$vals'";
		$res = mysql_query($que);

		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 2)
		{
			ERROR("FULL_JOIN",2);
			exit;
		}
	}

	// 3. 각 시/군별로 출전섢수 1명이 2개 세부종목까지만 신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND pla_id = '$player' AND spo_code = '$spo_code'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 3 || ($obj2->cnt + count($spo_code_detail)) > 3)
	{
		ERROR("ONE_FULL_JOIN",3);
		exit;
	}

	// 휠체어 사용여부 판단 (T51-T54, F43 휠체어사용)
	if($tro_level_code == "T51-T54" || $tro_level_code == "F51-F58")
	{
		// 휠체어 선수만 참가 가능 //
		if($obj->wheel != "0")
		{
			ERROR("NO_USE_WHEEL");
			exit;
		}
	}
}

// 배구
if($spo_code == "VOLLEYBALL")
{
	// 감독,코치 트레이너 ,주무 외 선수는 14명 이내에서 참가신청가능
	unset($que);
	unset($res);
	unset($obj2);
	//$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND kubun = '9'";

	$que = "SELECT count(*) cnt FROM wp_game_player WHERE gam_uid = '$gam_uid' AND clu_code = '$obj->clu_code' AND spo_code = '$spo_code' AND spo_sec_code = '$spo_sec_code' AND tro_code = '$tro_code' AND tro_level_code = '$tro_level_code' AND kubun = '9'";


	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 14)
	{
		ERROR("FULL_JOIN",14);
		exit;
	}
}

// 탁구
if($spo_code == "TABLETENNIS")
{
	/*
	if($tro_code == "CISS")
	{
		ERROR("TRO_ERROR");
		exit;
	}
	*/

	// 2. 각 시/군은 세부 2종목 이내 신청
	foreach($spo_code_detail as $kesy => $vals)
	{
		if($spo_sec_code == "TT100")
		{
			//개인전
			unset($que);
			unset($res);
			unset($obj2);
			$que = "SELECT count(*) cnt FROM wp_game_player
					WHERE gam_uid = '$gam_uid'
				    	    AND clu_code = '$obj->clu_code'
					    AND spo_code = '$spo_code'
					    AND spo_sec_code = '$spo_sec_code'
					    AND tro_code = '$tro_code'
					    AND sex = '$sex'
					    AND tro_level_code = '$tro_level_code'
					    AND spo_code_detail = '$vals'";
			$res = mysql_query($que);
			if(!$res){
				ERROR("QUERY_ERROR");
				exit;
			}
			$obj2 = mysql_fetch_object($res);
			if($obj2->cnt >= 3){
				ERROR("FULL_JOIN",3);
				exit;
			}
			//휠체어 사용여부 판단 (TT1~TT5까지 휠체어사용)
			if($tro_level_code == "TT1~TT2" || $tro_level_code == "TT3" || $tro_level_code == "TT4~TT5"){
				if($obj->wheel != "0")
				{
					ERROR("NO_USE_WHEEL");
					exit;
				}
			}
			else
			{
				// 휠체어 사용 선수는 참가 불가 //
				if($obj->wheel == "0")
				{
					ERROR("ONLY_USE_WHEEL");
					exit;
				}
			}
		}
		//2. 단체전 2단식 1복식( 총 4명으로만 구성)
		if($spo_sec_code == "TT200"){ //단체전
			/*
			//총 4명으로만 구성
			unset($que);
			unset($res);
			unset($obj2);
			$que = "SELECT count(*) cnt FROM wp_game_player
					WHERE gam_uid = '$gam_uid'
				    	    AND clu_code = '$obj->clu_code'
					    AND spo_code = '$spo_code'
					    AND spo_sec_code = '$spo_sec_code'";
			$res = mysql_query($que);
			if(!$res){
				ERROR("QUERY_ERROR");
				exit;
			}
			$obj2 = mysql_fetch_object($res);
			if($obj2->cnt >= 4){
				ERROR("TT_FULL_JOIN",4);
				exit;
			}
			*/
			if($vals == "TT201"){
				//단식경기는 각 단식 경기별 1명만..
				unset($que);
				unset($res);
				unset($obj2);
				$que = "SELECT count(*) cnt FROM wp_game_player
						WHERE gam_uid = '$gam_uid'
					    	    AND clu_code = '$obj->clu_code'
						    AND spo_code = '$spo_code'
						    AND spo_sec_code = '$spo_sec_code'
						    AND tro_code = '$tro_code'
						    AND tro_level_code = '$tro_level_code'
						    AND spo_code_detail = '$vals'";
				$res = mysql_query($que);
				if(!$res){
					ERROR("QUERY_ERROR");
					exit;
				}
				$obj2 = mysql_fetch_object($res);
				if($obj2->cnt >= 4){
					ERROR("FULL_JOIN",4);
					exit;
				}
			}
			//휠체어 사용여부 판단 (TT-A 휠체어사용)
			if($tro_level_code == "TT-A"){
				if($obj->wheel != "0"){
					ERROR("NO_USE_WHEEL");
					exit;
				}
			}
			else if($tro_level_code == "TT-B")
			{
				if($obj->wheel == "0")
				{
					ERROR("ONLY_USE_WHEEL");
					exit;
				}
			}

		}
	}

}
//축구
if($spo_code == "FOOTSAL"){
	//1. 시군별 출전선수는 8명 3팀신청가능 (예비포함 14명) /*AND spo_sec_code = '$spo_sec_code';*/
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player 
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'";
			    
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 31){
		ERROR("FULL_JOIN",31);
		exit;
	}
	//2. 팀당 예비선수 포함하여 8명 신청가능 			    /*AND spo_sec_code = '$spo_sec_code'*/
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND tro_level_code = '$tro_level_code'
			    AND spo_code_detail = '$spo_code_detail'";
	$res = mysql_query($que);





	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 11){
		ERROR("FULL_JOIN",11);
		exit;
	}
}
//게이트볼
if($spo_code == "GATEBALL"){
	//1. 시군별 출전선수는 7명 1팀신청가능 (예비포함 7명)
	unset($que);
	unset($res);
	unset($obj2);

 if($kubun == $wp_kubun_선수 )  //선수일때만 카운팅 체크 2018.02.21
 {

	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
				AND kubun = '9'
			    AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 7){
		ERROR("FULL_JOIN",7);
		exit;
	}


 }

}





//다트
if($spo_code == "DART"){
	// 시각장애인 개인전이면 시군별 2명의 선수만 출전 가능하다.
	if($spo_code_detail[0] == "DT101" && $tro_level_code == "DT-B")
	{
		// 해당 세부종목, 같은 시군의 스탠딩 선수를 검색
		$n_wheel_que = "SELECT count(*) cnt
				FROM wp_game_player wgp
				JOIN wp_player_ctrl wpc
				ON wgp.pla_id = wpc.wp_id
				WHERE gam_uid = '$gam_uid'
				    AND wgp.clu_code = '$obj->clu_code'
				    AND wgp.spo_code = '$spo_code'
				    AND wgp.spo_sec_code = '$spo_sec_code'
				    AND wgp.tro_level_code = '$tro_level_code'
				    AND wgp.spo_code_detail = '$spo_code_detail[0]'
				    ";
		$n_wheel_res = mysql_query($n_wheel_que);
		$n_wheel_obj = mysql_fetch_object($n_wheel_res);

		if($n_wheel_obj->cnt >= 2)
		{
			ERROR("FULL_JOIN",2);
			exit;
		}
	}
	// 휠체어 개인전이면 시군별 2명의 선수만 출전 가능하다.
	else if($tro_level_code == "DT-C")
	{
		if($obj->wheel == "0")
		{
			// 해당 세부종목, 같은 시군의 휠체어 선수를 검색
			$wheel_que = "SELECT count(*) cnt
					FROM wp_game_player wgp
					JOIN wp_player_ctrl wpc
					ON wgp.pla_id = wpc.wp_id
					WHERE gam_uid = '$gam_uid'
					    AND wgp.clu_code = '$obj->clu_code'
					    AND wgp.spo_code = '$spo_code'
					    AND wgp.spo_sec_code = '$spo_sec_code'
					    AND wgp.tro_level_code = '$tro_level_code'
					    AND wgp.spo_code_detail = '$spo_code_detail[0]'
					    AND wpc.wheel = '0'
					    ";
			$wheel_res = mysql_query($wheel_que);
			$wheel_obj = mysql_fetch_object($wheel_res);

			if($wheel_obj->cnt > 2)
			{
				ERROR("C_USE_WHEEL");
				exit;
			}
		}
		// 휠체어 선수만 참가 가능 //
		else
		{
			ERROR("NO_USE_WHEEL");
			exit;
		}
	}


	//1. 시군별 출전선수는 5명 1팀신청가능 (감독 1명)
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
                AND kubun = '9'
			    AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 9){
		ERROR("FULL_JOIN",9);
		exit;
	}
}

//바둑
if($spo_code == "BADUK")
{
	//1. 시군별 출전선수는 제한없음
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);

/*
	if($obj2->cnt >= 3)
	{
		ERROR("FULL_JOIN",3);
		exit;
	}
*/
	//2. 3인조 경기이면 3명까지만 신청가능
	if($spo_code_detail[0] == 'BD201')
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player
				WHERE gam_uid = '$gam_uid'
				    AND clu_code = '$obj->clu_code'
				    AND spo_code = '$spo_code'
				    AND spo_sec_code = '$spo_sec_code'
				    AND tro_level_code = '$tro_level_code'
				    AND spo_code_detail = '$spo_code_detail[0]'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 3)
		{
			ERROR("FULL_JOIN",3);
			exit;
		}
	}
}

//셔플보드
if($spo_code == "SHUFFLEBOARD"){
	//1. 시군별 출전선수는 3명 1팀신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 3){
		ERROR("FULL_JOIN",3);
		exit;
	}

	//2. 팀당 예비선수 포함하여 3명 신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'
			    AND tro_level_code = '$tro_level_code'
			    AND spo_code_detail = '$spo_code_detail[0]'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 3){
		ERROR("FULL_JOIN",3);
		exit;
	}
}


//커롤링
if($spo_code == "CUROLLING"){
	//1. 시군별 출전선수는 3명 1팀신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 3){
		ERROR("FULL_JOIN",3);
		exit;
	}

	//2. 팀당 예비선수 포함하여 3명 신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'
			    AND tro_level_code = '$tro_level_code'
			    AND spo_code_detail = '$spo_code_detail[0]'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 3)
	{
		ERROR("FULL_JOIN",3);
		exit;
	}
}
//타겟3종
if($spo_code == "TARGET"){
	//1. 시군별 출전선수는 3명 1팀신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 3)
	{
		ERROR("FULL_JOIN",3);
		exit;
	}

	//2. 팀당 예비선수 포함하여 3명 신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'
			    AND tro_level_code = '$tro_level_code'
			    AND spo_code_detail = '$spo_code_detail[0]'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 3){
		ERROR("FULL_JOIN",3);
		exit;
	}
}
//핸들러
if($spo_code == "HANDLER"){
	//1. 시군별 출전선수는 2명 2팀신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 4){
		ERROR("FULL_JOIN",4);
		exit;
	}

	//2. 팀당 예비선수 포함하여 2명 신청가능
	// 2014-09-19 박민철 수정 - 단체전 4명으로 수정
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'
			    AND tro_level_code = '$tro_level_code'
			    AND spo_code_detail = '$spo_code_detail[0]'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 4){
		ERROR("FULL_JOIN",4);
		exit;
	}
}

//사격
if($spo_code == "SHUOOTING"){
	//1. 시군별 세부종목 출전선수는 2명 까지만 신청가능함
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'
			    AND tro_code = '$tro_code'
			    AND tro_level_code = '$tro_level_code'
			    AND spo_code_detail = '$spo_code_detail'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 2){
		ERROR("FULL_JOIN",2);
		exit;
	}
	//공기소총 등록했으면 공기권총으로 등록 불가
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND pla_id = '$player'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 1){
		ERROR("ONE_FULL_JOIN",1);
		exit;
	}
}
//사이클
if($spo_code == "CYCLING"){
	//1. 시군별 세부종목 출전선수는 2명 까지만 신청가능함
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'
			    AND tro_code = '$tro_code'
			    AND tro_level_code = '$tro_level_code'
			    AND spo_code_detail = '$spo_code_detail'";
	$res = mysql_query($que);
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 2){
		ERROR("FULL_JOIN",2);
		exit;
	}
}





//파크골프
if($spo_code == "GOLF")
{
	//1. 시군별 출전선수는 20명까지 참가 가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND spo_sec_code = '$spo_sec_code'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 19)
	{
		ERROR("FULL_JOIN",20);
		exit;
	}

	//2. 2인조 경기이면 2명까지만 신청가능
	if($spo_code_detail[0] == 'GF201')
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player
				WHERE gam_uid = '$gam_uid'
				    AND clu_code = '$obj->clu_code'
				    AND spo_code = '$spo_code'
				    AND spo_sec_code = '$spo_sec_code'
				    AND tro_level_code = '$tro_level_code'
				    AND spo_code_detail = '$spo_code_detail[0]'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 2)
		{
			ERROR("FULL_JOIN",2);
			exit;
		}
	}

	//3. 4인조 경기이면 4명까지만 신청가능
	if($spo_code_detail[0] == 'GF202')
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player
				WHERE gam_uid = '$gam_uid'
				    AND clu_code = '$obj->clu_code'
				    AND spo_code = '$spo_code'
				    AND spo_sec_code = '$spo_sec_code'
				    AND tro_level_code = '$tro_level_code'
				    AND spo_code_detail = '$spo_code_detail[0]'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 4)
		{
			ERROR("FULL_JOIN",4);
			exit;
		}
	}

	//4. 각 시/군별로 출전선수 1명이 2개 세부종목까지만 신청가능
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND pla_id = '$player'
			    AND spo_code = '$spo_code'";
	$res = mysql_query($que);
	if(!$res)
	{
		ERROR("QUERY_ERROR");
		exit;
	}
	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 3 || ($obj2->cnt + count($spo_code_detail)) > 3)
	{
		ERROR("ONE_FULL_JOIN",3);
		exit;
	}
}

//실내조정
if($spo_code == "ROWING")
{
	// 1. 각 시/군은 한 세부종목에 3명 이내 신청(2015.03.31 KDW: 제 23회에서 4명에서 3명으로 변경됨) //
	foreach($spo_code_detail as $key_f => $val_f)
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player
				WHERE gam_uid = '$gam_uid'
				    AND clu_code = '$obj->clu_code'
				    AND spo_code = '$spo_code'
				    AND spo_sec_code = '$spo_sec_code'
				    AND tro_code = '$tro_code'
				    AND tro_level_code = '$tro_level_code'
				    AND spo_code_detail = '$val_f'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		/* 2015.03.31 KDW: 제 23회 참가요강 변경으로 주석 처리함.
		if($obj2->cnt >= 3)
		{
			ERROR("FULL_JOIN",3);
			exit;
		}
		*/
	}
	if($spo_sec_code == "RW200")
	{
		// 2. 시군별 출전선수는 남자 1명, 여자 1명으로 구성된 2인조 혼성 2개팀만 참가가능  //
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT (SELECT sex FROM wp_player_ctrl WHERE wp_id = wgp.pla_id) p_sex, count(*) cnt  FROM wp_game_player wgp
				WHERE gam_uid = '$gam_uid'
				    AND clu_code = '$obj->clu_code'
				    AND spo_code = '$spo_code'
				    AND spo_sec_code = '$spo_sec_code'
				    GROUP BY p_sex";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		while($obj2 = mysql_fetch_object($res))
		{
			if($obj2->p_sex == $obj->sex)
			{
				$rw_cnt = $obj2->cnt;
				
				/* 2015.03.31 KDW: 제 23회 참가요강 변경으로 주석 처리함.
				if($rw_cnt > 2)
				{
					ERROR("ROWING_RW200_JOIN");
					exit;
				}
				*/
			}
		}
	}
}


// 승마 : 2014-06-11 경기종목 추가로 인하여 추가
if($spo_code == "HORSERIDING")
{

}

//테니스
if($spo_code == "TENNIS")
{

}

//댄스스포츠
if($spo_code == "DANCE"){



	//1. 시군별 세부종목 출전선수는 3팀까지 신청가능함
	unset($que);
	unset($res);
	unset($obj2);
	$que = "SELECT count(*) cnt FROM wp_game_player
			WHERE gam_uid = '$gam_uid'
			    AND clu_code = '$obj->clu_code'
			    AND spo_code = '$spo_code'
			    AND tro_code = '$tro_code'";
	$res = mysql_query($que);
	
	if(!$res){
		ERROR("QUERY_ERROR");
		exit;
	}


	$obj2 = mysql_fetch_object($res);
	if($obj2->cnt >= 30){
		ERROR("DANCE_FULL_JOIN",30);
		exit;
	}


}


//양궁 2019년 추가
if($spo_code == "ARCHERY"){


        //리커브, 컴파운드 한개 종목만 참가 가능 
	    if($spo_code_detail[0] == "AR101" && $spo_code_detail[1] == "AR201") {
		
		  ERROR("ARCHERY_ONE_JOIN");
 	      exit;

		} else if($spo_code_detail[0] == "AR301" && $spo_code_detail[1] == "AR401") {
		
		  ERROR("ARCHERY_ONE_JOIN");
 	      exit;

		}



	if($spo_code_detail[0] == "AR101" || $spo_code_detail[0] == 'AR201') //남 개인전 리커브, 컴파운드 15명까지 등록가능
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player
				WHERE gam_uid = '$gam_uid'
				    AND clu_code = '$obj->clu_code'
				    AND spo_code = '$spo_code'
				    AND tro_level_code = '$tro_level_code'
				    AND spo_code_detail = '$spo_code_detail[0]'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
		$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 15)
		{
			ERROR("FULL_JOIN",15);
			exit;
		}
	}

	//2. 단체전 2인조 경기, 4명까지만 신청가능
	if($spo_code_detail[0] == 'AR301' || $spo_code_detail[0] == 'AR401')
	{
		unset($que);
		unset($res);
		unset($obj2);
		$que = "SELECT count(*) cnt FROM wp_game_player
				WHERE gam_uid = '$gam_uid'
				    AND clu_code = '$obj->clu_code'
				    AND spo_code = '$spo_code'
				    AND tro_level_code = '$tro_level_code'
				    AND spo_code_detail = '$spo_code_detail[0]'";
		$res = mysql_query($que);
		if(!$res)
		{
			ERROR("QUERY_ERROR");
			exit;
		}
	$obj2 = mysql_fetch_object($res);
		if($obj2->cnt >= 4)
		{
			ERROR("FULL_JOIN",4);
			exit;
		}
	}


}

//}

/* 2016-03-14 김용은 수정
   이하 소스 부분에 필드명 group_str(팀구분) 을 추가함.
   group_str 은 팀구분을 위한 필드명이어서 team으로 하여야 하나
   이미 필드명이 등록된 관계로 group_str 로 함.sex_kind필드 추가함
*/

// TB 필드명 //
$fields = array("gam_uid","pla_id","pla_name","spo_code","spo_sec_code","spo_code_detail","sex",
			"tro_code","tro_level_code","clu_code","clu_class_name","kubun","group_str","sex_kind");

//등록될 클럽
$club_name = $wp_club_code[$obj->clu_code];

//역도이면 한 종목에 3종목을 동시 입력
if($spo_code == "POWERLIFTING" && $kubun == $wp_kubun_선수){
	if($spo_sec_code == "PL100" || $spo_sec_code == "PL200" || $spo_sec_code == "PL300"){
		$spo_sec_code = "PL100";
		foreach($spo_code_detail as $keys => $vals){
			// TB 필드값 //
			$values = array("$gam_uid","$player","$obj->name","$spo_code","$spo_sec_code","$vals","$sex",
						"$tro_code","$tro_level_code","$obj->clu_code","$club_name","$kubun","$group_str","$sex_kind");
			// DB 저장 //
			$insert = $Mysql->Insert(wp_game_player,$fields,$values);
		}

		$spo_sec_code = "PL200";

		foreach($spo_code_detail as $keys => $vals){
			// TB 필드값 //
			$values = array("$gam_uid","$player","$obj->name","$spo_code","$spo_sec_code","$vals","$sex",
						"$tro_code","$tro_level_code","$obj->clu_code","$club_name","$kubun","$group_str","$sex_kind");
			// DB 저장 //
			$insert = $Mysql->Insert(wp_game_player,$fields,$values);
		}

		$spo_sec_code = "PL300";
		foreach($spo_code_detail as $keys => $vals){
			// TB 필드값 //
			$values = array("$gam_uid","$player","$obj->name","$spo_code","$spo_sec_code","$vals","$sex",
						"$tro_code","$tro_level_code","$obj->clu_code","$club_name","$kubun","$group_str","$sex_kind");
			// DB 저장 //
			$insert = $Mysql->Insert(wp_game_player,$fields,$values);
		}
	}
	if($spo_sec_code == "PL400" || $spo_sec_code == "PL500" || $spo_sec_code == "PL600"){
		$spo_sec_code = "PL400";
		foreach($spo_code_detail as $keys => $vals){
			// TB 필드값 //
			$values = array("$gam_uid","$player","$obj->name","$spo_code","$spo_sec_code","$vals","$sex",
						"$tro_code","$tro_level_code","$obj->clu_code","$club_name","$kubun","$group_str","$sex_kind");
			// DB 저장 //
			$insert = $Mysql->Insert(wp_game_player,$fields,$values);
		}

		$spo_sec_code = "PL500";
		foreach($spo_code_detail as $keys => $vals){
			// TB 필드값 //
			$values = array("$gam_uid","$player","$obj->name","$spo_code","$spo_sec_code","$vals","$sex",
						"$tro_code","$tro_level_code","$obj->clu_code","$club_name","$kubun","$group_str","$sex_kind");
			// DB 저장 //
			$insert = $Mysql->Insert(wp_game_player,$fields,$values);
		}

		$spo_sec_code = "PL600";
		foreach($spo_code_detail as $keys => $vals){
			// TB 필드값 //
			$values = array("$gam_uid","$player","$obj->name","$spo_code","$spo_sec_code","$vals","$sex",
						"$tro_code","$tro_level_code","$obj->clu_code","$club_name","$kubun","$group_str","$sex_kind");
			// DB 저장 //
			$insert = $Mysql->Insert(wp_game_player,$fields,$values);
		}
	}
}else{
	if($kubun == $wp_kubun_선수 || $join_status == "5" )
	{ //선수이거나 인솔/참가 구분이 5인경우

		// 인솔자를 강제로 선수로 변경 //
		$kubun = 9;

		foreach($spo_code_detail as $keys => $vals)
		{
			// TB 필드값 //
			$values = array("$gam_uid","$player","$obj->name","$spo_code","$spo_sec_code","$vals","$sex",
						"$tro_code","$tro_level_code","$obj->clu_code","$club_name","$kubun","$group_str","$sex_kind");
			// DB 저장 //
			$insert = $Mysql->Insert(wp_game_player,$fields,$values);
		}
	}
	else
	{
		// TB 필드값 //
		$values = array("$gam_uid","$player","$obj->name","$spo_code","$spo_sec_code","$spo_code_detail","$sex",
					"$tro_code","$tro_level_code","$obj->clu_code","$club_name","$kubun","$group_str","$sex_kind");
		// DB 저장 //
		$insert = $Mysql->Insert(wp_game_player,$fields,$values);
	}
}

// 페이지 이동 //
echo( "<script>
				alert('정상적으로 참가신청이 완료 되었습니다.');
				//window.opener.location.href='http://www.jnsadplayer.or.kr/wp_game_player/index.html?mode=search';
				window.opener.location.href='index.html?keyfield=pla_name&key=$obj->name';
				parent.window.close();
			   </script>" );
?>
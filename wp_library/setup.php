<?
// 페이지당 출력수 //
$max_record = "30";
$max_record_album = "16";
$max_record_product = "100";
$max_record_movie = "9";
$max_record_each = 32;

// 링크 페이지수 //
$max_page = "10";

// NEW ICON 유지시간 //
$notify_new_article = 1;

// 시간 계산(1일) //
$time_limit = 60*60*24*$notify_new_article;

// 답변글 들여쓰기 한계치 //
$reply_indent = 5;

// 답변글 삭제결정 (삭제1, 삭제불가시 0) //
$allow_delete_thread = 0;

// 요일 변환 //
$week_change = array("일","월","화","수","목","금","토");

// 저장 경로 //
$pds_path = "../wp_data_pds/";
$file_path = "../wp_data_file/";
$photo_path = "../wp_data_photo/";
$movie_path = "../wp_data_movie/";
$upload_file_size_8M = 1024*80000;

// 도메인명 //
$admin_domain = getenv("SERVER_NAME");
$domain_name = "전라남도 장애인 체육회 선수관리 시스템";

// 이전 URL //
$before_url = getenv("HTTP_REFERER");

// 현재 URL //
$location_url = getenv("REQUEST_URI");

// 현재 IP //
$ip = getenv("REMOTE_ADDR");

// 현재 시간 //
$signdate = time();

// 금지 단어 //
$no_string = array("광고","성인","섹스");

// 금지 IP //
$no_ip = array("122.32.165.71");

// 년월일 //
$n_year = date("Y");
$n_mon = date("m");
$n_day = date("d");
$n_week = date("w");
$n_hour = date("H");
$n_min = date("i");
$n_today = date("Y-m-d");
$c_mon = 12;
$c_day = 31;

// 관리 제어 //
$control_dir = "playerjnsadorkr";

//사이트 암호화
$site_md5 = "kodaewoong";

// 회사 설정 //
$company_title = "전라남도 장애인 체육회 선수관리 시스템";
$company_name = "전라남도 장애인 체육회";
$company_manager = "";
$company_domain = "www.jnsadplayer.or.kr";
$company_address = "";
$company_tel = "";
$company_fax = "";
$company_status = "전라남도 장애인 체육회 선수관리 시스템";
$company_pw = "jnsad!@#";
$from_mail = "";

// 계정 용량 //
$user_disk = 1000;

// 내용 크기 //
$document_width = 740;

// 파일관련 셋팅 //
$u_file_type = array (
	"img"=>".jpg .jpeg .gif .png",
	"normal"=>".doc .xls .ppt .docx .xlsx .pptx .hwp .txt .zip .pdf .alz .jpg .gif .bmp .psd",
	"movie"=>".swf .mpg .avi .mpeg",
);
$img_file_type = array (
	"jpg"=>"jpg",
	"jpeg"=>"jpeg",
	"gif"=>"gif",
	"png"=>"png",
);
$normal_file_type = array (
	"doc"=>"doc",
	"docx"=>"docx",
	"xls"=>"xls",
	"xlsx"=>"xlsx",
	"ppt"=>"ppt",
	"pptx"=>"pptx",
	"hwp"=>"hwp",
	"txt"=>"txt",
	"zip"=>"zip",
	"pdf"=>"pdf",
	"jpg"=>"jpg",
	"gif"=>"gif",
	"bmp"=>"bmp",
	"psd"=>"psd",
);
$movie_file_type = array (
	"swf"=>"swf",
	"mpg"=>"mpg",
	"avi"=>"avi",
	"mpeg"=>"mpeg",
);
$year_type = array (
	"2012"=>"2012년",
	"2011"=>"2011년",
	"2010"=>"2010년",
	"2009"=>"2009년",
);

// 경기 진행일
$game_date = "2019년 5월 1일";

// 상장 날짜 //
$prize_print_date = "2019년 5월 3일";

// 경기 진행 회차
$game_number = "27";

// 경기진행 시군
$game_city = "영암군";
$game_city_code = "yasad";


// 메달별점수 - 2017년 대회부터 메달별 점수 집계
$gold_score = "60";
$silver_score = "40";
$bronze_score = "20";

// 링크 세팅 //
$basic_get = "code=$code&uid=$uid&mode=$mode&type=$type&page=$page&keyfield=$keyfield&key=$key";
$add_get = "&kubun=$kubun&spo_code=$spo_code&spo_sec_code=$spo_sec_code&spo_code_detail=$spo_code_detail&tro_code=$tro_code&tro_level_code=$tro_level_code&clu_code=$clu_code&prz_kind=$prz_kind&s_isuse=$s_isuse&sex=$sex&gam_uid=$gam_uid&s_gam_uid=$s_gam_uid";
$add_get .= "&orderby1=$orderby1&orderby2=$orderby2&orderby3=$orderby3&orderby4=$orderby4&game_code=$game_code&m_number=$m_number&level=$level&t_kind=$t_kind&rank=$rank&test_game=$test_game&test_game1=$test_game1&giveup=$giveup&s_welfare=$s_welfare&s_picture=$s_picture&match_person=$match_person";

// 기능별 코드 데이터
$cd_trouble = "TROUBLE_CTRL"; //장애코드 입력화면
$cd_sports = "SPORTS_CTRL"; //장애종목 관리입력화면
$cd_club = "CLUB_CTRL"; //장애종목 관리입력화면
$cd_player = "PLAYER_CTRL"; //선수 관리입력화면
$cd_game = "GAME_CTRL"; //대회일정관리 화면
$cd_game_record = "GAME_RECORD"; //대회실적관리 화면
$cd_etc = "ETC_CTRL"; //기타 부가관리
$cd_game_result = "GAME_RESULT"; //대회실적관리 화면
$cd_match_program= "MATCH_CTRL"; //대진표관리

//기능별 코드관리
$wp_site_code = array(
	"TROUBLE_CTRL"=> array("top"),
	"SPORTS_CTRL"=> array("top"),
	"CLUB_CTRL"=> array("top"),
	"MATCH_CTRL"=> array("top"),
	"PLAYER_CTRL"=> array("top","normal","sponsor","user","s_group"),
	"GAME_CTRL"=> array("top","sponsor"),
	"GAME_RECORD"=> array("top","normal","sponsor"),
	"GAME_RESULT"=> array("top","sponsor"),
	"ETC_CTRL"=> array("top","normal","sponsor","player","user","s_group"),
);
//기능별 코드관리 여기까지

//구분코드
$wp_kubun_code = array(
	"1" => "단장",
	"2" => "담당",
	"3" => "임원",
	"4" => "보호자",
	"5" => "자원봉사자",
	"6" => "인솔자",
	"7" => "감독",
	"8" => "코치",
	"9" => "선수",
);
$wp_kubun_단장 = "1";
$wp_kubun_담당 = "2";
$wp_kubun_임원 = "3";
$wp_kubun_보호자 = "4";
$wp_kubun_자원봉사자 = "5";
$wp_kubun_종목인솔자 = "6";
$wp_kubun_감독 = "7";
$wp_kubun_코치 = "8";
$wp_kubun_선수 = "9";

//휠체어 사용여부
$wp_wheel_code = array(
	"0" => "사용",
	"1" => "미사용",
	);
//장애등급
$wp_trouble_level = array(
	"1" => "1급",
	"2" => "2급",
	"3" => "3급",
	"4" => "4급",
	"5" => "5급",
	"6" => "6급",
	"0" => "무급",
	);
//권한 코드 //
$wp_auth_code = array(
	"top"=>"최고관리자",
	"normal"=>"일반관리자",
	"sponsor"=>"주최관리자",
	"user"=>"일반사용자",
	"s_group"=>"경기단체",
	);
//성별 코드 //
$wp_sex_code = array(
	"male"=>"남자",
	"female"=>"여자",
	"both"=>"혼성",
	//"both_m"=>"혼성 남",
	//"both_f"=>"혼성 여",
	"none"=>"무관",
	);
//결혼유무
$wp_married_code = array(
	"0" => "기혼",
	"1" => "미혼",
	);
//메뉴 코드 //
$wp_menu_code = array(
	"menu_01"=>"경기종목관리",
	"menu_02"=>"장애등급관리",
	"top"=>"최고관리자",
	);
// 관리자 코드 //
$wp_manager_code = array(
	"normal"=>"일반관리자",
	"sponsor"=>"주최관리자",
	"top"=>"최고관리자",
	"s_group"=>"경기단체",
	);
//관리자 레벨
$wp_manager_level = array(
	"1" => "검색만 가능",
	"2" => "기록 가능",
	"9" => "관리 가능",
	);
//장애코드
$wp_trouble_code = array(
	"IWAS"=>"절단(지체)및기타장애",
	"IWAS_2"=>"척추(경추)장애",
	"CP-ISRA"=>"뇌성(뇌병변)마비",
	"IBSA"=>"시각장애",
	"CISS"=>"청각장애",
	"INAS-FID"=>"지적장애",
	//"IWAS_3"=>"지체장애",
	"NOTROU"=>"비장애(생체)",
	);
//학교구분코드
$wp_school_code = array(
	"0" => "초등학교",
	"1" => "중학교",
	"2" => "고등학교",
	"3" => "대학교",
	"4" => "대학원",
	"5" => "특수학교",
	);
//졸업구분코드
$wp_grade_code = array(
	"0" => "졸업",
	"1" => "중퇴",
	"2" => "재학",
	"3" => "전학",
	"4" => "수료",
	);
//상벌구분코드
$wp_prize_code = array(
	"0" => "상",
	"1" => "벌",
	);
//국내/국제구분코드
$wp_kind_code = array(
	"in" => "국내",
	"out" => "국제",
	);
//대회경기상태
$wp_game_status = array(
	"1" => "승인요청",
	"2" => "승인",
	"3" => "삭제요청",
	"4" => "삭제",
	"5" => "취소",
	);
//사용여부코드
$wp_isuse_code = array(
	"0" => "사용",
	"1" => "미사용",
	);
//마감구분코드
$wp_magam_code = array(
	"0" => "마감전",
	"1" => "마감",
	);
//요일코드
$wp_week_code = array(
	"0" => "일",
	"1" => "월",
	"2" => "화",
	"3" => "수",
	"4" => "목",
	"5" => "금",
	"6" => "토",
	);
//기록코드 //
$wp_best_code = array(
	"GR"=>"대회신기록",
	"PR"=>"올림픽신기록",
	"WR"=>"세계신기록",
	);
//레포트 출력순
$wp_orderby_code = array(
	"kubun"=>"선수구분순",
	"name"=>"성명순",
	"sex"=>"성별순",
	"tro_code"=>"장애유형순",
	"signdate"=>"등록일순",
);
//경기결과  출력순
$wp_game_orderby = array(
	"game_code"=>"게임번호순",
	"spo_code"=>"경기종목순",
	"rank_tot"=>"기록순",
	"name"=>"이름순",
);
//상장종류
$wp_prize_kind = array(
	"01"=>"종합상장",
	"02"=>"모범선수단상장",
	"03"=>"성취상장",
	"04"=>"장려상장",
	"05"=>"단체전상장",
	"06"=>"최우수선수상장",
	"07"=>"개인전상장",
	"12"=>"입장상"
);



// 상장번호 관리
$wp_prize_num_code =  array(
	"GOALBALL" => "15",			// 골볼
	"BILLIARDS" => "20",			// 당구
	"LAWNBOWL" => "16",			// 론볼
	"VOLLEYBALL" => "10",			// 배구
	"BADMINTON" => "13",			// 배드민턴
	"BOCCIA" => "17",				// 보치아
	"SWIMMING" => "09",			// 수영
	"BOWLING" => "14",			// 볼링
	"POWERLIFTING" => "12",		// 역도
	"ATHIETICS_AS100" => "07",	// 육상 필드
	"ATHIETICS_AS200" => "08",	// 육상 트랙
	"ROWING" => "19",			// 조정
	"FOOTSAL" => "18",			// 축구
	"TABLETENNIS" => "11",		// 탁구
	"GATEBALL" => "21",			// 게이트볼
	"BADUK" => "22",				// 바둑 BADUK
	"GOLF" => "23",				// 파크골프 GOLF
	"TENNIS" => "24",				// 테니스 TENNIS
	"FENCING" => "25",			// 펜싱 FENCING
	"DART" => "27",				// 다트 DART
	"HORSERIDING" => "26",		// 승마 HORSERIDING
	"SHUFFLEBOARD" => "28",		// 셔플보드 SHUFFLEBOARD
	"CUROLLING" => "29",			// 커롤링 CUROLLING
	"TARGET" => "30",				// 타겟3종 TARGET
	"HANDLER" => "31",			// 핸들러 HANDLER
	"DANCE" => "32",
	"ARCHERY" => "33",	// 양궁 ARCHERY
);

//게임구분
$wp_game_level = array(
	"128"=>"128강",
	"64"=>"64강",
	"32"=>"32강",
	"16"=>"16강",
	"8"=>"8강",
	"4"=>"4강",
	"3"=>"3.4위전",
	"1"=>"결승",
);

//
$wp_rank_code = array(
	1=>"1위",
	2=>"2위",
	3=>"3위",
	3=>"3위",
	4=>"4위",
	5=>"5위",
	6=>"6위",
	7=>"7위",
	8=>"8위"
);

//게임구분
$wp_game_kind = array(
	1=>"개인전",
	2=>"단체전",
	3=>"단체전(시/군)",
);
//승패구분
$wp_win_code = array(
	"W"=>"승",
	"F"=>"패",
	"G"=>"기권",
	"N"=>"실격",
);

//진행중인 대회 검색 [27회 장애인체육회 대회]
$que = "SELECT * FROM wp_game_ctrl WHERE status='2' ORDER BY isuse asc, uid desc";
$res = mysql_query($que);
$before_gam_uid = 0;		// 이전경기 UID
while($obj = mysql_fetch_object($res))
{
	if($obj->isuse == 0) $gam_uid = $obj->uid;
	else if($obj->isuse == 1 && $before_gam_uid == 0) $before_gam_uid = $obj->uid;
}

// 전체종목코드
$wp_game_code = array();

// 정식종목코드
$wp_real_game = array();

// 생활체육종목
$wp_life_game = array();

// 체험종목
$wp_exp_game = array();

// 토너먼트 경기 코드
$wp_tot_game = array();

// 스코어경기 코드
$wp_score_game = array();

// 리그전  경기 코드
$wp_league_game = array();

// 장애등급 코드값 가져오기
$query = "SELECT spo_code, level_code,level_name FROM wp_trouble_ctrl WHERE gam_uid = '$gam_uid' AND isuse = '0'  GROUP BY spo_code,level_code ORDER BY spo_code ASC, code ASC";
$result = mysql_query($query) or die( '['. mysql_error(). ']['  . $PHP_SELF . '][' . __LINE__ . ']');
$wp_trouble_level_code = array();
$b_spo_code = "";
while($obj = mysql_fetch_object($result))
{
	if($b_spo_code != $obj->spo_code && $b_spo_code != "")
	{
		$wp_trouble_level_code[$b_spo_code] = $_tmp_spo;
		$_tmp_spo = array();
	}
	$_tmp_spo[$obj->level_code] = $obj->level_name;
	$b_spo_code = $obj->spo_code;
} // end while
$wp_trouble_level_code[$b_spo_code] = $_tmp_spo;
unset($query);
unset($result);
unset($obj);
function getTrouble_level_code($spo_code, $trouble_level_code)
{
	global $wp_trouble_level_code;
	$_tmp = $wp_trouble_level_code[$spo_code];
	return $_tmp[$trouble_level_code];
}

//2차종목코드 가져오기
$query = "SELECT * FROM wp_sports_event_detail WHERE gam_uid='$gam_uid' AND isuse = '0' GROUP BY spo_code, code ORDER BY code ASC, orderby ASC";
$result = mysql_query($query) or die( '['. mysql_error(). ']['  . $PHP_SELF . '][' . __LINE__ . ']');
$wp_spo_sec_code = array();
$_tmp_spo = "";
$b_spo_code = "";
while($obj = mysql_fetch_object($result))
{
	if($b_spo_code != $obj->spo_code && $b_spo_code != "")
	{
		$wp_spo_sec_code[$b_spo_code] = $_tmp_spo;
		$_tmp_spo = array();
	}
	$_tmp_spo[$obj->code] = $obj->name;
	$b_spo_code = $obj->spo_code;
} // end while
$wp_spo_sec_code[$b_spo_code] = $_tmp_spo;
unset($query);
unset($result);
unset($obj);
function getSpo_sec_code($code1, $code2)
{
	global $wp_spo_sec_code;
	$_tmp = $wp_spo_sec_code[$code1];
	return $_tmp[$code2];
}

// 종목코드값 가져오기
$query = "SELECT * FROM wp_sports_event where gam_uid='$gam_uid' AND isuse = '0' order by gam_type ASC, orderby asc, uid desc";
$result = mysql_query($query) or die( '['. mysql_error(). ']['  . $PHP_SELF . '][' . __LINE__ . ']');
while($obj = mysql_fetch_object($result))
{
	$wp_sports_code[$obj->code] = $obj->name;
	$wp_game_code[$obj->code] = $obj->name;//경기코드
	if($obj->gam_type =="1")
	{
		$wp_real_game[$obj->code] = $obj->name;//정식종목
	}
	if($obj->gam_type =="2")
	{
		$wp_life_game[$obj->code] = $obj->name;//생활체육종목
	}
	if($obj->gam_type =="3")
	{
		$wp_exp_game[$obj->code] = $obj->name;//체험종목
	}
	if($obj->gam_kind =="tot")
	{
		$wp_tot_game[] = $obj->code;//토너먼트 경기 코드
	}
	if($obj->gam_kind =="score")
	{
		$wp_score_game[] = $obj->code;//스코어경기 코드
	}
	if($obj->gam_kind =="league")
	{
		$wp_league_game[] = $obj->code;//리그전  경기 코드
	}
} // end while
unset($query);
unset($result);
unset($obj);


// 세부종목코드값 가져오기
$query = "SELECT code_detail,code_detail_name FROM wp_sports_event_detail WHERE gam_uid='$gam_uid' AND isuse='0' ORDER BY code_detail ASC";
$result = mysql_query($query) or die( '['. mysql_error(). ']['  . $PHP_SELF . '][' . __LINE__ . ']');
while($obj = mysql_fetch_object($result))
{
	$wp_sports_detail_code[$obj->code_detail] = $obj->code_detail_name;
}
unset($query);
unset($result);
unset($obj);


//클럽코드값 가져오기
/*
2012년 04월 06일 주석 - 기존 등록되어있던 아이디들을 삭제한 후 시/군 단체의 목록이 출력되지않아. 미사용 데이터를 체크하는 isuse 조건을 제거하였다.
//$query = "SELECT code,name FROM wp_club_ctrl where isuse = '0' AND level > '2' order by uid desc";
*/
$query = "SELECT code,name FROM wp_club_ctrl where level > '2' order by uid desc";
$result = mysql_query($query) or die( '['. mysql_error(). ']['  . $PHP_SELF . '][' . __LINE__ . ']');
while($obj = mysql_fetch_object($result))
{
	$wp_club_code[$obj->code] = $obj->name;
} // end while

unset($query);
unset($result);
unset($obj);

//진행중인 대회 검색
$query = "SELECT * FROM wp_game_ctrl WHERE status = '2' ORDER BY f_period DESC,signdate DESC ";
$result = mysql_query($query);
$wp_games = array();
while($obj = mysql_fetch_object($result))
{
	$wp_games[$obj->uid] = $obj->name;
}
unset($query);
unset($result);
unset($obj);


//이전 대회 검색 2018_02_21일 추가
$query = "SELECT * FROM wp_game_ctrl WHERE status = '2' ORDER BY f_period DESC,signdate DESC Limit 3";
$result = mysql_query($query);
$wp_games_before = array();
while($obj = mysql_fetch_object($result))
{
	$wp_games_before[$obj->uid] = $obj->name;
}
unset($query);
unset($result);
unset($obj);




//경기종목별 성별코드
$query = "SELECT spo_code, sex FROM wp_game_event WHERE gam_uid='$gam_uid' AND spo_code = '$spo_code' GROUP BY sex";
$result = mysql_query($query);
$wp_game_sex = array();
while($obj = mysql_fetch_object($result))
{
	$wp_game_sex[$obj->sex] = $wp_sex_code[$obj->sex];
}
unset($query);
unset($result);
unset($obj);

// 경기번호별 경기구분을 검색, 단체전(2),개인전(1),시군전(3)
$query = "SELECT * FROM wp_sports_event_detail WHERE gam_uid='$gam_uid' GROUP BY spo_code, code, code_detail ORDER BY spo_code ASC, code ASC";
//$query = "SELECT * FROM wp_sports_event_detail WHERE gam_uid='$gam_uid' GROUP BY spo_code, code ORDER BY spo_code ASC, code ASC";
$result = mysql_query($query);
$game_kind_code = array();
while($obj = mysql_fetch_object($result))
{
	$game_kind_code[$obj->spo_code."-".$obj->code."-".$obj->code_detail] = $obj->game_kind;
}
unset($query);
unset($result);
unset($obj);

//시/군 코드
$wp_city_code = array(
	"S00"=>"전라남도",
	"S01"=>"목포시",
	"S02"=>"여수시",
	"S03"=>"순천시",
	"S04"=>"나주시",
	"S05"=>"광양시",
	"G01"=>"담양군",
	"G02"=>"곡성군",
	"G03"=>"구례군",
	"G04"=>"고흥군",
	"G05"=>"보성군",
	"G06"=>"화순군",
	"G07"=>"장흥군",
	"G08"=>"강진군",
	"G09"=>"해남군",
	"G10"=>"영암군",
	"G11"=>"무안군",
	"G12"=>"함평군",
	"G13"=>"영광군",
	"G14"=>"장성군",
	"G15"=>"완도군",
	"G16"=>"진도군",
	"G17"=>"신안군",
);

//시/군 코드
$wp_city_con_code = array(
	"S00"=>"jnsad",
	"S01"=>"moksad",
	"S02"=>"yssad",
	"S03"=>"scsad",
	"S04"=>"njsad",
	"S05"=>"gysad",
	"G01"=>"dysad",
	"G02"=>"gssad",
	"G03"=>"grsad",
	"G04"=>"ghsad",
	"G05"=>"bssad",
	"G06"=>"hssad",
	"G07"=>"jhsad",
	"G08"=>"gjsad",
	"G09"=>"hnsad",
	"G10"=>"yasad",
	"G11"=>"masad",
	"G12"=>"hpsad",
	"G13"=>"ygsad",
	"G14"=>"jssad",
	"G15"=>"wdsad",
	"G16"=>"jdsad",
	"G17"=>"sasad",
);

// 시/군 코드 //
$sigun_code = array(
	"moksad" => "목포시",
	"yssad" => "여수시",
	"scsad" => "순천시",
	"njsad" => "나주시",
	"gysad" => "광양시",
	"dysad" => "담양군",
	"gssad" => "곡성군",
	"grsad" => "구례군",
	"ghsad" => "고흥군",
	"bssad" => "보성군",
	"hssad" => "화순군",
	"jhsad" => "장흥군",
	"gjsad" => "강진군",
	"hnsad" => "해남군",
	"yasad" => "영암군",
	"masad" => "무안군",
	"hpsad" => "함평군",
	"ygsad" => "영광군",
	"jssad" => "장성군",
	"wdsad" => "완도군",
	"jdsad" => "진도군",
	"sasad" => "신안군",
);

// 종목구분 //
$wp_gam_type_code = array (
	"1" => "정식종목",
	"2" => "시범종목",
	"3" => "체험종목",
);

// 경기구분 //
$wp_gam_kind_code = array (
	"tot" => "토너먼트경기",
	"score" => "기록경기",
	"league" => "리그전",
);

// 비선수 생활체육 참가 구분 //
$not_players_join_code = array(
	"1" => "임원",
	"2" => "보호자",
	"3" => "자원봉사자",
	"4" => "인솔자",
	"5" => "대회참가",
);

// 총괄인원표 출력 구분 //
$total_game_player = array(
	"0" => "누적포함",
	"1" => "실제인원",
);

// 성별 코드 //
$wp_result_sex = array(
	"male"=>"남자",
	"female"=>"여자",
);

// 답변 코드 //
$wp_yorn_code = array(
	"1" => "예",
	"2" => "아니오",
);

// 승인 코드 //
$wp_status_code = array(
	"0" => "대기",
	"1" => "승인",
);
//============= 코드 추가 부분 끝 ======================

// 참가신청 나이제한 만14세//
//$player_age_ymd = "20020306";		// 2002년 03월 06일 이전

if (!function_exists('dump')) {
	function dump($str) {
		echo "<pre>";
		print_r($str);
		echo "</pre>";
	}
}
?>

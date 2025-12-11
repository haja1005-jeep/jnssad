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

//전체 게임수를 가져온다.
$game_cnt = array();

//기존데이터 삭제
$delete = $Mysql->Delete(wp_game_prize," WHERE gam_uid='$s_gam_uid' AND prz_kind in('', '05') ");

//정식종목 경기만 검색
foreach($wp_game_code as $keys => $values){
	if(in_array($values, $wp_life_game)) continue;
	$lg .= "'{$keys}',";
}

$where .= " AND spo_code in (";
$where .= substr($lg,0,strlen($lg)-1).")";

$g_que = "SELECT spo_code, game_code FROM wp_game_record WHERE gam_uid='$s_gam_uid' AND test_game <> '1' AND game_kind in ('2','3') $where GROUP BY game_code ORDER BY spo_code ASC, game_code ASC";
$g_res = mysql_query($g_que);

while($g_obj = mysql_fetch_object($g_res)){
	//생활체육은 상장집계에서 제외
	if(in_array($g_obj->spo_code, $wp_life_game)) {
		continue;
	}
	$game_cnt[] = $g_obj->game_code;
}

// 상장테이블에 상장 등록
foreach($game_cnt as $keys => $values){
	unset($query);
	unset($result);
	unset($obj);

	// 순위가 1,2,3위인 검색
	$query = "SELECT *, group_concat(name ORDER BY name ASC separator ',') pla_names FROM wp_game_record WHERE gam_uid='$s_gam_uid' AND game_kind in ('2','3') AND game_code='$values' AND test_game <> '1' AND rank_tot in ('1','2','3') GROUP BY rank_tot ORDER BY game_code ASC, rank_tot ASC ";
	$result = mysql_query($query);
	while($obj = mysql_fetch_object($result)){

		$where = "WHERE gam_uid = '$s_gam_uid' AND spo_code = '$obj->spo_code' ";
		$prz_string = $obj->spo_code;

		// 상장번호를 자동으로 증가하기 위해 검색
		$prz_que = "SELECT max(prz_num) prz_num FROM wp_game_prize $where";
		$prz_res = mysql_query($prz_que);
		$prz_obj = mysql_fetch_object($prz_res);
		if($prz_obj->prz_num){
			$_tmp = $prz_obj->prz_num;
			$_t = explode("-",$_tmp);
			$prz_num = $_t[2]+1;
		} else{
			$prz_num = 1;
		}

		$year = date("y");
		$prz_num = STR_PAD($prz_num, 3,"0",STR_PAD_LEFT);
		$prz_kind = "05";//단체전상장
		$prz_num_all = $year."-".$wp_prize_num_code[$prz_string]."-".$prz_num;//상장번호

		$spo_name = $wp_sports_code[$obj->spo_code];

		// TB 필드명 //
		$fields = array("gam_uid","game_code","game_kind","pla_id","pla_name","prz_kind","prz_num","spo_code","spo_name","spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","prz_pla_name","clu_code","rank","count","signdate");
		// TB 필드값 //
		$values = array("$obj->gam_uid","$obj->game_code","$obj->game_kind","$obj->id","$obj->name","$prz_kind","$prz_num_all","$obj->spo_code","$spo_name","$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->pla_names","$obj->clu_code","$obj->rank_tot",0,$signdate);

		// DB 저장 //
		$insert = $Mysql->Insert(wp_game_prize,$fields,$values);
	}
}

//페이지이동
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?gam_uid=$gam_uid&spo_code=$spo_code&mode=prize'>");
?>

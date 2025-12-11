<?
// Session 시작 //
require_once "../wp_library/head.php";

// 경로 체크 //
Referer_Check($admin_domain);

// 전송 체크 //
Method_Check($REQUEST_METHOD);
// 공백 체크 //
Null_Check($gam_uid);

// 빈칸 체크 //
Input_Check($gam_uid);

//로그인 체크
if(!$site_auth->isLogin()){
	Error("ADMIN_ERROR");
	exit;
}
//권한 체크 - 관리자, 주최관리자만 볼 수 있음
if(!$site_auth->isConn($cd_player,$Admin_auth)){
	Error("AUTH_ERROR");
	exit;
}

foreach($uid as $keys => $values){
	unset($query);
	unset($result);
	unset($obj);
	$query = "SELECT * FROM wp_game_player WHERE uid = '$values' AND gam_uid=$gam_uid";
	$result = mysql_query($query);
	if(!$result){
		ERROR("QUERY_ERROR");
		exit;
	}
	if(mysql_num_rows($result) < 1){
		ERROR("NOT_DATA");
		exit;
	}
	$obj = mysql_fetch_object($result);
	//해당클럽 소속의 선수가 아니면 에러
	if($Admin_auth != "top" && strcmp($obj->clu_code, $Admin_code) != 0){
		Error("NO_CLUB_PLAYER");
		exit;
	}
}

// TB 필드명 //
$fields = array("back_no","lane_no","group_no");

foreach($uid as $pkeys => $pvalues){
	$back_no = "backno_".$pvalues;
	$lane_no = "laneno_".$pvalues;
	$group_no = "groupno_".$pvalues;
	// TB 필드값 //
	$values = array($$back_no,$$lane_no,$$group_no);
	// DB 저장 //
	$insert = $Mysql->Update(wp_game_player,$fields,$values," where uid='$pvalues'");
}
// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>
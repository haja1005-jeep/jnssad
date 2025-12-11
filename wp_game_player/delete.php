<?
// Session 시작 //
require_once "../wp_library/head.php";

// 경로 체크 //
Referer_Check($admin_domain);

// 전송 체크 //
//Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($gam_uid);

// 빈칸 체크 //
Input_Check($gam_uid);

//로그인 체크
if(!$site_auth->isLogin()){
	Error("ADMIN_ERROR");
	exit;
}
if($type == "gpdel")
{
	if($spo_code == "POWERLIFTING")
	{
		echo " where gam_uid='$gam_uid' AND pla_id='$pla_id' AND spo_sec_code in ('PL100','PL200','PL300') AND spo_code_detail = '$spo_code_detail'";
		//exit;
		if($spo_sec_code == "PL100")
		{
			$Mysql->Delete(wp_game_player," where gam_uid='$gam_uid' AND pla_id='$pla_id' AND spo_sec_code in ('PL100','PL200','PL300') AND spo_code_detail = '$spo_code_detail'");
		}
		else
		{
			$Mysql->Delete(wp_game_player," where gam_uid='$gam_uid' AND pla_id='$pla_id' AND spo_sec_code in ('PL400','PL500','PL600') AND spo_code_detail = '$spo_code_detail'");
		}
	}
	else
	{
		$Mysql->Delete(wp_game_player," where gam_uid='$gam_uid' AND uid='$uid'");
	}
}
else
{
	//삭제한 데이터 체크
	foreach($pla_id as $keys => $values){
		unset($query);
		unset($result);
		unset($obj);
		$query = "SELECT * FROM wp_game_player WHERE pla_id = '$values' AND gam_uid = '$gam_uid'";
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

	foreach($pla_id as $pkeys => $pvalues){
		// DB 삭제 //
		$Mysql->Delete(wp_game_player," where gam_uid='$gam_uid' AND pla_id='$pvalues'");
	}
}
// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>
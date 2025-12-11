<?
// Session 시작 //
require_once "../wp_library/head.php";

//권한 체크 - 관리자, 주최관리자만 볼 수 있음
if(!$site_auth->isConn($cd_player,$Admin_auth)){
	Error("AUTH_ERROR");
	exit;
}

foreach ($pla_id as $key_f => $val_f)
{
	$_tmp = explode("_",$val_f);
	$pla_id = $_tmp[0];
	$spo_code = $_tmp[1];
	$spo_sec_code = $_tmp[2];
	$spo_code_detail =  $_tmp[3];

	$_t_detail = "p_spo_code_detail_{$pla_id}";
	$spo_code_detail = $$_t_detail;

	//echo $pla_id."-".$spo_code."-".$spo_sec_code."-".$spo_code_detail."==".$spo_code_detail."<br>";

	if($spo_sec_code == "PL100" ||$spo_sec_code == "PL200" ||$spo_sec_code == "PL300")
	{
		$where = " AND spo_sec_code in ('PL100','PL200','PL300') ";
	}
	else if($spo_sec_code == "PL400" ||$spo_sec_code == "PL500" ||$spo_sec_code == "PL600")
	{
		$where = " AND spo_sec_code in ('PL400','PL500','PL600') ";
	}

	// TB 필드명 //
	$fields = array("spo_code_detail");

	// TB 필드값 //
	$values = array("$spo_code_detail");

	//echo "select * from wp_game_player WHERE gam_uid= '$gam_uid' AND spo_code = '$spo_code' $where AND pla_id='$pla_id'";
	// DB 저장 //
	$update = $Mysql->Update(wp_game_player,$fields,$values," WHERE gam_uid= '$gam_uid' AND spo_code = '$spo_code' $where AND pla_id='$pla_id'");

}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?gam_uid=$gam_uid&clu_code=$clu_code&spo_code=$spo_code&keyfield=$keyfield&key=$key&mode=powerlifting'>");
?>
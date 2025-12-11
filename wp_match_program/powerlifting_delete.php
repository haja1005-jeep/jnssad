<?
// Session 시작 //
require_once "../wp_library/head.php";

//권한 체크 - 관리자, 주최관리자만 볼 수 있음
if(!$site_auth->isConn($cd_player,$Admin_auth)){
	Error("AUTH_ERROR");
	exit;
}

if($spo_sec_code == "PL100" ||$spo_sec_code == "PL200" ||$spo_sec_code == "PL300")
{
	$where = " AND spo_sec_code in ('PL100','PL200','PL300') ";
}
else if($spo_sec_code == "PL400" ||$spo_sec_code == "PL500" ||$spo_sec_code == "PL600")
{
	$where = " AND spo_sec_code in ('PL400','PL500','PL600') ";
}

// DB 저장 //
$delete = $Mysql->Delete(wp_game_player," WHERE gam_uid='$gam_uid' AND spo_code='$spo_code' $where AND spo_code_detail = '$spo_code_detail' and pla_id = '$pla_id'");

// 페이지 이동 //
if($s_game_code)
{
	echo ("<meta http-equiv='Refresh' content='0; URL=index.html?mode=match&spo_code=$spo_code&s_game_code=$s_game_code'>");
}
else
{
	// 페이지 이동 //
	echo ("<meta http-equiv='Refresh' content='0; URL=index.html?gam_uid=$gam_uid&clu_code=$clu_code&spo_code=$spo_code&keyfield=$keyfield&key=$key&mode=powerlifting'>");
}
?>
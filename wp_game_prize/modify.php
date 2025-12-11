<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

if(!$spo_code)
{
	$spo_code = "";
}
if(!$spo_name)
{
	$spo_name = "";
}
if(!$prz_pla_name)
{
	$prz_pla_name ="";
}
if(!$rank)
{
	$rank = "";
}

// TB 필드명 //
$fields = array("gam_uid","prz_num","spo_code","spo_name","prz_pla_name","clu_code","rank","signdate");

// TB 필드값 //
$values = array("$gam_uid","$prz_num","$spo_code","$spo_name","$prz_pla_name","$clu_code","$rank",$signdate);

// DB 저장 //
$insert = $Mysql->update(wp_game_prize,$fields,$values," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list&prz_kind=$prz_kind'>");
?>
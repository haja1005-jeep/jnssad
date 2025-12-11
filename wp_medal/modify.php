<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($gam_uid);
Null_Check($clu_code);
Null_Check($spo_code);

// 빈칸 체크 //
Input_Check($gam_uid);
Input_Check($clu_code);
Input_Check($spo_code);

if(!$gold) $gold = 0;
if(!$silver) $silver = 0;
if(!$bronze) $bronze = 0;
// DB 저장 //
$insert = mysql_query("UPDATE wp_medal set gold=gold+$gold, silver=silver+$silver, bronze=bronze+$bronze WHERE gam_uid='$gam_uid' AND clu_code='$clu_code' AND spo_code='$spo_code'");
// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?mode=list'>");
?>
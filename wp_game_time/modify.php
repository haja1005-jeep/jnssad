<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($spo_code);
Null_Check($official_01);
Null_Check($official_02);

// 숫자 체크 //
Num_Check($st_hour_01);
Num_Check($st_minute_01);
Num_Check($et_hour_01);
Num_Check($et_minute_01);
Num_Check($st_hour_02);
Num_Check($st_minute_02);
Num_Check($et_hour_02);
Num_Check($et_minute_02);
Num_Check($tel1_01);
Num_Check($tel2_01);
Num_Check($tel3_01);
Num_Check($tel1_02);
Num_Check($tel2_02);
Num_Check($tel3_02);

// 인풋체크 //
Input_Check($office);
Input_Check($place);

$t_st_hour_01 = str_pad($st_hour_01, 2, "0", STR_PAD_LEFT);
$t_st_minute_01 = str_pad($st_minute_01, 2, "0", STR_PAD_LEFT);
$t_et_hour_01 = str_pad($et_hour_01, 2, "0", STR_PAD_LEFT);
$t_et_minute_01 = str_pad($et_minute_01, 2, "0", STR_PAD_LEFT);
$t_st_hour_02 = str_pad($st_hour_02, 2, "0", STR_PAD_LEFT);
$t_st_minute_02 = str_pad($st_minute_02, 2, "0", STR_PAD_LEFT);
$t_et_hour_02 = str_pad($et_hour_02, 2, "0", STR_PAD_LEFT);
$t_et_minute_02 = str_pad($et_minute_02, 2, "0", STR_PAD_LEFT);

// TB 필드명 //
$fields = array("gam_uid","gam_kind","spo_code","spo_sec_code","st_hour_01","st_minute_01","et_hour_01","et_minute_01","st_hour_02","st_minute_02","et_hour_02","et_minute_02","place","office","official_01","official_02","tel1_01","tel2_01","tel3_01","tel1_02","tel2_02","tel3_02","sort","signdate");

// TB 필드값 //
$values = array("$gam_uid","$gam_kind","$spo_code","$spo_sec_code","$t_st_hour_01","$t_st_minute_01","$t_et_hour_01","$t_et_minute_01","$t_st_hour_02","$t_st_minute_02","$t_et_hour_02","$t_et_minute_02","$place","$office","$official_01","$official_02","$tel1_01","$tel2_01","$tel3_01","$tel1_02","$tel2_02","$tel3_02","$sort",$signdate);

// DB 저장 //
$insert = $Mysql->update(wp_game_time,$fields,$values," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get&mode=list'>");
?>
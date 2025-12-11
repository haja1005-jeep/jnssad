<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($prz_kind);
Null_Check($clu_code);

/* 2014-09-26 박민철 수정 - 개인상장, 단체상장 외에는 상장 종류에 대한 번호로 상장번호를 제공한다. 

$query = "SELECT * FROM wp_game_prize WHERE gam_uid = '$gam_uid' ORDER BY uid DESC LIMIT 1";
$result = mysql_query($query);
$num =mysql_num_rows($result);

if($num == 0){
	$prz_num = 1;
} else{

	$obj = mysql_fetch_object($result);
	$_tmp = $obj->prz_num;
	$_t = explode("-",$_tmp);

	if(intval($prz_kind) != 5 && intval($prz_kind) != 7)
	{
		$prz_num = $_t[1] +1;
	}
	else
	{
		$prz_num = $_t[2] +1;
	}
}

$prz_num = str_pad($prz_num, 3, "0", STR_PAD_LEFT);
*/

$_tmp_prz_num = "1";
if($prz_kind == "01")
{
	$_tmp_prz_num = $rank;
}

$prz_num = str_pad($_tmp_prz_num, 3, "0", STR_PAD_LEFT);

$year = date("Y");

// 상장번호 생성 //
//if(intval($prz_kind) != 888 && intval($prz_kind) != 7)
//{
//	$prz_num = $year."-".$prz_num;
//}
//else
//{
	$prz_num = $year."-".$prz_kind."-".$prz_num;
//}

// TB 필드명 //
$fields = array("gam_uid","prz_kind","prz_num","spo_code","spo_name","prz_pla_name","clu_code","rank","count","signdate");

// TB 필드값 //
$values = array("$gam_uid","$prz_kind","$prz_num","$spo_code","$spo_name","$prz_pla_name","$clu_code","$rank",0,$signdate);

// DB 저장 //
$insert = $Mysql->Insert(wp_game_prize,$fields,$values);

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list&prz_kind=$prz_kind'>");
?>
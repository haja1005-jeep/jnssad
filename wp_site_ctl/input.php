<?
// 클래스 //
require_once "../library/head.php";

// 경로 체크 //
Referer_Check($admin_domain);

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($t_name);

// 단어 체크 //
while(list($str_val, $str_lab) = each($no_string))
{
	if(eregi($str_lab,$subject))
	{
		Error("INFO_ERROR");
		exit;
	}
}
//읽기(비밀글)
if(is_array($read_level_s)){
	foreach($read_level_s as $a_key){
		$read_s_txt .= $a_key.":";
	}
}
//읽기
if(is_array($read_level)){
	foreach($read_level as $a_key){
		$read_txt .= $a_key.":";
	}
}
if(is_array($write_level)){
	foreach($write_level as $a_key){
		$write_txt .= $a_key.":";
	}
}
if(is_array($reply_level)){
	foreach($reply_level as $a_key){
		$reply_txt .= $a_key.":";
	}
}
if(is_array($delete_level)){
	foreach($delete_level as $a_key){
		$delete_txt .= $a_key.":";
	}
}
if(is_array($modify_level)){
	foreach($modify_level as $a_key){
		$modify_txt .= $a_key.":";
	}
}

if(is_array($wkind)){
	foreach($wkind as $a_key){
		$wkind_txt .= $a_key.":";
	}
}

//파일 사이즈
$file_size_m  = ($file_size * 1000) * 1024;

// TB 필드명 //
$fields = array("t_name","t_name_txt","read_level_s","read_level","write_level","reply_level","delete_level","modify_level","file","secret","editor","wkind","file_type","cust_type","file_size");

// TB 필드값 //
$values = array("$t_name","$t_name_txt","$read_s_txt","$read_txt","$write_txt","$reply_txt","$delete_txt","$modify_txt","$file","$secret","$editor","$wkind_txt","$file_type","$cust_type","$file_size_m");
// DB 수정 //
$update = $Mysql->Insert(wp_board_ctl,$fields,$values);

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?code=$code&mode=list&type=$type&main_menu=$main_menu&sub_menu=$sub_menu'>");
?>
<?
// Session 시작 //
require_once "../wp_library/head.php";

if($prz_kind == "07")
{
	print_r($chk_id);
	for($i=0;$i < count($chk_id); $i++){
		echo "<br>".$chk_id[$i];
		$_tt = explode("/",$chk_id[$i]);
		$id=$_tt[0]; //아이디
			// 개인상장 입력체크 //
			$c_que = "SELECT * FROM wp_game_prize WHERE gam_uid='$gam_uid' AND game_code='$game_code' AND prz_kind='07' AND pla_id = '$id' AND spo_code='$_tt[1]' AND spo_sec_code = '$_tt[2]'";
			$c_res = mysql_query($c_que);
			$c_obj = mysql_fetch_object($c_res);
			$c_num = mysql_num_rows($c_res);
			if(!$c_num)
			{

				$query = "SELECT * FROM wp_game_prize WHERE gam_uid = '$gam_uid' ORDER BY uid DESC LIMIT 1";
				$result = mysql_query($query);
				$num =mysql_num_rows($result);
				if($num == 0){
					$prz_num = 1;
				} else{
					$obj = mysql_fetch_object($result);
					$_tmp = $obj->prz_num;
					$_t = explode("-",$_tmp);
					$prz_num = $_t[2]+1;
				}
				$year = date("y");
				// 상장번호 생성 //
				$prz_num = str_pad($prz_num, 3, "0", STR_PAD_LEFT);
				$prz_num_all = $year."-".$prz_kind."-".$prz_num;

				$query = "SELECT * FROM wp_game_record WHERE gam_uid='$gam_uid' AND game_kind='1' AND game_code='$game_code' AND id='$id' AND spo_code='$_tt[1]' AND spo_sec_code = '$_tt[2]'";
				echo "<br>".$query."<br>";
				$result = mysql_query($query);
				$obj = mysql_fetch_object($result);
				// TB 필드명 //
				$fields = array("gam_uid","game_code","pla_id","pla_name","prz_kind","prz_num","spo_code","spo_name","spo_sec_code","spo_code_detail","sex","tro_code","tro_level_code","prz_pla_name","clu_code","rank","count","signdate");
				// TB 필드값 //
				$values = array("$obj->gam_uid","$game_code","$obj->id","$obj->name","$prz_kind","$prz_num_all","$obj->spo_code","$spo_name","$obj->spo_sec_code","$obj->spo_code_detail","$obj->sex","$obj->tro_code","$obj->tro_level_code","$obj->name","$obj->clu_code","$obj->rank_tot",0,$signdate);
echo $prz_num_all."<br>";
				// DB 저장 //
				$insert = $Mysql->Insert(wp_game_prize,$fields,$values);
			}
	}
}
// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=input'>");
?>
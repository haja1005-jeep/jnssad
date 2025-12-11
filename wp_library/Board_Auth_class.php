<?
class Auth
{
	var $t_auth = array();
	var $cnt = 0;

	// 권한가져오기//
	function Init()
	{
		global $t_auth;
		global $cnt;
		if($cnt == 0){
			include_once 'mysql_class.php';
			// DB 생성 //
			$Mysql = new Mysql;
			// DB 연결 //
			$Mysql->Connect();

			$query = "SELECT * FROM wp_board_ctl WHERE flag='0' ";
			$result = mysql_query($query) or die( '[' . $PHP_SELF . '][' . __LINE__ . ']');
			while($mObj = mysql_fetch_array($result)){
				$t_name = $mObj[t_name];
				$i_a = array();
				$i_a['uid'] = $mObj[uid]; //고유번호
				$i_a['t_name'] = $mObj[t_name]; //테이블명
				$i_a['t_name_txt'] = $mObj[t_name_txt]; //테이블 한글명
				$i_a['read_level_s'] = $mObj[read_level_s]; //읽기권한(비밀글)
				$i_a['read_level'] = $mObj[read_level]; //읽기권한
				$i_a['write_level'] = $mObj[write_level]; //쓰기권한
				$i_a['reply_level'] = $mObj[reply_level]; //답변권한
				$i_a['delete_level'] = $mObj[delete_level]; //삭제권한
				$i_a['modify_level'] = $mObj[modify_level]; //수정권한
				$i_a['file'] = $mObj[file]; //파일갯수
				$i_a['secret'] = $mObj[secret]; //비밀글설정여부
				$i_a['editor'] = $mObj[editor]; //에디터 설정여부
				$i_a['wkind'] = $mObj[wkind]; //공지설정권한
				$i_a['file_type'] = $mObj[file_type]; //업로드파일타입
				$i_a['cust_type'] = $mObj[cust_type]; //업로드파일타입 'custom'일경우 사용되는 사용자 정의 타입
				$i_a['file_size'] = $mObj[file_size]; //업로드파일타입
				$t_auth[$t_name] = $i_a;
			} // end while
		}
		$cnt++;
	}
	//쓰기권한체크
	function isWrite($t_name,$u_level){
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['write_level'];
		$tmp = split(":",$_tmp);
		//모든 사람에게 사용가능하면 true 리턴
		if(in_array("0",$tmp)) return true;
		//레벨별 체크
		if(!$u_level || !$t_name) return false;
		if(in_array($u_level,$tmp)) return true;
		else return false;
	}
	//읽기권한체크
	function isRead($t_name,$u_level){
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['read_level'];
		$tmp = split(":",$_tmp);
		//모든 사람에게 사용가능하면 true 리턴
		if(in_array("0",$tmp)) return true;
		//레벨별 체크
		if(!$u_level || !$t_name) return false;
		if(in_array($u_level,$tmp)) return true;
		else return false;
	}
	//비밀글읽기권한체크
	function isRead_Sec($t_name,$u_level){
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['read_level_s'];
		$tmp = split(":",$_tmp);
		//모든 사람에게 사용가능하면 true 리턴
		if(in_array("0",$tmp)) return true;
		//레벨별 체크
		if(!$u_level || !$t_name) return false;
		if(in_array($u_level,$tmp)) return true;
		else return false;
	}
	//답변권한체크
	function isReply($t_name,$u_level){
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['reply_level'];
		$tmp = split(":",$_tmp);
		//모든 사람에게 사용가능하면 true 리턴
		if(in_array("0",$tmp)) return true;
		//레벨별 체크
		if(!$u_level || !$t_name) return false;
		if(in_array($u_level,$tmp)) return true;
		else return false;
	}
	//삭제권한체크
	function isDelete($t_name,$u_level){
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['delete_level'];
		$tmp = split(":",$_tmp);
		//모든 사람에게 사용가능하면 true 리턴
		if(in_array("0",$tmp)) return true;
		//레벨별 체크
		if(!$u_level || !$t_name) return false;
		if(in_array($u_level,$tmp)) return true;
		else return false;
	}
	//수정권한체크
	function isModify($t_name,$u_level){
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['modify_level'];
		$tmp = split(":",$_tmp);
		//모든 사람에게 사용가능하면 true 리턴
		if(in_array("0",$tmp)) return true;
		//레벨별 체크
		if(!$u_level || !$t_name) return false;
		if(in_array($u_level,$tmp)) return true;
		else return false;
	}
	//첨부파일 갯수
	function fileCount($t_name){
		if(!$t_name) return false;
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['file'];
		return $_tmp;
	}
	//비밀글 사용여부
	function isSecret($t_name){
		if(!$t_name) return false;
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['secret'];
		if($_tmp == 'yes') return true;
		else return false;
	}
	//에디터 사용여부
	function isEditor($t_name){
		if(!$t_name) return false;
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['editor'];
		if($_tmp == 'yes') return true;
		else return false;
	}
	//공지설정권한
	function isWkind($t_name,$u_level){
		if(!$u_level || !$t_name) return false;
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['wkind'];
		$tmp = split(":",$_tmp);
		if(in_array($u_level,$tmp)) return true;
		else return false;
	}
	//업로드 파일
	function fileType($t_name){
		if(!$t_name) return false;
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['file_type'];
		return $_tmp;
	}
	//사용자 지정 업로드 파일
	function custType($t_name){
		if(!$t_name) return false;
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['cust_type'];
		return $_tmp;
	}
	//파일용량
	function fileSize($t_name){
		if(!$t_name) return false;
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['file_size'];
		return $_tmp;
	}
	//파일용량 (Mbyte) 로 리턴
	function fileSizeM($t_name){
		if(!$t_name) return false;
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['file_size'];
		$_tmp = ($_tmp / 1000) / 1024;
		return $_tmp;
	}
	//테이블명
	function getTableName($t_name){
		if(!$t_name) return false;
		global $t_auth;
		$i_a = $t_auth[$t_name];
		$_tmp = $i_a['t_name_txt'];
		return $_tmp;
	}
}
Auth::Init(); //권한 가져오기
//Auth::isWrite('edu_01','9'); //권한 가져오기
//echo Auth::fileCount('edu_01');
//print_r($t_auth);
?>
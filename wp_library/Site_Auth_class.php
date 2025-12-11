<?
class Site_Auth
{
	var $t_auth = array();
	var $cnt = 0;

	// 권한가져오기//
	function Init()
	{
	}
	//접근권한.
	function isConn($cd,$s_auth){
		global $wp_site_code;
		$auths = $wp_site_code[$cd];
		if(in_array($s_auth,$auths))
			return true;
		else return false;
	}
	//로그인
	function isLogin(){
		global  $Admin_Id;
		global  $Site_Admin;
		global  $site_md5;

		if(!session_is_registered("Site_Admin") || !session_is_registered("Admin_Id"))
		{
			if($Admin_Id != md5($Site_Admin.$site_md5)){
				return false;
			}else{
				return false;
			}
		}
		return true;
	}
}
Site_Auth::Init(); //권한 가져오기
//Auth::isWrite('edu_01','9'); //권한 가져오기
//echo Auth::fileCount('edu_01');
?>
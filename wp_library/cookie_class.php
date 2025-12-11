<?
class Cookie
{
	var $auth_id = NULL;
	var $auth_pw = NULL;
	var $auth_level = NULL;
	var $login_confirm = FALSE;
	var $uniqid = 'wwwwooyoungcameracom';

	// 사이트 인증 //
	function SiteAuth()
	{
		global $HTTP_COOKIE_VARS;

		$this->auth_id = $HTTP_COOKIE_VARS["wooyoung_auth"];
		$this->auth_pw = $HTTP_COOKIE_VARS["wooyoung_level"];
		$this->auth_level = $HTTP_COOKIE_VARS["wooyoung_auth_level"];
		if($this->auth_id)
		{
			// 로그인 체크 //
			$this->CheckLogin($this->auth_id,$this->auth_pw);
		}
	}

	// 암호화 함수 //
	function ConvertHash($pw)
	{
		return md5(crypt($pw,$this->uniqid));
	}

	// 아이디 확인 //
	function GetUserInfo($inpu_id)
	{
		// DB 생성 //
		$Mysql = new Mysql;

		// DB 연결 //
		$Mysql->Connect();

		$result = "SELECT id,passwd, auth FROM member WHERE auth >= '1' AND id='".$inpu_id."'";
		$Mysql->ResultQuery($result);
		if($Mysql->row)
		{
			$row = mysql_fetch_object($Mysql->result);
			$memberPW = $row->passwd;
			$this->auth_level = $row->auth;
			return $memberPW;
		}
		else
		{
			return NULL;
		}
	}

	// 로그인 체크 //
	function CheckLogin($input_id,$input_pw)
	{
		$temp_data = $this->GetUserInfo($input_id);
		if($temp_data != NULL)
		{
			// 비밀번호 암호화 //
			$authPW = $input_pw;
			$ke_Member = md5(Encrypt($authPW));
			$ke_User = md5(Encrypt($this->ConvertHash($temp_data)));

			// 비밀번호 비교 //
			if(strcmp($ke_Member,$ke_User))
			{
				Error("PW_ERROR");
				exit;
			}
			else
			{
				$this->login_confirm = TRUE;
			}
		}
		return $this->login_confirm;
	}

	// 로그인 확인 //
	function StateLogin()
	{
		return $this->login_confirm;
	}

	// 쿠키 인증 //
	function LoginAuth($input_id,$input_pw)
	{
		$hash_pw = Encrypt($input_pw);
		if($this->CheckLogin($input_id,$this->ConvertHash($hash_pw)))
		{
			$TIGER_HASH = $this->ConvertHash($hash_pw);
			$user_sid = md5(uniqid(rand()));
			setcookie("wooyoung_auth",$input_id,0,'/');
			setcookie("wooyoung_level",$TIGER_HASH,0,'/');
			setcookie("wooyoung_auth_level",$TIGER_HASH,0,'/');
			setcookie("infomationsignal",$user_sid,0,'/');
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// 쿠키 삭제 //
	function LogOut()
	{
		setcookie("wooyoung_auth",'',0,'/');
		setcookie("wooyoung_level",'',0,'/');
		setcookie("wooyoung_auth_level",'',0,'/');
		setcookie("infomationsignal",'',0,'/');
		$this->login_confirm = FALSE;
	}
}
?>
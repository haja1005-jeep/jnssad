<?
// 공백 체크 //
function Null_Check($str)
{
	if(!ereg("([^[:space:]]+)", $str) || ereg("([[:space:]]+)",$str))
	{
		Error("NULL_ERROR");
		exit;
	}
}

// 빈칸 체크 //
function Input_Check($str)
{
	if(!ereg("([^[:space:]]+)", $str))
	{
		Error("INPUT_ERROR");
		exit;
	}
}

// 한글 체크 //
function Name_Check($str)
{
	for($i = 0 ; $i < strlen($str) ; $i++)
	{
		if(ord($str[$i]) <= 0x80)
		{
			Error("NAME_ERROR");
			exit;
		}
	}
}
// 숫자/영어체크 //
function Eng_Check($str)
{
	for($i = 0 ; $i < strlen($str) ; $i++)
	{
		if(ord($str[$i]) > 0x80)
		{
			Error("ENG_ERROR");
			exit;
		}
	}
}

// 숫자 체크 //
function Num_Check($str)
{
	if(ereg("[^0-9]", $str))
	{
		Error("NUM_ERROR");
		exit;
	}
}

// 아이디 체크 //
function Id_Check($id)
{
	if(!ereg("[[:alnum:]+]{5,12}",$id))
	{
		Error("ID_ERROR");
		exit;
	}
}

// 비밀번호 체크 //
function Pw_Check($passwd)
{
	if(!ereg("[[:alnum:]+]{5,12}",$passwd))
	{
		Error("PW_ERROR");
		exit;
	}
}

// 전자우편 체크 //
function Mail_Check($email)
{
	$email = trim($email);
	if(ereg("([^[:space:]]+)", $email) && (!ereg("(^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$)", $email)))
	{
		Error("EMAIL_ERROR");
		exit;
	}
}

// 홈페이지 체크 //
function Home_Check($homepage)
{
	$homepage = trim($homepage);
	if(ereg("([^[:space:]]+)", $homepage) && (!ereg("http://([0-9a-zA-Z./@~?&=_]+)", $homepage)))
	{
		Error("SITE_ERROR");
		exit;
	}
}

// 암호 설정 //
function Encrypt($string)
{
	$secret = md5("kodaewoong");
	$hash = md5($secret.$string);
	$crypted = md5(crypt($string,$hash));
	return $crypted;
}

// 문자열 자르기 //
function Cut_Han($str, $length)
{
	if(strlen($str) <= $length)
	{
		return $str;
	}
	$cut_str = substr($str, 0, $length - 3);
	preg_match('/^([\x00-\x7f]|.{2})*/', $cut_str, $buffer);
	return $buffer[0] . '...';
}

// HTTP_REFERER 체크 //
function Referer_Check($url)
{
	if(!eregi($url,getenv("HTTP_REFERER")))
	{
		//Error("ACTION_ERROR");
		//exit;
	}
}

// 전송 체크 //
function Method_Check($str)
{
	if($str != 'POST')
	{
		Error("ACTION_ERROR");
		exit;
	}
}

// 2047 인코딩 //
function Encode_2047($str)
{
	return '=?euc-kr?b?'.base64_encode($str).'?=';
}

// 태그 적용 //
function Html_Change($text)
{
	$text = eregi_replace("&quot;","\"",$text);
	$text = eregi_replace("&#39;","'",$text);
	$text = eregi_replace("&lt;","<",$text);
	$text = eregi_replace("&gt;",">",$text);
	return $text;
}

// 관리자 태그 체크 //
function Tag_Check($text)
{
	$text = eregi_replace("\"","&quot;",$text);
	$text = eregi_replace("'","&#39;",$text);
	$text = eregi_replace("<","&lt;",$text);
	$text = eregi_replace(">","&gt;",$text);
	$text = eregi_replace("meta"," ",$text);
	$text = trim($text);
	$text1 = strtolower($text);
	$table = array("<table","</table","<tr","</tr","<td","</td","<th","</th");
	for($i = 0 ; $i < 8 ; $i++)
	{
		$table1= split($table[$i],$text1);
		$i++;
		$table2= split($table[$i],$text1);
		if(sizeof($table1) != sizeof($table2))
		{
			Error("HTML_ERROR");
			exit;
		}
	}
	$table3 = split($table[1],$text1);
	$total = sizeof($table3);
	for($i = 2 ; $i < 8 ; $i++)
	{
		if(eregi($table[$i],$table3[$total-1]))
		{
			Error("HTML_ERROR");
			exit;
		}
	}
	$text = trim($text);
	return $text;
}

// 사용자 태그 체크 //
function Html_Check($text)
{
	$text = eregi_replace("\"","&quot;",$text);
	$text = eregi_replace("'","&#39;",$text);
	$text = eregi_replace("<","&lt;",$text);
	$text = eregi_replace(">","&gt;",$text);
	$text = eregi_replace("applet"," ",$text);
	$text = eregi_replace("meta"," ",$text);
	$text = eregi_replace("script"," ",$text);
	$text = eregi_replace("onclick"," ",$text);
	$text = eregi_replace("onload"," ",$text);
	$text = eregi_replace("form"," ",$text);
	$text = eregi_replace("onchange"," ",$text);
	$text = eregi_replace("iframe"," ",$text);
	$text = eregi_replace("object"," ",$text);
	$text = trim($text);
	$text1 = strtolower($text);
	$table = array("<table","</table","<tr","</tr","<td","</td","<th","</th");
	for($i = 0 ; $i < 8 ; $i++)
	{
		$table1= split($table[$i],$text1);
		$i++;
		$table2= split($table[$i],$text1);
		if(sizeof($table1) != sizeof($table2))
		{
			Error("HTML_ERROR");
			exit;
		}
	}
	$table3 = split($table[1],$text1);
	$total = sizeof($table3);
	for($i = 2 ; $i < 8 ; $i++)
	{
		if(eregi($table[$i],$table3[$total-1]))
		{
			Error("HTML_ERROR");
			exit;
		}
	}
	$text = trim($text);
	return $text;
}

// 태그 내용 /
function Strip_Tag($text)
{
	// 임시 저장 //
	$buf = "";

	// strpos 함수는 php버전별로 못찾을경우 리턴값이 다름
	$pos = strpos($text, "<");

	// 문자 위치값 리턴 //
	if($pos === false) return $text;

	while(true)
	{
		if($pos - 1 < 0)  $pos = 0;
		$buf .= substr($text, 0, $pos);
		$pos = strpos($text, ">");
		if($pos === false) break;
		$text = substr($text, $pos+1);
		$pos = strpos ($text, "<");
		if($pos === false) break;
	}
	return $buf;
}
// <P>를 <br>태그로 수정 //
function stripTags($tag,$string)
{
	$string = str_replace("&lt;/$tag&gt;","<br>",$string);
	$string = str_replace("&lt;$tag&gt;","",$string);
	return $string;
}
// 주민등록번호 체크 //
function Jumin_Check($jumin1,$jumin2)
{
	$jumin = $jumin1.$jumin2;
	$lastnumber = substr($jumin,12,1);
	$add = '234567892345';
	$length = strlen($jumin);
	$total = 0;
	if($length <> 13)
	{
		Error("JUMIN_ERROR");
		exit;
	}

	for($i = 0; $i < 12; $i++)
	{
		$total = $total + (substr($jumin,$i,1)*substr($add,$i,1));
	}

	$rest = $total%11;
	$result = 11 - $rest;
	if($result == 10)
	{
		$result = 0;
	}
	if($result == 11)
	{
		$result = 1;
	}
	if($result <> $lastnumber)
	{
		Error("JUMIN_ERROR");
		exit;
	}
	return true;
}
// 키워드 체크 //
function Keyword_Check($site)
{
	$enginList = array(
		"yahoo"=>"p",
		"paran"=>"Query",
		"naver"=>"query",
		"empas"=>"q",
		"daum"=>"q",
		"hanmir"=>"QR",
		"hanafos"=>"query",
		"nate"=>"query",
		"nate"=>"q",
		"altavista"=>"q",
		"google"=>"q",
	);
	$browsers = implode("|",array_keys($enginList));
	$site = parse_url($site);
	preg_match("/($browsers)/i",$site['host'],$results);
	if(!$results[0]) return false;
	$querystring = explode("&", $site['query']);
	$query = array();
	foreach($querystring as $q)
	{
		list($k, $v) = explode("=", $q, 2);
		$query[$k] = trim($v);
	}
	$r = $query[$enginList[$results[0]]];
	if(!$r) return false;
	if(preg_match('/(UTF-8)/i',$site[query],$match))
	{
		$q = iconv("UTF-8", "EUC-KR", urldecode($r));
	}
	else if(preg_match('/(google)/i',$site[host],$match))
	{
		$q = iconv("UTF-8", "EUC-KR", urldecode($r));
	}
	else if(preg_match('/(UTF)/i',$site[query],$match))
	{
		$q = iconv("UTF", "EUC-KR", urldecode($r));
	}
	else
	{
		$q = urldecode($r);
	}
	//return array("query"=>$q, "host"=>$results[0]);
	$s_keyword = $q;
	$s_site = $results[0];
	return $s_keyword.";".$s_site;
}

// 사업자 등록번호 체크 //
function Saup_Check($num)
{
	$num = trim($num);
	$checknumber = $num[9];
	$add = '13713713';
	$lenght = strlen($num);
	$total = 0;
	if($lenght <> 10)
	{
		error("SAUP_ERROR");
		exit;
	}

	for($i = 0; $i < 8; $i++)
	{
		$total = $total + ($num[$i]* $add[$i]);
	}

	$lastnumber = ($num[8] * 5);
	$lastnumber = (string)$lastnumber;
	$k = strlen($lastnumber);

	if($k < 2)
	{
		$lastnumber = $lastnumber[0];
	}
	else
	{
		$lastnumber = $lastnumber[0]+$lastnumber[1];
	}

	$total = $total+$lastnumber;
	$total = (string)$total;
	$j = strlen($total)-1;
	$total = 10 - $total[$j];
	if($checknumber <> $total)
	{
		error("SAUP_ERROR");
		exit;
	}
	return true;
}

// 썸네일 //
function Thumnail($file,$save_filename,$save_path,$max_width,$max_height)
{
	// 이미지 정보 //
	$img_info = getImageSize($file);

	// 이미지 확장자 //
	if($img_info[2] == 1)
	{
		// GIF //
		$src_img = ImageCreateFromGif($file);
	}
	else if($img_info[2] == 2)
	{
		// JPEG //
		$src_img = ImageCreateFromJPEG($file);
	}
	else if($img_info[2] == 3)
	{
		// PNG //
		$src_img = ImageCreateFromPNG($file);
	}
	else
	{
		return 0;
	}

	// 이미지 크기 //
	$img_width = $img_info[0];
	$img_height = $img_info[1];
	if($img_width <= $max_width)
	{
		$max_width = $img_width;
		$max_height = $img_height;
	}
	if($img_width > $max_width)
	{
		$max_height = ceil(($max_width / $img_width) * $img_height);
	}

	// 트루타입 이미지 //
	$dst_img = imagecreatetruecolor($max_width, $max_height);

	// R255, G255, B255 색상 //
	ImageColorAllocate($dst_img, 255, 255, 255);

	// 이미지 생성 //
	ImageCopyResampled($dst_img, $src_img, 0, 0, 0, 0, $max_width, $max_height, ImageSX($src_img),ImageSY($src_img));

	// 이미지 저장 //
	if($img_info[2] == 1)
	{
		// GIF //
		ImageInterlace($dst_img);
		ImageGIF($dst_img, $save_path.$save_filename);
	}
	else if($img_info[2] == 2)
	{
		// JPEG //
		ImageInterlace($dst_img);
		ImageJPEG($dst_img, $save_path.$save_filename);
	}
	else if($img_info[2] == 3)
	{
		// PNG //
		ImageInterlace($dst_img);
		ImagePNG($dst_img, $save_path.$save_filename);
	}

	// 임시 이미지 삭제 //
	ImageDestroy($dst_img);
	ImageDestroy($src_img);
}

// 메세지 출력 //
function Popup_Msg($msg)
{
	echo ("
		<script language='javascript'>
		<!--//
		alert('$msg');
		history.back(-1);
		//-->
		</script>
	");
}

// 확인 체크 //
function Confirm_Msg($msg)
{
	echo ("
		<script language='javascript'>
		<!--//
		msg = confirm('$msg')
		if(msg == false)
		{
			history.back(-1);
		}
		else
		{
			var URL = '../member/member_agree.html';
			location.href = URL;
		}
		//-->
		</script>
	");
}

// 메세지 설정 //
function Error($errcode,$etc="")
{
	switch ($errcode)
	{
		case ("TRO_ERROR") :
			Popup_Msg("해당 종목은 접수가 마감되었습니다.");
			break;
		case ("POINT_ERROR") :
			Popup_Msg("사용가능한 포인트가 없습니다. 다시 로그인하여 사용하여주십시요!!");
			break;
		case ("NULL_ERROR") :
			Popup_Msg("공백없이 입력하여 주시겠습니까?");
			break;

		case ("INPUT_ERROR") :
			Popup_Msg("빈칸없이 입력하여 주시겠습니까?");
			break;

		case ("NAME_ERROR") :
			Popup_Msg("입력하신 성명을 한글로 입력하여 주시겠습니까?");
			break;

		case ("ENG_ERROR") :
			Popup_Msg("영문과 숫자의 조합으로 입력하여 주시겠습니까?");

		case ("NUM_ERROR") :
			Popup_Msg("숫자만 입력하여 주시겠습니까?");
			break;

		case ("ID_ERROR") :
			Popup_Msg("아이디를 확인하여 주시겠습니까?");
			break;

		case ("PW_ERROR") :
			Popup_Msg("비밀번호를 확인하여 주시겠습니까?");
			break;
		case ("NEW_PW_ERROR") :
			Popup_Msg("입력하신 새 비밀번호가 일치하지 않습니다. 확인해주시겠습니까?");
			break;

		case ("EMAIL_ERROR") :
			Popup_Msg("입력하신 E-mail 주소를 확인하여 주시겠습니까?");
			break;

		case ("SITE_ERROR") :
			Popup_Msg("입력하신 URL 주소를 확인하여 주시겠습니까?");
			break;

		case ("ACTION_ERROR") :
			Popup_Msg("현 브라우저에서 적용되지 않는 비정상적인 실행입니다.");
			break;

		case ("HTML_ERROR") :
			Popup_Msg("입력하신 내용중 HTML 부분을 확인하여 주시겠습니까?");
			break;

		case ("JUMIN_ERROR") :
			Popup_Msg("입력하신 주민등록번호를 확인하여 주시겠습니까?");
			break;

		case ("SAUP_ERROR") :
			Popup_Msg("입력하신 사업자 등록번호를 확인하여 주시겠습니까?");
			break;

		case ("ADMIN_ERROR") :
			echo ("
				<script language='javascript'>
				<!--//
				alert('관리자 인증이 되지 않았거나 오랫동안 사용하지 않아서 관리자 인증이 끊겼으므로 다시 인증을 받으시겠습니까?');
				parent.location.href = '/';
				//-->
				</script>
			");
			break;

		case ("OVERLAP_ERROR") :
			Popup_Msg("입력하신 정보중 이미 등록된 중복 정보가 있습니다.");
			break;

		case ("SIZE_ERROR") :
			Popup_Msg("첨부하신 파일의 용량이 초과되었으므로 8M이하로 다시 등록하여 주시겠습니까?");
			break;

		case ("UPLOAD_ERROR") :
			Popup_Msg("첨부하신 파일이 등록 불가 확장자 파일이므로 확장자를 확인하여 주시겠습니까?");
			break;

		case ("DELETE_ERROR") :
			Popup_Msg("삭제하실 수 없는 내용입니다.");
			break;

		case ("THREAD_ERROR") :
			Popup_Msg("답변글이 있을 경우 삭제하실 수 없습니다.");
			break;

		case ("DOWN_ERROR") :
			Popup_Msg("첨부파일이 존재하지 않아 다운로드 하실 수 없습니다.");
			break;

		case ("DATE_ERROR") :
			Popup_Msg("입력하신 날짜를 확인하여 주시겠습니까?");
			break;

		case ("INFO_ERROR") :
			Popup_Msg("입력하신 정보가 잘못되어 있으므로 다시 확인하여 주시겠습니까?");
			break;

		case ("POLL_ERROR") :
			Popup_Msg("이미 투표를 하였습니다.");
			break;

		case ("RECOMMEND_ERROR") :
			Popup_Msg("이미 추천을 하였습니다.");
			break;

		case ("EA_ERROR") :
			Popup_Msg("1개 이상의 항목을 선택하여 주시겠습니까?");
			break;

		case ("FIND_ERROR") :
			Popup_Msg("입력하신 내용의 검색결과가 없습니다.");
			break;

		case ("CLOSE_ERROR") :
			Popup_Msg("본인만 확인하실 수 있는 비공개 내용입니다.");
			break;

		case ("LOGIN_ERROR") :
			Confirm_Msg("현재 회원가입이 되어있지 않거나 로그아웃 상태이므로 로그인 또는 회원가입을 하여 주시겠습니까?");
			break;

		case ("MEMBER_ERROR") :
			$page_move_url = getenv("REQUEST_URI");
			echo ("
				<script language='javascript'>
				<!--//
				location.href = '../member/member_login.html?page_move_url=$page_move_url';
				//-->
				</script>
			");
			break;

		case ("AUTH_ERROR") :
			Popup_Msg("선택하신 서비스에 대한 인증된 권한이 없습니다.");
			break;

		case ("SERVICE_ERROR") :
			Popup_Msg("회원만이 이용하실 수 있는 코너입니다.");
			break;

		case ("QUERY_ERROR") :
			$err_no = mysql_errno();
			$err_msg = mysql_error();
			$error_msg = "Error Code (".$err_no.") : $err_msg";
			$error_msg = addslashes($error_msg);
			Popup_Msg($error_msg);
			break;

		case ("DB_ERROR") :
			$err_no = mysql_errno();
			$err_msg = mysql_error();
			$error_msg = "ERROR CODE".$err_no.":$err_msg";
			echo("$error_msg");
			break;
		case ("DOUBLE_ERROR") :
			Popup_Msg("회원님은 이미 리서치에 응해주셨습니다.");
			break;
		case ("DELETE_CODE_ERROR") :
			Popup_Msg("등록된 상품을 이동후 삭제해주세요!!.");
			break;
		case ("EXEC_ERROR") :
			Popup_Msg("처리중 에러입니다. 일부데이터가 처리되지 않았습니다.데이터를 확인해주세요!");
			break;
		case ("DOUBLE_GAME") :
			Popup_Msg("주최중인 대회가 있습니다.");
			break;
		case ("NOT_DATA") :
			Popup_Msg("해당 데이터가 존재하지 않습니다.");
			break;
		case ("ALREADY_INPUT_ERROR") :
			Popup_Msg("해당 선수는 이미  선수로 등록되어있습니다.");
			break;
		case ("END_GAME_ERROR") :
			Popup_Msg("마감된 대회이거나, 승인되지 않은 대회입니다.");
			break;
		case ("NO_CHG_GAME") :
			Popup_Msg("마감된 대회이거나, 승인된 데이터는 수정불가능합니다.");
			break;
		case ("NO_CLUB_PLAYER") :
			Popup_Msg("해당 시/군에 소속된 선수가 아닙니다. 확인 해주세요!");
			break;
		case ("NOT_EQUAL_KUBUN") :
			Popup_Msg($etc."선수의 정보가 등록하자고 하는 선수 구분과 일치하지 않습니다.");
			break;
		case ("NOT_EQUAL_CAPACITY") :
			Popup_Msg($etc."선수의 정보가 해당 대회에 참가할 수 있는 자격조건과 일치하지 않습니다.");
			break;
		case ("ALREADY_JOIN") :
			Popup_Msg($etc."선수는 이미 참가하였습니다.");
			break;
		case ("FULL_JOIN") :
			Popup_Msg("해당 종목은 ".$etc."명까지만 참가가능합니다.");
			break;
		case ("DANCE_FULL_JOIN") :
			Popup_Msg("해당 종목은 ".$etc."팀까지만 신청가능합니다.");
			break;
		case ("LAWNBOING_JOIN") :
			Popup_Msg("단식에 참가한 선수는 복식에 참가 하실 수 없습니다.");
			break;
		case ("ROWING_RW200_JOIN") :
			Popup_Msg("해당 종목은 남자 1명, 여자 1명으로 구성된 1개 조만 참가 가능합니다.");
			break;
		case ("LIFE_SPORTS_OVER") :
			Popup_Msg("생활체육은 2종목까지만 참가가능합니다..");
			break;
		case ("SPORTS_OVER") :
			Popup_Msg("전문체육 1종목, 생활체육 2종목까지 참가가능합니다..");
			break;
		case ("ONE_FULL_JOIN") :
			Popup_Msg("해당 종목은 출전선수 1명이 세부  ".$etc."종목까지만 참가가능합니다.");
			break;
		case ("NO_SELECT_GAME") :
			Popup_Msg("대회를 선택후 검색하신 후 참가신청해주세요!");
			break;
		case ("NOT_EQUAL_PLAYER") :
			Popup_Msg($etc."의 선수정보와 경기 정보가 일치하지 않습니다.");
			break;
		case ("NO_ORTHER_GAME") :
			Popup_Msg("중복 신청할 수 없습니다. 타 종목으로 신청되어있습니다. 확인바랍니다.");
			break;
		case ("TT_FULL_JOIN") :
			Popup_Msg("탁구 단체전은 총 4명까지만 참가가능합니다.");
			break;
		case ("BD_ONE_JOIN") :
			Popup_Msg("당구는 단체전 한경기에만 참가 가능합니다.");
			break;
		case ("TT_203_FULL_JOIN") :
			Popup_Msg("탁구 단체전 복식은 총 2명까지만 참가가능합니다.");
			break;
		case ("BL_TWO_FULL_JOIN") :
			Popup_Msg("볼링 2인조 경기는 경기당 2명까지만 참가가능합니다.");
			break;
		case ("NO_USE_WHEEL") :
			Popup_Msg("휠체어 사용 선수만 참가 가능합니다.");
			break;
		case ("ONLY_USE_WHEEL") :
			Popup_Msg("휠체어 사용 선수는 참가 불가 종목입니다.");
			break;
		case ("C_USE_WHEEL") :
			Popup_Msg("휠체어 사용 선수가 이미 등록 되어있습니다.");
			break;
		case ("N_USE_WHEEL") :
			Popup_Msg("스탠딩 선수가 이미 등록 되어있습니다.");
			break;
		case ("NOT_INPUT_PLAYER") :
			Popup_Msg("신청기간이 끝났으므로 선수는 더이상 신청할 수 없습니다.");
			break;
		case ("NOT_BOCHIA_GROUP") :
			Popup_Msg("보치아 단체전은 각 등급별 개인전 참가자만 가능합니다.");
			break;
		case ("GAME_SERIAL_ERROR") :
			Popup_Msg("경기 번호를 입력하지 않았습니다.");
			break;
		case ("NO_BIRTH_ERROR") :
			Popup_Msg("만 14세 이상만 참가하실 수 있습니다.");
			break;
		case ("WOMAN_JOIN") :
			Popup_Msg("반드시 여성1명이 참가하여야 합니다.");
			break;
		case ("BILLIARDS_BIS_MALE_ONE_JOIN") :
			Popup_Msg("남자 BIS 개인전은 3구, 원큐션 한 종목만 출전할수 있습니다.");
			break;

		case ("ARCHERY_ONE_JOIN") :
			Popup_Msg("리커브, 컴파운드 한 종목만 출전할수 있습니다.");
			break;
	
	
	}
}

// 초를 'HH:mm:ss' 형태로 환산 //
function getTimeFromSeconds($seconds)
{
    $h = sprintf("%02d", intval($seconds) / 3600);
    $tmp = $seconds % 3600;
    $m = sprintf("%02d", $tmp / 60);
    $s = sprintf("%02d", $tmp % 60);

    return $m.':'.$s;
}
?>
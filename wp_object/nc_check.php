<?php
// =============================================//
// version : 1.5 (2003.07.3)
// 사용방법 :
// nc_p.php Page에 Post 방식으로 a1=이름,a2=주민번호를 보낸다.
// =============================================//

// =============================================//
// 회원사 ID, 비밀번호 및 기타  설정
// =============================================//
// sURLnc의 값을 실제 이 페이지를 부르는 page로 설정해야 동작합니다.
// 외부 사용자가 이 URL을 스크래핑하여 불법으로 사용하는 것을 막기 위함.

 	define("sURLnc", "http://player.jnsad.or.kr/wp_player_ctrl/input.html");   	// 이전 URL을 입력하세요.
//	define("sURLnc", "http://www.test.co.kr/nc.php");

// @SITEID 및 @SITEPW 를 실제 부여 받은 사이트 id 및 패스워드로 로 바꾸기 바람니다.

$sSiteID = "V799";  	// 사이트 id
$sSitePW = "52234141";   // 비밀번호

// 네임 체크 모듈 위치 //
$cb_encode_path = "/home/player/public_html/wp_object/cb_namecheck";

// ============================================ //
// Main 시작
// ============================================ //
// Passed Data value :
// $a1 : 이름
// $a2 : 주민번호
// ============================================ //

	$strJumin= $jumin1.$jumin2;		// 주민번호
	$strName = $name;		//이름
	$iReturnCode  = "";
	// sURLnc의 값을 실제 이 페이지를 부르는 page(HTTP_REFERER)로 설정해야 동작합니다.
	// echo "HTTP_REFERER=($HTTP_REFERER)"; 로 값을 확인해 볼수 잇습니다.
	// nc_p.php 페이지를 외부 사용자가 불법으로 사용하는 것을 막기 위함.
//	if ($HTTP_REFERER == sURLnc)
//	 {
	$iReturnCode = `$cb_encode_path $sSiteID $sSitePW $strJumin $strName`;
/*	 } else {
		echo ("
		   <script language='javascript'>
				alert('잘못된 경로에서의 접근입니다.');
				self.close();
		   </script>
		   ");
		   exit;
	 }
*/
//	echo "성명확인 서비스 결과<hr><p>성명 확인 결과 값이 저장된 \$iReturnCode를 이용하여 회원사 추가처리 루틴을 삽입<P>";
//	echo "iReturnCode=($iReturnCode)" ;
$iReturnCode = "1";
        switch($iReturnCode){
	      case 1:
			echo ("
		            <script language='javascript'>
		               alert('실명인증이 확인되었습니다.');
		               window.opener.sForm.check_name.value = '1';
		               self.close();
		            </script>
			   ");
			break;
		case 2:
			echo ("
		            <script language='javascript'>
		               alert('본인이 아닙니다. 성명, 또는 주민번호를 확인하여주세요.');
		               self.close();
		            </script>
			   ");
			break;
		case 3:
			echo ("
		            <script language='javascript'>
		             	 alert('본인인증을 위한 데이터가 존재하지 않습니다. 개인정보를 등록하시겠습니까?');
		               location.href ='http://www.creditbank.co.kr/its/its.cb?m=namecheckMismatch';
		            </script>
			   ");
			break;
		case 5:
			echo ("
		            <script language='javascript'>
		               alert('주민번호 입력오류입니다. 주민번호를 확인하여주세요.');
		               self.close();
		            </script>
			   ");
			break;
		case 50;
			echo ("
		            <script language='javascript'>
		            	  alert('정보도용차단 요청 주민번호입니다.');
		            	  top.resizeBy(300,250);
		               location.href ='http://www.creditbank.co.kr/its/itsProtect.cb?m=namecheckProtected';
		            </script>
			   ");
			break;
		default:
			echo ("
			   <script language='javascript'>
					alert('인증에 실패 하였습니다. 리턴코드:[$iReturnCode]');
					self.close();
			   </script>
			   ");
			break;
 }
?>


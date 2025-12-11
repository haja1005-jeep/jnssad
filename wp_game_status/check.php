<?php
@session_start();
@header("Content-type: text/html; charset=euc-kr");


//권한 관리 클래스
class_exists('Auth') || require('../wp_library/Board_Auth_class.php');
$auth_class = new Auth;

// Mysql 클래스 //
require_once "../wp_library/mysql_class.php";
// DB 생성 //
$Mysql = new Mysql;
// DB 연결 //
$Mysql->Connect();

// Setup 설정 //
require_once "../wp_library/setup.php";

// Paging 클래스 //
require_once "../wp_library/page_class.php";

// Error 함수 //
require_once "../wp_library/check.php";

// Upload 클래스 //
require_once "../wp_library/upload_class.php";

// 토너먼트 대진표정보 클래스  //
require_once "../wp_library/tournament_class.php";

//사이트권한 관리 클래스
class_exists('Site_Auth') || require('../wp_library/Site_Auth_class.php');
$site_auth = new Site_Auth;


// 리스트
$sql = "
    SELECT
        t.*,
        (t.score + t.medal_score) AS total_score
    FROM
        wp_score AS t
    WHERE
        pla_name NOT IN('add_point', 'base') AND t.gam_uid = '15' AND t.clu_code = 'yssad' AND t.spo_code = 'DANCE'
    ORDER BY spo_code ASC, score DESC, rank ASC
";
$result = mysql_query($sql);
while($obj = mysql_fetch_object($result)) {
    dump($obj);
}
?>

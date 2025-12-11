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

/**
 * 데이터 처리
 */
switch ($mode)
{
    /**
     * 다관왕 엑셀 다운로드
     */
    case 'get_winner_xls' :

        // 금메달 두개부터 체크
        $where = 'medal_cnt >= 2';

        // 대회
        if ($s_gam_uid) {
            $where .= " AND t.gam_uid = '{$s_gam_uid}'";
        }

        // 시/군
        if ($clu_code) {
            $where .= " AND t.clu_code = '{$clu_code}'";
        }

        // 종목
        if ($spo_code) {
            $where .= " AND t.spo_code = '{$spo_code}'";
        }

        $sql = "
            SELECT
                t.*,
                t2.name,
                t2.picture_chg
            FROM
                (
                    SELECT
                        id,
                        gam_uid,
                        clu_code,
                        spo_code,
                        -- GROUP_CONCAT(game_code) as game_code,
                        GROUP_CONCAT(spo_sec_code) as spo_sec_code,
                        GROUP_CONCAT(spo_code_detail) as spo_code_detail,
                        GROUP_CONCAT(tro_code) as tro_code,
                        GROUP_CONCAT(tro_level_code) as tro_level_code,
                        GROUP_CONCAT(rank_tot) as rank_tot,
                        GROUP_CONCAT(test_game) as test_game,
                        -- SUM(if (rank_tot = 1, 1, 0)) as medal_cnt
                        COUNT(1) as medal_cnt
                    FROM
                        wp_game_record
                    WHERE
                        id != ''
                        AND rank_tot = 1
                        AND gam_uid = '{$s_gam_uid}'
                    GROUP BY id, gam_uid, clu_code
                ) AS t
                JOIN wp_player_ctrl AS t2 ON t.id = t2.wp_id
            WHERE
                {$where}
            ORDER BY medal_cnt DESC, spo_code ASC
        ";
        $result = mysql_query($sql);
        $total = mysql_num_rows($result);

        $xlsPhotoUrl  = "http://" . $_SERVER['HTTP_HOST'] . "/wp_data_photo/";

        $html_list = '';
        while($list = mysql_fetch_object($result)) {
            $sigun = $wp_club_code[$list->clu_code];
            if ($list->picture_chg) {
                $list->picture_chg = "<img src='" . $xlsPhotoUrl . urlencode($list->picture_chg) . "' width='80'>";
            }

            $sports = $wp_sports_code[$list->spo_code] ? $wp_sports_code[$list->spo_code] : '';
            $spo_code_txt = $wp_sports_code[$list->spo_code];

            // 세부종목
            $sec_code_arr    = @explode(',', $list->spo_sec_code);
            $code_detail_arr = @explode(',', $list->spo_code_detail);
            $tro_code_arr    = @explode(',', $list->tro_code);
            $level_code_arr  = @explode(',', $list->tro_level_code);
            $rank_tot_arr    = @explode(',', $list->rank_tot);
            $test_game_arr   = @explode(',', $list->test_game);
            $game_code_arr   = @explode(',', $list->game_code);

            $sports_detail_txt = '';
            $detail_array = array();
            foreach ((array) $sec_code_arr as $key => $cd) {
                $sec_code_txt   = getSpo_sec_code($list->spo_code, $cd);
                $detail_txt     = $wp_sports_detail_code[$code_detail_arr[$key]];
                $tro_code_txt   = $wp_trouble_code[$tro_code_arr[$key]];
                $level_code_txt = $level_code_arr[$key];
                //$game_code_txt  = $game_code_arr[$key];
                $is_test        = $test_game_arr[$key];

                $rank_txt = $rank_tot_arr[$key];
                if (empty($rank_txt) || $rank_txt <> 1) continue;

                $dArr = array();
                //$dArr[] = $spo_code_txt;
                if ($sec_code_txt && $list->spo_code != 'ATHIETICS')   $dArr[] = $sec_code_txt;
                if ($detail_txt)     $dArr[] = $detail_txt;
                if ($tro_code_txt)   $dArr[] = $tro_code_txt;
                if ($level_code_txt) $dArr[] = $level_code_txt;
                //if ($game_code_txt)  $dArr[] = $game_code_txt;
                if ($is_test)        $dArr[] = "시범";

                $detail_array[] = @implode(' / ', $dArr);
            }
            $sports_detail_txt = @implode('<br>', $detail_array);

            $html_list .= "
            <tr>
                <td height='80'>{$sigun}</td>
                <td>{$list->name}</td>
                <td>{$list->picture_chg}</td>
                <td>" . number_format($list->medal_cnt) . "</td>
                <td>{$sports}</td>
                <td class='text-left'>{$sports_detail_txt}</td>
                <td>" . $wp_games[$s_gam_uid] . "</td>
                <td><button class='btn btn-info btn-xs' data-btn='btn-detail' data-name='{$list->name}' data-id='{$list->id}'>상세정보</button></td>
            </tr>
            ";
        }

        $filename = "다관왕현황_" . date('Ymd') . ".xls";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Content-Description: PHP4 Generated Data");

        echo '
        <table border="1">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col width="100">
            </colgroup>
        	<thead>
        		<tr>
        			<th>시군</th>
        			<th>선수명</th>
        			<th>사진</th>
        			<th>메달수</th>
        			<th>종목</th>
        			<th>세부종목</th>
        			<th>메달획득 일자</th>
                    <th>메뉴</th>
        		</tr>
        	</thead>
        	<tbody>
        		' . $html_list . '
        	</tbody>
        </table>
        ';

        break;

    /**
     * 선수의 메달 획득 상세 Layer 데이터
     */
    case 'get_player_data' :

        if (empty($player_id)) {
            die(json_encode(array(
                'code' => 100,
                'msg' => toUTF8('필수값이 누락 되었습니다.')
            )));
        }

        $sql = "
            SELECT
                *
            FROM
                wp_game_record
            WHERE
                id = '{$player_id}'
                AND gam_uid = '{$s_gam_uid}'
            ORDER BY uid
        ";
        $result = mysql_query($sql);

        $html_list = '';
        while($obj = mysql_fetch_object($result)) {
            $sigun = $wp_club_code[$obj->clu_code];
            $rank = $obj->rank_tot ? $obj->rank_tot . '위' : '';

            $sports = $wp_sports_code[$obj->spo_code] ? $wp_sports_code[$obj->spo_code] : '';
            $sports_detail = $wp_sports_detail_code[$obj->spo_code_detail];

            $gubun = $wp_game_kind[$obj->game_kind];

            $html_list .= "
            <tr>
                <td>{$sigun}</td>
                <td>{$rank}</td>
                <td>{$gubun}</td>
                <td>{$sports}</td>
                <td>{$sports_detail}</td>
            </tr>
            ";
        }

        $html = "
        <table class='table table-bordered text-center' style='margin-bottom:0;'>
            <thead>
                <tr>
                    <th>시/군</th>
                    <th>순위</th>
                    <th>경기구분</th>
                    <th>종목명</th>
                    <th>세부종목</th>
                </tr>
            </thead>
            <tbody>
                {$html_list}
            </tbody>
        </table>
        ";

        echo json_encode(array(
            'code' => 200,
            'msg' => 'Success',
            'html' => toUTF8($html)
        ));
        break;
}

function toUTF8($txt)
{
    return iconv('euc-kr', 'utf-8', $txt);
}
?>

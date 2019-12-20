<?php
//===================================================================================
//[ｼｽﾃﾑ    ]
//[ﾌｧｲﾙ名  ]    incCommona.php
//[処理内容]    共通関数群
//[備考    ]    ｼｽﾃﾑ共通関数群、各機能関数群以外から直接呼び出さない事
//[作成    ]    2019/05/27 E.KINJO    新規作成（ツギラクからコピー）
//[履歴    ]
//===================================================================================

//===============================================================================
//機  能    :ｾｯｼｮﾝﾁｪｯｸ及びｾｯｼｮﾝ破棄処理
//引  数    :ARG1 - ｾｯｼｮﾝﾁｪｯｸ文字列
//戻り値    :true:ｾｯｼｮﾝ有 false:無
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function CheckSession($chkstr)
{

    // ｾｯｼｮﾝ情報の確認
    //if ( session_id() != "" ) {
    if (empty($chkstr)) {
        // ｾｯｼｮﾝ切れの場合

        // ｾｯｼｮﾝ変数を全て解除
        $_SESSION = array();

        // ｾｯｼｮﾝを切断するにはｾｯｼｮﾝｸｯｷｰも削除する。
        // Note: ｾｯｼｮﾝ情報だけでなくｾｯｼｮﾝを破壊する。
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, "/");
        }

        // 最終的にｾｯｼｮﾝを破壊する
        session_destroy();

        return false;
    } else {
        return true;
    }
}

//===============================================================================
//機  能    :ﾛｸﾞ書込関数
//引  数    :ARG1 - ﾕｰｻﾞID
//        :ARG2 - ﾛｸﾞ名
//        :ARG3 - 変更後ｴﾝｺｰﾄﾞ
//        :ARG4 - 変更前ｴﾝｺｰﾄﾞ
//        :ARG5 - ﾛｸﾞﾌｧｲﾙ出力先
//戻り値    :true:正常 false:異常
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function WriteLog($user_id, $logStr, $to_encoding, $from_encoding, $logfile)
{

    // 出力ﾒｯｾｰｼﾞの生成
    // $_SERVER["REMOTE_ADDR"] → IPｱﾄﾞﾚｽ
    $msg = date("Y/m/d H:i:s") .
        ", " . $user_id .
        ", " . $_SERVER["REMOTE_ADDR"] .
        ", " . mb_convert_encoding($logStr, $to_encoding, $from_encoding) . PHP_EOL;

    # ﾛｸﾞﾌｧｲﾙ出力
    $res = @file_put_contents($logfile, $msg, FILE_APPEND);
    if ($res === false) {
        //throw new Exception("ファイルのオープンに失敗しました。");
        return false;
    }

    return true;
}

/**
 * ﾌｧｲﾙの内容を取得する
 *
 * @param string $filepath ﾌｧｲﾙﾊﾟｽ
 * @return void ﾌｧｲﾙの内容（存在しない場合は false を返す）
 */
function GetFileData($filepath)
{

    if (file_exists($filepath)) {
        // ﾌｧｲﾙｵｰﾌﾟﾝ
        $fp = fopen($filepath, "r");
        // ﾌｧｲﾙをﾊﾞｲﾅﾘﾓｰﾄﾞで開く
        $contents = fread($fp, filesize($filepath));
        // ﾌｧｲﾙｸﾛｰｽﾞ
        fclose($fp);
    } else {
        return false;
    }
    return $contents;
}

/**
 * 特殊文字を HTML エンティティに変換する(短縮関数)
 *
 * @param string $str
 * @return string
 */
function SetHtmlEncode($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

//===============================================================================
//機  能    :HTMLｴﾝｺｰﾄﾞ兼空白処理
//引  数    :ARG1 - 文字列
//        :ARG2 - 空白時の置換文字列
//戻り値    :変換/置換後の文字列
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function SetReplaceNull($str, $rpl)
{
    if (strlen(trim($str)) == 0) {
        $ret = $rpl;
    } else {
        $ret = SetHtmlEncode($str);
    }
    return $ret;
}

/**
 * HTMLｺﾒﾝﾄをすべて指定の値に変換する
 *
 * @param array $ary 変換対象のキーと値の連想配列
 * @param string $html HTML構成文字列
 * @return string HTML構成文字列
 */
function SetKeyReplaceHtml($ary, $html)
{
    foreach ($ary as $key => $value) {
        $html = preg_replace("/<!--$key-->/", $value, $html);
    }
    return $html;
}

/**
 * ページ数を取得する(ページング機能)
 *
 * @param integer $totalDataCnt 対象データの件数
 * @param integer $countPerPage 1ページあたりのデータ件数
 * @return integer ページ数
 */
function GetPageCount($totalDataCnt, $countPerPage)
{
    return ceil($totalDataCnt / $countPerPage);
}
/**
 * ページ内のデータの開始行番号を取得する
 *
 * @param integer $countPerPage 1ページあたりの最大データ表示件数
 * @param integer $curPageNo 現在のページ番号
 * @return integer
 */
function GetPageDataStart($countPerPage, $curPageNo, $pageCount)
{
    return $countPerPage * ($curPageNo - 1 ) + 1;
}

/**
 * ページ内のデータの終了行番号を取得する
 *
 * @param integer $dataCount 取得したデータ件数
 * @param integer $countPerPage 1ページあたりの最大データ表示件数
 * @param integer $pageDataStart ページ内のデータの開始行番号
 * @return integer
 */
function GetPageDataEnd($dataCount, $countPerPage, $pageDataStart)
{
    if ($pageDataStart + $countPerPage - 1 < $dataCount) {
        return $pageDataStart + $countPerPage - 1;
    } else {
        return $dataCount;
    }
}

/**
 * ページング要素の最初（左端）の番号を取得する
 *
 * @param integer $curPageNo 現在のページ番号
 * @param integer $pageCount 取得データに対するページ数
 * @param integer $pagingDispCount 画面に表示するページング要素数
 * @return integer
 */
function GetPagingStartNo($curPageNo, $pageCount, $pagingDispCount)
{
    $pagingStartNo = 1;
    $provisionalStartNo = 1;
    $provisionalEndNo = $pageCount;

    if ($pageCount < $pagingDispCount) {
        $pagingStartNo = 1;
    } else {
        $provisionalStartNo = $curPageNo - floor(($pagingDispCount - 1) / 2);
        $provisionalEndNo = $provisionalStartNo + $pagingDispCount - 1;

        if ($provisionalStartNo < 1) {
            $pagingStartNo = 1;
        } elseif ($provisionalEndNo > $pageCount) {
            $pagingStartNo = $pageCount - $pagingDispCount + 1;
        } else {
            $pagingStartNo = $provisionalStartNo;
        }
    }
    return $pagingStartNo;
}

/**
 * ページング要素の最後（右端）の番号を取得する
 *
 * @param integer $pagingStartNo ページング要素の最初（左端）の番号
 * @param integer $pageCount 取得データに対するページ数
 * @param integer $pagingDispCount 画面に表示するページング要素数
 * @return integer
 */
function GetPagingEndNo($pagingStartNo, $pageCount, $pagingDispCount)
{
    if ($pagingStartNo + $pagingDispCount - 1 > $pageCount) {
        return $pageCount;
    } else {
        return $pagingStartNo + $pagingDispCount - 1;
    }
}

/**
 * SQL(Oracle)の引数をエスケープする。
 * プリペアドステートメントが利用できない場合にのみ使用する。
 *
 * @param void $sqlArgument SQLｸｴﾘの引数
 * @return void
 */
function EscapeSqlOracle($sqlArgument)
{
    if (is_int($sqlArgument) || is_float($sqlArgument)) {
        return $sqlArgument;
    }
    $sqlArgument = str_replace("'", "''", $sqlArgument);
    // TODO: 改行コードのエスケープも必要
    return "'" . $sqlArgument . "'";
}

//===============================================================================
//機  能    :日付文字列の整形
//引  数    :ARG1 - 日付文字列
//        :ARG2 - 整形ﾀｲﾌﾟ
//戻り値    :整形後の日付文字列
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function SetFormatDate($ymd, $mode)
{

    if (strlen($ymd) != 0) {
        switch ($mode) {
            case 1:
                // YYYYMMDD→YYYY/MM/DD
                $ret = substr($ymd, 0, 4) . "/" . substr($ymd, 4, 2) . "/" . substr($ymd, 6, 2);
                break;
            case 2:
                // YYYYMMDD→YY/MM/DD
                $ret = substr($ymd, 2, 2) . "/" . substr($ymd, 4, 2) . "/" . substr($ymd, 6, 2);
                break;
            case 3:
                // YYYY/MM/DD→YYYYMMDD
                // YY/MM/DD→YYMMDD
                $ret = str_replace("/", "", $ymd);
                break;
            case 4:
                // YY/MM/DD→YYYYMMDD
                $ret = '20' . str_replace("/", "", $ymd);
                break;
            case 5:
                // YYYYMMDD→YYYY/MM
                $ret = substr($ymd, 0, 4) . "/" . substr($ymd, 4, 2);
                break;
            case 6:
                // YYMMDD→YYYY/MM/DD
                $ret = '20' . substr($ymd, 0, 2) . "/" . substr($ymd, 2, 2) . "/" . substr($ymd, 4, 2);
                break;
            case 7:
                // YYYY/MM/DD→元号略称YY年M月D日
            case 8:
                // YYYY/MM/DD→元号略称YY年M月D日(曜日)
                list($y, $m, $d) = explode("/", $ymd);
                $ymd = $y . $m . $d;
                if ($ymd <= "19120729") {
                    $gg = "M";
                    $yy = $y - 1867;
                } elseif ($ymd >= "19120730" && $ymd <= "19261224") {
                    $gg = "T";
                    $yy = $y - 1911;
                } elseif ($ymd >= "19261225" && $ymd <= "19890107") {
                    $gg = "S";
                    $yy = $y - 1925;
                } elseif ($ymd >= "19890108") {
                    $gg = "H";
                    $yy = $y - 1988;
                }
                if ($mode == 6) {
                    $ret = $gg . $yy . "年" . ltrim($m, "0") . "月" . ltrim($d, "0") . "日";
                    break;
                }
                // 7の場合のみ
                //曜日配列を準備
                $weekjp_array = array('日', '月', '火', '水', '木', '金', '土');
                //タイムスタンプを取得
                $timestamp = mktime(0, 0, 0, ltrim($m, "0"), ltrim($d, "0"), $y);
                //曜日番号を取得
                $weekno = date('w', $timestamp);
                //日本語曜日を取得
                $weekjp = $weekjp_array[$weekno];

                $ret = $gg . $yy . "年" . ltrim($m, "0") . "月" . ltrim($d, "0") . "日(" . $weekjp . ")";
                break;

            case 9:
                // YYMMDD→YYYY年MM月DD日
                $ret = substr($ymd, 0, 4) . "年" . substr($ymd, 4, 2) . "月" . substr($ymd, 6, 2) . '日';
                break;

            default:
                $ret = $ymd;
                break;
        }
    } else {
        $ret = $ymd;
    }
    return $ret;
}

//===============================================================================
//機  能    :時間の整形
//引  数    :ARG1 - 時間文字列(hhmmss or hh:mm:ss)
//        :ARG2 - 整形ﾀｲﾌﾟ
//戻り値    :整形後の時間文字列
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function SetFormatTime($hms, $mode)
{

    if (strlen($hms) != 0) {
        switch ($mode) {
            case 1:    // hhmmss→hh:mm:ss
                $ret = substr($hms, 0, 2) . ":" . substr($hms, 2, 2) . ":" . substr($hms, 4, 2);
                break;
            case 2:    // hhmmss→hh:mm
                $ret = substr($hms, 0, 2) . ":" . substr($hms, 2, 2);
                break;
            case 3:    // hhmmss→mm:ss
                $ret = substr($hms, 2, 2) . ":" . substr($hms, 4, 2);
                break;
            case 4: // hhmm or mmss → hh:mm or mm:ss
                $ret = substr($hms, 0, 2) . ":" . substr($hms, 2, 2);
                break;
            case 5:    // hh:mm:dd等の区切り文字除外
                $ret = str_replace(":", "", $hms);
                break;
            default:
                $ret = $hms;
                break;
        }
    } else {
        $ret = $hms;
    }
    return $ret;
}

//===============================================================================
//機  能    :日付文字列の整形
//引  数    :ARG1 - 日付文字列(YYYYMMDD)
//        :ARG2 - 整形ﾀｲﾌﾟ
//戻り値    :整形後の日付文字列
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：削除予定
//===============================================================================
function SetFormatTime_bk($hms)
{

    if (strlen($hms) != 0) {
        $ret = substr($hms, 0, 2) . ":" . substr($hms, 2, 2) . ":" . substr($hms, 4, 2);
    } else {
        $ret = $hms;
    }
    return $ret;
}

/**
 * 右から数えて指定した文字数分の文字列を取得する
 *
 * @param string $str 元の文字列
 * @param integer $n 文字数
 * @param string $encode ｴﾝｺｰﾄﾞ
 * @return void
 */
function Right($str, $n, $encode)
{
    return mb_substr($str, ($n) * (-1), $n, $encode);
}

/**
 * 左から数えて指定した文字数分の文字列を取得する
 *
 * @param string $str 元の文字列
 * @param integer $n 文字数
 * @param string $encode ｴﾝｺｰﾄﾞ
 * @return void
 */
function Left($str, $n, $encode)
{
    //指定文字ｺｰﾄﾞで、left関数。$strの左から$n文字取得
    return mb_substr($str, 0, $n, $encode);
}

//===============================================================================
//機  能    :連想配列判断
//引  数    :arg1 - 変数
//戻り値    :true:連想配列／false:連想配列以外
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================

/**
 * 変数が連想配列かどうかを判定する
 *
 * @param mixed $variable 判定対象の変数
 * @return boolean True：連想配列である
 */
function is_hash($variable)
{
    $i = 0;
    if (is_array($variable)) {
        foreach ($variable as $key => $value) {
            if ($key !== $i++) return true;
        }
    }
    return false;
}

//===============================================================================
//機  能    :禁則文字チェック
//引  数    :arg1 - 文字列
//        :arg2 - チェック区分(定数)
//戻り値    :true:禁則文字有／false:禁則文字無
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function isProhibitChar($string, $check = 0)
{

    // チェック区分デフォルト値
    if (empty($check)) {
        $check = (CHECK_PROHIBITION | CHECK_SURROGATE_PAIR | CHECK_NOT_SJIS);
    }

    // 禁則文字チェック
    switch (true) {
        case (($check & CHECK_PROHIBITION) && preg_match("/([\<\>\'\"\&\%]+)/", $string) !== 0):        // 禁止文字
        case (($check & CHECK_SURROGATE_PAIR) && !mb_check_encoding($string, ENCODE_UTF8)):        // サロゲートペア
        case (($check & CHECK_NOT_SJIS) && !mb_detect_encoding($string, ENCODE_SJIS_WIN)):        // SJISコード以外
            return true;
        default:
            return false;
    }
}

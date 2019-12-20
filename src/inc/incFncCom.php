<?php
//===================================================================================
//[ｼｽﾃﾑ    ]    -
//[ﾌｧｲﾙ名  ]    incFncCom.php
//[処理内容]    Tugiraku用_共通関数群
//[作成    ]    2019/05/27 E.KINJO    新規作成（ツギラクからコピー）
//[履歴    ]
//===================================================================================

/**
 * エラー状態をクリアする
 *
 * @return void
 */
function fncCom_ClearErr()
{

    $_SESSION[S_ERR_FLG] = false;
    $_SESSION[S_ERR_MSG] = '';
}

/**
 * エラー内容をセッションに保持させる
 *
 * @param string $errmsg ｴﾗｰﾒｯｾｰｼﾞ
 * @return void
 */
function fncCom_SetErr($errmsg)
{

    $_SESSION[S_ERR_FLG] = true;
    $_SESSION[S_ERR_MSG] = $errmsg;
}

//===============================================================================
//機  能    :ﾛｸﾞｲﾝ画面遷移処理
//引  数    :なし
//戻り値    :なし
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_LoginRedirect()
{

    // ｾｯｼｮﾝ情報の破棄
    session_destroy();
    // ﾛｸﾞｲﾝ画面に遷移
    header('Location: ' . SYS_DIR . 'SessionErr.php');
    exit();
}

//===============================================================================
//機  能    :ｾｯｼｮﾝ確認処理
//引  数    :なし
//戻り値    :なし
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_CheckSession()
{

    $str = $_SESSION[S_LOGIN_US_CD];
    // ｾｯｼｮﾝﾁｪｯｸ
    if (!CheckSession($str)) {
        // ｾｯｼｮﾝ情報が切れていた場合
        fncCom_LoginRedirect();
        exit();
    }
}


/**
 * DB接続情報を取得してセッション変数に保持させる
 *
 * @return void
 */
function fncCom_GetDbInfo()
{

    $dbinfo = new DBInfo();
    // 接続情報の取得
    $dbinfo->GetDBInfo(INFO_FILE);

    if ($dbinfo->CheckFile()) {
        // DB接続情報をｾｯｼｮﾝに保存
        $_SESSION[S_DB_UID]  = trim($dbinfo->GetUid());
        $_SESSION[S_DB_PWD]  = trim($dbinfo->GetPwd());
        $_SESSION[S_DB_DSN]  = trim($dbinfo->GetDsn());
        $_SESSION[S_ERR_FLG] = false;
        $_SESSION[S_ERR_MSG] = '';
    } else {
        // DB接続情報が見つからず
        $_SESSION[S_DB_UID]  = '';
        $_SESSION[S_DB_PWD]  = '';
        $_SESSION[S_DB_DSN]  = '';
        $_SESSION[S_ERR_FLG] = true;
        $_SESSION[S_ERR_MSG] = INFO_FILE.' is Nothing. DB接続情報が取得出来ませんでした。';
    }
    unset($dbinfo);

    if ($_SESSION[S_ERR_FLG]) {
        echo $_SESSION[S_ERR_MSG];
        exit();
    }
}

//===============================================================================
//機  能    :ﾛｸﾞ用ﾊﾟﾗﾒｰﾀ設定
//引  数    :ARG1 - 案件区分
//        :ARG2 - 画面ID
//戻り値    :なし
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_SetLogPara($type, $id)
{

    $_SESSION[S_LOG_TYPE] = $type;    // 案件区分
    $_SESSION[S_LOG_DISP] = $id;    // 画面ID

}

/**
 * ログファイルに書き込む
 *
 * @param string $logStr 書き込む内容
 * @return void
 */
function fncCom_WriteLog($logStr)
{
    if ($_SESSION[LOG_FILE_FLG]) {
        if (!WriteLog($_SESSION[S_LOGIN_US_ID], $logStr, ENCODE_UTF8, ENCODE_DEF, LOG_FILE)) {
            fncCom_SetErr('ファイルのオープンに失敗しました。');
        }
    }
}

/**
 * ﾌｧｲﾙ内の文字列を取得する
 *
 * @param string $filepath ﾌｧｲﾙﾊﾟｽ
 * @return string
 */
function fncCom_GetFileData($filepath)
{
    // ﾌｧｲﾙ取得
    $contents = GetFileData($filepath);

    if (!$contents) {
        // ｴﾗｰ処理
        fncCom_SetErr('ファイルのオープンに失敗しました。');
        $contents = '';
    }
    $contents = fncCom_cacheBusting($contents);

    return $contents;
}

//===============================================================================
//機  能    :空文字処理
//引  数    :ARG1 - 文字列
//戻り値    :置換後の文字列
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_SetReplaceNull($str)
{
    // 空文字処理
    return SetReplaceNull($str, HTML_SPACE);
}

/**
 * HTMLｺﾒﾝﾄをすべて指定の値に変換する
 *
 * @param array $ary 変換対象のキーと値の連想配列
 * @param string $html HTML構成文字列
 * @return string HTML構成文字列
 */
function fncCom_SetKeyReplaceHtml($ary, $html)
{
    // HTMLへの置換
    return SetKeyReplaceHtml($ary, $html);
}

/**
 * HTMLコメントを指定値に変換する
 *
 * @param array $ary ﾃﾞｰﾀ配列
 * @param string $kwd ｷｰﾜｰﾄﾞ
 * @param string $html HTML
 * @return string 置換後のHTML
 */
function fncCom_SetListReplaceHtml($ary, $kwd, $html)
{

    $list_html = array();
    $work_html = array_map('rtrim', explode("<!--$kwd-->", $html));

    for ($i = 0; $i < count($ary); $i++) {
        $list_html[$i] = fncCom_SetKeyReplaceHtml($ary[$i], $work_html[1]);
    }

    return $work_html[0] . join('', $list_html) . $work_html[2];
}

/**
 * ページ数を取得する(ページング機能)
 *
 * @param integer $totalDataCnt 対象データの件数
 * @param integer $countPerPage 1ページあたりのデータ件数
 * @return integer ページ数
 */
function fncCom_GetPageCount($totalDataCnt, $countPerPage)
{
    return GetPageCount($totalDataCnt, $countPerPage);
}

/**
 * ページ内のデータの開始行番号を取得する
 *
 * @param integer $countPerPage 1ページあたりの最大データ表示件数
 * @param integer $curPageNo 現在のページ番号
 * @return integer
 */
function fncCom_GetPageDataStart($countPerPage, $curPageNo, $pageCount)
{
    return GetPageDataStart($countPerPage, $curPageNo, $pageCount);
}

/**
 * ページ内のデータの終了行番号を取得する
 *
 * @param integer $dataCount 取得したデータ件数
 * @param integer $countPerPage 1ページあたりの最大データ表示件数
 * @param integer $pageDataStart ページ内のデータの開始行番号
 * @return integer
 */
function fncCom_GetPageDataEnd($dataCount, $countPerPage, $pageDataStart)
{
    return GetPageDataEnd($dataCount, $countPerPage, $pageDataStart);
}

/**
 * ページング要素の最初（左端）の番号を取得する
 *
 * @param integer $curPageNo 現在のページ番号
 * @param integer $pageCount 取得データに対するページ数
 * @param integer $pagingDispCount 画面に表示するページング要素数
 * @return integer
 */
function fncCom_GetPagingStartNo($curPageNo, $pageCount, $pagingDispCount)
{
    return GetPagingStartNo($curPageNo, $pageCount, $pagingDispCount);
}

/**
 * ページング要素の最後（右端）の番号を取得する
 *
 * @param integer $pagingStartNo ページング要素の最初（左端）の番号
 * @param integer $pageCount 取得データに対するページ数
 * @param integer $pagingDispCount 画面に表示するページング要素数
 * @return integer
 */
function fncCom_GetPagingEndNo($pagingStartNo, $pageCount, $pagingDispCount)
{
    return GetPagingEndNo($pagingStartNo, $pageCount, $pagingDispCount);
}

/**
 * ページンクコンポーネントを生成する
 *
 * @param string $contents HTML構成文字列
 * @param array $datalist 抽出結果のﾚｺｰﾄﾞｾｯﾄ
 * @param integer $curPageNo 現在のページ番号
 * @param integer $countPerPage 1ページあたりの表示件数
 * @return string HTML構成文字列
 */
function funcCom_SetPagingContents($contents, $datalist, $curPageNo, $countPerPage = 0)
{
    $aryPagingHTML = explode('<!--PAGING_AREA-->', $contents);
    $strPagingHTML = $aryPagingHTML[1];

    if ($countPerPage === 0) {
        $countPerPage = PAGE_DATA_MAX_COUNT;
    }

    if ($datalist) {
        // ページング要素生成用データの準備
        $dataCount = count($datalist);
        $pageCount = fncCom_GetPageCount($dataCount, $countPerPage);
        if ($curPageNo < 1) {
            $curPageNo = 1;
        } elseif ($curPageNo > $pageCount) {
            $curPageNo = $pageCount;
        }
        // 表示中データ開始行番号、終了行番号
        $pageDataStart = fncCom_GetPageDataStart($countPerPage, $curPageNo, $pageCount);
        $pageDataEnd = fncCom_GetPageDataEnd($dataCount, $countPerPage, $pageDataStart);
        // ページング開始番号、終了
        $pagingStartNo = fncCom_GetPagingStartNo($curPageNo, $pageCount, PAGE_DISP_COUNT);
        $pagingEndNo = fncCom_GetPagingEndNo($pagingStartNo, $pageCount, PAGE_DISP_COUNT);

        $isVisibleBefore = ($curPageNo > 1);
        $isVisibleAfter = ($curPageNo < $pageCount);

        // HTML生成
        $strPagingHTML = str_replace('<!--DATA_COUNT-->', $dataCount, $strPagingHTML);
        $strPagingHTML = str_replace('<!--PAGE_DATA_START-->', $pageDataStart, $strPagingHTML);
        $strPagingHTML = str_replace('<!--PAGE_DATA_END-->', $pageDataEnd, $strPagingHTML);

        // ページング要素の分割
        $aryPagingList = explode('<!--PAGING_LIST_AREA-->', $strPagingHTML);

        $aryPagingFirst = explode('<!--PAGING_FIRST_AREA-->', $aryPagingList[1]);
        $strPagingFirst = $aryPagingFirst[1];
        $aryPagingBefore = explode('<!--PAGING_BEFORE_AREA-->', $aryPagingList[1]);
        $strPagingBefore = $aryPagingBefore[1];
        $aryPagingLink = explode('<!--PAGING_LINK_AREA-->', $aryPagingList[1]);
        $strPagingLink = $aryPagingLink[1];
        $aryPagingCurrent = explode('<!--PAGING_CURPAGE_AREA-->', $aryPagingList[1]);
        $strPagingCurrent = $aryPagingCurrent[1];
        $aryPagingAfter = explode('<!--PAGING_AFTER_AREA-->', $aryPagingList[1]);
        $strPagingAfter = $aryPagingAfter[1];
        $aryPagingLast = explode('<!--PAGING_LAST_AREA-->', $aryPagingList[1]);
        $strPagingLast = $aryPagingLast[1];



        // 最初へ、前へ、次へ、最後への表示制御
        if ($pagingStartNo != 1) {
            $strPagingFirst = str_replace('<!--PAGING_CSS-->', '', $strPagingFirst);
        } else {
            $strPagingFirst = str_replace('<!--PAGING_CSS-->', 'transparent', $strPagingFirst);
        }
        if ($isVisibleBefore) {
            $strPagingBefore = str_replace('<!--PAGING_CSS-->', '', $strPagingBefore);
        } else {
            $strPagingBefore = str_replace('<!--PAGING_CSS-->', 'transparent', $strPagingBefore);
        }
        if ($isVisibleAfter) {
            $strPagingAfter = str_replace('<!--PAGING_CSS-->', '', $strPagingAfter);
        } else {
            $strPagingAfter = str_replace('<!--PAGING_CSS-->', 'transparent', $strPagingAfter);
        }
        if ($pagingEndNo != $pageCount) {
            $strPagingLast = str_replace('<!--PAGING_CSS-->', '', $strPagingLast);
        } else {
            $strPagingLast = str_replace('<!--PAGING_CSS-->', 'transparent', $strPagingLast);
        }

        $strPagingList = '';
        for ($i = $pagingStartNo; $i <= $pagingEndNo; $i++) {

            if ($i == $pagingStartNo) {
                // 最初へ、前へリンクの生成
                $strPagingList .= str_replace('<!--PAGE_NO-->', 1, $strPagingFirst);
                $strPagingList .= str_replace('<!--PAGE_NO-->', $curPageNo - 1, $strPagingBefore);
            }

            if ($i == $curPageNo) {
                // 現在ページ
                $strPagingList .= str_replace('<!--PAGE_NO-->', $i, $strPagingCurrent);
            } else {
                // ページングリンク
                $strPagingList .= str_replace('<!--PAGE_NO-->', $i, $strPagingLink);
            }

            // 次へ、最後へリンクの生成
            if ($isVisibleAfter && $i == $pagingEndNo) {
                $strPagingList .= str_replace('<!--PAGE_NO-->', $curPageNo + 1, $strPagingAfter);
                $strPagingList .= str_replace('<!--PAGE_NO-->', $pageCount, $strPagingLast);
            }
        }
        $strPagingHTML = $aryPagingList[0] . $strPagingList . $aryPagingList[2];
    } else {
        $strPagingHTML = '';
    }

    $contents = $aryPagingHTML[0] . $strPagingHTML . $aryPagingHTML[2];
    return $contents;
}

/**
 * ページ内に表示するデータセットを取得する
 *
 * @param array $datalist 対象のデータセット（全レコード）
 * @param integer $curPageNo 現在のページ番号
 * @param integer $countPerPage 1ページあたりの最大表示件数
 * @return array
 */
function fncCom_GetPageData($datalist, $curPageNo, $countPerPage = 0)
{
    $pageData = array();
    $dataCount = 0;
    if ($datalist) {
        $dataCount = count($datalist);
    }

    if ($countPerPage === 0) {
        $countPerPage = PAGE_DATA_MAX_COUNT;
    }

    $pageCount = fncCom_GetPageCount($dataCount, $countPerPage);
    if ($curPageNo < 1) {
        $curPageNo = 1;
    } elseif ($curPageNo > $pageCount) {
        $curPageNo = $pageCount;
    }
    $pageDataStart = fncCom_GetPageDataStart($countPerPage, $curPageNo, $pageCount);
    $pageDataEnd = fncCom_GetPageDataEnd($dataCount, $countPerPage, $pageDataStart);

    for ($i = $pageDataStart; $i <= $pageDataEnd; $i++) {
        $pageData[] = $datalist[$i - 1];
    }

    return $pageData;
}

//===============================================================================
//機  能    :日付文字列の整形
//引  数    :ARG1 - 日付文字列(YYYYMMDD)
//        :ARG2 - 整形ﾀｲﾌﾟ
//戻り値    :整形後の日付文字列
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_SetFormatDate($ymd, $mode)
{
    // 日付文字列整形処理
    return SetFormatDate($ymd, $mode);
}

//===============================================================================
//機  能    :時間文字列の整形
//引  数    :ARG1 - 時間文字列
//        :ARG2 - 整形ﾀｲﾌﾟ
//戻り値    :整形後の時間文字列
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_SetFormatTime($hms, $mode)
{
    // 時間文字列整形処理
    return SetFormatTime($hms, $mode);
}

/**
 * 右から数えて指定した文字数分の文字列を取得する
 *
 * @param string $str 元の文字列
 * @param integer $n 文字数
 * @return string
 */
function fncCom_Right($str, $n)
{
    // Right関数処理
    return Right($str, $n, ENCODE_DEF);
}

/**
 * 左から数えて指定した文字数分の文字列を取得する
 *
 * @param string $str 元の文字列
 * @param integer $n 文字数
 * @return string
 */
function fncCom_Left($str, $n)
{
    // Left関数処理
    return Left($str, $n, ENCODE_DEF);
}

//===============================================================================
//機  能    :未定義変数のﾊﾟﾗﾒｰﾀ設定
//引  数    :ARG1 -
//        :ARG2 - 置換ﾃﾞｰﾀ
//戻り値    :整形後の日付文字列
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_SetIssetVal($key, $default = '')
{

    if (isset($key)) {
        return $key;
    } else {
        return $default;
    }
}

//===============================================================================
//機  能    :ﾍｯﾀﾞ/ﾀﾌﾞ部設定関数
//引  数    :ARG1 - ﾏｽﾀﾀｲﾄﾙ
//        :ARG2 - 表示ﾌﾗｸﾞ(True:表示 False:非表示)
//戻り値    :表示用配列
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_SetMstDispCom($name, $file, $flg)
{

    $mst_com = array();

    // ﾏｽﾀ共通部分
    $mst_com['LOGIN_USER_ID'] = $_SESSION[S_LOGIN_US_CD];
    $mst_com['LOGIN_USER_NM'] = $_SESSION[S_LOGIN_US_FA];
    $mst_com['MST_NAME']      = $name;
    $mst_com['MST_FILE']      = $file;

    if ($flg) {
        $mst_com['MST_LIST_S']    = '';
        $mst_com['MST_LIST_E']    = '';
    } else {
        $mst_com['MST_LIST_S']    = '<!--';
        $mst_com['MST_LIST_E']    = '-->';
    }

    return $mst_com;
}

//===============================================================================
//機  能    :日時整形関数
//引  数    :ARG1 - ﾊﾟﾗﾒｰﾀ
//        :ARG2 - 日付表示ﾓｰﾄﾞ
//        :ARG3 - 時間表示ﾓｰﾄﾞ（追加)
//戻り値    :整形後ﾊﾟﾗﾒｰﾀ
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_SetFormatDateTime($val, $mode, $time_mode)
{

    if (strlen($val) != 0) {
        $ret = SetFormatDate(substr($val, 0, 8), $mode) . ' ' . SetFormatTime(substr($val, 8, 6), $time_mode);
    } else {
        $ret = $val;
    }
    return $ret;
}

//===============================================================================
// コンボボックス生成関連
//===============================================================================

/**
 * ｺﾝﾎﾞﾎﾞｯｸｽを生成する(OPTION部)
 *
 * @param array $ary 表示用配列(1次:ﾃﾞｰﾀｶｳﾝﾄ、2次:ｺｰﾄﾞ/表示文字列)
 * @param void $sel_val 選択値
 * @param boolean $def_flg 空白表示ﾌﾗｸﾞ(初期値:false)
 * @param array $def_ary 空白時の表示配列
 * @param boolean $group_flg colgroupを使用するかどうか(初期値:false)
 * @return string HTML(OPTION部)
 */
function fncCom_SetCombBox($ary, $sel_val, $def_flg = false, $def_ary = null, $group_flg = false)
{
    if (!isset($def_flg)) {
        $def_flg = false;
    }
    if (!isset($def_ary)) {
        $def_ary = array();
    }
    if (!isset($group_flg)) {
        $group_flg = false;
    }

    $opt_html = '';
    // 空白時処理
    if ($def_flg) {
        $opt_html .= '<option value="'. $def_ary['CODE'] . '" ';
        if ($sel_val == $def_ary['CODE'] && $def_ary['CODE'] !== '') {
            $opt_html .= HTML_SELECTED;
        }
        $opt_html .= '>' . $def_ary['NAME'] . '</option>';
    }

    $code = '';
    // ｺﾝﾎﾞﾘｽﾄ生成処理
    for ($i = 0; $i < count($ary); $i++) {
        // if ($group_flg && ($code != $ary[$i]['CODE'])) {
        //     $opt_html .= '<optgroup label="' . $ary[$i]['CODE'] . '">';
        // }
        $opt_html .= '<option value="'. $ary[$i]['CODE'] . '" ';
        if ($sel_val == $ary[$i]['CODE']) {
            $opt_html .= HTML_SELECTED;
        }
        $opt_html .= '>' . $ary[$i]['NAME'] . '</option>';
        // if ($group_flg && ($code != $ary[$i]['CODE'])) {
        //     $opt_html .= '</optgroup>';
        //     $code = $ary[$i]['CODE'];
        // }
    }

    return $opt_html;
}

/**
 * DBからデータリストを取得する（プリペアドステートメントを使わない場合）
 *
 * @param string $sql SQLｸｴﾘ
 * @return array データリスト
 */
function fncCom_GetDataList($sql)
{

    $datalist = array();
    $obj = new DataObject();

    // DB接続
    $obj->DBOpen();
    if ($obj->GetErrFlg()) {
        // ｴﾗｰ処理
        fncCom_SetErr($obj->errMessage);
        echo $_SESSION[S_ERR_MSG];
        exit();
    }

    // SQL実行
    $obj->OpenRecordSet($sql, array());
    if ($obj->GetErrFlg()) {
        // ｴﾗｰ処理
        fncCom_SetErr($obj->errMessage);
        echo $_SESSION[S_ERR_MSG];
    } else {
        // ﾃﾞｰﾀ取得
        $datalist = $obj->GetRecordSet();
    }

    // DB切断
    $obj->DBClose();
    unset($obj);

    return $datalist;
}

/**
 * SQL実行関数
 *
 * @param string $sql SQLｸｴﾘ
 * @return void
 */
function fncCom_ExecuteSQL($sql)
{

    $obj = new DataObject();

    // DB接続
    $obj->DBOpen();
    if ($obj->GetErrFlg()) {
        // ｴﾗｰ処理
        fncCom_SetErr($obj->errMessage);
        echo $_SESSION[S_ERR_MSG];
        exit();
    }

    // SQL実行
    $res = $obj->ExecuteNoAuto($sql);
    if ($obj->GetErrFlg()) {
        // ﾛｰﾙﾊﾞｯｸ処理
        $obj->RollBack();
        fncCom_SetErr($obj->errMessage);
        echo $_SESSION[S_ERR_MSG];
    } else {
        // ｺﾐｯﾄ処理
        $obj->Commit();
    }

    // DB切断
    $obj->DBClose();
    unset($obj);
}

//===============================================================================
//機  能    :SQL実行関数
//引  数    :SQL
//戻り値    :
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_GetRecordSet($sql)
{

    $data_ary = array();

    // DB接続
    $obj = new DataObject();
    $obj->DBOpen();

    if ($obj->GetErrFlg()) {
        // ｴﾗｰ処理
        $_SESSION[S_ERR_FLG] = true;
        $_SESSION[S_ERR_MSG] = $obj->errMessage;
        echo $_SESSION[S_ERR_MSG];
    }

    //echo $sql.NEXT_LINE;
    $obj->OpenRecordSet($sql, array());
    if ($obj->GetErrFlg()) {
        // ｴﾗｰ処理
        $_SESSION[S_ERR_FLG] = true;
        $_SESSION[S_ERR_MSG] = $obj->errMessage;
        echo $_SESSION[S_ERR_MSG];
    } else {
        // ﾃﾞｰﾀ取得
        $data_ary = $obj->GetRecordSet();
    }

    // DB切断
    $obj->DBClose();
    unset($obj);

    return $data_ary;
}

//===============================================================================
//機  能    :ﾌｧｲﾙ名取得
//引  数    :ARG1 - ﾌｧｲﾙﾊﾟｽ
//戻り値    :ﾌｧｲﾙ名
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_GetFileNm($path)
{

    if (empty($path)) {
        return $path;
    }
    $dirlist = explode('/', $path);
    $dirlist = explode('.', $dirlist[count($dirlist) - 1]);
    $nm = '';
    for ($i = 0; $i < count($dirlist) - 1; $i++) {
        $nm .= $dirlist[$i];
    }
    return $nm;
}

//===============================================================================
//機  能    :CSVファイルエクスポート関数
//引  数    :arg1 - ヘッダー用配列(テーブル項目名 => 表示タイトル）
//        :arg2 - ファイル出力用配列
//        :arg3 - CSVファイル名
//戻り値    :なし
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_ExportCSV($header_list, $datalist, $file_name, $rep_flg = false)
{

    // CSVファイル名の設定
    $csv_file = $file_name;

    // CSVデータの初期化
    $csv_data = '';

    //ヘッダー部
    foreach ($header_list as $key => $val) {
        $csv_data .= $val . ',';
    }
    $csv_data = mb_convert_encoding($csv_data, ENCODE_DEF);
    $csv_data .= "\r\n";

    //データ部
    for ($i = 0; $i < count($datalist); $i++) {
        foreach ($header_list as $key2 => $val2) {
            foreach ($datalist[$i] as $key1 => $val1) {
                if ($rep_flg) {
                    $datalist[$i][$key1] = str_replace("\r\n", '', $val1);
                }
                //データ内ダブルクォーテーション2重化
                $datalist[$i][$key1] = str_replace('"', '""', $val1);
                //データ内改行コード削除
                $datalist[$i][$key1] = str_replace("\r", "", $val1);
                $datalist[$i][$key1] = str_replace("\r[^\n]", "", $val1);
                $datalist[$i][$key1] = str_replace(HTML_CRLF, "", $val1);
                $datalist[$i][$key1] = str_replace(HTML_ENTER, "", $val1);
                if ($key1 == $key2) {
                    $work = mb_convert_encoding($val1, ENCODE_DEF);
                    $csv_data .= '"' . $work . '"' . ",";
                }
            }
        }
        //$csv_data .= "\n";
        $csv_data .= "\r\n";
    }
    //$csv_data .= "\n";

    // MIMEタイプの設定
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=test.csv");

    // ファイル名の表示
    // header("Content-Disposition: attachment; filename=$csv_file");
    // header("Cache-Control: public");
    // header("Pragma: public");

    // データの出力
    return ($csv_data);
}

/**
 * CSVファイル用配列取得関数
 *
 * @param string $aryHeaderList ヘッダー用配列(テーブル項目名 => 表示タイトル）
 * @param array $datalist ファイル出力用配列
 * @param boolean $isSjis   //true時、UTF-8→SJIS変換
 * @return void
 */
function fncCom_getArrayForCSV($aryHeaderList, $datalist, $isSjis = false)
{
    $aryReturn = array();

    $intListCount = count($datalist);

    for ($i = 0; $i < $intListCount; $i++) {
        foreach ($aryHeaderList as $key2 => $val2) {
            $aryReturn[0][$val2] = $val2;
            foreach ($datalist[$i] as $key1 => $val1) {
                if ($key1 == $key2) {
                    //データ内ダブルクォーテーション2重化
                    $datalist[$i][$key1] = str_replace('"', '""', $val1);
                    //データ内改行コード削除

                    $datalist[$i][$key1] = str_replace("\r", '', $val1);
                    $datalist[$i][$key1] = str_replace("\r[^\n]", '', $val1);
                    $datalist[$i][$key1] = str_replace(HTML_CRLF, '', $val1);
                    $datalist[$i][$key1] = str_replace(HTML_ENTER, '', $val1);


                    $aryReturn[$i + 1][$val2] = $datalist[$i][$key1];
                }
            }
        }
    }
    if ($isSjis) {
        mb_convert_variables(ENCODE_SJIS_WIN, ENCODE_DEF, $aryReturn);
    }

    return $aryReturn;
}

//===============================================================================
//機  能    :ﾃﾞｨﾚｸﾄﾘ削除処理
//引  数    :arg1 - ﾌｧｲﾙﾊﾟｽ
//戻り値    :
//備  考    :ﾃﾞｨﾚｸﾄﾘ単位での削除処理
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_DeleteDir($rootPath)
{

    if (is_dir($rootPath)) {
        $strDir = opendir($rootPath);
        while ($strFile = readdir($strDir)) {
            if ($strFile != '.' && $strFile != '..') {  //ﾃﾞｨﾚｸﾄﾘでない場合のみ
                unlink($rootPath . '/' . $strFile);
            }
        }
        rmdir($rootPath);
    }
}

//===============================================================================
//機  能    :ﾌｧｲﾙ/ﾌｫﾙﾀﾞ削除関数
//引  数    :
//戻り値    :
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_FileAndDirDelete($dir)
{

    //echo $dir.NEXT_LINE;
    if (is_dir("$dir")) {
        $strDir = opendir("$dir");
        while ($strFile = readdir($strDir)) {
            // ﾃﾞｨﾚｸﾄﾘでない場合のみ
            if ($strFile != '.' && $strFile != '..') {
                unlink("$dir" . '/' . $strFile);
            }
        }
        rmdir("$dir");
    }
    return;
}

//===============================================================================
//機  能    :空ﾃﾞｨﾚｸﾄﾘ削除関数
//引  数    :
//戻り値    :
//備  考    :
//-------------------------------------------------------------------------------
//作成日：2019/05/27 Add E.KINJO    新規作成
//更新日：
//===============================================================================
function fncCom_Remove_Empty_Dir_Recursive($dir)
{

    if (!is_dir($dir)) return false;
    if (!($dh = opendir($dir))) return false;
    while (($file = readdir($dh)) !== false) {
        if (strpos($file, ".") === 0) continue;
        if (
            is_dir($dir . "/" . $file) &&
            fncCom_Remove_Empty_Dir_Recursive($dir . $file)
        ) continue;
        closedir($dh);
        return false;
    }
    closedir($dh);
    rmdir($dir);
    return true;
}

/**
 * アプリケーション固有ヘッダ部のHTMLを取得
 *
 * @return string アプリケーションヘッダ部のHTML
 */
function fncCom_GetAppHeader()
{
    return fncCom_GetFileData(COM_HEADER);
}

/**
 * アプリケーション固有フッタ部のHTMLを取得
 *
 * @return string アプリケーションフッタ部のHTML
 */
function fncCom_GetAppFooter()
{
    return fncCom_GetFileData(COM_FOOTER);
}

/**
 * 共通ヘッダタグのHTMLを取得
 *
 * @return string アプリケーションヘッドタグ部のHTML
 */
function fncCom_GetAppHeadTag()
{
    return fncCom_GetFileData(COM_TAG);
}

/**
 * フォーム固有ヘッダ部のHTMLを取得
 *
 * @return string フォームヘッダ部のHTML
 */
function fncCom_GetFormHeader()
{
    return fncCom_GetFileData(COM_FORM_HEADER);
}

/**
 * フォーム固有ヘッダ部のHTMLを取得
 *
 * @return string フォームフッタ部のHTML
 */
function fncCom_GetFormFooter()
{
    return fncCom_GetFileData(COM_FORM_FOOTER);
}

/**
 * アプリケーション内共通ヘッダ部のHTML設定処理
 *
 * @param string $contents HTML構成文字列
 * @param string $page_title ページタイトル
 * @return string HTML構成文字列
 */
function fncCom_SetAppHeader($contents, $page_title)
{
    // 共通ヘッダタグの取得
    $appHeadTag = fncCom_GetAppHeadTag();
    $aryHtml = explode('<!--HEAD_TAG_AREA-->', $contents);
    $contents = $aryHtml[0] . $appHeadTag . $aryHtml[2];

    // アプリケーションヘッダの取得
    $appHeader = fncCom_GetAppHeader();
    $aryHtml = explode('<!--APP_HEADER_AREA-->', $contents);
    $contents = $aryHtml[0] . $appHeader . $aryHtml[2];

    // アプリケーションフッタの取得
    $appFooter = fncCom_GetAppFooter();
    $aryHtml = explode('<!--APP_FOOTER_AREA-->', $contents);
    $contents = $aryHtml[0] . $appFooter . $aryHtml[2];

    // アプリケーションヘッダの取得
    $frmHeader = fncCom_GetFormHeader();
    $aryHtml = explode('<!--FORM_HEADER_AREA-->', $contents);
    $contents = $aryHtml[0] . $frmHeader . $aryHtml[2];

    // アプリケーションフッタの取得
    $frmFooter = fncCom_GetFormFooter();
    $aryHtml = explode('<!--FORM_FOOTER_AREA-->', $contents);
    $contents = $aryHtml[0] . $frmFooter . $aryHtml[2];

    // // ユーザー名の設定
    // $contents = str_replace('<!--USER_NM-->', h($_SESSION[S_LOGIN_US_NM]), $contents);

    $contents = str_replace('<!--APP_NAME-->', APP_NAME, $contents);
    $contents = str_replace('<!--PAGE_TITLE-->', h($page_title), $contents);
    $contents = str_replace('<!--APP_VERSION-->', APP_VERSION, $contents);

    return $contents;
}

/**
 * アプリケーション内共通ヘッダ部のHTML設定処理(サブ画面用)
 *
 * @param string $contents HTML構成文字列
 * @return string HTML構成文字列
 */
function fncCom_SetAppHeaderSub($contents)
{
    // 共通ヘッダタグの取得
    $appHeadTag = fncCom_GetAppHeadTag();
    $aryHtml = explode('<!--HEAD_TAG_AREA-->', $contents);
    $contents = $aryHtml[0] . $appHeadTag . $aryHtml[2];

    return $contents;
}

/**
 * HTMLエンコード処理
 *
 * @param string $str
 * @return string エンコード済み文字列
 */
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, ENCODE_DEF);
}

/**
 * SQL(Oracle)の引数をエスケープする。
 *
 * @param void $sqlArgument
 * @return void
 */
function escSql($sqlArgument)
{
    return EscapeSqlOracle($sqlArgument);
}

/**
 * ユーザー権限外のページの場合画面遷移する
 *
 * @param integer $UserKbn
 * @return void
 */
function fncCom_CheckKubun($UserKbn)
{
    if ($UserKbn !== 1) {
        Header('Location: ../../php/error_kbn.php');
        exit;
    }
}

/**
 * 各入力項目の入力内容をチェックし、エラーがあればメッセージをセットする
 *
 * @param [array] $aryInpList
 * @return array    引数にエラーメッセージをセットした配列
 */
function fncCom_CheckInp($aryInpList)
{
    // require("../../characterCheck/characterCheck.php");
    // require("../../common/commonFunction.php");
    // $charChk = new characterCheck();

    foreach ($aryInpList as &$inputData) {
        //入力項目のセット
        $strKoumokuNm = '【' . $inputData[ARY_ECHK_KOUMOKUNAME] . '】';
        $inpValue = $inputData[ARY_ECHK_INPUTVALUE];
        $intMaxWords = $inputData[ARY_ECHK_MAXWORDS];
        $kbnFormat = $inputData[ARY_ECHK_FORMAT];
        $inpErrMsg = '';

        //必須入力チェック
        if ($inputData[ARY_ECHK_REQUIRED] === true) {
            if (fncCom_IsEmptyString($inpValue)) {
                $inpErrMsg .= $strKoumokuNm . '必須入力です。<br>';
            }
        }

        //入力桁数チェック
        if (!fncCom_ValidateMaxLength($inpValue, $intMaxWords)) {
            $inpErrMsg .= $strKoumokuNm . '入力可能文字数を超えています。<br>';
        }

        // //特殊文字チェック
        // if ($inputData[ARY_ECHK_TOKUSHUMOJI] === true) {
        //     $result = mb_convert_encoding($charChk -> checkCharacter($inpValue), 'UTF-8');
        //     if ($result === '1') {
        //         $inpErrMsg .= $strKoumokuNm . '禁則文字が含まれています。<br>';
        //     }
        // }

        //書式チェック
        if (!fncCom_IsValidated($inpValue, $kbnFormat)) {
            switch (true) {
                case $kbnFormat == FORMKBN_HANKAKU:
                    $inpErrMsg .= $strKoumokuNm . '半角英数のみで入力してください。<br>';
                    break;

                case $kbnFormat == FORMKBN_HANKAKU_ALL:
                    $inpErrMsg .= $strKoumokuNm . '半角英数のみで入力してください。<br>';
                    break;

                case $kbnFormat == FORMKBN_ZENKAKU:
                    $inpErrMsg .= $strKoumokuNm . '全角のみで入力してください。<br>';
                    break;

                case $kbnFormat == FORMKBN_ALPHABET:
                    $inpErrMsg .= $strKoumokuNm . '半角アルファベットのみで入力してください。<br>';
                    break;

                case $kbnFormat == FORMKBN_ALPHABET_ALL:
                    $inpErrMsg .= $strKoumokuNm . 'アルファベットのみで入力してください。<br>';
                    break;

                case $kbnFormat == FORMKBN_NUMBER || $kbnFormat == FORMKBN_NUMBER_NEGATIVE:
                    $inpErrMsg .= $strKoumokuNm . '半角数字のみで入力してください。<br>';
                    break;

                case $kbnFormat == FORMKBN_NUMBER_ALL:
                    $inpErrMsg .= $strKoumokuNm . '数字のみで入力してください。<br>';
                    break;

                case $kbnFormat == FORMKBN_NUMBER_ZENKAKU:
                    $inpErrMsg .= $strKoumokuNm . '全角数字のみで入力してください。<br>';
                    break;

                case $kbnFormat == FORMKBN_YYYYMMDD:
                    $inpErrMsg .= $strKoumokuNm . '日付として正しくありません。<br>';
                    break;

                case $kbnFormat == FORMKBN_YYMMDD:
                    $inpErrMsg .= $strKoumokuNm . '日付として正しくありません。<br>';
                    break;

                case $kbnFormat == FORMKBN_YYYYMM:
                    $inpErrMsg .= $strKoumokuNm . '日付として正しくありません。<br>';
                    break;
            }
        }

        if ($inpErrMsg !== '') {
            $inputData[ARY_ECHK_ERRMSG] = $inpErrMsg;
            $inputData[ARY_ECHK_HASERROR] = true;
        }
    }
    unset($inputData);
    return $aryInpList;
}

/**
 * 引数が空文字ならTrue、それ以外ならFalseを返す
 *
 * @param string $value 判定対象の文字列
 * @return boolean
 */
function fncCom_IsEmptyString($value)
{
    if ($value === '') {
        return true;
    } else {
        return false;
    }
}

/**
 * 指定された最大バイトを超えた値ならばFalse
 * 超えない、または指定最大バイトが0ならTrue
 *
 * @param string $value 判定対象の文字列
 * @param integer $maxbyte   指定最大バイト
 * @return boolean
 */
function fncCom_ValidateMaxbytes($value, $maxbyte)
{
    if ($maxbyte === 0) {
        return false;
    } elseif (strlen($value) > $maxbyte) {
        return true;
    } else {
        return false;
    }
}

/**
 * 指定された最大文字数を超えた値ならばFalse
 * 超えない、または指定最大文字数が0ならTrue
 *
 * @param string $value     入力値
 * @param integer $maxbyte   指定最大バイト
 * @return boolean
 */
function fncCom_ValidateMaxLength($value, $maxWords)
{
    if ($maxWords === 0) {
        return true;
    } elseif (mb_strlen($value, ENCODE_DEF) > $maxWords) {
        return false;
    } else {
        return true;
    }
}

/**
 * 入力値が指定されたフォーマットとして正しければTrue
 * 正しくなければFalse
 *
 * @param string $value     入力値
 * @param integer $kbnFormat
 * @return boolean
 */
function fncCom_IsValidated($value, $kbnFormat)
{
    if ($value === '') {
        return true;
    } else {
        switch (true) {
                // すべて許可
            case $kbnFormat == FORMKBN_ALL:
                return true;
                break;

                // 半角英数（記号無）のみ
            case $kbnFormat == FORMKBN_HANKAKU:
                return preg_match('/\A[A-Za-z0-9]+\z/', $value);
                break;

                // 半角英数（記号含）のみ
            case $kbnFormat == FORMKBN_HANKAKU_ALL:
                return preg_match('/\A[ -~]+\z/', $value);
                break;

                // 全角のみ
            case $kbnFormat == FORMKBN_ZENKAKU:
                return preg_match('/\A[^0-9a-zA-Z]+\z/', $value);
                break;

                // 半角アルファベットのみ
            case $kbnFormat == FORMKBN_ALPHABET:
                return preg_match('/\A[A-Za-z]+\z/', $value);
                break;

                // アルファベット（全角含）のみ
            case $kbnFormat == FORMKBN_ALPHABET_ALL:
                return preg_match('/\A[Ａ-Ｚａ-ｚA-Za-z]+\z/', $value);
                break;

                // 半角数字のみ
            case $kbnFormat == FORMKBN_NUMBER:
                return preg_match('/\A[0-9]+\z/', $value);
                break;

                // 半角数字（マイナスも含む）
            case $kbnFormat == FORMKBN_NUMBER_NEGATIVE:
                return preg_match('/\A[-]?[0-9]+\z/', $value);
                break;

                // 数字（全角含）のみ
            case $kbnFormat == FORMKBN_NUMBER_ALL:
                return preg_match('/\A[０-９0-9]+\z/', $value);
                break;

                // 数字（全角）のみ
            case $kbnFormat == FORMKBN_NUMBER_ZENKAKU:
                return preg_match('/\A[０-９]+\z/', $value);
                break;

                // 日付（YYYYMMDD)
            case $kbnFormat == FORMKBN_YYYYMMDD:
                if (preg_match('/\A[^0-9]+\z/', $value)) {
                    return false;
                } elseif (mb_strlen($value) !== 8) {
                    return false;
                } else {
                    $year = fncCom_Left($value, 4);
                    $month = mb_substr($value, 4, 2);
                    $day = fncCom_Left($value, 2);
                    return checkdate($month, $day, $year);
                }
                break;

                // 日付（YYMMDD)
            case $kbnFormat == FORMKBN_YYMMDD:
                if (preg_match('/\A[^0-9]+\z/', $value)) {
                    return false;
                } elseif (mb_strlen($value) !== 6) {
                    return false;
                } else {
                    $year = '20' . fncCom_Left($value, 2);
                    $month = mb_substr($value, 2, 2);
                    $day = fncCom_Left($value, 2);
                    return checkdate($month, $day, $year);
                }
                break;

                // 日付（YYYYMM)
            case $kbnFormat == FORMKBN_YYYYMM:
                if (preg_match('/\A[^0-9]+\z/', $value)) {
                    return false;
                } elseif (mb_strlen($value) !== 6) {
                    return false;
                } else {
                    $year = fncCom_Left($value, 4);
                    $month = mb_substr($value, 4, 2);
                    $day = '01';
                    return checkdate($month, $day, $year);
                }
                break;
        }
        return true;
    }
}

/**
 * セッションIDをハッシュ化して返す
 *
 * @return string ハッシュ化されたセッションID
 */
function fncCom_GenerateToken()
{
    return hash('sha256', session_id());
}


/**
 * パラメータマスタから該当セクションのリストを取得する
 *
 * @param string $section
 * @param string $strWhere
 * @return array ﾚｺｰﾄﾞｾｯﾄ
 */
function fncCom_getParamList($section, $strWhere, $sel_val)
{

    // ヒアドキュメント内定数展開用
    $_ = function ($s) {
        return $s;
    };

    $sql = <<<EOT
    Select	PARAM_CD As CODE
        ,	VALUE1 As NAME
    From {$_(DB_SCHEMA)}.{$_(DB_ID)}_M_PARAM
EOT;

    $sqlWhere = ' Where	SECTION = ' . escSql($section);
    if ($strWhere !== '') {
        $sqlWhere .= ' And ' . $strWhere;
    }
    $sqlOrder = ' Order By SORT_NO, PARAM_CD ';

    $sql .= $sqlWhere . $sqlOrder;
    $datalist = fncCom_GetDataList($sql);

    $def_ary = array('CODE' => '', 'NAME' => '');

    $strReturnAry = fncCom_SetCombBox($datalist, $sel_val, true, $def_ary);
    return $strReturnAry;
}


/**
 * 配列から、エラーがある入力項目のエラーメッセージをhtmlにセットする
 *
 * @param string $contents      html
 * @param array $aryInpList   エラーメッセージがセットされた配列
 * @return void
 */
function fncCom_setErrorMsg($contents, $aryInpList)
{

    $contents = mb_convert_encoding($contents, 'HTML-ENTITIES', ENCODE_DEF);
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    @$dom->loadHTML($contents);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);
    $script = 'alert("入力に誤りがあります。\n赤くなった項目にカーソルを合わせて、問題を確認して下さい。");';

    foreach ($aryInpList as $aryErrorData) {
        if ($aryErrorData[ARY_ECHK_HASERROR]) {
            $htmlIdNm = $aryErrorData[ARY_ECHK_TH_SPAN_ID];
            $inpErrMsg = $aryErrorData[ARY_ECHK_ERRMSG];
            $nowClass = '';

            //エラーがある項目のthタグにエラー用CSSクラスをセット
            $th_node = $xpath->query('//th[@id="th_' . $htmlIdNm . '"]')->item(0);
            if (isset($th_node)) {
                $nowClass  = $th_node->getAttribute('class') . " ";
                if (!preg_match("/" . HTML_INP_ERR . "/", $nowClass)) {
                    $th_node->setAttribute("class",  $nowClass . HTML_INP_ERR);
                }
            }

            //エラーメッセージをthタグ内のspanにセット
            $span_node = $xpath->query('//span[@id="spn_' . $htmlIdNm . '"]', $th_node)->item(0);
            if (isset($span_node)) {
                $span_node->appendChild($dom->createTextNode($inpErrMsg));
            }
        }
    }
    $contents = $dom->saveHTML();
    $body_node = $xpath->query('//body')->item(0);
    if (isset($body_node)) {
        $body_node->setAttribute("onLoad",  $script);
    }

    $contents = $dom->saveHTML();
    $contents = mb_convert_encoding($contents, ENCODE_DEF, 'HTML-ENTITIES');
    return $contents;
}

/**
 * 与えられた数値をカンマ区切りの文字列で返す
 *
 * @param integer $number
 * @param boolean $isSetYen true:円マークを頭にセットする
 * @return string
 */
function fncCom_SetFormatNumber($number, $isSetYen)
{
    $returnValue = '';
    $returnValue = number_format($number);
    if ($isSetYen) {
        $returnValue = '￥' . $returnValue;
    }
    return $returnValue;
}


/**
 * 端数処理
 *
 * @param [int,float] $value
 * @param [int] $intHasuKbn
 * @return [int] $intValue
 */
function fncCom_setHasu($value, $intHasuKbn)
{
    //丸め処理
    switch (true) {
        case $intHasuKbn === 1:
            $value       = (int) ceil($value);
            break;

        case $intHasuKbn === 2:
            $value       = (int) floor($value);
            break;

        case $intHasuKbn === 3;
            $value       = (int) round($value);
            break;
    }

    return $value;
}

/**
 * エラーチェックリストを参照し、エラー項目があるかを返す
 *
 * @param [array] $aryInpList
 * @return boolean
 */
function fncCom_hasErrorInput($aryInpList)
{
    $flgHasError = false;
    foreach ($aryInpList as $aryErrorData) {
        if ($aryErrorData[ARY_ECHK_HASERROR] === true) {
            $flgHasError = true;
            break;
        }
    }
    return $flgHasError;
}

/**
 * Csrf対策チェック
 *
 * @param string $strToken
 * @return void
 */
function fncCom_isValidPost($strToken)
{
    if ($strToken !== fncCom_GenerateToken()) {
        echo "不正なアクセスを検出しました。<br>";
        exit;
    }
}

/**
 *
 */
function fncCom_moveInfoPage($aryPost, $strJuchuId = null)
{
    if (isset($aryPost['txtFromPage'])) {
        header('HTTP/1.1 307 Temporary Redirect');
        if (isset($strJuchuId)) {
            header('Location: ' . PHP_INFORM . '?NO=' . $strJuchuId);
        } else {
            header('Location: ' . PHP_INFORM);
        }
        exit;
    } else {
        return false;
    }
}

/**
 * 全角スペースのTrim
 *
 * @param [string] $pString
 * @return void
 */
function fncCom_mbTrim($pString)
{
    return preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $pString);
}

/**
 * 連想配列であるかどうかを判定する
 *
 * @param array $variable
 * @return boolean
 */
function fncCom_isHash($variable)
{
    return is_hash($variable);
}

/**
 * ログイン情報以外のセッション情報の破棄
 *
 * @return void
 */
function fncCom_initSession()
{
    $arySaveUserNm = $_SESSION[S_LOGIN_US_NM];
    $arySaveUserKengen = $_SESSION[S_LOGIN_US_KENGEN];
    $arySaveUserId = $_SESSION[S_LOGIN_US_ID];
    $arySaveErrMsg = $_SESSION[S_ERR_MSG];
    $arySaveDbUid = $_SESSION[S_DB_UID];
    $arySaveDbPwd = $_SESSION[S_DB_PWD];
    $arySaveDbDsn = $_SESSION[S_DB_DSN];
    $arySaveCertifcate = $_SESSION[S_CERTIFICATE];

    $_SESSION = array();

    $_SESSION[S_LOGIN_US_NM] = $arySaveUserNm;
    $_SESSION[S_LOGIN_US_KENGEN] = $arySaveUserKengen;
    $_SESSION[S_LOGIN_US_ID] = $arySaveUserId;
    $_SESSION[S_ERR_MSG] = $arySaveErrMsg;
    $_SESSION[S_DB_UID] = $arySaveDbUid;
    $_SESSION[S_DB_PWD] = $arySaveDbPwd;
    $_SESSION[S_DB_DSN] = $arySaveDbDsn;
    $_SESSION[S_CERTIFICATE] = $arySaveCertifcate;
}

/**
 * １～１２月までのセレクトボックス用Html文字列を出力する
 *
 * @return string
 */
function fncCom_getMonthList($sel_val = '')
{
    $opt_html = '';
    // ｺﾝﾎﾞﾘｽﾄ生成処理
    $opt_html .= "<option value=''>※選択してください</option>";
    for ($i = 1; $i <= 12; $i++) {
        $opt_html .= "<option value='" . $i . "'";
        if ($sel_val !== '') {
            if ((int) $sel_val === $i) {
                $opt_html .= HTML_SELECTED;
            }
        }
        $opt_html .= ">" . $i . "月" . "</option>\n";
    }
    return $opt_html;
}

/**
 * テーブル名とキーを指定し、データが存在するかを返す
 *
 * @param [string] $strTableNm
 * @param [array()] $aryKeys[0] = array('COL_NM' => キー項目のカラム名, 'KEY_VAL' => キーの値), 'DATA_TYPE' => 'SQLT_CHR or SQLT_INT'] $aryKeys[1]....
 * @return boolean
 */
function fncCom_DataExits($strTableNm, $aryKeys)
{
    $_ = function ($s) {
        return $s;
    }; //定数展開用
    $sqlParams = array();
    $returnValue = false;

    //////////////////////////////////////////////////
    // 3. DB接続情報取得
    //////////////////////////////////////////////////
    fncCom_GetDbInfo();
    //////////////////////////////////////////////////
    // 4. DB接続
    //////////////////////////////////////////////////
    $obj = new DataObject();
    // DB接続
    $obj->DBOpen();

    if ($obj->GetErrFlg()) {
        // ｴﾗｰ処理
        fncCom_SetErr($obj->errMessage);
        echo $_SESSION[S_ERR_MSG];
        exit();
    }

    //存在チェック
    $sql = " Select COL." . $aryKeys[0]['COL_NM'];
    $sql .= " From " . DB_SCHEMA . "." . $strTableNm . " COL ";
    $sql .= " Where COL." . $aryKeys[0]['COL_NM'] . " = :" . $aryKeys[0]['COL_NM'];

    $sqlParams[] = array('SQL_PARAM_PARAM' => ':' . $aryKeys[0]['COL_NM'], 'SQL_PARAM_VAL' => $aryKeys[0]['KEY_VAL'], 'SQL_PARAM_TYPE' => $aryKeys[0]['DATA_TYPE']);

    $intKeyCount = count($aryKeys);
    if ($intKeyCount > 1) {
        for ($i = 1; $i < $intKeyCount; $i++) {
            $sql = $sql . " AND COL." . $aryKeys[$i]['COL_NM'] . "= :" . $aryKeys[$i]['COL_NM'];
            $sqlParams[] = array('SQL_PARAM_PARAM' => ':' . $aryKeys[$i]['COL_NM'], 'SQL_PARAM_VAL' => $aryKeys[$i]['KEY_VAL'], 'SQL_PARAM_TYPE' => $aryKeys[$i]['DATA_TYPE']);
        }
    }
    $obj->OpenRecordSet($sql, $sqlParams);
    if ($obj->GetErrFlg()) {
        fncCom_SetErr($obj->errMessage);
        echo $_SESSION[S_ERR_MSG];
        exit();
    }

    $datalist = $obj->GetRecordSet();
    if (count($datalist) > 0) {
        $returnValue = true;
    } else {
        $returnValue = false;
    }

    return $returnValue;
}

/**
 * セッション情報を確認し、見つからなければエラー画面に遷移する
 *
 * @return void
 */
function fncCom_isLogin()
{
    if (!isset($_SESSION[S_LOGIN_US_ID]) || !isset($_SESSION[S_LOGIN_US_NM])) {
        header('Location: ../../SessionError.html');
        exit;
    }
}


/**
 * css、JavaScriptファイル読み込み時にクエリ文字列を追加
 *
 * @param string $contents HTMLファイル文字列
 * @return HTMLファイル文字列
 */
function fncCom_cacheBusting($contents)
{
    $contents = str_replace('<!--APP_VERSION-->', APP_VERSION, $contents);
    return $contents;
}

/**
 * tmpFileフォルダ内で今日以外の日付のものを削除する
 *
 * @param [type] $strFilePath
 * @param [type] $strFileName
 * @param [type] $strExtension
 * @return void
 */
function fncCom_deleteTmpFile($strFilePath, $strFileName, $strExtension)
{
    $today = date('Ymd');
    $strGrobPath = $strFilePath . '*' . '.' . $strExtension;
    $strPreg = str_replace('/', '\/', $strFilePath . $strFileName . '_');

    foreach (glob($strGrobPath) as $file) {

        if (!preg_match('/' . $strPreg  . $today . '/', $file)) {
            // globで取得したファイルをunlinkで1つずつ削除していく
            unlink($file);
        }
    }
}

/**
 * Undocumented function
 *
 * @param [type] $intMode
 * @param [type] $strJuchuId
 * @return void
 */
function fncCom_insertAccessLog($intMode, $strJuchuId = null)
{

    $_ = function ($s) {
        return $s;
    }; //定数展開用
    $sqlParams = array();
    $strDate = date('Ymd');
    $strTime = date('His');
    $strIP = fncCom_isSet($_SERVER['REMOTE_ADDR']);
    $strUserId = $_SESSION[S_LOGIN_US_ID];
    $strUserNm = $_SESSION[S_LOGIN_US_NM];
    $strSessionId = fncCom_mbTrim(fncCom_Left((string) session_id(), 40));
    $strActionNm = fncCom_mbTrim(fncCom_Left(fncCom_isSet($_SERVER['SCRIPT_NAME']), 40));
    $strCertificate = $_SESSION[S_CERTIFICATE];

    //ログイン時以外は受注データを取得
    if (isset($strJuchuId)) {

        fncCom_GetDbInfo();

        $obj = new DataObject();
        // DB接続
        $obj->DBOpen();

        $sql = <<<EOT
        Select
            JUCHU.APP_NO,
            JUCHU.DAN_NM,
            JUCHU.GOTO_ID,
            JUCHU.HEYA_ID,
            JUCHU.TENANT_NM,
            JUCHU.TENANT_TEL
        From
            {$_(DB_SCHEMA)}.T_JUCHU JUCHU
            Where JUCHU.JUCHU_ID = :JUCHU_ID
EOT;

        $sqlParams[] = array('SQL_PARAM_PARAM' => ':JUCHU_ID', 'SQL_PARAM_VAL' => $strJuchuId, 'SQL_PARAM_TYPE' => SQLT_CHR);

        $obj->OpenRecordSet($sql, $sqlParams);
        if ($obj->GetErrFlg()) {
            fncCom_SetErr($obj->errMessage);
            echo $_SESSION[S_ERR_MSG];
            exit();
        }
        $datalist = $obj->GetRecordSet();

        if (count($datalist) === 0) {
            $aryJuchuDate = array(
                ACCESSLOG_JUCHUDATE_APP => '',
                ACCESSLOG_JUCHUDATE_DANCHI => '',
                ACCESSLOG_JUCHUDATE_GOTO => '',
                ACCESSLOG_JUCHUDATE_HEYA => '',
                ACCESSLOG_JUCHUDATE_TENANT_NM => '',
                ACCESSLOG_JUCHUDATE_TENANT_TEL => ''
            );
        } else {
            $aryJuchuDate = $datalist[0];
        }
        $sqlParams = array();

        // DB切断
        $obj->DBClose();
        unset($obj);
    }

    switch (true) {
        case $intMode === ACCESSLOG_LOGIN && !isset($aryJuchuDate):
            $aryInsValues = array('DPCO0000', '', '1000', '', '', '', '', '', '');
            break;
        case $intMode === ACCESSLOG_LOGIN_ERR && !isset($aryJuchuDate):
            $aryInsValues = array('DPCO0000', '', '5000', '', '', '', '', '', '');
            break;
        case $intMode === ACCESSLOG_IRAI_INS_OG:
            $aryInsValues = array(
                'DPOG1000',
                '',
                '2104',
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_APP]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_DANCHI]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_GOTO]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_HEYA]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_NM]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_TEL])
            );
            break;
        case $intMode === ACCESSLOG_IRAI_UPD_OG:
            $aryInsValues = array(
                'DPOG1010',
                '',
                '2101',
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_APP]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_DANCHI]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_GOTO]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_HEYA]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_NM]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_TEL])
            );
            break;
        case $intMode === ACCESSLOG_IRAI_INS2_OG:
            $aryInsValues = array(
                'DPOG1010',
                '',
                '2102',
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_APP]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_DANCHI]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_GOTO]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_HEYA]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_NM]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_TEL])
            );
            break;
        case $intMode === ACCESSLOG_IRAI_INS_GG:
            $aryInsValues = array(
                'DPGG1000',
                '',
                '2104',
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_APP]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_DANCHI]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_GOTO]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_HEYA]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_NM]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_TEL])
            );
            break;
        case $intMode === ACCESSLOG_IRAI_UPD_GG:
            $aryInsValues = array(
                'DPGG1010',
                '',
                '2101',
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_APP]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_DANCHI]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_GOTO]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_HEYA]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_NM]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_TEL])
            );
            break;
        case $intMode === ACCESSLOG_IRAI_INS2_GG:
            $aryInsValues = array(
                'DPGG1010',
                '',
                '2102',
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_APP]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_DANCHI]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_GOTO]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_HEYA]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_NM]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_TEL])
            );
            break;
        case $intMode === ACCESSLOG_IRAI_DTL:
            $aryInsValues = array(
                'DPCO2000',
                '',
                '2104',
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_APP]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_DANCHI]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_GOTO]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_HEYA]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_NM]),
                fncCom_isSet($aryJuchuDate[ACCESSLOG_JUCHUDATE_TENANT_TEL])
            );
            break;

        default:
            return false;
    }

    //////////////////////////////////////////////////
    // 3. DB接続情報取得
    //////////////////////////////////////////////////
    fncCom_GetDbInfo();
    //////////////////////////////////////////////////
    // 4. DB接続
    //////////////////////////////////////////////////
    $obj = new DataObject();
    // DB接続
    $obj->DBOpen();

    if ($obj->GetErrFlg()) {
        // ｴﾗｰ処理
        fncCom_SetErr($obj->errMessage);
        echo $_SESSION[S_ERR_MSG];
        exit();
    }

    $sql = <<<EOT
    Insert
    Into {$_(DB_SCHEMA)}.T_ACCESS_LOG(
      LOG_ID
      , LOG_DATE
      , LOG_TIME
      , USER_ID
      , USER_NM
      , HOST_NM
      , IP_ADDRESS
      , CERTIFICATE_NO
      , SESSION_ID
      , ACTION_NM
      , DISPLAY_ID
      , PRINT_ID
      , OPERATION_CD
      , MAIL_ADDRESS
      , FILE_PATH
      , APP_NO
      , DANCHI_NM
      , GOTO_ID
      , HEYA_ID
      , TENANT_NM
      , TENANT_TEL
      , SEND_FLAG
    )
    Values (
        ( Select
            Max(LOG_ID) + 1
        From
            {$_(DB_SCHEMA)}.T_ACCESS_LOG
        )
      , :LOG_DATE
      , :LOG_TIME
      , :USER_ID
      , :USER_NM
      , ''
      , :IP_ADDRESS
      , :CERTIFICATE_NO
      , :SESSION_ID
      , :ACTION_NM
      , :DISPLAY_ID
      , :PRINT_ID
      , :OPERATION_CD
      , ''
      , ''
      , :APP_NO
      , :DANCHI_NM
      , :GOTO_ID
      , :HEYA_ID
      , :TENANT_NM
      , :TENANT_TEL
      , 0
    )
EOT;

    $sqlParams[] = array('SQL_PARAM_PARAM' => ':LOG_DATE', 'SQL_PARAM_VAL' => $strDate, 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':LOG_TIME', 'SQL_PARAM_VAL' => $strTime, 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':USER_ID', 'SQL_PARAM_VAL' => $strUserId, 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':USER_NM', 'SQL_PARAM_VAL' => $strUserNm, 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':IP_ADDRESS', 'SQL_PARAM_VAL' => $strIP, 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':CERTIFICATE_NO', 'SQL_PARAM_VAL' => $strCertificate, 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':SESSION_ID', 'SQL_PARAM_VAL' => $strSessionId, 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':ACTION_NM', 'SQL_PARAM_VAL' => $strActionNm, 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':DISPLAY_ID', 'SQL_PARAM_VAL' => $aryInsValues[0], 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':PRINT_ID', 'SQL_PARAM_VAL' => $aryInsValues[1], 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':OPERATION_CD', 'SQL_PARAM_VAL' => $aryInsValues[2], 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':APP_NO', 'SQL_PARAM_VAL' => $aryInsValues[3], 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':DANCHI_NM', 'SQL_PARAM_VAL' => $aryInsValues[4], 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':GOTO_ID', 'SQL_PARAM_VAL' => $aryInsValues[5], 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':HEYA_ID', 'SQL_PARAM_VAL' => $aryInsValues[6], 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':TENANT_NM', 'SQL_PARAM_VAL' => $aryInsValues[7], 'SQL_PARAM_TYPE' => SQLT_CHR);
    $sqlParams[] = array('SQL_PARAM_PARAM' => ':TENANT_TEL', 'SQL_PARAM_VAL' => $aryInsValues[8], 'SQL_PARAM_TYPE' => SQLT_CHR);

    $obj->ExecuteAuto($sql, $sqlParams);

    if ($obj->GetErrFlg()) {
        fncCom_SetErr($obj->errMessage);
        echo $_SESSION[S_ERR_MSG];
        exit();
    }

    // DB切断
    $obj->DBClose();
}

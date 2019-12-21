<?php

/**
 * 画面で使用するセッション変数をクリアする
 *
 * @return void
 */
function clearSession()
{
    unset($_SESSION[S_INP_POSTED]);
}


/**
 *ユーザーマスタ一覧表示にて件数と表示項目の値を取得
 *
 * @param string $contents HTML構成文字列
 * @param array $aryPost Formの各項目のIDと値の連想配列
 * @return string $contents HTML構成文字列
 */
function setDataList($contents, $datalist, $curPageNo = 1, $countPerPage = 0)
{
    if ($countPerPage == 0) {
        $countPerPage = PAGE_DATA_MAX_COUNT;
    }

    $strMasterList = '';
    $aryResultHTML = explode('<!--RESULT_AREA-->', $contents);
    if ($datalist) {
        $aryTableHTML = explode('<!--LIST_DATA_AREA-->', $aryResultHTML[1]);

        $pageDataList = fncCom_GetPageData($datalist, $curPageNo, $countPerPage);

        foreach ($pageDataList as $data) {
            $strRow = $aryTableHTML[1];

            //各表示項目のフォーマットを整える
            $strRow = fncCom_SetKeyReplaceHtml($data, $strRow);

            $strMasterList .= $strRow;
        }
        $strMasterList = $aryTableHTML[0] . $strMasterList . $aryTableHTML[2];
    } else {
        $strMasterList = '';
    }
    $contents = $aryResultHTML[0] . $strMasterList . $aryResultHTML[2];

    // //ページングコンポーネントを生成
    // $contents = funcCom_SetPagingContents($contents, $datalist, $curPageNo, $countPerPage);

    return $contents;
}


/**
 * M_USERのデータを取得する
 *
 * @param array $aryPost Formの各項目のIDと値の連想配列
 * @return array 取得結果のレコードセット
 */
function getDataMUser()
{

    // ヒアドキュメント内定数展開用
    $_ = function ($s) {
        return $s;
    };

    $strAlertMsg = '';
    $sqlParams = array();

    // DB接続情報取得
    fncCom_GetDbInfo();

    $obj = new DataObject();
    // DB接続
    $obj->DBOpen();

    if ($obj->GetErrFlg()) {
        //ｴﾗｰ処理
        fncCom_SetErr($obj->errMessage);
        echo $_SESSION[S_ERR_MSG];
        exit();
    }

    //データ取得
    $sql = <<<SQL
    select
        USER_ID
        ,USER_NM
        ,SIGN_FILE_NM
    from
        {$_(DB_SCHEMA)}.{$_(DB_ID)}_M_ESIGN

SQL;

    $sql .= setWhere($aryPost);

    //初期表示時
    $sql .= " Order By USER_ID ";

    $sqlParams = setSqlParams($aryPost);
    $obj->OpenRecordSet($sql, $sqlParams);
    if ($obj->GetErrFlg()) {
        fncCom_SetErr($obj->errMessage);
        echo $_SESSION[S_ERR_MSG];
        exit();
    }
    $datalist = $obj->GetRecordSet();
    //DB切断
    $obj->DBClose();

    return $datalist;
}

/**
 * setMasterList()のSQL文に対して、Where句の設定をする
 *
 * @param array $aryPost
 * @return string SQLのWhere句
 */
function setWhere($aryPost)
{

    $strWhere = ' Where 1 = 1';
    $strWhere .= ' And SIGN_IMG Is Null';

    // if (isset($aryPost)) {
    //     //ユーザーID（部分一致）
    //     if ($aryPost['txtUserId'] != '') {
    //         $strWhere .= ' AND UPPER(MU.USER_ID) LIKE UPPER(:USER_ID) ';
    //     }
    // } else {
    //     // 利用停止とｼｽﾃﾑ管理の権限は抽出対象外とする
    //     $strWhere .= ' AND NOT MU.USER_KENGEN IN (0, 9) ';
    // }
    return $strWhere;
}

/**
 * プリペアドステートメント実行用の配列を生成する
 *
 * @param array $aryPost 項目のIDと値をもつ連想配列
 * @return array プリペアドステートメント実行用の配列
 */
function setSqlParams($aryPost)
{
    $aryReturn = array();
    $strUserVal = '';

    // if (isset($aryPost)) {
    //     //ユーザーID（部分一致）
    //     if ($aryPost['txtUserId'] !== '') {
    //         $strUserVal = '%' . $aryPost['txtUserId'] . '%';
    //         $aryReturn[] = array('SQL_PARAM_PARAM' => ':USER_ID', 'SQL_PARAM_VAL' => $strUserVal, 'SQL_PARAM_LEN' => null, 'SQL_PARAM_TYPE' => SQLT_CHR);
    //     }
    // }

    return $aryReturn;
}


/**
 * 初回表示時の値を連想配列にセットして返す
 *
 * @return array
 */
function getEmptySetList()
{
    $aryReturn = array(
        'TXT_USER_ID' => '',
        'TXT_USER_NM' => '',
        'LST_USER_KENGEN' => '',
        'RANDOM_TOKEN' => fncCom_GenerateToken()
    );
    return $aryReturn;
}

/**
 * ポストされた値を連想配列にセットして返す
 *
 * @param array $aryPost 項目のIDと値をもつ連想配列
 * @return array
 */
function getSetListByPost($aryPost)
{
    $aryReturn = array(
        'TXT_USER_ID' => $aryPost['txtUserId'],
        'TXT_USER_NM' => $aryPost['txtUserNm'],
        'LST_USER_KENGEN' => $aryPost['lstUserKengen'],
        'RANDOM_TOKEN' => fncCom_GenerateToken()
    );
    return $aryReturn;
}
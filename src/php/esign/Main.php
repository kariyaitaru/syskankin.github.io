<?php

//////////////////////////////////////////////////
// 1. ﾌｧｲﾙ読込処理(共通)
//////////////////////////////////////////////////
include('../../inc/Include.php');
include(INC_DIR . 'esign' . DS . 'incMain.php');

// //////////////////////////////////////////////////
// // 2. ログイン情報確認(共通)
// //////////////////////////////////////////////////
// fncCom_isLogin();


//////////////////////////////////////////////////
// 3. 変数設定
//////////////////////////////////////////////////
$contents = '';
$strPage_Title = '電子サイン';
$strPage_Info = '';


// //////////////////////////////////////////////////
// // 4. 不要なセッション情報の破棄
// //////////////////////////////////////////////////
clearSession();
// TODO: 便宜的にログインユーザーIDを設定しておく
$_SESSION[S_LOGIN_US_ID] = 'SYSCON';

//////////////////////////////////////////////////
// 5. ﾃﾝﾌﾟﾚｰﾄ取得
//////////////////////////////////////////////////
$contents = fncCom_GetFileData(HTM_DIR . 'esign' . DS . 'main.html');
// 共通ヘッダの取得
$contents = fncCom_SetAppHeader($contents, $strPage_Title);


// POST時の値を変数に保持
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['btn']) {
        case 'Search':
            // データ取得
            $datalist = getData();
            break;

        case 'Upload':
            // データ登録
            $postlist = filter_input_array(INPUT_POST, $_POST);
            writeData($postlist);
            break;

        default:
            break;
    }
}

// 検索結果を一覧表示する
$contents = setDataList($contents, $datalist, $pageNo);

echo $contents;

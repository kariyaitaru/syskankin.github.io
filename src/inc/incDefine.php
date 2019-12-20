<?php
//===================================================================================
//[ｼｽﾃﾑ    ]    -
//[ﾌｧｲﾙ名  ]    incDefine.php
//[処理内容]    Tugiraku用_定数定義
//[作成    ]    2019/05/27 E.KINJO    新規作成（一部ツギラクからコピー）
//[履歴    ]
//===================================================================================

//////////////////////////////////////////////////////////////////////
// 文字ｺｰﾄﾞ
//////////////////////////////////////////////////////////////////////
define('ENCODE_UTF8', 'UTF-8');    // UTF8
define('ENCODE_UTF8N', 'UTF-8N');    // UTF8N
define('ENCODE_EUC', 'EUC-JP');    // EUC
define('ENCODE_SJIS', 'SJIS');    // SJIS
define('ENCODE_EUC_WIN', 'encJP-win');    // EUC(Windows拡張文字対応)
define('ENCODE_SJIS_WIN', 'SJIS-win');    // SJIS(Windows拡張文字対応)
// システム用エンコーディング
define('ENCODE_DEF', ENCODE_UTF8);

//////////////////////////////////////////////////////////////////////
// ﾃﾞｨﾚｸﾄﾘ関連定数
//////////////////////////////////////////////////////////////////////
define("DS", DIRECTORY_SEPARATOR);
// ｼｽﾃﾑ関連
define('SYS_DIR', dirname(__FILE__).DS.'..'.DS.'php'.DS);
define('LIB_DIR', dirname(__FILE__).DS.'..'.DS.'lib'.DS);
define('INC_DIR', dirname(__FILE__).DS);
define('CLS_DIR', dirname(__FILE__).DS.'..'.DS.'class'.DS);
define('HTM_DIR', dirname(__FILE__).DS.'..'.DS.'..'.DS.'tmp'.DS);

// DB接続情報
define('ORA_DIR', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'oracle'.DS);
// ﾛｸﾞ関連
define('LOG_DIR', '..'.DS.'..'.DS.'..'.DS.'..'.DS.'log'.DS);


define('APP_NAME', 'esign');
// define('APP_VERSION', '0.1');
define('APP_VERSION', rand());

//////////////////////////////////////////////////////////////////////
// DB、SQL関連
//////////////////////////////////////////////////////////////////////
// DBスキーマ名
define('DB_SCHEMA', 'PWA_SCHEMA');    // DBスキーマ
define('DB_ID', 'PWA');    // DBWAS
// DBｷｬﾗｸﾀｾｯﾄ
define('DB_CHARSET', ENCODE_UTF8);

//////////////////////////////////////////////////////////////////////
// 各種ﾌｧｲﾙﾊﾟｽ
//////////////////////////////////////////////////////////////////////
define('INFO_FILE', ORA_DIR.'connect.inf');    // Oracle設定情報ﾌｧｲﾙ
define('LOG_FILE', LOG_DIR . APP_NAME.'-'.date('Ymd').'.log');    // ﾛｸﾞﾌｧｲﾙ
// define('DEBUG_LOG_FILE', LOG_DIR . 'debug.log');    // ﾃﾞﾊﾞｯｸﾞ用ﾛｸﾞﾌｧｲﾙ

define('SESSION_FILE', INC_DIR.'incSessionSet.php');    // ｾｯｼｮﾝ設定ﾌｧｲﾙ
define('COMMON_FILE', INC_DIR.'incCommon.php');    // 共通関数ﾌｧｲﾙ
define('SYS_COM_FILE', INC_DIR.'incFncCom.php');    // ｼｽﾃﾑ共通関数ﾌｧｲﾙ
define('DATA_OBJ_FILE', CLS_DIR.'DataObject.php');    // ﾃﾞｰﾀｵﾌﾞｼﾞｪｸﾄﾌｧｲﾙ

//////////////////////////////////////////////////////////////////////
// php、htmlファイルパス
//////////////////////////////////////////////////////////////////////
define('COM_TAG', HTM_DIR.'Com'.DS.'_AppHeadTag.html');        // metaタグ等
define('COM_HEADER', HTM_DIR.'Com'.DS.'_AppHeader.html');      // ヘッダー
define('COM_FOOTER', HTM_DIR.'Com'.DS.'_AppFooter.html');      // フッター
define('COM_FORM_HEADER', HTM_DIR.'Com'.DS.'_FormHeader.html');      // フォームヘッダ
define('COM_FORM_FOOTER', HTM_DIR.'Com'.DS.'_FormFooter.html');      // フォームフッタ


//////////////////////////////////////////////////////////////////////
// HTML関連
//////////////////////////////////////////////////////////////////////
define('HTML_SPACE', '&nbsp;');        // ｽﾍﾟｰｽ
define('HTML_SELECTED', 'selected');        // ｾﾚｸﾄ
define('HTML_CHECKED', 'checked');        // ﾁｪｯｸ
define('HTML_DISABLED', 'disabled');        // disable
define('HTML_READONLY', 'readonly');        // disable
define('HTML_CRLF', '\n');        // 改行ｺｰﾄﾞ
define('HTML_ENTER', '<BR>');        // HTML改行ｺｰﾄﾞ
define('HTML_INP_ERR', 'inp_error');        // 入力エラー用CSSクラス名
define('HTML_INP_ERR_COLOR', 'inp_error_color');        // 入力エラー用CSSクラス名

//////////////////////////////////////////////////////////////////////
// ｾｯｼｮﾝ情報用
//////////////////////////////////////////////////////////////////////
// DB関連
define('S_DB_UID', 'S_DB_UID');        // DB:ﾕｰｻﾞID
define('S_DB_PWD', 'S_DB_PWD');        // DB:ﾊﾟｽﾜｰﾄﾞ
define('S_DB_DSN', 'S_DB_DSN');        // DB:DSN
define('S_ERR_FLG', 'S_ERR_FLG');        // ｴﾗｰﾌﾗｸﾞ
define('S_ERR_MSG', 'S_ERR_MSG');        // ｴﾗｰﾒｯｾｰｼﾞ

define('S_DB_MODE_KBN', 'S_DB_MODE_KBN');        // DB操作の区分

// ﾛｸﾞｲﾝ者関連
define('S_LOGIN_US_NM', 'S_LOGIN_US_NM');    // ﾛｸﾞｲﾝﾕｰｻﾞ:氏名
define('S_LOGIN_US_KENGEN', 'S_LOGIN_US_KENGEN');  // ﾛｸﾞｲﾝﾕｰｻﾞ:ユーザー権限
define('S_LOGIN_US_ID', 'S_LOGIN_US_ID');  // ﾛｸﾞｲﾝﾕｰｻﾞ:ユーザーID
define('S_FISCAL_YEAR', 'S_FISCAL_YEAR');  // 対象年度

define('S_POSTED_VALUES', 'S_POSTED_VALUES');        // POST（汎用）
define('S_POSTED_SEARCH', 'S_POSTED_SEARCH');        // POST（検索条件用）
define('S_INP_POSTED', 'S_INP_POSTED');        // POST（入力系画面用）

define('S_SEARCH_SQL_FOREXCEL', 'S_SEARCH_SQL_FOREXCEL');        // エクセル出力用検索結果取得SQL
define('S_SEARCH_SQLPARAMS_FOREXCEL', 'S_SEARCH_SQLPARAMS_FOREXCEL');        // エクセル出力用SQLパラメータ

//マスタメンテナンス用
define('M_KEISU_VALUES', 'M_KEISU_VALUES');       //係数マスタ用
define('M_ROMU_VALUES', 'M_ROMU_VALUES');       //労務単価マスタ用

//////////////////////////////////////////////////////////////////////
// エラーチェック用
//////////////////////////////////////////////////////////////////////
//フォーマット区分
define('FORMKBN_ALL', 1);  // すべて許可
define('FORMKBN_HANKAKU', 2);  // 半角英数（記号無）のみ
define('FORMKBN_HANKAKU_ALL', 3);  // 半角英数（記号含）のみ
define('FORMKBN_ZENKAKU', 4);  // 全角のみ
define('FORMKBN_ALPHABET', 5);  // 半角アルファベットのみ
define('FORMKBN_ALPHABET_ALL', 6);  // アルファベット（全角含）のみ
define('FORMKBN_NUMBER', 7);  // 半角数字のみ
define('FORMKBN_NUMBER_NEGATIVE', 8);  // 半角数字（マイナス含む）
define('FORMKBN_NUMBER_ALL', 9);  // 数字（全角含）のみ
define('FORMKBN_NUMBER_ZENKAKU', 10);  // 数字（全角）のみ
define('FORMKBN_YYYYMMDD', 11);  // 日付（YYYYMMDD)
define('FORMKBN_YYMMDD', 12);  // 日付（YYMMDD)
define('FORMKBN_YYYYMM', 13);  // 日付（YYYYMM)

//////////////////////////////////////////////////////////////////////
// その他
//////////////////////////////////////////////////////////////////////
// 同時ｱｸｾｽ制御関連-制限時間
define('CONCURRENT_TIME_LIMIT', 3000);    // 制限時間(1～59m) 例:1分:100,10分:1000
// define('COMMON_GROUP_ID', 'Z0037500010');    // 共用ID(ｸﾞﾙｰﾌﾟ)
define('WAM_USER_ID', 'HTTP_IV_USER');    // ｻｰﾊﾞｰ変数:WAMのﾛｸﾞｲﾝﾕｰｻﾞ
define('WAM_GROUP_ID', 'HTTP_IV_GROUPS');    // ｻｰﾊﾞｰ変数:WAMのﾛｸﾞｲﾝｸﾞﾙｰﾌﾟ
define('LOG_FILE_FLG', 'LOG_FILE_FLG');    // ﾛｸﾞﾌｧｲﾙ書込ﾌﾗｸﾞ

$define_list_def = array('' => '');    // SELECT空白項目


//禁則文字チェック
define('CHECK_PROHIBITION', 1);    // 使用禁止文字
define('CHECK_SURROGATE_PAIR', 2);    // サロゲートペア
define('CHECK_NOT_SJIS', 4);    // SJISコード以外

// HTMLページング要素
define('PAGE_DISP_COUNT', 5); //ページング要素表示数
define('PAGE_DATA_MAX_COUNT', 100); //1ページあたりの最大表示件数
define('PAGE_DATA_MAX_COUNT_KANREN_KOJI', 20); //1ページあたりの最大表示件数

// 登録更新画面のモード
define('INP_ACTION_MODE_INS', 'INS');
define('INP_ACTION_MODE_UPD', 'UPD');

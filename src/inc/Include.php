<?php
//===================================================================================
//[ｼｽﾃﾑ    ]    -
//[ﾌｧｲﾙ名  ]    Include.php
//[処理内容]    共通phpファイルをまとめてｲﾝｸﾙｰﾄﾞする
//[作成    ]    2019/05/27 E.KINJO    新規作成（ツギラクからコピー）
//[履歴    ]
//===================================================================================

    //////////////////////////////////////////////////
    // 1. ﾌｧｲﾙ読込処理(共通)
    //////////////////////////////////////////////////
    // 1-1. 共通関連ﾌｧｲﾙ読込
    try {
        if ( !include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'incDefine.php') ) {
            throw new Exception ("<p>ファイル読み込みエラー：incDefine.php：終了します");
        }
        if ( !include_once( SESSION_FILE ) ) {
            throw new Exception ("<p>ファイル読み込みエラー：session_set.php：終了します");
        }
        if ( !include_once( COMMON_FILE ) ) {
            throw new Exception ("<p>ファイル読み込みエラー：common.php：終了します");
        }
        if ( !include_once( SYS_COM_FILE ) ) {
            throw new Exception ("<p>ファイル読み込みエラー：fnc_com.php：終了します");
        }
        if ( !include_once( DATA_OBJ_FILE ) ) {
            throw new Exception ("<p>ファイル読み込みエラー：dataobject.php：終了します");
        }
    } catch (Exception $ex) {
        exit($ex->getMessage());
    }

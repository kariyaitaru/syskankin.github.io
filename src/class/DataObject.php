<?php
//===================================================================================
//[ｼｽﾃﾑ    ]-
//[ﾌｧｲﾙ名  ]DataObject.php
//[処理内容]DB関連ｸﾗｽ(ﾃﾞｰﾀｱｸｾｽ系ｵﾌﾞｼﾞｪｸﾄ)
//[作成    ]2019/05/27 E.KINJO新規作成（ツギラクからコピー）
//[履歴    ]
//===================================================================================

//==================================================
// DB接続情報取得ｸﾗｽ
//==================================================
class DBInfo
{

    var $exists; // INFﾌｧｲﾙ存在ﾌﾗｸﾞ(True:有 False:無)
    var $uid; // ﾕｰｻﾞID
    var $pwd; // ﾊﾟｽﾜｰﾄﾞ
    var $dsn; // ﾃﾞｰﾀｿｰｽ名

    //==================================================
    //機  能:初期化処理
    //引  数:なし
    //戻り値:なし
    //==================================================
    function SetInit()
    {
        $this->exists = false;
        $this->uid    = "";
        $this->pwd    = "";
        $this->dsn    = "";
    }

    //==================================================
    //機  能:DB接続情報取得
    //引  数:なし
    //戻り値:なし
    //==================================================
    function GetDBInfo($filepath)
    {

        // 初期化
        $this->Setinit();

        // ﾌｧｲﾙ存在ﾁｪｯｸ
        if (@file_exists($filepath)) {
            // ﾌｧｲﾙｵｰﾌﾟﾝ
            $fp = @fopen($filepath, "r");

            while (!feof($fp)) {
                // 行取得(空白除去)
                $line = str_replace(" ", "", fgets($fp, 128));
                //$line = str_replace(array("\r\n","\r","\n", " "), "", fgets( $fp, 128) );

                $keyw = substr($line, 0, 4);
                switch ($keyw) {
                    case "UID=":
                        $this->uid = str_replace($keyw, "", $line);
                        break;
                    case "PWD=":
                        $this->pwd = str_replace($keyw, "", $line);
                        break;
                    case "DSN=":
                        $this->dsn = str_replace($keyw, "", $line);
                        break;
                    default:
                        break;
                }
            }

            @fclose($fp);
            $this->exists = true;
        } else {
            $this->exists = false;
        }
    }

    //==================================================
    //機  能:各種ﾒﾝﾊﾞ変数取得
    //引  数:なし
    //戻り値:なし
    //==================================================
    function GetUid()
    {
        return $this->uid;
    }
    function GetPwd()
    {
        return $this->pwd;
    }
    function GetDsn()
    {
        return $this->dsn;
    }
    function CheckFile()
    {
        return $this->exists;
    }
}

//==================================================
// DB操作関連ｸﾗｽ
//==================================================
class DataObject
{

    var $errFlg; // ｴﾗｰﾌﾗｸﾞ(True:ｴﾗｰ有 False:ｴﾗｰ無)
    var $err; // ｴﾗｰ配列
    var $errCode; // ｴﾗｰｺｰﾄﾞ
    var $errMessage; // ｴﾗｰﾒｯｾｰｼﾞ
    var $conn; // DB接続ID(ｴﾗｰ時はFalse)
    var $stid; // ｽﾃｰﾄﾒﾝﾄﾊﾝﾄﾞﾙ
    var $recordset; // ﾚｺｰﾄﾞｾｯﾄ

    //==================================================
    //機  能:初期化処理
    //引  数:なし
    //戻り値:なし
    //==================================================
    function SetInit()
    {
        $this->errFlg     = false;
        $this->err        = array();
        $this->errCode    = "";
        $this->errMessage = "";
        $this->conn       = false;
        $this->stid       = "";
        $this->recordset  = array();
        putenv('NLS_LANG=JAPANESE_JAPAN.AL32UTF8');
    }

    //==================================================
    //機  能:ｴﾗｰｾｯﾄ
    //引  数:なし
    //戻り値:なし
    //==================================================
    function SetErr()
    {
        $this->errCode    = $this->err["code"];
        $this->errMessage = $this->err["message"];
    }

    //==================================================
    //機  能:各種ﾒﾝﾊﾞ変数取得
    //引  数:なし
    //戻り値:ｴﾗｰｺｰﾄﾞ
    //==================================================
    function GetErrFlg()
    {
        return $this->errFlg;
    }
    function GetErrCode()
    {
        return $this->errCode;
    }
    function GetErrMessage()
    {
        return $this->errMessage;
    }

    //==================================================
    //機  能:ﾃﾞｽﾄﾗｸﾀ
    //引  数:なし
    //戻り値:なし
    //==================================================
    function Destroy()
    {
        unset($this->errFlg);
        unset($this->err);
        unset($this->errCode);
        unset($this->errMessage);
        unset($this->conn);
        unset($this->stid);
        unset($this->recordset);
    }

    //==================================================
    //機  能:DB接続
    //引  数:ARG1 - ID
    //:ARG2 - PW
    //:ARG3 - DSN
    //戻り値:True:正常 False:異常
    //==================================================
    function DBOpen()
    {

        // 初期化処理
        $this->SetInit();
        // ｾｯｼｮﾝから取得
        $uid = $_SESSION[S_DB_UID];
        $pwd = $_SESSION[S_DB_PWD];
        $dsn = $_SESSION[S_DB_DSN];

        // DB接続
        $this->conn = oci_connect($uid, $pwd, $dsn);
        if (!$this->conn) {
            // ｴﾗｰ処理
            $this->errFlg = true;
            $this->err = oci_error();
            $this->SetErr();
            return false;
        }
        return true;
    }

    //==================================================
    //機  能:DB切断
    //引  数:なし
    //戻り値:True:正常 False:異常
    //==================================================
    function DBClose()
    {
        //ocilogoff($conn);
        @oci_close($this->conn);
        $this->Destroy();
        return true;
    }

    //==================================================
    //機  能:SQL実行(SELECT):ﾚｺｰﾄﾞｾｯﾄ
    //引  数:ARG1 - SQL
    //戻り値:True:正常 False:異常
    //==================================================
    function OpenRecordSet($sql, $sqlParams = null)
    {
        // SQL実行
        $this->stid = oci_parse($this->conn, $sql);
        if (isset($sqlParams)) {
            if (array_values($sqlParams) !== $sqlParams) {
                $sqlParams = array($sqlParams);
            }
            foreach ($sqlParams as $sqlParam) {
                if (!isset($sqlParam["SQL_PARAM_PARAM"])) {
                    continue;
                }
                if (!isset($sqlParam["SQL_PARAM_VAL"])) {
                    $sqlParam['SQL_PARAM_VAL'] = null;
                }
                if (!isset($sqlParam["SQL_PARAM_LEN"])) {
                    // oci_bind_by_name関数の初期値をセット
                    $sqlParam['SQL_PARAM_LEN'] = -1;
                }
                if (!isset($sqlParam["SQL_PARAM_TYPE"])) {
                    // oci_bind_by_name関数の初期値をセット
                    $sqlParam["SQL_PARAM_TYPE"] = SQLT_CHR;
                }
                // パラメータのバインド
                oci_bind_by_name(
                    $this->stid,
                    $sqlParam["SQL_PARAM_PARAM"],
                    $sqlParam["SQL_PARAM_VAL"],
                    $sqlParam["SQL_PARAM_LEN"],
                    $sqlParam["SQL_PARAM_TYPE"]
                );
            }
        }

        $result     = oci_execute($this->stid);
        if (!$result) {
            // ｴﾗｰ処理
            $this->errFlg = true;
            $this->err["code"] = "";
            $this->err["message"] = "SQLエラー：" . $sql;
            $this->SetErr();
            return false;
        }

        // 配列への格納
        $this->SetFetchArray();

        return true;
    }

    //==================================================
    //機  能:SQL実行(SELECT):ﾚｺｰﾄﾞｾｯﾄ
    //引  数:ARG1 - SQL
    //戻り値:True:正常 False:異常
    //==================================================
    function OpenRecordSetNoAuto($sql, $sqlParams = null)
    {

        // SQL実行
        $this->stid = oci_parse($this->conn, $sql);
        if (isset($sqlParams)) {
            if (array_values($sqlParams) !== $sqlParams) {
                $sqlParams = array($sqlParams);
            }
            foreach ($sqlParams as $sqlParam) {
                if (!isset($sqlParam["SQL_PARAM_PARAM"])) {
                    continue;
                }
                if (!isset($sqlParam["SQL_PARAM_VAL"])) {
                    $sqlParam['SQL_PARAM_VAL'] = null;
                }
                if (!isset($sqlParam["SQL_PARAM_LEN"])) {
                    // oci_bind_by_name関数の初期値をセット
                    $sqlParam['SQL_PARAM_LEN'] = -1;
                }
                if (!isset($sqlParam["SQL_PARAM_TYPE"])) {
                    // oci_bind_by_name関数の初期値をセット
                    $sqlParam["SQL_PARAM_TYPE"] = SQLT_CHR;
                }
                // パラメータのバインド
                oci_bind_by_name(
                    $this->stid,
                    $sqlParam["SQL_PARAM_PARAM"],
                    $sqlParam["SQL_PARAM_VAL"],
                    $sqlParam["SQL_PARAM_LEN"],
                    $sqlParam["SQL_PARAM_TYPE"]
                );
            }
        }

        $result     = oci_execute($this->stid, OCI_DEFAULT);
        if (!$result) {
            // ｴﾗｰ処理
            $this->errFlg = true;
            $this->err["code"] = "";
            $this->err["message"] = "SQLエラー：" . $sql;
            $this->SetErr();
            return false;
        }

        // 配列への格納
        $this->SetFetchArray();

        return true;
    }

    //==================================================
    //機  能:ﾚｺｰﾄﾞｾｯﾄの取得(配列への格納)
    //引  数:なし
    //戻り値:なし
    //==================================================
    function SetFetchArray()
    {

        $datacnt = 0; // 要素番号
        $this->recordset  = array();
        while ($row = oci_fetch_array($this->stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
            foreach ($row as $key => $value) {
                $this->recordset[$datacnt][$key] = $value;
            }
            $datacnt++;
        }
    }

    //==================================================
    //機  能:ﾚｺｰﾄﾞｾｯﾄ取得
    //引  数:なし
    //戻り値:なし
    //==================================================
    function GetRecordSet()
    {
        return $this->recordset;
    }

    //==================================================
    //機  能:SQL実行(INSERT/DELETE/UPDATE)
    //引  数:ARG1 - SQL
    //戻り値:True:正常 False:異常
    //==================================================
    function ExecuteNoAuto($sql, $sqlParams = null)
    {

        // SQL実行
        $this->stid = oci_parse($this->conn, $sql);
        //oci_execute( $this->stid, OCI_NO_AUTO_COMMIT );
        if (isset($sqlParams)) {
            if (array_values($sqlParams) !== $sqlParams) {
                $sqlParams = array($sqlParams);
            }
            foreach ($sqlParams as $sqlParam) {
                if (!isset($sqlParam["SQL_PARAM_PARAM"])) {
                    continue;
                }
                if (!isset($sqlParam["SQL_PARAM_VAL"])) {
                    $sqlParam['SQL_PARAM_VAL'] = null;
                }
                if (!isset($sqlParam["SQL_PARAM_LEN"])) {
                    // oci_bind_by_name関数の初期値をセット
                    $sqlParam['SQL_PARAM_LEN'] = -1;
                }
                if (!isset($sqlParam["SQL_PARAM_TYPE"])) {
                    // oci_bind_by_name関数の初期値をセット
                    $sqlParam["SQL_PARAM_TYPE"] = SQLT_CHR;
                }
                // パラメータのバインド
                oci_bind_by_name(
                    $this->stid,
                    $sqlParam["SQL_PARAM_PARAM"],
                    $sqlParam["SQL_PARAM_VAL"],
                    $sqlParam["SQL_PARAM_LEN"],
                    $sqlParam["SQL_PARAM_TYPE"]
                );
            }
        }

        $result = oci_execute($this->stid, OCI_DEFAULT);
        if (!$result) {
            // ﾛｰﾙﾊﾞｯｸ処理
            $this->RollBack();
            // ｴﾗｰ処理
            $this->errFlg = true;
            $this->err["code"] = "";
            $this->err["message"] = "SQLエラー：" . $sql;
            $this->SetErr();
            return false;
        }
        return true;
    }

    //==================================================
    //機  能:SQL実行(INSERT/DELETE/UPDATE)
    //引  数:ARG1 - SQL
    //戻り値:True:正常 False:異常
    //==================================================
    function ExecuteAuto($sql, $sqlParams = null)
    {
        // SQL実行
        $this->stid = oci_parse($this->conn, $sql);
        if (isset($sqlParams)) {
            if (array_values($sqlParams) !== $sqlParams) {
                $sqlParams = array($sqlParams);
            }
            foreach ($sqlParams as $sqlParam) {
                if (!isset($sqlParam["SQL_PARAM_PARAM"])) {
                    continue;
                }
                if (!isset($sqlParam["SQL_PARAM_VAL"])) {
                    $sqlParam['SQL_PARAM_VAL'] = null;
                }
                if (!isset($sqlParam["SQL_PARAM_LEN"])) {
                    // oci_bind_by_name関数の初期値をセット
                    $sqlParam['SQL_PARAM_LEN'] = -1;
                }
                if (!isset($sqlParam["SQL_PARAM_TYPE"])) {
                    // oci_bind_by_name関数の初期値をセット
                    $sqlParam["SQL_PARAM_TYPE"] = SQLT_CHR;
                }
                // パラメータのバインド
                oci_bind_by_name(
                    $this->stid,
                    $sqlParam["SQL_PARAM_PARAM"],
                    $sqlParam["SQL_PARAM_VAL"],
                    $sqlParam["SQL_PARAM_LEN"],
                    $sqlParam["SQL_PARAM_TYPE"]
                );
            }
        }

        $result     = oci_execute($this->stid);
        if (!$result) {
            // ｴﾗｰ処理
            $this->errFlg = true;
            $this->err["code"] = "";
            $this->err["message"] = "SQLエラー：" . $sql;
            $this->SetErr();
            return false;
        }

        return true;
    }

    //==================================================
    //機  能:ｺﾐｯﾄ処理
    //引  数:なし
    //戻り値:True:正常 False:異常
    //==================================================
    function Commit()
    {
        oci_commit($this->conn);
    }

    //==================================================
    //機  能:ﾛｰﾙﾊﾞｯｸ処理
    //引  数:なし
    //戻り値:なし
    //==================================================
    function RollBack()
    {
        oci_rollback($this->conn);
    }
}

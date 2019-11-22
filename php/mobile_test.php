<?php


    // ﾌｧｲﾙ取得
    $contents = GetFileData('../index.html');

    if ( !$contents ) {
        // ｴﾗｰ処理
        fncCom_SetErr("ファイルのオープンに失敗しました。");
        $contents = "";
    }

    echo $contents;
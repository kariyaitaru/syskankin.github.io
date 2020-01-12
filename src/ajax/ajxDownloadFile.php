<?php
$filename=$_GET['filename'];

if(isset($filename)){
    $fullpath = __DIR__ . '/../../tempfiles/' . $filename;
    if( file_exists($fullpath) ){
        header('Content-Type: application/octet-stream');
        header("Cache-Control: public");
        //-- ウェブブラウザが独自にMIMEタイプを判断する処理を抑止する
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: attachment; filename='.$filename.'');
        header('Content-Length: '.filesize($filename));
        header("Content-Transfer-Encoding: binary ");
        //-- keep-aliveを無効にする
        header('Connection: close');
        // ファイルの内容を出力する前に入力バッファの中身をクリアする
        ob_end_clean();
        ob_clean();
        readfile($fullpath);
    } else {
        echo '該当ファイルが存在しません。:' . $fullpath;
    }
}
exit;

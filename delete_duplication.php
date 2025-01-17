<?php


//1. POSTデータ取得


//2. DB接続します
//*** function化する！  *****************
require_once('funcs.php'); //funcs.phpの呼び出し
$pdo = db_conn(); //変数


//３．データ登録SQL作成
$stmt = $pdo->prepare(
    'DELETE FROM gs_db_webcrawl_02
WHERE id NOT IN (
    SELECT MIN(id)
    FROM gs_db_webcrawl_02
    GROUP BY tag_name, title
);'
    );

// 数値の場合 PDO::PARAM_INT
// 文字の場合 PDO::PARAM_STR


$status = $stmt->execute(); //実行

//４．データ登録処理後
if ($status === false) {
    //*** function化する！******\
    $error = $stmt->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {
    //*** function化する！*****************
    header('Location: home.php');
    exit();
}

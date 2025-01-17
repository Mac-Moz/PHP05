<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
        <div class="jumbotron">
            <fieldset>
                 <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="home.php">back_index</a>
                </div>
            </div>
            </fieldset>
        </div>

<?php

// 0. SESSION開始！！
session_start();

//１．関数群の読み込み
require_once('funcs.php');
loginCheck();

//RSS配信先のURLを設定
$rssurl = "https://www.fsa.go.jp/fsaNewsListAll_rss2.xml";
$tag_name = "金融庁_rss";


//セッションの初期化
$xmlurl = curl_init($rssurl);
curl_setopt($xmlurl, CURLOPT_RETURNTRANSFER, true); //戻り値を文字列に

//実行
$contents = curl_exec($xmlurl);

//システムリソースを解放
curl_close($xmlurl);

//XML文字列をｵﾌﾞｼﾞｪｸﾄに代入
$xmlfile = simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NOCDATA);

//タイトルを取得
$mtxml = $xmlfile->channel->title;
$dxml = $xmlfile->channel->description;
$rssch = "<h2>" . $mtxml . "</h2>";
$rssch .= $dxml . "<br /><br /><br />";

//rssフィードを取得
$rssitem = "";
date_default_timezone_set('Asia/Tokyo'); //「php.ini」ファイルで設定されていれば必要ありません
// foreach ($xmlfile->channel->item as $getitem) {
//     $title2 = $getitem->title;
//     $link2 = $getitem->link;
//     $date = $getitem->pubDate; /*rss-ver2.0*/
//     $date = strtotime($date);
//     $date = date("Y年n月j日 G時i分", $date);
//     $description2 = $getitem->description;
//     $description2 = strip_tags($description2, '<a><img><div>');
//     $rssitem .= "<a href = " . $link2 . " target='_blank'>" . $title2 . "</a><br />";
//     $rssitem .= $date . "<br />";
//     $rssitem .= $description2 . "<br /><br />";
// }

// PDOでデータベースに接続
try {
    require_once('funcs.php'); //funcs.phpの呼び出し
    $pdo = db_conn(); //DB接続情報を格納
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // データを挿入
    $stmt = $pdo->prepare("INSERT INTO gs_db_webcrawl_02 (tag_name,title, date, link, image) VALUES (:tag_name,:title, :date, :link, :image)");

    foreach ($xmlfile->channel->item as $getitem) {
        $date = $getitem->pubDate; /*rss-ver2.0*/
        $date = strtotime($date);
        $date = date("Y-n-j", $date);
        $title2 = $getitem->title;
        $link2 = $getitem->link;
        
        $stmt->execute([
            ':tag_name' => $tag_name ?? null, // タイトル
            ':title' => $title2 ?? null, // タイトル
            ':date' => $date ?? null,  // 日付
            ':link' => $link2 ?? null,  // リンク
            ':image' => "./img/noImage.jpg" ?? null // 画像URL
        ]);
    }

    echo "Data inserted successfully.";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}



?> 
</body>
</html>

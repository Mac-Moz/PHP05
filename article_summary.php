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
$id = $_POST['id'];
$link = $_POST['link'];
$image = $_POST['image'];
$date = $_POST['date'];
$title = $_POST['title'];



$apiKey = '*'; // OpenAI APIキーを設定
$url = $link; // まとめる対象のURL

require_once('funcs.php'); //funcs.phpの呼び出し
$result = summarizeUrlContent($url, $apiKey);

if ($result) {

    echo 'id: ' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '<br>';
    echo 'date: ' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '<br>';
    echo '<a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">';
    echo 'link: ' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '</a><br>';
    echo '<p>title: ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</p><br>';
    echo '<img src="' . htmlspecialchars($image, ENT_QUOTES, 'UTF-8') . '" alt="Image"><br>';
    echo '<p>記事サマリ:</p>';
    echo nl2br(htmlspecialchars($result, ENT_QUOTES, 'UTF-8'));
} else {
    echo "エラー: 内容を取得できませんでした。\n";
}



?>
<!DOCTYPE html>
<html lang="ja">

<head>

</head>

<body>

   <header>
     </header>

    <!-- method, action, 各inputのnameを確認してください。  -->
    <form method="POST" action="insert.php">
        <div class="jumbotron">
            <fieldset>
                 <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="home.php">back_index</a>
                </div>
            </div>
            </fieldset>
        </div>
    </form>


    <?php

    // 0. SESSION開始！！
    session_start();

    //１．関数群の読み込み
    require_once('funcs.php');
    loginCheck();

    // 設定
    $apiKey = "*"; // Scrapy CloudのAPIキー
    $projectId = "791112"; // プロジェクトID
    $jobId = "1/4"; // ジョブID
    $tag_name = "luup";


    // APIエンドポイント
    $url = "https://storage.scrapinghub.com/items/$projectId/$jobId";

    // cURLリクエストを初期化
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:"); // 認証
    curl_setopt($ch, CURLOPT_VERBOSE, true); // 詳細なデバッグ情報を有効化
    
    $response = curl_exec($ch);

    if ($response === false) {
        echo "cURL Error: " . curl_error($ch);
    }

    // HTTPステータスコードの取得
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // HTTPステータスコードのエラーチェック
    if ($httpCode != 200) {
        die("Failed to fetch data. HTTP Code: $httpCode");
    }


    // スペース区切りの文字列をJSON配列形式に修正
    $jsonString = '[' . str_replace('} {', '}, {', $response) . ']';
    $jsonString = str_replace('}', '},', $jsonString);
    $jsonString = preg_replace('/,\s*\]/', ']', $jsonString);
    $jsonString = json_decode($jsonString, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("JSON decode error: " . json_last_error_msg());
    }


    echo "<pre>";
    print_r("debag01");
    print_r($jsonString);
    echo "</pre>";


    // PDOでデータベースに接続
    try {
        require_once('funcs.php'); //funcs.phpの呼び出し
        $pdo = db_conn(); //DB接続情報を格納
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // データを挿入
        $stmt = $pdo->prepare("INSERT INTO gs_db_webcrawl_02 (tag_name,title, date, link, image) VALUES (:tag_name,:title, :date, :link, :image)");

        foreach ($jsonString as $item) {
            echo "<pre>";
            print_r("debag02");
            print_r($item["title"]);
            echo "</pre>";
            $stmt->execute([
                ':tag_name' => $tag_name ?? null, // タイトル
                ':title' => $item['title'] ?? null, // タイトル
                ':date' => $item['date'] ?? null,  // 日付
                ':link' => $item['link'] ?? null,  // リンク
                ':image' => $item['image'] ?? null // 画像URL
            ]);
        }

        echo "Data inserted successfully.";

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }

    ?>

 
</body>

</html>
<?php
//XSS対応（ echoする場所で使用！）
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function db_conn()
{
    try {
        $db_name = 'gs_db_webcrawl'; //データベース名
        $db_id = 'root'; //アカウント名
        $db_pw = ''; //パスワード：MAMPは'root'
        $db_host = 'localhost'; //DBホスト
        $pdo = new PDO('mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host, $db_id, $db_pw);
        return $pdo;
    } catch (PDOException $e) {
        exit('DB Connection Error:' . $e->getMessage());
    }
}
//DB接続関数：db_conn() 
//※関数を作成し、内容をreturnさせる。
//※ DBname等、今回の授業に合わせる。


//SQLエラー
function sql_error($stmt)
{
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit('SQLError:' . $error[2]);
}

//リダイレクト
function redirect($file_name)
{
    header('Location: ' . $file_name);
    exit();
}


// ログインチェク処理 loginCheck()

function loginCheck()
{
    if (!isset($_SESSION["chk_ssid"]) || $_SESSION['chk_ssid'] != session_id()) {
        // ログインを経由していない場合 または　ログインactとsessionIDが異なる場合は　EXIT処理("!")
        exit("LOGIN ERROR");
    } else {
        //sessionIDを再生成する
        session_regenerate_id(true);
        $_SESSION["chk_ssid"] = session_id();
    }

}
//AIによる記事内容のサマリ作成

function summarizeUrlContent($url, $apiKey) {
    // ChatGPTに送信するプロンプト
    $prompt = "以下のサイトの内容を日本語で箇条書きで簡潔にまとめてください。: $url";

    // OpenAI APIエンドポイント
    $endpoint = 'https://api.openai.com/v1/chat/completions';

    // リクエストデータの準備
    $data = [
        "model" => "gpt-3.5-turbo", // 使用するモデル
        "messages" => [
            [
                "role" => "user",
                "content" => $prompt
            ]
        ],
        "max_tokens" => 500, // 応答のトークン上限
        "temperature" => 0.7 // 応答のランダム性
    ];

    // cURLリクエストの設定
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // API呼び出し
    $response = curl_exec($ch);
    curl_close($ch);

    // レスポンスを処理
    if ($response === false) {
        return false; // リクエスト失敗
    }

    // JSONをデコード
    $result = json_decode($response, true);

    // 応答内容を取得
    if (isset($result['choices'][0]['message']['content'])) {
        return $result['choices'][0]['message']['content'];
    }

    return false; // 必要なデータが応答に含まれていない場合
}





//リダイレクト関数: redirect($file_name)

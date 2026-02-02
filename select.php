<?php
// エラー詳細を表示
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("funcs.php");

$keyword = $_GET["keyword"] ?? "";
$news_api_key = "f84138e58d7443be9ca4e20bf07f4132"; // ★ここに正確に貼り付け
$openai_key   = "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";   // ★ここに正確に貼り付け

// 1. NewsAPIへの接続 (cURLを使用)
if (!empty($keyword)) {
    $url = "https://newsapi.org/v2/everything?q=" . urlencode($keyword) . "&sortBy=relevancy&pageSize=10&apiKey=" . $news_api_key;
} else {
    $url = "https://newsapi.org/v2/top-headlines?country=us&apiKey=" . $news_api_key;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// NewsAPIに必須の「User-Agent」ヘッダーを追加（これがないと拒否されます）
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "User-Agent: NewsExplorer/1.0",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$news_res_raw = curl_exec($ch);
$news_error = curl_error($ch);
curl_close($ch);

$news_data = json_decode($news_res_raw, true);

$articles = [];
$titles = "";

// ニュースデータの整理
if (isset($news_data['status']) && $news_data['status'] === "ok") {
    foreach ($news_data['articles'] as $a) {
        $articles[] = [
            "title" => $a['title'],
            "url" => $a['url'],
            "source" => $a['source']['name']
        ];
        $titles .= $a['title'] . ". ";
    }
} else {
    // 具体的エラー内容を出す
    $msg = isset($news_data['message']) ? $news_data['message'] : ($news_error ?: "Unknown Connection Error");
    $articles[] = ["title" => "【接続エラー】: " . $msg, "url" => "#"];
}

// 2. OpenAIによる国判定 (すでにcURLを使っているのでそのまま)
$related = [];
if ($titles != "" && !empty($openai_key)) {
    $post_data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "Return ONLY a JSON array of ISO 3-letter country codes related to: " . $titles]
        ]
    ];
    $ch_ai = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch_ai, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_ai, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $openai_key"
    ]);
    curl_setopt($ch_ai, CURLOPT_POST, true);
    curl_setopt($ch_ai, CURLOPT_POSTFIELDS, json_encode($post_data));
    $ai_res_raw = curl_exec($ch_ai);
    $ai_res = json_decode($ai_res_raw, true);
    curl_close($ch_ai);
    
    $content = $ai_res['choices'][0]['message']['content'] ?? "[]";
    preg_match_all('/[A-Z]{3}/', $content, $matches);
    $related = array_unique($matches[0]);
}

header('Content-Type: application/json');
echo json_encode([
    "country" => $keyword ?: "Global",
    "articles" => $articles,
    "related_codes" => $related
]);
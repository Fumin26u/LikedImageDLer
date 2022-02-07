<?php
require_once('commonlib.php');
require_once($home . "../../vendor/autoload.php");
use Abraham\TwitterOAuth\TwitterOAuth;

// APIキー、トークンの設定
function getTweets($id, $st_time, $ed_time) {
    v($st_time);

    // ツイートの最大取得件数(MAX200)
    $count = 150;

    // APIキーとトークン
    include_once('../../apikey.php');

    // APIキーとトークンを用いてTwitterOAuthに接続
    $connection = new TwitterOAuth($API_KEY, $API_KEY_SECRET, $ACCESS_TOKEN, $ACCESS_TOKEN_SECRET);

    
    // 「いいね」ツイート一覧のエンドポイント(URL)
    $endPoint = 'favorites/list';

    // APIv2の場合
    // $endPoint = 'https://api.twitter.com/2/users/' . $id . '/liked_tweets';
    // $connection->setApiVersion('2');

    // Twitter ID(数値)を取得し、いいねのエンドポイント(URL)を代入
    $account = $id;
    $point = $endPoint;

    // 「いいね」したツイート一覧を取得
    $likes_tweet_list = $connection->get($point, ['screen_name' => $account, 'count' => $count]);

    // echo '<pre>';
    // var_dump($likes_tweet_list);
    // echo '</pre>';

    // GETで取得した日付のフォーマット
    $st_getTime = date('Y-m-d H:i:s', strtotime((string) $st_time));
    $ed_getTime = date('Y-m-d H:i:s', strtotime((string) $ed_time));

    $likes = [];
    $queue = [];
    // キューにツイートを1つずつ挿入
    foreach ($likes_tweet_list as $l) {

        // そのツイートの投稿日時を取得
        $posted_date = date('Y-m-d H:i:s', strtotime((string) $l->created_at));

        // 投稿日時がGETで取得した日付より古い場合、キューへの挿入を終了
        if ($posted_date < $st_getTime) break;
        // 投稿日時がGETで取得した日付より新しい場合、キューに挿入しない
        if ($posted_date > $ed_getTime) continue;

        // 画像付きツイートでない場合、キューに挿入しない
        if (!isset($l->extended_entities)) continue;
        $queue['post_time'] = $posted_date;
        $queue['user'] = $l->user->name;
        $text = $l->text;
        // $queue['text'] = substr($text, 0, strcspn($text, 'https://t.co/'));
        $queue['text'] = substr($text, 0, -24);
        $queue['images'] = [];
        $queue['url'] = $l->extended_entities->media[0]->url;

        // 画像は複数枚の可能性があるので配列に挿入
        foreach ($l->extended_entities->media as $m) {
            $queue['images'][] = $m->media_url_https;;
        }

        // キューのデータを一覧に追加
        array_push($likes, $queue);

    }

    return $likes;
}



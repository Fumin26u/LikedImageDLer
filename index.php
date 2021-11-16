<?php
require ('vendor/autoload.php');
require ('apiset.php');

// URL引数idが空だった場合、初期表示にする
if (isset($_GET['id']) && $_GET['id'] == '') {
  header('../', true, 303);
  exit;
}

if (isset($_GET['id'])) {
  // 日付を英語の日付にフォーマット
  // $time = date('l F j h:i:s yyyy',  $_GET['time']);
  // var_dump($time);
  $likes = getTweets($_GET['id']);
}

// var_dump($_GET);

// ページタイトルの設定
$title = isset($_GET['id']) ? 'ID: ' . $_GET['id'] . 'のいいねツイート一覧' : 'いいねツイート取得システム';

// echo('<pre>');
// var_dump($likes);
// echo('</pre>');
?>
<!DOCTYPE html>
<head>
<title><?= $title ?></title> 
<style>
main {
  margin: 0 auto;
  max-width: 1280px;
}

input {
  padding: 8px 12px;
}

img {
  max-width: 240px;
  display: inline-block;
}

tr {
  display: table-row;
  margin:  2em 0;
  padding: 1em 0;
  border-bottom: 2px solid black;
}
</style>
</head>
<body>
<main>
  <h1>いいねした画像一覧</h1>
  <p>以下の入力欄に取得したいユーザーのTwitter IDと、いつまでの投稿を取得したいかを、数値で入力してください。</p>
  <small>数値のTwitter IDは、<a href="https://idtwi.com/">idtwi</a>などから検索できます。</small>
  <form action="<?= $_SERVER['PHP_SELF'] ?>" method="GET">
    <input type="datetime-local" name="time">
    <input type="number" name="id" value="<?= $_GET['id'] ?>">
    <input type="submit">
  </form>
  <?php if (isset($likes)) { ?>
  <table>
    <tbody>
      <tr>
        <th>ツイート時間</th>
        <th>ツイート者</th>
        <th>ツイート内容</th>
        <th>ツイート画像</th>
      </tr>
      <?php foreach($likes as $l) { ?>
        <tr>
          <td><?= $l['post_time'] ?></td>
          <td><?= $l['user'] ?></td>
          <td><?= $l['text'] ?></td>
          <td>
            <?php foreach($l['images'] as $i) { ?>
              <img src="<?= $i ?>" alt="">
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } ?>
</main>
</body>

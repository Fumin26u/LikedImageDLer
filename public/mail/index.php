<?php
$home = "../";

use Mail\SupportMail;

require_once($home . '../commonlib.php');
require_once($home . '../vendor/autoload.php');
$msg = [];

// メール送信
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['cToken'] !== $_POST['cToken']) {

        $msg[] = '不正なアクセスが行われました';

    } else {

        $sent_mail = new SupportMail($_POST);
        $is_sent_mail = $sent_mail->SendSupportMail();
   
        if ($is_sent_mail) {
            $msg[] = 'メールを送信しました。';
        } else {
            $msg[] = 'メール送信に失敗しました。お手数ですが、時間を置いて再度お試しいただけますようよろしくお願いします。';
        }
    }
}

$cToken = bin2hex(random_bytes(32));
$_SESSION['cToken'] = $cToken;

$title = "お問い合わせ | TwimageDLer";
$canonical = "https://imagedler.com/mail/";

// 名前のValueの設定
$name = '';
if (isset($_SESSION['user_name'])) $name = h($_SESSION['user_name']);
if (isset($_POST['name'])) $name = h($_POST['name']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?></title>
<?php include_once $home . '../gtag.inc'; ?>
<link rel="stylesheet" href="mail.css">
<link rel="icon" href="<?= $home ?>favicon.png">
<link rel="canonical" href="<?= $canonical ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="TwimageDLer">
<meta property="og:url" content="<?= $canonical ?>">
<meta property="og:title" content="<?= $title ?>">
<meta property="og:image" content="<?= $home ?>ogpimage.png">
</head>
<body>
<?php include_once($home . '../header.php') ?>
<main>
<div class="message">
    <?php foreach ($msg as $m) { ?>
        <p><?= $m ?></p>
    <?php } ?>
</div>
<h2>お問い合わせ</h2>
<p>質問・ご要望などございましたら、以下のフォームからお願いします。</p>
<p><em>*</em>は必須入力です。</p>
<form action="<?= h($_SERVER['PHP_SELF']) ?>" method="POST">
    <dl class="form_list">
        <div>
            <dt>フォーム内容</dt>
            <dd>
                <input type="radio" name="type" value="質問" id="question" <?= (isset($_POST['type']) && $_POST['type'] == "質問") || !isset($_POST['type']) ? 'checked' : '' ?>>
                <label for="question">質問</label>
                <input type="radio" name="type" value="ご要望" id="suggestion" <?= isset($_POST['type']) && $_POST['type'] == "ご要望" ? 'checked' : '' ?>>
                <label for="suggestion">ご要望</label>
                <input type="radio" name="type" value="その他" id="others" <?= isset($_POST['type']) && $_POST['type'] == "その他" ? 'checked' : '' ?>>
                <label for="others">その他</label>
            </dd>
        </div>
        <div>
            <dt>メールアドレス</dt>
            <dd>
                <input 
                    type="text"
                    name="email"
                    value="<?= isset($_POST['email']) ? h($_POST['email']) : '' ?>"
                    autocomplete="email"
                >
            </dd>
        </div>
        <div>
            <dt>名前<em>*</em></dt>
            <dd>
                <input 
                    type="text"
                    name="name"
                    value="<?= $name ?>"
                    autocomplete="name"
                >
            </dd>
        </div>
        <div>
            <dt>お問い合わせ内容<em>*</em></dt>
            <dd><textarea name="content"><?= isset($_POST['content']) ? h($_POST['content']) : '' ?></textarea></dd>
        </div>
    </dl>
    <input type="hidden" name="cToken" value="<?= $cToken ?>">
    <!-- <input type="submit" value="送信"> -->
</form>
</main>
<?php include_once($home . '../footer.php') ?>
</body>

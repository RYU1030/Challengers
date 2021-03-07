<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is maintenance / demo mode via the "down" command we
| will require this file so that any prerendered template can be shown
| instead of starting the framework, which could cause an exception.
|
*/

#if (file_exists(__DIR__.'/../storage/framework/maintenance.php')) {
#    require __DIR__.'/../storage/framework/maintenance.php';
#}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

#$app = require_once __DIR__.'/../bootstrap/app.php';

#$kernel = $app->make(Kernel::class);

#$response = tap($kernel->handle(
#    $request = Request::capture()
#))->send();

#$kernel->terminate($request, $response);

#<?php

// メッセージを保存するファイルのパス設定
define( 'FILENAME', './message.txt');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();

//WeatherAPI
function get_json( $type = null ){
    $city = "Gifu-shi,jp";
    $appid = "26ee9374f81bcbeee9a5aaba977e7a3c";
    $url = "http://api.openweathermap.org/data/2.5/weather?q=" . $city . "&units=metric&APPID=" . $appid;
  
    $json = @file_get_contents( $url );
    $json = mb_convert_encoding( $json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN' );
    $json_decode = json_decode( $json );
  
    //現在の天気
    if( $type  === "weather" ):
      $out = $json_decode->weather[0]->main;
  
    //現在の天気アイコン
    elseif( $type === "icon" ):
      $out = "<img src='https://openweathermap.org/img/wn/" . $json_decode->weather[0]->icon . "@2x.png' width='50' height='50'>";


    //現在の気温
    elseif( $type  === "temp" ):
      $out = $json_decode->main->temp;
  
    //パラメータがないときは配列を出力
    else:
      $out = $json_decode;
  
    endif;
  
    return $out;
}

if( !empty($_POST['btn_submit']) ) {
    
    // 表示名の入力チェック
    if( empty($_POST['view_name']) ) {
        $error_message[] = '表示名を入力してください。';
    } else {
        $clean['view_name'] = htmlspecialchars( $_POST['view_name'], ENT_QUOTES);
    }
    
    // メッセージの入力チェック
    if( empty($_POST['message']) ) {
        $error_message[] = 'ひと言メッセージを入力してください。';
    } else {
        $clean['message'] = htmlspecialchars( $_POST['message'], ENT_QUOTES);
        $clean['message'] = preg_replace( '/\\r\\n|\\n|\\r/', '<br>', $clean['message']);
    }

    if( empty($error_message) ) {

        // データベースに接続
        $mysqli = new mysqli('us-cdbr-east-03.cleardb.com', 'bc964b2e2b3a58', '5aa89514', 'heroku_d7d09144238a87b');
        #$mysqli = new mysqli('db', 'phper', 'secret', 'laravel_local');
        
        // 接続エラーの確認
        if( $mysqli->connect_errno ) {
            $error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
        } else {

            // 文字コード設定
            $mysqli->set_charset('utf8');

            $sql = "set character_set_clients=utf8";

            // 書き込み日時を取得
            $now_date = date("Y-m-d H:i:s");
            
            // データを登録するSQL作成
            $sql = "INSERT INTO message (view_name, message, post_date) VALUES ( '$clean[view_name]', '$clean[message]', '$now_date')";
            
            // データを登録
            $res = $mysqli->query($sql);
        
            if( $res ) {
                $success_message = 'メッセージを書き込みました。';
            } else {
                $error_message[] = '書き込みに失敗しました。';
            }
        
            // データベースの接続を閉じる
            $mysqli->close();
        }
    }
}

// データベースに接続
$mysqli = new mysqli('us-cdbr-east-03.cleardb.com', 'bc964b2e2b3a58', '5aa89514', 'heroku_d7d09144238a87b');
#$mysqli = new mysqli('db', 'phper', 'secret', 'laravel_local');
// 接続エラーの確認
if( $mysqli->connect_errno ) {
    $error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {

    $mysqli->set_charset('utf8');
    
    $sql = "set character_set_results=utf8";
    $sql = "SELECT view_name,message,post_date FROM message ORDER BY post_date DESC";
    $res = $mysqli->query($sql);

    if( $res ) {
        $message_array = $res->fetch_all(MYSQLI_ASSOC);
    }

    $mysqli->close();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>よっぴーの掲示板</title>
<style>

/*------------------------------
 Reset Style
 
------------------------------*/
html, body, div, span, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
abbr, address, cite, code,
del, dfn, em, img, ins, kbd, q, samp,
small, strong, sub, sup, var,
b, i,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, figcaption, figure,
footer, header, hgroup, menu, nav, section, summary,
time, mark, audio, video {
    margin:0;
    padding:0;
    border:0;
    outline:0;
    font-size:100%;
    vertical-align:baseline;
    background:transparent;
}

body {
    line-height:1;
}

article,aside,details,figcaption,figure,
footer,hgroup,menu,nav,section {
    display:block;
}

header {
    display: flex;
    align-items: center;
}

nav ul {
    list-style:none;
    display: flex;
    margin: 0 0 0 auto;
}

blockquote, q {
    quotes:none;
}

blockquote:before, blockquote:after,
q:before, q:after {
    content:'';
    content:none;
}

a {
    margin:0;
    padding:0;
    font-size:100%;
    vertical-align:baseline;
    background:transparent;
}

/* change colours to suit your needs */
ins {
    background-color:#ff9;
    color:#000;
    text-decoration:none;
}

/* change colours to suit your needs */
mark {
    background-color:#ff9;
    color:#000;
    font-style:italic;
    font-weight:bold;
}

del {
    text-decoration: line-through;
}

abbr[title], dfn[title] {
    border-bottom:1px dotted;
    cursor:help;
}

table {
    border-collapse:collapse;
    border-spacing:0;
}

hr {
    display:block;
    height:1px;
    border:0;
    border-top:1px solid #cccccc;
    margin:1em 0;
    padding:0;
}

input, select {
    vertical-align:middle;
}

/*------------------------------
Common Style
------------------------------*/
body {
    padding: 30px;
    font-size: 100%;
    font-family:'ヒラギノ角ゴ Pro W3','Hiragino Kaku Gothic Pro','メイリオ',Meiryo,'ＭＳ Ｐゴシック',sans-serif;
    color: #222;
    background: #f7f7f7;
}

a {
    color: #4b4b4b;
    text-decoration: none;
}

li {
    margin: 0 0 0 15px;
    font-size: 14px;
}

ul {
    list-style: none;
    margin: 0;
}

a:hover {
    text-decoration: underline;
}

.wrapper {
    display: flex;
    margin: 0 auto 50px;
    padding: 0 20px;
    max-width: 1200px;
    align-items: flex-start;
}

h1 {
    margin-bottom: 30px;
    font-size: 100%;
    color: #222;
    text-align: center;
}

nav {
   margin: 0 0 0 auto;
}

/*-----------------------------------
入力エリア
-----------------------------------*/

label {
    display: block;
    margin-bottom: 7px;
    font-size: 86%;
}

input[type="text"],
textarea {
    margin-bottom: 20px;
    padding: 10px;
    font-size: 86%;
    border: 1px solid #ddd;
    border-radius: 3px;
    background: #fff;
}

input[type="text"] {
    width: 200px;
}
textarea {
    width: 50%;
    max-width: 50%;
    height: 70px;
}
input[type="submit"] {
    appearance: none;
    -webkit-appearance: none;
    padding: 10px 20px;
    color: #fff;
    font-size: 86%;
    line-height: 1.0em;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    background-color: #37a1e5;
}
input[type=submit]:hover,
button:hover {
    background-color: #2392d8;
}

hr {
    margin: 20px 0;
    padding: 0;
}

.success_message {
    margin-bottom: 20px;
    padding: 10px;
    color: #48b400;
    border-radius: 10px;
    border: 1px solid #4dc100;
}

.error_message {
    margin-bottom: 20px;
    padding: 10px;
    color: #ef072d;
    list-style-type: none;
    border-radius: 10px;
    border: 1px solid #ff5f79;
}

.success_message,
.error_message li {
    font-size: 86%;
    line-height: 1.6em;
}


/*-----------------------------------
掲示板エリア
-----------------------------------*/

article {
    margin-top: 20px;
    padding: 20px;
    border-radius: 10px;
    background: #fff;
}
article.reply {
    position: relative;
    margin-top: 15px;
    margin-left: 30px;
}
article.reply::before {
    position: absolute;
    top: -10px;
    left: 20px;
    display: block;
    content: "";
    border-top: none;
    border-left: 7px solid #f7f7f7;
    border-right: 7px solid #f7f7f7;
    border-bottom: 10px solid #fff;
}
    .info {
        margin-bottom: 10px;
    }
    .info h2 {
        display: inline-block;
        margin-right: 10px;
        color: #222;
        line-height: 1.6em;
        font-size: 86%;
    }
    .info time {
        color: #999;
        line-height: 1.6em;
        font-size: 72%;
    }
    article p {
        color: #555;
        font-size: 86%;
        line-height: 1.6em;
    }

@media only screen and (max-width: 1000px) {

    body {
        padding: 30px 5%;
    }

    input[type="text"] {
        width: 100%;
    }
    textarea {
        width: 100%;
        max-width: 100%;
        height: 70px;
    }
}

.wrapp {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: flex-start;
      border: 1px solid red;
      width: 700px;
    }

    .wrapp div {
      display: flex;
      justify-content: space-between;
      border: 1px solid black;
      padding: 10px;
      margin: 10px;
      width: 660px;
    }
</style>

<header>
    <h1>
        <a href="/">よっぴーの掲示板</a>
    </h1>
    <nav class="pc-nav">
        <ul>
            <li><a href="#">ABOUT</a></li>
            <li><a href="#">SERVICE</a></li>
            <li><a href="#">COMPANY</a></li>
            <?php $day = new DateTime(); ?>
            <li><?php echo $day->format('Y-m-d'); ?></li>
        </ul>   
    </nav>

    <nav class="pc-nav">
        <ul>Aichi</ul> 
        <ul>
            <?php echo get_json("icon"); ?>
        </ul>     
        <ul>
            <?php echo get_json("weather"); ?>
        </ul>
    </nav>

</header>

</head>
<body>

<?php if( !empty($success_message) ): ?>
    <p class="success_message"><?php echo $success_message; ?></p>
<?php endif; ?>
<?php if( !empty($error_message) ): ?>
    <ul class="error_message">
        <?php foreach( $error_message as $value ): ?>
            <li>・<?php echo $value; ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<form method="post">
    <div>
        <label for="view_name">表示名</label>
        <input id="view_name" type="text" name="view_name" value="">
    </div>
    <div>
        <label for="message">ひと言メッセージ</label>
        <textarea id="message" name="message"></textarea>
    </div>
    <input type="submit" name="btn_submit" value="書き込む">
</form>

<!-- 以下広告（GMO） -->
<a href="https://px.a8.net/svt/ejp?a8mat=2BW2PJ+3GFM7M+50+2HZO35" rel="nofollow">
<img border="0" width="468" height="60" alt="" src="https://www23.a8.net/svt/bgt?aid=140904631209&wid=001&eno=01&mid=s00000000018015115000&mc=1"></a>
<img border="0" width="1" height="1" src="https://www18.a8.net/0.gif?a8mat=2BW2PJ+3GFM7M+50+2HZO35" alt="">

<hr>
<section>
<?php if( !empty($message_array) ){ ?>
<?php foreach( $message_array as $value ){ ?>
<article>
    <div class="info">
        <h2><?php echo $value['view_name']; ?></h2>
        <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
    </div>
    <p><?php echo $value['message']; ?></p>
</article>
<?php } ?>
<?php } ?>
</section>

<!-- 以下広告（ベースフード） -->
<a href="https://px.a8.net/svt/ejp?a8mat=3H5UZO+1SBLE+41FU+639IP" rel="nofollow">
<img border="0" width="468" height="60" alt="" src="https://www21.a8.net/svt/bgt?aid=210225444003&wid=001&eno=01&mid=s00000018849001023000&mc=1"></a>
<img border="0" width="1" height="1" src="https://www19.a8.net/0.gif?a8mat=3H5UZO+1SBLE+41FU+639IP" alt="">

</body>
</html>

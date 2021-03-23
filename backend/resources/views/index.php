<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="{{ asset('/resources/css/top.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="header-left">
                <p class="challengers-logo">Challengers</p>
                <div class="search-box">
                    <input type="text" class="key-word-input">
                </div>
            </div>
            <div class="header-right">
                <p class="register-and-login">
                    <a href="register-button">会員登録</a>
                </p>
                <p class="register-and-login">
                    <a href="login-button">ログイン</a>
                </p>
            </div>
        </div>
    </header>
    <main>
        <div class="top-message-container">
            <h1 class="top-message">Share your everyday Challenges.</h1>
            <p class="app-concept">Challengersは、ADHDやASDのような特性を持った人々が、日々の「挑戦」を乗り越えるため<br>実践している工夫やノウハウを共有するコミュニティサービスです。:)</p>
        </div>
        <div class="trended-articles">
            <div class="trended-articles-outer">
                <h1 class="trended-articles-header">急上昇記事</h1>
                <div class="trended-articles-each">
                    <div class="written-by-and-at">
                        <p>投稿者名</p>
                        <p>投稿日時</p>
                    </div>
                    <p class="article-title">タイトル</p>
                    <p class="number-of-likes">Nいいね</p>
                </div>
            </div>
        </div>
        <div class="popular-articles">
            <div class="popular-articles-outer">
                <h1 class="popular-articles-header">人気の記事</h1>
                <div class="popular-articles-each">
                    <div class="written-by-and-at">
                        <p>投稿者名</p>
                        <p>投稿日時</p>
                    </div>
                    <p class="article-title">タイトル</p>
                    <p class="number-of-likes">Nいいね</p>
                </div>
            </div>
        </div>
        <div class="advertisement-top">
        </div>
    </main>
    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <p class="challengers-logo">Challengers</p>
                <p class="top-message-footer">Share your everyday Challenges.</p>
                <p><i></i>Challengers 2021</p>
            </div>
            <div class="footer-right">
                <ul class="site-link">
                    <li>フッターメニュー</li>
                    <li>フッターメニュー</li>
                    <li>フッターメニュー</li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>

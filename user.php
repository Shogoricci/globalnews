<!DOCTYPE html>
<html lang="ja">
<head>
 <style>
    /* 全体設定 */
    body, html {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: #000;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* 背景の世界地図アニメーション */
    .map-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 200%; /* ループ用に2倍の幅 */
        height: 100%;
        background-image: url('https://www.transparenttextures.com/patterns/world-map.png');
        background-repeat: repeat-x;
        background-size: contain;
        opacity: 0.3;
        filter: invert(1) sepia(1) saturate(5) hue-rotate(140deg); /* ネオンブルー化 */
        animation: slide 60s linear infinite;
        z-index: -1;
    }

    @keyframes slide {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
    }

    /* ログイン・登録ボックスのネオンデザイン */
    .glass-panel {
        width: 380px;
        padding: 40px;
        background: rgba(0, 5, 10, 0.85);
        backdrop-filter: blur(10px);
        border: 2px solid #00f2ff;
        border-radius: 25px;
        box-shadow: 0 0 25px rgba(0, 242, 255, 0.6);
        text-align: center;
    }

    h2 {
        color: #00f2ff;
        text-shadow: 0 0 10px #00f2ff;
        margin-bottom: 30px;
        font-size: 28px;
        letter-spacing: 2px;
    }

    .input-group {
        margin-bottom: 20px;
        text-align: left;
    }

    input {
        width: 100%;
        padding: 12px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid #00f2ff;
        border-radius: 5px;
        color: #fff;
        box-sizing: border-box;
        outline: none;
    }

    input:focus {
        box-shadow: 0 0 10px #00f2ff;
    }

    /* ログイン・登録ボタン */
    button {
        width: 100%;
        padding: 15px;
        background: #00f2ff;
        border: none;
        border-radius: 5px;
        color: #000;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 0 15px #00f2ff;
        margin-top: 10px;
    }

    button:hover {
        background: #00c2cc;
        box-shadow: 0 0 25px #00f2ff;
    }

    .link-text {
        margin-top: 20px;
        display: block;
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        opacity: 0.8;
    }

    .link-text:hover {
        opacity: 1;
        text-decoration: underline;
    }
</style>
</head>
<body>
    <div class="map-bg"></div>

    <div class="glass-panel">
        <h2>Join the Globe</h2>
        <form action="user_insert.php" method="post">
            <div class="input-group">
                <input type="text" name="username" placeholder="Choose Username" required>
            </div>
            <div class="input-group">
                <input type="password" name="lpw" placeholder="Create Password" required>
            </div>
            <div class="input-group">
                <input type="text" name="favorite_theme" placeholder="Favorite Theme (e.g. Technology)">
            </div>
            <button type="submit">CREATE ACCOUNT</button>
            <a href="login.php" class="link-text">Already have an account? Login</a>
        </form>
    </div>
</body>
</html>
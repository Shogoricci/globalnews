<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Login - News Globe</title>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; background: radial-gradient(circle, #001a33 0%, #000 100%); display: flex; justify-content: center; align-items: center; font-family: 'Segoe UI', sans-serif; }
        /* 回転する地球儀の背景 */
        .globe-container { position: fixed; width: 600px; height: 600px; border-radius: 50%; box-shadow: inset 0 0 50px #00f2ff, 0 0 50px rgba(0, 242, 255, 0.2); z-index: -1; overflow: hidden; opacity: 0.6; }
        .globe-map { width: 200%; height: 100%; background: url('https://www.transparenttextures.com/patterns/world-map.png') repeat-x; background-size: contain; filter: invert(1) brightness(1.5) sepia(1) hue-rotate(150deg); animation: rotateGlobe 30s linear infinite; }
        @keyframes rotateGlobe { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        
        .login-box { width: 350px; padding: 40px; background: rgba(0, 10, 20, 0.7); backdrop-filter: blur(15px); border: 1px solid rgba(0, 242, 255, 0.5); border-radius: 30px; box-shadow: 0 0 30px rgba(0, 242, 255, 0.3); text-align: center; color: white; }
        h2 { color: #00f2ff; text-shadow: 0 0 10px #00f2ff; letter-spacing: 3px; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: rgba(255,255,255,0.1); border: 1px solid #00f2ff; border-radius: 10px; color: white; outline: none; box-sizing: border-box; }
        button { width: 100%; padding: 15px; margin-top: 20px; background: #00f2ff; border: none; border-radius: 10px; color: #000; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 0 15px #00f2ff; }
        button:hover { box-shadow: 0 0 30px #00f2ff; transform: scale(1.02); }
        .link { margin-top: 20px; display: block; color: #aaa; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
    <div class="globe-container"><div class="globe-map"></div></div>
    <div class="login-box">
        <h2>GLOBE LOGIN</h2>
        <form action="login_act.php" method="post">
            <input type="text" name="lid" placeholder="Username" required>
            <input type="password" name="lpw" placeholder="Password" required>
            <button type="submit">ENTER SYSTEM</button>
            <a href="user.php" class="link">Create New Account</a>
        </form>
    </div>
</body>
</html>
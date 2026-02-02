<?php
// エラー表示（デバッグ用）
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("funcs.php");
loginCheck(); 
$pdo = db_conn();

// 1. ユーザーIDを取得（お気に入り機能用）
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
$stmt->bindValue(':username', $_SESSION["username"], PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch();
$u_id = ($user) ? $user['id'] : 0;

// 2. 登録済みのお気に入りテーマを読み込む
$favs = [];
try {
    $stmt = $pdo->prepare("SELECT theme_name FROM favorites WHERE user_id = :uid");
    $stmt->bindValue(':uid', $u_id, PDO::PARAM_INT);
    $stmt->execute();
    $favs = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $favs = [];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Daily News Globe</title>
    <style>
        body { margin: 0; background: #000; color: white; overflow: hidden; font-family: 'Segoe UI', sans-serif; }
        #map { width: 100vw; height: 100vh; background: #000; }
        
        /* 検索バー UI */
        .search-container {
            position: absolute; top: 20px; left: 50%; transform: translateX(-50%);
            z-index: 1000; display: flex; gap: 10px; background: rgba(0, 15, 30, 0.85);
            padding: 12px 20px; border-radius: 50px; border: 1px solid #00f2ff;
            box-shadow: 0 0 20px rgba(0, 242, 255, 0.5); backdrop-filter: blur(10px);
        }
        #search-input { background: transparent; border: none; color: white; width: 220px; outline: none; font-size: 16px; }
        #fav-select { background: #001a33; color: #00f2ff; border: 1px solid #00f2ff; border-radius: 20px; padding: 5px 10px; outline: none; cursor: pointer; }
        .search-btn { background: #00f2ff; border: none; color: #000; padding: 6px 20px; border-radius: 20px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .search-btn:hover { background: #fff; box-shadow: 0 0 15px #00f2ff; }
        .fav-add-btn { background: #ff00ff; border: none; color: #fff; padding: 6px 15px; border-radius: 20px; font-weight: bold; cursor: pointer; font-size: 12px; }

        /* ニュースパネル */
        #news-panel {
            position: absolute; top: 100px; right: 20px; width: 360px; 
            background: rgba(0, 10, 20, 0.95); border: 1px solid #00f2ff;
            border-radius: 20px; padding: 25px; display: none; z-index: 1000;
            max-height: 75vh; overflow-y: auto; box-shadow: 0 0 30px rgba(0, 242, 255, 0.3);
        }
        .article { padding: 12px 0; border-bottom: 1px solid #333; transition: 0.3s; }
        .article:hover { background: rgba(0, 242, 255, 0.1); }
        .article a { color: #fff; text-decoration: none; font-size: 14px; font-weight: bold; line-height: 1.4; }
        .rel-tag { font-size: 10px; color: #ff00ff; margin-top: 8px; display: block; border: 1px solid #ff00ff; width: fit-content; padding: 2px 6px; border-radius: 4px; }

        /* UIパーツ */
        .logout-btn { position: absolute; top: 20px; left: 20px; z-index: 1000; color: #ff00ff; text-decoration: none; font-size: 12px; border: 1px solid #ff00ff; padding: 8px 15px; border-radius: 10px; }
        .loading { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); display: none; justify-content: center; align-items: center; z-index: 2000; color: #00f2ff; font-weight: bold; font-size: 20px; }
    </style>
</head>
<body>
    <div class="loading" id="loader">AI ANALYZING DAILY NEWS...</div>
    <a href="logout.php" class="logout-btn">LOGOUT</a>

    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search daily theme...">
        <button class="fav-add-btn" onclick="saveFavorite()">★ SAVE</button>
        <select id="fav-select">
            <option value="">Favorites</option>
            <?php foreach($favs as $f): ?>
                <option value="<?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <button class="search-btn" onclick="executeSearch()">SEARCH</button>
    </div>

    <div id="map"></div>

    <div id="news-panel">
        <div style="float:right; cursor:pointer;" onclick="document.getElementById('news-panel').style.display='none'">✕</div>
        <h3 id="panel-title" style="color:#00f2ff; margin-top:0; font-size: 20px;">News Feed</h3>
        <div id="news-list"></div>
    </div>

    <!-- あなたのAPIキーを入れたGoogle Maps読み込み -->
    <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDp6Mpc7pMf-Q8vpHRKbSLi4_BSBfX9oFY&libraries=maps"></script>

    <script>
        let map;

        async function initMap() {
            try {
                const { Map } = await google.maps.importLibrary("maps");
                map = new Map(document.getElementById("map"), {
                    center: { lat: 20, lng: 0 },
                    zoom: 3,
                    mapId: "DEMO_MAP_ID",
                    backgroundColor: "#000",
                    disableDefaultUI: true
                });

                // 国境線のデータをロード
                map.data.loadGeoJson('https://raw.githubusercontent.com/johan/world.geo.json/master/countries.geo.json');

                // 初期スタイル（目立たないグレー）
                map.data.setStyle({
                    fillColor: 'transparent',
                    strokeColor: '#333',
                    strokeWeight: 0.5,
                    fillOpacity: 0
                });

                map.addListener('click', (e) => {
                    if (e.latLng) fetchNews(null, e.latLng.lat(), e.latLng.lng());
                });

            } catch (error) {
                console.error("Map Load Error:", error);
            }
        }

        // 蛍光ハイライト関数
        function highlightCountries(codes) {
            console.log("Glowing for:", codes);
            map.data.setStyle((feature) => {
                const id = feature.getId(); // 例: 'USA', 'JPN'
                if (codes.includes(id)) {
                    return {
                        fillColor: '#ff00ff', // 蛍光ピンク
                        fillOpacity: 0.6,
                        strokeColor: '#00f2ff', // 蛍光水色
                        strokeWeight: 2,
                        visible: true
                    };
                }
                return {
                    fillColor: 'transparent',
                    strokeColor: '#333',
                    strokeWeight: 0.5,
                    fillOpacity: 0
                };
            });
        }

        function executeSearch() {
            const query = document.getElementById('search-input').value;
            const fav = document.getElementById('fav-select').value;
            fetchNews(query || fav);
        }

        async function saveFavorite() {
            const theme = document.getElementById('search-input').value;
            if(!theme) return alert("Enter a theme name.");
            const res = await fetch(`fav_insert.php?theme=${encodeURIComponent(theme)}`);
            if(res.ok) { alert("Theme saved!"); location.reload(); }
        }

        // お気に入り選択時に自動検索
        document.getElementById('fav-select').addEventListener('change', (e) => {
            if(e.target.value) fetchNews(e.target.value);
        });

        // ニュース取得・表示メイン関数
        async function fetchNews(keyword = null, lat = null, lng = null) {
            const loader = document.getElementById('loader');
            loader.style.display = 'flex';
            
            try {
                const res = await fetch(`select.php?keyword=${encodeURIComponent(keyword)}&lat=${lat}&lng=${lng}`);
                const data = await res.json();
                
                document.getElementById('news-panel').style.display = 'block';
                document.getElementById('panel-title').innerText = "Daily: " + (keyword || "World News");
                
                // ニュースリストの表示をリッチにする
                if (data.articles && data.articles.length > 0) {
                    document.getElementById('news-list').innerHTML = data.articles.map(art => `
                        <div class="article">
                            <div style="font-size: 10px; color: #00f2ff; margin-bottom: 4px;">
                                ${art.date || ''} | ${art.source || 'Global Source'}
                            </div>
                            <a href="${art.url}" target="_blank">
                                ${art.title}
                            </a>
                        </div>`).join('');
                } else {
                    document.getElementById('news-list').innerHTML = "<div style='color:#ccc;'>No new news found for today. Try a broader keyword like 'Economy' or 'Tech'.</div>";
                }

                // 関連国の蛍光
                if (data.related_codes && data.related_codes.length > 0) {
                    highlightCountries(data.related_codes);
                } else {
                    // 判定がない場合は一度リセット
                    highlightCountries([]);
                }

            } catch (e) {
                console.error("Fetch Error:", e);
                document.getElementById('news-list').innerHTML = "<div style='color:red;'>Failed to load news. Check select.php and API keys.</div>";
            }
            loader.style.display = 'none';
        }

        window.onload = initMap;
    </script>
</body>
</html>
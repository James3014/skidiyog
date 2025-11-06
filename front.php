<?php
// 前台首頁 - 顯示雪場列表（無需資料庫）

// 硬編碼的雪場資料
$parks = [
    [
        'name' => 'niseko',
        'cname' => '二世古',
        'description' => '北海道最大雪場，粉雪天堂'
    ],
    [
        'name' => 'hakuba',
        'cname' => '白馬',
        'description' => '長野著名雪場，多雪域選擇'
    ],
    [
        'name' => 'nozawa',
        'cname' => '野澤',
        'description' => '溫泉雪場，日本最陡坡道'
    ],
    [
        'name' => 'nagano',
        'cname' => '長野',
        'description' => '奧運雪場，設施完善'
    ],
    [
        'name' => 'zao',
        'cname' => '藏王',
        'description' => '東北大型雪場，樹冰景觀'
    ],
    [
        'name' => 'iski',
        'cname' => 'iSKI',
        'description' => '山梨小眾雪場，親民價格'
    ]
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKidiyog - 日本滑雪場指南</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        header {
            background: rgba(0,0,0,0.3);
            color: white;
            padding: 30px 0;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 { font-size: 48px; margin-bottom: 10px; }
        .subtitle { font-size: 18px; opacity: 0.95; }
        .content {
            margin-top: 40px;
        }
        .section-title {
            color: white;
            font-size: 32px;
            margin: 40px 0 30px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .parks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .park-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        .park-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .park-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            text-align: center;
            padding: 20px;
        }
        .park-info {
            padding: 20px;
        }
        .park-name {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .park-cname {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }
        .park-description {
            font-size: 14px;
            color: #777;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #764ba2;
        }
        .info-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin: 30px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .info-section h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .info-section p {
            line-height: 1.8;
            margin-bottom: 15px;
            color: #555;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .feature {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .feature-title {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        footer {
            background: rgba(0,0,0,0.3);
            color: white;
            text-align: center;
            padding: 30px;
            margin-top: 50px;
        }
        footer a { color: #fff; text-decoration: none; }
        footer a:hover { text-decoration: underline; }
        .admin-link {
            background: rgba(255,255,255,0.2);
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .admin-link:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>🎿 SKidiyog</h1>
            <p class="subtitle">日本滑雪場完整指南 | 教練預約 | 行程規劃</p>
        </div>
    </header>

    <div class="container content">
        <div class="section-title">精選雪場</div>

        <div class="parks-grid">
            <?php foreach ($parks as $park): ?>
            <div class="park-card">
                <div class="park-image">
                    <?php echo $park['cname']; ?>
                </div>
                <div class="park-info">
                    <div class="park-name"><?php echo $park['name']; ?></div>
                    <div class="park-cname"><?php echo $park['cname']; ?></div>
                    <div class="park-description"><?php echo $park['description']; ?></div>
                    <a href="/park-detail.php?name=<?php echo $park['name']; ?>" class="btn">查看詳情</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="info-section">
            <h2>📍 為何選擇 SKidiyog？</h2>
            <div class="features">
                <div class="feature">
                    <div class="feature-title">✓ 完整雪場資訊</div>
                    <p>日本主要雪場詳細介紹、交通指南、租借服務</p>
                </div>
                <div class="feature">
                    <div class="feature-title">✓ 英語教練預約</div>
                    <p>專業教練團隊，提供初級到進階課程</p>
                </div>
                <div class="feature">
                    <div class="feature-title">✓ 行程規劃服務</div>
                    <p>一站式規劃您的滑雪之旅</p>
                </div>
                <div class="feature">
                    <div class="feature-title">✓ 24小時客服</div>
                    <p>中文、英文、日文三語支援</p>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h2>🎯 最新資訊</h2>
            <p>
                <strong>2025-26 冬季季節已開始！</strong><br>
                所有日本主要雪場現已開放。無論您是初學者還是高級滑雪者，SKidiyog 都能為您找到完美的雪場和教練。
            </p>
            <p>
                立即預約英語教練課程，享受專業指導。我們的教練團隊具有國際認證資格，能為各個級別的滑雪者提供個性化課程。
            </p>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>SKidiyog © 2025 | 日本滑雪場預約平台</p>
            <p style="margin-top: 20px; font-size: 14px;">
                <a href="/home.php">後台管理入口</a> |
                <a href="https://github.com/James3014/skidiyog" target="_blank">GitHub</a>
            </p>
            <a href="/home.php" class="admin-link">進入管理後台</a>
        </div>
    </footer>
</body>
</html>

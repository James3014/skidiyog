<?php
// 最簡單的首頁 - 不依賴資料庫
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKidiyog - 自助滑雪平台</title>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        header { text-align: center; color: white; padding: 40px 0; }
        h1 { font-size: 48px; margin-bottom: 20px; }
        .subtitle { font-size: 20px; opacity: 0.9; }
        .content { background: white; border-radius: 10px; padding: 40px; margin: 40px 0; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .admin-section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 30px; }
        .admin-links { display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap; }
        .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; transition: background 0.3s; }
        .btn:hover { background: #764ba2; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .status { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>SKidiyog</h1>
            <p class="subtitle">自助滑雪平台 - 雪場資訊、教練預約、行程安排</p>
        </header>

        <div class="content">
            <h2>歡迎使用 SKidiyog</h2>
            <p style="margin: 20px 0; font-size: 18px;">
                SKidiyog 是一個完整的自助滑雪平台，提供日本主要雪場的資訊、英語教練預約、行程規劃等服務。
            </p>

            <div class="status">
                <strong>✓ 系統狀態</strong><br>
                應用已成功部署到 Zeabur。資料庫連接正在配置中。
            </div>

            <h3 style="margin-top: 30px; margin-bottom: 15px;">快速導航</h3>
            <ul style="margin-left: 20px; line-height: 2;">
                <li><a href="/front.php">查看前台首頁</a></li>
                <li><a href="/bkAdmin/parks.php">後台管理 - 雪場</a></li>
                <li><a href="/bkAdmin/articles.php">後台管理 - 文章</a></li>
                <li><a href="/bkAdmin/instructors.php">後台管理 - 教練</a></li>
                <li><a href="/README.md">部署說明文件</a></li>
            </ul>

            <div class="admin-section">
                <h3>📋 後台管理面板</h3>
                <p>在以下連結登入管理員帳號，可編輯雪場資訊、文章和教練資料：</p>
                <div class="admin-links">
                    <a href="/bkAdmin/parks.php" class="btn">雪場管理</a>
                    <a href="/bkAdmin/articles.php" class="btn">文章管理</a>
                    <a href="/bkAdmin/instructors.php" class="btn">教練管理</a>
                </div>
            </div>

            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px;">
                <p>最後更新：2025-11-06 | 版本：Zeabur 部署版</p>
                <p>GitHub: <a href="https://github.com/James3014/skidiyog" target="_blank">https://github.com/James3014/skidiyog</a></p>
            </div>
        </div>
    </div>
</body>
</html>

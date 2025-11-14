-- ============================================================
-- 文章 - FAQ Tags 對應 SQL 更新語句
-- ============================================================
-- 執行日期: 2025-11-14
-- 目的: 更新所有現有文章的 tags 欄位，以支援自動 FAQ 關聯
-- 注意: 使用逗號分隔的 tags 格式，帶 # 符號
-- ============================================================

-- 文章 idx=1: 日本自助滑雪春雪篇，四月也可以滑雪
UPDATE articles SET tags = '#行程規劃,#旺季預約,#雪場資訊,#季節性' WHERE idx = 1;

-- 文章 idx=8: 自學滑雪(sb)和教練指導的優劣分析
UPDATE articles SET tags = '#教練選擇,#課程諮詢,#滑雪課程,#初學指南' WHERE idx = 8;

-- 文章 idx=9: 滑雪分享及聚會
UPDATE articles SET tags = '#社群互動,#雪友聚會' WHERE idx = 9;

-- 文章 idx=10: 如何花最少錢成為國際滑雪教練
UPDATE articles SET tags = '#教練職業,#國際資格,#職涯發展' WHERE idx = 10;

-- 文章 idx=11: 學習滑雪上課人數很重要
UPDATE articles SET tags = '#同堂安排,#團體預約,#課程安排,#教練服務' WHERE idx = 11;

-- 文章 idx=12: 學習Snowboard分級進度確認
UPDATE articles SET tags = '#進階課程,#進度分級,#課程設計' WHERE idx = 12;

-- 文章 idx=13: SKIDIY X 真空雪板 捕獲野生教練活動(已結束)
UPDATE articles SET tags = '#活動推廣,#品牌合作' WHERE idx = 13;

-- 文章 idx=14: Snowboard滑行教學-QuickRide系統(1)-基礎
UPDATE articles SET tags = '#進階課程,#單板教學,#技術細節' WHERE idx = 14;

-- 文章 idx=15: 親子及家族滑雪需注意的事項
UPDATE articles SET tags = '#親子同堂,#兒童課程,#安全保障,#家庭課程' WHERE idx = 15;

-- 文章 idx=16: 日本自助滑雪攻略-自助滑雪與跟團滑雪投保旅遊平安險的選擇
UPDATE articles SET tags = '#保險須知,#費用說明,#旅平險,#風險管理' WHERE idx = 16;

-- 文章 idx=18: 新手日本自助滑雪攻略
UPDATE articles SET tags = '#初學指南,#行程規劃,#課程選擇,#新手須知' WHERE idx = 18;

-- 文章 idx=19: Snowboard滑行教學-QuickRide系統(2)-滑行
UPDATE articles SET tags = '#進階課程,#單板教學,#技術細節' WHERE idx = 19;

-- 文章 idx=20: Snowboard滑行教學-QuickRide系統(3)-控制
UPDATE articles SET tags = '#進階課程,#單板教學,#技術細節' WHERE idx = 20;

-- 文章 idx=21: 單板(SB) VS 雙板(ski)要不要戴護具?如何避免受傷
UPDATE articles SET tags = '#安全保障,#運動傷害,#防護建議' WHERE idx = 21;

-- 文章 idx=22: 日本滑雪通app使用說明及會員電子身份認證
UPDATE articles SET tags = '#工具使用,#app推薦,#行程規劃' WHERE idx = 22;

-- 文章 idx=23: 步驟123如何挑選自己的滑雪裝備[Snowboard 雪板篇]
UPDATE articles SET tags = '#雪具選購,#單板裝備,#初學裝備,#租借建議' WHERE idx = 23;

-- 文章 idx=24: YKT x Skidiy 日本自由行一日行程
UPDATE articles SET tags = '#活動推廣,#行程安排' WHERE idx = 24;

-- 文章 idx=25: SKIDIY x 波賽頓 澎湖夏日水上活動專屬優惠
UPDATE articles SET tags = '#品牌合作,#季節性活動' WHERE idx = 25;

-- 文章 idx=26: SKIDIY自助滑雪團體課程攻略與介紹 vs 私人課程差異
UPDATE articles SET tags = '#同堂安排,#團體預約,#課程費用,#課程安排' WHERE idx = 26;

-- 文章 idx=27: Skidiy合作優惠整理
UPDATE articles SET tags = '#優惠推廣,#住宿推薦,#雪場資訊' WHERE idx = 27;

-- 文章 idx=28: 藏王(Zao)滑雪場常見問題與滑雪場上課注意事項
UPDATE articles SET tags = '#雪場資訊,#課程安排,#注意事項' WHERE idx = 28;

-- 文章 idx=29: 雪鏡百百種怎麼挑選不踩雷-VIGHT雪鏡推薦
UPDATE articles SET tags = '#雪具選購,#防護用品,#裝備建議' WHERE idx = 29;

-- 文章 idx=30: 雙板(ski)進度分級與湊班建議
UPDATE articles SET tags = '#進度分級,#進階課程,#同堂安排' WHERE idx = 30;

-- 文章 idx=31: 雙板雪杖選購建議
UPDATE articles SET tags = '#雪具選購,#雙板裝備,#裝備建議,#租借建議' WHERE idx = 31;

-- 文章 idx=32: 如何挑選適合的滑雪教練(加拿大單板篇)
UPDATE articles SET tags = '#教練選擇,#課程諮詢,#教練安排,#國外課程' WHERE idx = 32;

-- 文章 idx=33: 身障雪友自助日本滑雪攻略分享(Adapitive Ski、biski)
UPDATE articles SET tags = '#特殊需求,#輔具諮詢,#安全保障,#課程客製' WHERE idx = 33;

-- 文章 idx=34: 滑雪場優惠早鳥雪票連結整理
UPDATE articles SET tags = '#優惠推廣,#雪票優惠,#雪場資訊' WHERE idx = 34;

-- 文章 idx=35: 團體滑雪課程：中級滑雪者提升技巧與互動的理想選擇
UPDATE articles SET tags = '#同堂安排,#進階課程,#團體預約,#課程安排' WHERE idx = 35;

-- 文章 idx=36: Cardo x Skidiy 合作專案
UPDATE articles SET tags = '#品牌合作,#產品推廣' WHERE idx = 36;

-- 文章 idx=38: Skidiy 十年首度正式招募｜打造亞洲最具影響力的滑雪教學平台
UPDATE articles SET tags = '#招聘資訊,#品牌文化,#職涯發展' WHERE idx = 38;

-- ============================================================
-- 驗證：查看更新結果
-- ============================================================
-- SELECT idx, title, tags FROM articles ORDER BY idx;

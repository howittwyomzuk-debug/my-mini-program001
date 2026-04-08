<?php
/**
 * 配置文件
 */

// 网站配置
define('SITE_NAME', '诚信资料网');
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);

// 价格配置
define('MEMBER_PRICE_MONTH', 19.9);   // 月卡
define('MEMBER_PRICE_QUARTER', 39.9); // 季卡
define('MEMBER_PRICE_YEAR', 99);       // 年卡
define('MEMBER_PRICE_LIFETIME', 199);  // 终身

// 是否开启支付（开发阶段先关闭）
define('ENABLE_PAYMENT', false);

// 时区
date_default_timezone_set('Asia/Shanghai');

// 连接SQLite数据库（不需要单独装MySQL）
$db_file = dirname(__FILE__) . '/data/eshop.db';
if (!is_dir(dirname($db_file))) {
    mkdir(dirname($db_file), 0755, true);
}
try {
    $pdo = new PDO(
        "sqlite:" . $db_file,
        "",
        "",
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );

    // 如果数据库不存在，初始化表结构
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='articles'")->fetch();
    if (!$tables) {
        // 创建表
        $pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                email TEXT,
                level INTEGER DEFAULT 0,
                expire_time INTEGER DEFAULT 0,
                money REAL DEFAULT 0.00,
                create_time INTEGER NOT NULL,
                login_time INTEGER
            );

            CREATE TABLE category (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                sort INTEGER DEFAULT 0,
                status INTEGER DEFAULT 1
            );

            CREATE TABLE articles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cat_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                summary TEXT,
                content LONGTEXT NOT NULL,
                price REAL DEFAULT 0.00,
                is_paid INTEGER DEFAULT 1,
                click INTEGER DEFAULT 0,
                status INTEGER DEFAULT 1,
                create_time INTEGER NOT NULL
            );

            CREATE TABLE user_buy (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                article_id INTEGER NOT NULL,
                price REAL NOT NULL,
                buy_time INTEGER NOT NULL
            );

            CREATE TABLE orders (
                id TEXT PRIMARY KEY,
                user_id INTEGER NOT NULL,
                type INTEGER NOT NULL,
                price REAL NOT NULL,
                status INTEGER DEFAULT 0,
                pay_time INTEGER,
                create_time INTEGER NOT NULL
            );
        ");

        // 插入初始分类
        $pdo->exec("
            INSERT INTO category (id, name, sort, status) VALUES
            (1, '主题班会', 1, 1),
            (2, '班主任经验', 2, 1),
            (3, '教学资料', 3, 1),
            (4, 'PPT模板', 4, 1);
        ");
    }
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>
# 诚信资料网 - 付费资料分享网站

和 http://abc.jdwxmh.com 一样功能的移动端适配付费资料分享网站。

## 功能特性

- ✅ 移动端响应式设计，手机浏览友好
- ✅ 文章分类管理
- ✅ 用户注册登录系统
- ✅ 会员分级（月卡/季卡/年卡/终身）
- ✅ 单篇付费购买
- ✅ 试读功能（付费文章只显示摘要，会员/购买后看全文）
- ✅ PHP + PDO + MySQL，轻量高效

## 部署步骤

### 1. 上传文件
将整个 `eshop-site` 目录上传到你的腾讯云轻量服务器网站目录。

### 2. 创建数据库
```bash
# 导入初始数据库结构
mysql -u root -p < eshop-site/sql/init.sql
```

### 3. 配置数据库
编辑 `config.php`，修改数据库连接信息：
```php
define('DB_HOST', 'localhost');
define('DB_USER', '你的数据库用户名');
define('DB_PASS', '你的数据库密码');
define('DB_NAME', 'eshop');

define('SITE_URL', '你的网站域名');
```

### 4. 添加示例数据
可以手动在 `articles` 表插入文章：
```sql
INSERT INTO articles (cat_id, title, summary, content, price, is_paid, create_time) VALUES
(1, '《用"魔法"打败"魔法"》班主任经验分享', '这是一篇很好的班主任经验分享...', '全文内容在这里...', 5.00, 1, UNIX_TIMESTAMP());
```

### 5. 配置Nginx/Apache
- 将网站根目录指向 `eshop-site`
- 确保 PHP 版本 >= 5.6 (推荐 7.4)
- 开启 PDO MySQL 扩展

### 6. 开启支付（可选）
如需实现在线支付，需要集成微信/支付宝支付接口：
- 修改 `config.php` 中 `define('ENABLE_PAYMENT', true);`
- 在 `controller/user.php` 中完成支付回调处理

## 目录结构

```
eshop-site/
├── index.php          # 入口文件
├── config.php         # 配置文件
├── controller/        # 控制器
│   ├── list.php      # 文章列表
│   ├── article.php   # 文章详情
│   └── user.php      # 用户中心
├── views/            # 视图
│   ├── header.php    # 头部
│   └── footer.php    # 底部
├── sql/              # 数据库
│   └── init.sql      # 初始化脚本
└── README.md         # 说明文档
```

## 权限说明

- **游客**：只能看免费文章和付费文章试读
- **会员**：可以看全站所有文章
- **单买用户**：购买了哪篇就能看哪篇

## 说明

这是一个极简实现，所有核心功能都已完成：
- ✅ 分类浏览
- ✅ 用户注册登录
- ✅ 会员系统
- ✅ 付费试读
- ✅ 移动端适配

如需增加功能（比如支付、后台管理）可以后续再扩展。

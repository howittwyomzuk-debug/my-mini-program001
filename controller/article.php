<?php
/**
 * 文章详情控制器
 */
require_once dirname(dirname(__FILE__)) . '/config.php';
require_once dirname(dirname(__FILE__)) . '/views/header.php';

// 获取文章ID
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 查询文章
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ? AND status = 1");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if (!$article) {
    echo '<div class="container mt-5"><div class="alert alert-danger">文章不存在或已删除</div></div>';
    require_once dirname(dirname(__FILE__)) . '/views/footer.php';
    exit;
}

// 增加点击量
$pdo->prepare("UPDATE articles SET click = click + 1 WHERE id = ?")->execute([$article_id]);

// 检查用户是否已购买
$is_bought = false;
$is_vip = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // 检查是否VIP会员
    $stmt = $pdo->prepare("SELECT level, expire_time FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user['level'] >= 1 && ($user['expire_time'] > time() || $user['expire_time'] == 0)) {
        $is_vip = true;
    }

    // 检查是否已购买本篇
    $stmt = $pdo->prepare("SELECT id FROM user_buy WHERE user_id = ? AND article_id = ?");
    $stmt->execute([$user_id, $article_id]);
    if ($stmt->fetch()) {
        $is_bought = true;
    }
}

// 判断是否可以查看全文
$can_view_full = !$article['is_paid'] || $is_bought || $is_vip;
?>

<div class="container mt-3">
    <div class="article-detail">
        <!-- 标题 -->
        <div class="article-header text-center mb-4">
            <h2><?php echo htmlspecialchars($article['title']); ?></h2>
            <div class="text-muted mt-2">
                <span>阅读 <?php echo $article['click']; ?></span>
                <?php if ($article['is_paid']): ?>
                    <span class="ms-3">价格: ￥<?php echo $article['price']; ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- 内容 -->
        <div class="article-content mb-4">
            <?php if ($can_view_full): ?>
                <!-- 可以看全文 -->
                <div class="full-content">
                    <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </div>
            <?php else: ?>
                <!-- 只能试读 -->
                <?php if ($article['summary']): ?>
                    <div class="trial-content">
                        <?php echo nl2br(htmlspecialchars($article['summary'])); ?>
                    </div>
                    <hr>
                    <div class="alert alert-warning text-center">
                        <h5>试读结束，开通会员文档无限下载使用</h5>
                        <p class="mb-0">本内容为付费内容，请先购买或加入会员！</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <h5>本内容为付费内容，请先购买或加入会员</h5>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- 购买按钮 -->
        <?php if (!$can_view_full): ?>
            <div class="buy-box text-center mb-5">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <p class="text-muted">请先登录后购买</p>
                    <a href="<?php echo SITE_URL; ?>?mod=mobile&act=user&do=login" class="btn btn-primary btn-lg">登录</a>
                <?php else: ?>
                    <?php if ($article['price'] > 0): ?>
                        <a href="<?php echo SITE_URL; ?>?mod=mobile&act=user&do=buy&id=<?php echo $article['id']; ?>"
                           class="btn btn-danger btn-lg">立即购买 ￥<?php echo $article['price']; ?></a>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="<?php echo SITE_URL; ?>?mod=mobile&act=user&do=vip" class="btn btn-outline-primary">开通会员查看更多</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- 返回列表 -->
        <div class="text-center mt-4 mb-4">
            <a href="javascript:history.back();" class="btn btn-secondary">返回列表</a>
        </div>
    </div>
</div>

<?php require_once dirname(dirname(__FILE__)) . '/views/footer.php'; ?>
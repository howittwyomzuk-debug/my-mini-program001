<?php
/**
 * 文章列表控制器
 */
require_once dirname(dirname(__FILE__)) . '/config.php';
require_once dirname(dirname(__FILE__)) . '/views/header.php';

// 获取分类ID
$cat_id = isset($_GET['beid']) ? intval($_GET['beid']) : 0;

// 查询文章列表
if ($cat_id > 0) {
    $sql = "SELECT * FROM articles WHERE cat_id = ? AND status = 1 ORDER BY create_time DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cat_id]);
} else {
    $sql = "SELECT * FROM articles WHERE status = 1 ORDER BY create_time DESC";
    $stmt = $pdo->query($sql);
}
$articles = $stmt->fetchAll();

// 查询分类
$stmt = $pdo->query("SELECT * FROM category WHERE status = 1 ORDER BY sort");
$categories = $stmt->fetchAll();
?>

<div class="container mt-3">
    <div class="page-header">
        <h1 class="text-center"><?php echo SITE_NAME; ?></h1>
    </div>

    <!-- 分类导航 -->
    <div class="category-nav mt-3 mb-3">
        <div class="d-flex flex-wrap">
            <a href="<?php echo SITE_URL; ?>?mod=mobile&act=article&do=list" class="btn btn-sm btn-outline-primary m-1 <?php echo $cat_id == 0 ? 'active' : ''; ?>">全部</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo SITE_URL; ?>?mod=mobile&act=article&do=list&beid=<?php echo $cat['id']; ?>"
                   class="btn btn-sm btn-outline-primary m-1 <?php echo $cat_id == $cat['id'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 文章列表 -->
    <div class="article-list">
        <ul class="list-group">
            <?php if (empty($articles)): ?>
                <li class="list-group-item text-center text-muted">暂无资料</li>
            <?php endif; ?>

            <?php foreach ($articles as $article): ?>
                <li class="list-group-item article-item">
                    <a href="<?php echo SITE_URL; ?>?mod=mobile&act=article&do=article&id=<?php echo $article['id']; ?>&beid=<?php echo $article['cat_id']; ?>"
                       class="text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-1"><?php echo htmlspecialchars($article['title']); ?></h5>
                            <?php if ($article['is_paid']): ?>
                                <span class="badge bg-danger">付费</span>
                            <?php else: ?>
                                <span class="badge bg-success">免费</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($article['summary']): ?>
                            <p class="mb-1 text-muted small"><?php echo htmlspecialchars($article['summary']); ?></p>
                        <?php endif; ?>
                        <small class="text-muted">
                            <?php echo date('Y-m-d', $article['create_time']); ?>
                            <?php if ($article['price'] > 0): ?>
                                · ￥<?php echo $article['price']; ?>
                            <?php endif; ?>
                        </small>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- 用户入口 -->
    <div class="text-center mt-4 mb-4">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?php echo SITE_URL; ?>?mod=mobile&act=user" class="btn btn-primary">个人中心</a>
        <?php else: ?>
            <a href="<?php echo SITE_URL; ?>?mod=mobile&act=user&do=login" class="btn btn-primary">登录</a>
            <a href="<?php echo SITE_URL; ?>?mod=mobile&act=user&do=register" class="btn btn-outline-primary">注册</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(dirname(__FILE__)) . '/views/footer.php'; ?>
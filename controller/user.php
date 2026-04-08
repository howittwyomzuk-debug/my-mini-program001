<?php
/**
 * 用户中心控制器
 */
require_once dirname(dirname(__FILE__)) . '/config.php';
require_once dirname(dirname(__FILE__)) . '/views/header.php';

$do = isset($_GET['do']) ? $_GET['do'] : 'index';

// 开始会话
session_start();
?>

<?php if ($do == 'login'): ?>
    <!-- 登录页 -->
    <div class="container mt-5">
        <h2 class="text-center">用户登录</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="post" action="?mod=mobile&act=user&do=dologin">
                    <div class="mb-3">
                        <label class="form-label">用户名</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">密码</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">登录</button>
                    </div>
                    <div class="text-center mt-3">
                        还没有账号？<a href="?mod=mobile&act=user&do=register">立即注册</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php elseif ($do == 'dologin'): ?>
    <?php
    // 处理登录
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // 更新登录时间
        $pdo->prepare("UPDATE users SET login_time = ? WHERE id = ?")->execute([time(), $user['id']);

        echo '<div class="container mt-5"><div class="alert alert-success">登录成功，正在跳转...<meta http-equiv=\"refresh\" content=\"1;url=?mod=mobile&act=user\"></div></div>';
    } else {
        echo '<div class="container mt-5"><div class="alert alert-danger">用户名或密码错误</div></div>';
    }
    ?>
<?php elseif ($do == 'register'): ?>
    <!-- 注册页 -->
    <div class="container mt-5">
        <h2 class="text-center">用户注册</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="post" action="?mod=mobile&act=user&do=doregister">
                    <div class="mb-3">
                        <label class="form-label">用户名</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">邮箱（选填）</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">密码</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">注册</button>
                    </div>
                    <div class="text-center mt-3">
                        已有账号？<a href="?mod=mobile&act=user&do=login">立即登录</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php elseif ($do == 'doregister'): ?>
    <?php
    // 处理注册
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    // 检查用户名是否存在
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo '<div class="container mt-5"><div class="alert alert-danger">用户名已存在</div></div>';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, email, level, money, create_time) VALUES (?, ?, ?, 0, 0.00, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $hash, $email, time());

        echo '<div class="container mt-5"><div class="alert alert-success">注册成功！<a href="?mod=mobile&act=user&do=login">点击登录</a></div></div>';
    }
    ?>
<?php elseif ($do == 'index'): ?>
    <!-- 用户中心首页 -->
    <?php
    if (!isset($_SESSION['user_id'])) {
        echo '<meta http-equiv=\"refresh\" content=\"0;url=?mod=mobile&act=user&do=login\">';
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // 判断会员状态
    if ($user['level'] >= 1) {
        if ($user['expire_time'] == 0) {
            $vip_status = '<span class="badge bg-success">永久会员</span>';
        } elseif ($user['expire_time'] > time()) {
            $vip_status = '<span class="badge bg-success">有效期至 ' . date('Y-m-d', $user['expire_time']) . '</span>';
        } else {
            $vip_status = '<span class="badge bg-danger">已过期</span>';
        }
    } else {
        $vip_status = '<span class="badge bg-secondary">非会员</span>';
    }
    ?>

    <div class="container mt-3">
        <h2>个人中心</h2>
        <div class="card mt-3">
            <div class="card-body">
                <p><strong>用户名：</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>会员状态：</strong> <?php echo $vip_status; ?></p>
                <p><strong>账户余额：</strong> ￥<?php echo number_format($user['money'], 2); ?></p>
                <p><strong>注册时间：</strong> <?php echo date('Y-m-d', $user['create_time']); ?></p>
            </div>
        </div>

        <div class="mt-4">
            <a href="?mod=mobile&act=user&do=vip" class="btn btn-primary btn-block w-100">开通/续费会员</a>
        </div>

        <div class="mt-3">
            <a href="?mod=mobile&act=user&do=logout" class="btn btn-outline-secondary w-100">退出登录</a>
        </div>
    </div>
<?php elseif ($do == 'logout'): ?>
    <?php
    session_destroy();
    echo '<div class="container mt-5"><div class="alert alert-success">已退出登录，正在跳转...<meta http-equiv=\"refresh\" content=\"1;url=?\"></div></div>';
    ?>
<?php elseif ($do == 'vip'): ?>
    <!-- 开通会员页 -->
    <div class="container mt-3">
        <h2 class="text-center">开通会员</h2>
        <div class="row mt-4">
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-header">
                        <h5>月卡</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-primary">￥<?php echo MEMBER_PRICE_MONTH; ?></h3>
                        <p>30天全站无限下载</p>
                        <a href="?mod=mobile&act=user&do=order&type=1&month=1" class="btn btn-primary">立即开通</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-header">
                    <h5>季卡</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-primary">￥<?php echo MEMBER_PRICE_QUARTER; ?></h3>
                        <p>90天全站无限下载</p>
                        <a href="?mod=mobile&act=user&do=order&type=1&month=3" class="btn btn-primary">立即开通</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-header">
                    <h5>年卡</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-primary">￥<?php echo MEMBER_PRICE_YEAR; ?></h3>
                        <p>365天全站无限下载</p>
                        <a href="?mod=mobile&act=user&do=order&type=1&month=12" class="btn btn-primary">立即开通</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-header bg-danger text-white">
                    <h5>终身</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-danger">￥<?php echo MEMBER_PRICE_LIFETIME; ?></h3>
                        <p>永久有效 一次购买终身使用</p>
                        <a href="?mod=mobile&act=user&do=order&type=1&month=0" class="btn btn-danger">立即开通</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-muted text-center">会员权益：开通会员后可下载全站所有付费资料，无需再次付费</p>
        </div>
    </div>
<?php else: ?>
    <div class="container mt-5">
        <div class="alert alert-danger">页面不存在</div>
    </div>
<?php endif; ?>

<?php require_once dirname(dirname(__FILE__)) . '/views/footer.php'; ?>
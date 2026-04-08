<?php
/**
 * 诚信资料网 - 移动端适配付费资料分享网站
 * 入口文件
 */

// 加载配置
require_once 'config.php';

// 简单路由
$mod = isset($_GET['mod']) ? $_GET['mod'] : 'mobile';
$act = isset($_GET['act']) ? $_GET['act'] : 'article';
$do = isset($_GET['do']) ? $_GET['do'] : 'index';

// 路由处理
switch ($mod) {
    case 'mobile':
        switch ($act) {
            case 'article':
                switch ($do) {
                    case 'article':
                        // 文章详情页
                        require_once 'controller/article.php';
                        break;
                    case 'list':
                    default:
                        // 文章列表页
                        require_once 'controller/list.php';
                        break;
                }
                break;
            case 'user':
                // 用户中心
                require_once 'controller/user.php';
                break;
            default:
                require_once 'controller/list.php';
                break;
        }
        break;
    default:
        require_once 'controller/list.php';
        break;
}
?>
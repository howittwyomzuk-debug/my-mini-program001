<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo SITE_NAME; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .article-item {
            margin-bottom: 2px;
        }
        .article-item a {
            text-decoration: none;
            display: block;
        }
        .article-item a:hover {
            background-color: #f8f9fa;
        }
        .category-nav .btn.active {
            background-color: #0d6efd;
            color: white;
        }
        .article-detail h2 {
            font-size: 1.5rem;
            line-height: 1.4;
        }
        .full-content {
            line-height: 1.8;
            font-size: 1rem;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .trial-content {
            line-height: 1.6;
            color: #666;
        }
        .container {
            max-width: 768px;
        }
    </style>
</head>
<body>
<?php
// 启动session
if (!isset($_SESSION)) {
    session_start();
}
?>
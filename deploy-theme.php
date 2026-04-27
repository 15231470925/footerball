<?php
/**
 * Theme File Deployer
 * 将此文件临时放到 WordPress 根目录，运行后删除
 * 用法: curl -X POST -F "file=functions.php" -F "content=<@functions.php" http://yoursite.com/deploy-theme.php
 */

// 简单的认证保护
$auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$expected = 'Basic ' . base64_encode('liuxiyun@dhgate.com:TrFf mw71 cJtk lUpt SBkU BSGH');

if ($auth_header !== $expected) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$file = $_POST['file'] ?? '';
$content = $_POST['content'] ?? '';

if (empty($file) || empty($content)) {
    // 尝试从 raw POST body 读取
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if ($data) {
        $file = $data['file'] ?? '';
        $content = $data['content'] ?? '';
    }
}

if (empty($file) || empty($content)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing file or content']);
    exit;
}

// 安全检查：只允许特定文件
$allowed_files = [
    'functions.php',
    'style.css',
    'single-teams.php',
    'single-players.php',
    'single-lifestyle.php',
    'header.php',
    'footer.php',
    'front-page.php',
];

// CSS 文件
$allowed_css = ['header.css', 'footer.css', 'home.css', 'players.css', 'lifestyle.css', 'teams.css', 'main.css'];

$basename = basename($file);
$is_css = in_array($basename, $allowed_css);

if (!in_array($basename, $allowed_files) && !$is_css) {
    http_response_code(400);
    echo json_encode(['error' => 'File not allowed: ' . $basename]);
    exit;
}

// 确定目标路径
$theme_dir = __DIR__ . '/wp-content/themes/twentytwentyfive-child/';
if ($is_css) {
    $target = $theme_dir . 'css/' . $basename;
} else {
    $target = $theme_dir . $basename;
}

// 确保 css 目录存在
if ($is_css) {
    $css_dir = $theme_dir . 'css/';
    if (!is_dir($css_dir)) {
        mkdir($css_dir, 0755, true);
    }
}

// 写入文件
if (file_put_contents($target, $content) !== false) {
    echo json_encode([
        'success' => true,
        'file' => $file,
        'path' => $target,
        'size' => strlen($content),
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to write file']);
}

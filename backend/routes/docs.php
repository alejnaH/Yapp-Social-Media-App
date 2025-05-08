<?php


Flight::route('GET /public/v1/docs', function() {
    include __DIR__ . '/public/v1/docs/index.php';
});

Flight::route('GET /public/v1/docs/swagger.php', function() {
    include __DIR__ . '/public/v1/docs/swagger.php';
});

Flight::route('GET /public/v1/docs/@file', function($file) {
    $filePath = __DIR__ . '/public/v1/docs/' . $file;
    if (file_exists($filePath)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'js':
                header('Content-Type: application/javascript');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
        }
        readfile($filePath);
    }
});
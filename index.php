<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
require_once __DIR__ . '/includes/Emc.php';

try {
    if($_POST) {
        $post = $_POST;
        $params = [];

        if($post['params']) {
            $params = $post['params'];
        }

        $emc = new Emc();

        echo json_encode($emc->request($post['method'], $params), JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    echo json_encode([
        'error' => [
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ]
    ], JSON_UNESCAPED_UNICODE);
}
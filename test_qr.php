<?php
require_once __DIR__ . '/core/phpqrcode/qrlib.php';
try {
    QRcode::svg('test', false, 'M', 5, 2);
    echo "SUCCESS";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

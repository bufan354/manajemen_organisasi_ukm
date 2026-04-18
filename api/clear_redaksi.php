<?php
require_once '../core/Session.php';
Session::start();
if (isset($_SESSION['redaksi_to_copy'])) {
    unset($_SESSION['redaksi_to_copy']);
}
echo json_encode(['status' => 'success']);

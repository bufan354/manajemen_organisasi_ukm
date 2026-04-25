<?php
require_once __DIR__ . '/../models/LogKeamanan.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

class LogKeamananController
{
    private LogKeamananModel $logModel;

    public function __construct()
    {
        $this->logModel = new LogKeamananModel();
    }

    /**
     * Ekspor log keamanan ke CSV
     */
    public function exportExcel(): void
    {
        Session::requireSuperAdmin();
        
        $search    = $_GET['search'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate   = $_GET['end_date'] ?? '';

        $logs = $this->logModel->getForExport($search, $startDate, $endDate);

        $headers = ['ID', 'Waktu', 'Aktivitas', 'Email User', 'Nama User', 'IP Address', 'User Agent', 'Detail'];
        $rows = [];
        foreach ($logs as $log) {
            $rows[] = [
                $log['id'],
                $log['waktu'],
                $log['aktivitas'],
                $log['user_email'] ?? '-',
                $log['user_nama'] ?? '-',
                $log['ip_address'],
                $log['user_agent'],
                $log['detail']
            ];
        }

        $title = "Log Aktivitas Keamanan";
        $filename = "Log_Keamanan_" . date('Y-m-d_H-i-s') . ".xls";
        $ukm = null; // Sistem branding

        include __DIR__ . '/../../views/admin/export_excel.php';
        exit;
    }

    /**
     * Ekspor log keamanan ke JSON
     */
    public function exportJson(): void
    {
        Session::requireSuperAdmin();
        
        $search    = $_GET['search'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate   = $_GET['end_date'] ?? '';

        $logs = $this->logModel->getForExport($search, $startDate, $endDate);

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="log_keamanan_' . date('Y-m-d_H-i-s') . '.json"');
        
        echo json_encode($logs, JSON_PRETTY_PRINT);
        exit;
    }
}

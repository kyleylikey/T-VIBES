<?php
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/Logs.php';

class LogsController {
    private $logsModel;

    public function __construct() {
        $this->logsModel = new Logs();
    }

    public function fetchLogs($page, $logsPerPage, $searchTerm = '') {
        $totalLogs = $this->logsModel->getTotalLogsCount($searchTerm);
        $totalPages = ceil($totalLogs / $logsPerPage);

        // Ensure the current page is within valid bounds
        if ($totalPages == 0) {
            $totalPages = 1;
        }
        
        if ($page > $totalPages) {
            $page = $totalPages;
        } elseif ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $logsPerPage;
        $logs = $this->logsModel->getAllLogs($logsPerPage, $offset, $searchTerm);

        return [
            'logs' => $logs,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }
}

// Handle AJAX requests for search
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$logsPerPage = 10;
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$controller = new LogsController();
$paginationData = $controller->fetchLogs($page, $logsPerPage, $searchTerm);

$logs = $paginationData['logs'];
$totalPages = $paginationData['totalPages'];
$currentPage = $paginationData['currentPage'];

// Return JSON for AJAX requests
if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode($paginationData);
    exit;
}
?>
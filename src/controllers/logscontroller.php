<?php
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/Logs.php';

class LogsController {
    private $logsModel;

    public function __construct() {
        $this->logsModel = new Logs();
    }

    public function fetchLogs() {
        return $this->logsModel->getAllLogs();
    }
}

$controller = new LogsController();
$logs = $controller->fetchLogs();

?>
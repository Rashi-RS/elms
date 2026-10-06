<?php
declare(strict_types=1);

namespace App\controllers;

use App\models\ActivityLog;

class ActivityLogController extends Controller
{
    private ActivityLog $activityLogModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->activityLogModel = new ActivityLog();
    }

    public function index(): void
    {
        $search = trim((string)$this->request->input('search', ''));
        $page = (int)$this->request->input('page', 1);

        $logs = $this->activityLogModel->getAllWithUser($search, $page, 20);

        $this->render('admin/activity_logs/index', [
            'title' => 'System Activity Audit Logs — ELMS',
            'logs' => $logs,
            'search' => $search,
        ]);
    }
}

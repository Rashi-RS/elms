<?php
declare(strict_types=1);

namespace App\controllers;

use App\helpers\Session;
use App\models\Notification;

class NotificationController extends Controller
{
    private Notification $notifModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->notifModel = new Notification();
    }

    public function index(): void
    {
        $userId = Session::userId();
        $notifications = $this->notifModel->getAllForUser($userId, 50);

        $this->render('employee/notifications', [
            'title' => 'Notifications Center — ELMS',
            'notifications' => $notifications,
        ]);
    }

    public function markRead(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $userId = Session::userId();

        $success = $this->notifModel->markAsRead($id, $userId);

        if ($this->request->isAjax()) {
            $this->json(['success' => $success, 'message' => 'Notification marked as read.']);
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/notifications');
    }

    public function markAllRead(): void
    {
        $userId = Session::userId();
        $success = $this->notifModel->markAllAsRead($userId);

        if ($this->request->isAjax()) {
            $this->json(['success' => $success, 'message' => 'All notifications marked as read.']);
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/notifications', 'success', 'All notifications marked as read.');
    }

    public function getUnreadCount(): void
    {
        $userId = Session::userId();
        $count = $userId ? $this->notifModel->getUnreadCount($userId) : 0;
        $this->json(['success' => true, 'count' => $count]);
    }
}

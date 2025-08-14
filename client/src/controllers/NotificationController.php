<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Notification.php';

class NotificationController extends BaseController
{

    private $apiGatewayUrl = 'http://localhost:6000'; //For now we work directly with the auth service

    public function index()
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $notificationModel = new Notification();
        $notifcationData = $notificationModel->getNotificationById($user['user_id']);


        $this->render('notification/general', [
            "notifications" => $notifcationData['data'] ?? []
        ]);
    }
}

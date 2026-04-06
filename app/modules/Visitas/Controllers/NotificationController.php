<?php

namespace App\Modules\Visitas\Controllers;

use App\Modules\Usuarios\Helpers\AccessControl;
use App\Modules\Visitas\Models\Notification;

class NotificationController
{
    private function resolvePanelUrl(): string
    {
        return AccessControl::defaultPanelPath();
    }

    public function read($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: /SGA-SEBANA/public/login');
            exit;
        }

        $model = new Notification();
        $noti = $model->find($id);

        if (!$noti || (int) ($noti['usuario_id'] ?? 0) !== (int) $userId) {
            header('Location: ' . $this->resolvePanelUrl() . '?error=no_autorizado');
            exit;
        }

        $model->markAsReadForUser($id, $userId);

        if (!empty($noti['url_accion'])) {
            header('Location: ' . $noti['url_accion']);
        } else {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $this->resolvePanelUrl()));
        }
        exit;
    }

    public function archive($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: /SGA-SEBANA/public/login');
            exit;
        }

        $model = new Notification();
        $model->archiveForUser($id, $userId);

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $this->resolvePanelUrl()));
        exit;
    }

    public function markAllAsRead()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $model = new Notification();
            $model->markAllAsReadByUser($userId);
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $this->resolvePanelUrl()));
        exit;
    }
}

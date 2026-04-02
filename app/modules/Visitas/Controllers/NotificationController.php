<?php

namespace App\Modules\Visitas\Controllers;

use App\Modules\Visitas\Models\Notification;

class NotificationController
{
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
            header('Location: /SGA-SEBANA/public/home?error=no_autorizado');
            exit;
        }

        $model->markAsReadForUser($id, $userId);

        if (!empty($noti['url_accion'])) {
            header('Location: ' . $noti['url_accion']);
        } else {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/SGA-SEBANA/public/home'));
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

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/SGA-SEBANA/public/home'));
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

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/SGA-SEBANA/public/home'));
        exit;
    }
}

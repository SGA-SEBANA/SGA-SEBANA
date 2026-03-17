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

        $model = new Notification();

        // Marcar como leída
        $model->markAsRead($id);


        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/SGA-SEBANA/public/home'));
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

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/SGA-SEBANA/public/home'));
        exit;
    }
}
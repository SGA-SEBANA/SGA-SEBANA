<?php

namespace App\Modules\Visitas\Controllers;

use App\Modules\Usuarios\Helpers\AccessControl;
use App\Modules\Visitas\Models\Notification;

class NotificationController
{
    private function resolvePanelUrl(): string
    {
        $roleKey = AccessControl::currentRoleKey();
        if (AccessControl::isAffiliateRole()) {
            return '/SGA-SEBANA/public/visit-requests';
        }
        if ($roleKey === 'operador') {
            return '/SGA-SEBANA/public/afiliados';
        }
        if ($roleKey === 'auditor') {
            return '/SGA-SEBANA/public/bitacora';
        }
        if ($roleKey === 'admin_rrll') {
            return '/SGA-SEBANA/public/casos-rrll';
        }
        if ($roleKey === 'admin_solicitudes') {
            return '/SGA-SEBANA/public/admin/visit-requests';
        }
        return '/SGA-SEBANA/public/home';
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

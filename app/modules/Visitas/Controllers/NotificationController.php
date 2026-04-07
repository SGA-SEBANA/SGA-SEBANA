<?php

namespace App\Modules\Visitas\Controllers;

use App\Modules\Usuarios\Helpers\AccessControl;
use App\Modules\Usuarios\Helpers\SecurityHelper;
use App\Modules\Visitas\Models\Notification;

class NotificationController
{
    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function requirePostWithCsrf(string $fallback): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $fallback);
            exit;
        }

        if (!SecurityHelper::validateCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF invalido o expirado. Recargue la pagina e intente nuevamente.';
            header('Location: ' . $fallback);
            exit;
        }
    }

    private function resolvePanelUrl(): string
    {
        return AccessControl::defaultPanelPath();
    }

    public function read($id)
    {
        $this->ensureSession();
        $this->requirePostWithCsrf($this->resolvePanelUrl());

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
        $this->ensureSession();
        $this->requirePostWithCsrf($this->resolvePanelUrl());

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
        $this->ensureSession();
        $this->requirePostWithCsrf($this->resolvePanelUrl());

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $model = new Notification();
            $model->markAllAsReadByUser($userId);
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $this->resolvePanelUrl()));
        exit;
    }
}

<?php

namespace App\Modules\Visitas\Controllers;

use App\Modules\Visitas\Models\Notification;

class NotificationController
{
    /**
     * HU-NO-01 E2: Marcar como leída y redirigir al recurso (afiliado, error, etc.)
     */
    public function read($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $model = new Notification();

        // Buscamos la notificación antes de marcarla para saber a dónde mandarte
        $noti = $model->find($id);

        // Marcar como leída en la DB
        $model->markAsRead($id);

        // Si la notificación tiene una URL de acción, te mandamos directo allá.
        // Si no (o si falló la búsqueda), te devolvemos a donde estabas.
        if ($noti && !empty($noti['url_accion'])) {
            header("Location: " . $noti['url_accion']);
        } else {
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/SGA-SEBANA/public/home'));
        }
        exit;
    }

    /**
     * HU-NO-01, 03, 04 E2: "Eliminar" (Archivar) la notificación para que no estorbe
     */
    public function archive($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $model = new Notification();
        
        // Usamos el método archive que pusimos en el Modelo
        $model->archive($id);

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/SGA-SEBANA/public/home'));
        exit;
    }

    public function markAllAsRead()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Mantenemos tu lógica de user_id
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $model = new Notification();
            $model->markAllAsReadByUser($userId);
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/SGA-SEBANA/public/home'));
        exit;
    }
}
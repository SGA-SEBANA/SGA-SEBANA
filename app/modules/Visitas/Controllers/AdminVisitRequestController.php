<?php

namespace App\Modules\Visitas\Controllers;

use App\Modules\Visitas\Models\VisitRequest;
use App\Modules\Visitas\Models\Notification;
use App\Helpers\Paginator;

class AdminVisitRequestController
{

public function index()
{
    $model = new VisitRequest();

    $filtros = [];

    $pagination = Paginator::make(
        $model,
        'getVisits',
        $filtros,
        $_GET['page'] ?? 1,
        10
    );

    $solicitud = $pagination['data'];

    $page = $pagination['page'];

    $totalPaginas = $pagination['totalPaginas'];

    require BASE_PATH . '/app/modules/Visitas/Views/Admin/index.php';
}

public function calendar()
{
    $model = new VisitRequest();

    $events = $model->getCalendarEvents();
    $visits = $model->getUpcomingVisits();

    include BASE_PATH . '/app/modules/Visitas/Views/Admin/calendar.php';
}

public function acceptVisits($id)
{
    $model = new VisitRequest();

    $model->acceptVisit($id);
    $this->notifyAffiliateStatus((int) $id, 'aprobada');

    header("Location: /SGA-SEBANA/public/admin/visit-requests");
    exit;
}

public function rejectRequest($id)
{
    $model = new VisitRequest();

    $model->updateEstado($id, 'rechazada');
    $this->notifyAffiliateStatus((int) $id, 'rechazada');

    header("Location: /SGA-SEBANA/public/admin/visit-requests");
    exit;
}

public function calendarEvents()
{
    $model = new VisitRequest();

    $visitas = $model->getApprovedVisits();

    $events = [];

    foreach ($visitas as $v)
    {
        $events[] = [
            'title' => $v['nombre_empleado'],
            'start' => $v['fecha_visita'] . 'T' . $v['hora_visita']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($events);
    exit;
}

private function notifyAffiliateStatus(int $solicitudId, string $estado): void
{
    $model = new VisitRequest();
    $solicitud = $model->getVisitById($solicitudId);
    if (!$solicitud) {
        return;
    }

    $afiliadoId = (int) ($solicitud['afiliado_id'] ?? 0);
    if ($afiliadoId <= 0) {
        return;
    }

    $usuarioId = $model->resolveUserIdByAfiliado($afiliadoId);
    if (!$usuarioId) {
        return;
    }

    $notification = new Notification();
    $codigo = (string) ($solicitud['codigo_solicitud'] ?? ('#' . $solicitudId));
    $notification->createNotification(
        $usuarioId,
        'sistema',
        'visitas',
        'Actualizacion de solicitud de visita',
        "Tu solicitud {$codigo} fue {$estado}.",
        'visita',
        $solicitudId,
        '/SGA-SEBANA/public/visit-requests'
    );
}

}

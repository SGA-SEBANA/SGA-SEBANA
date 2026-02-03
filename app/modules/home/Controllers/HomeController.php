<?php

namespace App\Modules\Home\Controllers;

use App\Core\ControllerBase;

use App\Modules\Home\Models\HomeModel;
use App\Modules\Usuarios\Models\Bitacora;

class HomeController extends ControllerBase
{
    public function index()
    {
        $homeModel = new HomeModel();
        $bitacoraModel = new Bitacora();

        $stats = $homeModel->getSummaryStats();
        $recentLogs = $bitacoraModel->getRecent(10);

        $this->view('index', [
            'title' => 'Dashboard - SGA-SEBANA',
            'stats' => $stats,
            'recentLogs' => $recentLogs
        ]);
    }
}

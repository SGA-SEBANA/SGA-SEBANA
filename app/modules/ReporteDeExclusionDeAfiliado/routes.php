<?php

use App\Modules\ReporteDeExclusionDeAfiliado\Controllers\ReporteDeExclusionDeAfiliadoController;

// =====================================================
// RUTAS REPORTE DE EXCLUSIÓN DE AFILIADOS
// =====================================================

// Listado con filtros
$router->get('/ReporteDeExclusionDeAfiliado', [ReporteDeExclusionDeAfiliadoController::class, 'index']);

// Detalle individual
$router->get('/ReporteDeExclusionDeAfiliado/show/{id}', [ReporteDeExclusionDeAfiliadoController::class, 'show']);

// Exportar PDF/Excel
$router->get('/ReporteDeExclusionDeAfiliado/exportar/{formato}', [ReporteDeExclusionDeAfiliadoController::class, 'exportar']);

// Descargar PDF de detalle
$router->get('/ReporteDeExclusionDeAfiliado/pdf/{id}', [ReporteDeExclusionDeAfiliadoController::class, 'descargarPdf']);


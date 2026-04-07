<?php

use App\Modules\CasosRRLL\Controllers\CasosRRLLController;

// =====================================================
// CASOS RELACIONES LABORALES ROUTES
// =====================================================

// =============== RUTAS PARA CASOS ===============

// Listado de Casos
$router->get('/casos-rrll', [CasosRRLLController::class, 'index']);

// Crear Caso
$router->get('/casos-rrll/create', [CasosRRLLController::class, 'create']);
$router->post('/casos-rrll/store', [CasosRRLLController::class, 'store']);

// Ver Detalles del Caso
$router->get('/casos-rrll/show/{id}', [CasosRRLLController::class, 'show']);

// Editar Caso
$router->get('/casos-rrll/edit/{id}', [CasosRRLLController::class, 'edit']);
$router->post('/casos-rrll/update/{id}', [CasosRRLLController::class, 'update']);

// Cambiar Estado del Caso
$router->post('/casos-rrll/cambiar-estado/{id}', [CasosRRLLController::class, 'cambiarEstado']);

// Cambiar Responsable del Caso
$router->post('/casos-rrll/cambiar-responsable/{id}', [CasosRRLLController::class, 'cambiarResponsable']);

// Archivar Caso
$router->post('/casos-rrll/archivar/{id}', [CasosRRLLController::class, 'archivar']);

// Eliminar Caso
$router->post('/casos-rrll/delete/{id}', [CasosRRLLController::class, 'delete']);

// Adjuntar documento al expediente o etapa
$router->post('/casos-rrll/{casoId}/documentos/upload', [CasosRRLLController::class, 'adjuntarDocumento']);

// =============== RUTAS PARA ETAPAS ===============

// Listado de Etapas de un Caso
$router->get('/casos-rrll/{casoId}/etapas', [CasosRRLLController::class, 'etapas']);

// Crear Etapa
$router->get('/casos-rrll/{casoId}/etapas/create', [CasosRRLLController::class, 'crearEtapa']);
$router->post('/casos-rrll/{casoId}/etapas/store', [CasosRRLLController::class, 'guardarEtapa']);

// Editar Etapa
$router->get('/casos-rrll/etapas/{etapaId}/edit', [CasosRRLLController::class, 'editarEtapa']);
$router->post('/casos-rrll/etapas/{etapaId}/update', [CasosRRLLController::class, 'actualizarEtapa']);

// Cambiar Estado de Etapa
$router->post('/casos-rrll/etapas/{etapaId}/cambiar-estado', [CasosRRLLController::class, 'cambiarEstadoEtapa']);

// Historial de Etapas
$router->get('/casos-rrll/{casoId}/etapas/historial', [CasosRRLLController::class, 'historialEtapas']);

// Eliminar Etapa
$router->post('/casos-rrll/etapas/{etapaId}/delete', [CasosRRLLController::class, 'eliminarEtapa']);

// =============== RUTAS PARA REPORTES ===============

// Exportar Casos a PDF
$router->get('/casos-rrll/reporte/pdf', [CasosRRLLController::class, 'exportarPDF']);

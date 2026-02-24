<?php
$title = "Dashboard - SGA-SEBANA";
ob_start();
?>

<!-- ROW 1: WELCOME & OVERVIEW -->
<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Resumen del Sistema</h2>
            <!-- Quick Date Badge -->
            <button class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-calendar-note"></i><?= date('d M, Y') ?>
            </button>
        </div>
    </div>
</div>

<!-- ROW 2: STATS CARDS -->
<div class="row m-t-25">
    <!-- AFILIADOS -->
    <div class="col-sm-6 col-lg-3">
        <div class="overview-item overview-item--c1">
            <div class="overview__inner">
                <div class="overview-box clearfix">
                    <div class="icon">
                        <i class="zmdi zmdi-account-o"></i>
                    </div>
                    <div class="text">
                        <h2><?= $stats['afiliados']['total'] ?></h2>
                        <span>Afiliados Totales</span>
                    </div>
                </div>
                <div class="overview-chart">
                    <!-- Sparkline placeholder or small text -->
                    <div style="color: rgba(255,255,255,0.8); font-size: 13px; text-align: right; padding-right: 20px;">
                        Activos: <strong><?= $stats['afiliados']['activos'] ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- PUESTOS (NEW) -->
    <div class="col-sm-6 col-lg-3">
        <div class="overview-item overview-item--c2">
            <div class="overview__inner">
                <div class="overview-box clearfix">
                    <div class="icon">
                        <i class="zmdi zmdi-briefcase"></i>
                    </div>
                    <div class="text">
                        <h2><?= $stats['puestos']['total'] ?></h2>
                        <span>Puestos Totales</span>
                    </div>
                </div>
                <div class="overview-chart">
                    <div style="color: rgba(255,255,255,0.8); font-size: 13px; text-align: right; padding-right: 20px;">
                        Ocupados: <strong><?= $stats['puestos']['activos'] ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JUNTA DIRECTIVA -->
    <div class="col-sm-6 col-lg-3">
        <div class="overview-item overview-item--c3">
            <div class="overview__inner">
                <div class="overview-box clearfix">
                    <div class="icon">
                        <i class="zmdi zmdi-accounts-alt"></i>
                    </div>
                    <div class="text">
                        <h2><?= $stats['junta']['total'] ?></h2>
                        <span>Miembros Junta</span>
                    </div>
                </div>
                <div class="overview-chart">
                    <div style="color: rgba(255,255,255,0.8); font-size: 13px; text-align: right; padding-right: 20px;">
                        Vigentes
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- USUARIOS / LOGS -->
    <div class="col-sm-6 col-lg-3">
        <div class="overview-item overview-item--c4">
            <div class="overview__inner">
                <div class="overview-box clearfix">
                    <div class="icon">
                        <i class="zmdi zmdi-receipt"></i>
                    </div>
                    <div class="text">
                        <h2><?= $stats['logs_hoy']['total'] ?></h2>
                        <span>Acciones Hoy</span>
                    </div>
                </div>
                <div class="overview-chart">
                    <div style="color: rgba(255,255,255,0.8); font-size: 13px; text-align: right; padding-right: 20px;">
                        En Bitácora
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 3: CHARTS -->
<div class="row">
    <!-- AFILIADOS GROWTH CHART -->
    <div class="col-lg-8">
        <div class="au-card recent-report">
            <div class="au-card-inner">
                <h3 class="title-2">Crecimiento de Afiliados (Últimos 6 Meses)</h3>
                <div class="chart-info">
                    <div class="chart-info__left">
                        <div class="chart-note">
                            <span class="dot dot--blue"></span>
                            <span>Afiliaciones</span>
                        </div>
                    </div>
                </div>
                <div class="recent-report__chart">
                    <canvas id="afiliadosGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- PUESTOS DISTRIBUTION CHART -->
    <div class="col-lg-4">
        <div class="au-card chart-percent-card">
            <div class="au-card-inner">
                <h3 class="title-2 tm-b-5">Estado de Puestos</h3>
                <div class="row no-gutters">
                    <div class="col-xl-12">
                        <div class="percent-chart">
                            <canvas id="puestosPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 4: RECENT ACTIVITY & QUICK ACTIONS -->
<div class="row">
    <!-- RECENT ACTIVITY (FEED) -->
    <div class="col-lg-8">
        <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
            <div class="au-card-title" style="background-image:url('/SGA-SEBANA/public/assets/img/bg-title-01.jpg');">
                <div class="bg-overlay bg-overlay--blue"></div>
                <h3><i class="zmdi zmdi-time-interval"></i>Actividad Reciente</h3>
            </div>
            <div class="au-task js-list-load">
                <div class="au-task-list js-scrollbar3">
                    <?php if (empty($recentLogs)): ?>
                    <div class="au-task__item">
                        <div class="au-task__item-inner">
                            <h5 class="task">No hay actividad reciente.</h5>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($recentLogs as $log): ?>
                    <?php
                            // Determine color/icon based on action
                            $iconClass = 'zmdi-file-text';
                            $itemClass = 'au-task__item--primary';
                            if ($log['accion'] === 'CREATE') {
                                $itemClass = 'au-task__item--success';
                                $iconClass = 'zmdi-plus-circle';
                            } elseif ($log['accion'] === 'UPDATE') {
                                $itemClass = 'au-task__item--warning';
                                $iconClass = 'zmdi-edit';
                            } elseif ($log['accion'] === 'DELETE') {
                                $itemClass = 'au-task__item--danger';
                                $iconClass = 'zmdi-delete';
                            }
                            ?>
                    <div class="au-task__item <?= $itemClass ?>">
                        <div class="au-task__item-inner">
                            <h5 class="task">
                                <a href="#"><?= $log['descripcion'] ?></a>
                            </h5>
                            <span class="time">
                                <i class="zmdi <?= $iconClass ?>"></i>
                                <?= date('H:i', strtotime($log['fecha_creacion'])) ?> -
                                <?= $log['usuario_nombre'] ?? 'Sistema' ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="au-task__footer">
                    <a href="/SGA-SEBANA/public/bitacora" class="au-btn au-btn-load">Ver bitácora completa</a>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="col-lg-4">
        <div class="au-card au-card--bg-blue au-card-top-countries m-b-40">
            <div class="au-card-inner">
                <div class="table-responsive">
                    <h3 class="title-2 m-b-20" style="color: white;">Acciones Rápidas</h3>
                    <table class="table table-top-countries">
                        <tbody>
                            <tr>
                                <td>
                                    <a href="/SGA-SEBANA/public/afiliados/create"
                                        style="color: white; display: block; width: 100%;">
                                        <i class="zmdi zmdi-account-add m-r-10"></i> Nuevo Afiliado
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/SGA-SEBANA/public/puestos/create"
                                        style="color: white; display: block; width: 100%;">
                                        <i class="zmdi zmdi-assignment-account m-r-10"></i> Asignar Puesto
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/SGA-SEBANA/public/junta/create"
                                        style="color: white; display: block; width: 100%;">
                                        <i class="zmdi zmdi-plus-circle m-r-10"></i> Miembro Junta
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/SGA-SEBANA/public/puestos/reportes"
                                        style="color: white; display: block; width: 100%;">
                                        <i class="zmdi zmdi-chart m-r-10"></i> Ver Reportes
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="copyright">
            <p>Copyright © <?= date('Y') ?> SGA-SEBANA. All rights reserved.</p>
        </div>
    </div>
</div>

<!-- CHARTS JS INITIALIZATION -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. AFILIADOS GROWTH CHART
    try {
        const ctxGrowth = document.getElementById("afiliadosGrowthChart");
        if (ctxGrowth) {
            ctxGrowth.height = 150;
            new Chart(ctxGrowth, {
                type: 'line',
                data: {
                    labels: <?= json_encode($stats['charts']['growth']['labels']) ?>,
                    datasets: [{
                        label: 'Nuevas Afiliaciones',
                        backgroundColor: 'transparent',
                        borderColor: 'rgba(63, 166, 255, 0.85)', // Blue
                        pointBackgroundColor: 'rgba(63, 166, 255, 0.85)',
                        borderWidth: 3,
                        data: <?= json_encode($stats['charts']['growth']['data']) ?>
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }, // Hide legend
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        },
                        y: {
                            grid: {
                                color: "rgba(0,0,0,0.05)",
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    } catch (e) {
        console.error("Error init Growth Chart", e);
    }

    // 2. PUESTOS DISTRIBUTION CHART (Doughnut)
    try {
        const ctxPie = document.getElementById("puestosPieChart");
        if (ctxPie) {
            ctxPie.height = 200;
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [
                            <?= $stats['puestos']['activos'] ?>,
                            <?= $stats['puestos']['finalizados'] ?>,
                            <?= $stats['puestos']['suspendidos'] ?>
                        ],
                        backgroundColor: [
                            "rgba(0, 181, 233, 0.9)", // Activos (Blue/Cyan)
                            "rgba(250, 66, 81, 0.9)", // Finalizados (Red)
                            "rgba(255, 193, 7, 0.9)" // Suspendidos (Yellow)
                        ],
                        hoverBackgroundColor: [
                            "rgba(0, 181, 233, 0.7)",
                            "rgba(250, 66, 81, 0.7)",
                            "rgba(255, 193, 7, 0.7)"
                        ]
                    }],
                    labels: ["Activos", "Finalizados", "Suspendidos"]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    } catch (e) {
        console.error("Error init Pie Chart", e);
    }
});
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>
<div class="au-card m-b-30">
    <div class="au-card-inner">

        <h3 class="title-2 m-b-30">Proximas Visitas</h3>

        <div class="upcoming-events">

            <?php if (!empty($visits)): ?>

                <?php foreach ($visits as $visit): ?>

                    <?php
                        // Validar que exista la fecha
                        if (empty($visit['fecha_visita'])) {
                            continue;
                        }

                        $date = new DateTime($visit['fecha_visita']);

                        // Meses en español
                        $meses = [
                            'Jan' => 'ENE', 'Feb' => 'FEB', 'Mar' => 'MAR', 'Apr' => 'ABR',
                            'May' => 'MAY', 'Jun' => 'JUN', 'Jul' => 'JUL', 'Aug' => 'AGO',
                            'Sep' => 'SEP', 'Oct' => 'OCT', 'Nov' => 'NOV', 'Dec' => 'DIC'
                        ];

                        $month = $meses[$date->format('M')];
                        $day = $date->format('d');
                    ?>

                    <div class="event-item d-flex align-items-start mb-3 p-3 border rounded">

                        <div class="event-date text-center me-3">
                            <div class="fs-6 fw-bold text-primary"><?= $month ?></div>
                            <div class="fs-4 fw-bold"><?= $day ?></div>
                        </div>

                        <div class="event-details flex-grow-1">
                            <h6 class="mb-1">
                                <?= htmlspecialchars($visit['nombre_empleado']) ?>
                            </h6>

                            <small class="text-muted">
                                <?= date("H:i", strtotime($visit['hora_visita'])) ?>
                            </small>

                            <div class="mt-1">
                                <span class="badge bg-success">Visita</span>
                            </div>
                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p class="text-muted">No hay visitas próximas</p>

            <?php endif; ?>

        </div>

    </div>
</div>
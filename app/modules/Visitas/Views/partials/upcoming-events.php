<div class="au-card m-b-30">
    <div class="au-card-inner">

        <h3 class="title-2 m-b-30">Proximas Visitas</h3>

        <div class="upcoming-events">

            <?php if(!empty($visits)): ?>

            <?php foreach($visits as $visit): ?>

            <?php
                $date = new DateTime($visit['fecha_visita']);
                $month = strtoupper($date->format('M'));
                $day = $date->format('d');
                ?>

            <div class="event-item d-flex align-items-start mb-3 p-3 border rounded">

                <div class="event-date text-center me-3">
                    <div class="fs-6 fw-bold text-primary"><?= $month ?></div>
                    <div class="fs-4 fw-bold"><?= $day ?></div>
                </div>

                <div class="event-details flex-grow-1">
                    <h6 class="mb-1"><?= htmlspecialchars($visit['nombre_empleado']) ?></h6>

                    <small class="text-muted">
                        <?= date("h:i A", strtotime($visit['hora_visita'])) ?>
                    </small>

                    <div class="mt-1">
                        <span class="badge bg-success">Visit</span>
                    </div>
                </div>

            </div>

            <?php endforeach; ?>

            <?php else: ?>

            <p class="text-muted">No upcoming visits</p>

            <?php endif; ?>

        </div>

    </div>
</div>
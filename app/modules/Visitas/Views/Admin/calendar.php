<?php
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap">
            <h2 class="title-1">Calendario</h2>

            <a href="/SGA-SEBANA/public/admin/visit-requests" class="au-btn au-btn-icon au-btn--blue">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>

        </div>
    </div>
</div>

<div class="row m-t-25">

    <div class="col-lg-9">
        <div class="au-card">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-40">Calendario de eventos</h3>

                <div id="calendar" class="calendar-container"></div>

            </div>
        </div>
    </div>

    <div class="col-lg-3">

        <?php include BASE_PATH . '/app/modules/Visitas/Views/partials/upcoming-events.php'; ?>



    </div>

</div>

<div class="row">

    <div class="col-md-12">

        <div class="copyright">

            <p>Copyright © 2025 Colorlib. All rights reserved.</p>

        </div>

    </div>

</div>

<script src="/SGA-SEBANA/public/assets/vendor/fullcalendar-6.1.11/index.global.min.js"></script>


<script src="/SGA-SEBANA/public/assets/js/calendar.js"></script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>
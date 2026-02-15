<?php
/**
 * Vista de Reporte de Exclusiones/Bajas
 */
ob_start();
?>

<div class="row">
  <div class="col-md-12">
    <h2 class="title-1 mb-4">Reporte de Exclusiones / Bajas</h2>

    <!-- FILTROS -->
    <div class="card">
      <div class="card-header">
        <strong><i class="zmdi zmdi-filter-list"></i> Filtros de Reporte</strong>
      </div>
      <div class="card-body">
        <form action="/SGA-SEBANA/public/ReporteDeExclusionDeAfiliado" method="GET">
          <div class="row">
            <div class="col-md-4">
              <label for="nombre">Filtrar por nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control"
                     value="<?= htmlspecialchars($filtros['nombre'] ?? '') ?>"
                     placeholder="Ingrese nombre">
            </div>
            <div class="col-md-8 text-right align-self-end">
              <button type="submit" class="btn btn-primary">
                <i class="zmdi zmdi-search"></i> Buscar
              </button>
              <a href="/SGA-SEBANA/public/ReporteDeExclusionDeAfiliado/exportar/pdf" class="btn btn-danger">
                <i class="zmdi zmdi-download"></i> Exportar PDF
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- TABLA -->
    <div class="table-responsive table-responsive-data2 mt-4">
      <table class="table table-data2">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Cédula</th>
            <th>Fecha Baja</th>
            <th>Motivo</th>
            <th>Tipo Baja</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($exclusiones)): ?>
            <tr>
              <td colspan="7" class="text-center p-4">
                No se encontraron exclusiones en el periodo seleccionado.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($exclusiones as $exc): ?>
              <tr class="tr-shadow">
                <td><?= htmlspecialchars($exc['nombre_completo']) ?></td>
                <td><?= htmlspecialchars($exc['cedula']) ?></td>
                <td><?= htmlspecialchars($exc['fecha_baja']) ?></td>
                <td><?= htmlspecialchars($exc['motivo_baja']) ?></td>
                <td><?= htmlspecialchars($exc['tipo_baja']) ?></td>
                <td>
                  <span class="status--denied"><?= htmlspecialchars($exc['estado']) ?></span>
                </td>
                <td>
                  <div class="table-data-feature">
                    <a href="/SGA-SEBANA/public/ReporteDeExclusionDeAfiliado/show/<?= $exc['id'] ?>" class="item" title="Ver Detalle">
                      <i class="zmdi zmdi-eye"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <tr class="spacer"></tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>
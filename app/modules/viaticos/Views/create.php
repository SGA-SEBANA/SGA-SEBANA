<?php
/**
 * Vista: Crear Solicitud de Viáticos (Calculadora SEBANA Pro Integrada)
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Nueva Solicitud de Viáticos</h2>
            <a href="/SGA-SEBANA/public/viaticos" class="btn btn-secondary">
                <i class="zmdi zmdi-arrow-left me-2"></i> Volver
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle me-2"></i> Hubo un error al procesar la solicitud en la base de datos.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="/SGA-SEBANA/public/viaticos/store" method="POST" id="formViaticos" enctype="multipart/form-data">
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <strong><i class="zmdi zmdi-cutlery me-2"></i> 1. Gastos de Alimentación</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="d-flex align-items-center mb-3">
                                <input type="checkbox" name="aplica_desayuno" id="aplica_desayuno" class="form-check-input me-2 viatico-calc" style="width: 20px; height: 20px;">
                                <span class="ml-2">Desayuno (₡4,200)</span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="d-flex align-items-center mb-3">
                                <input type="checkbox" name="aplica_almuerzo" id="aplica_almuerzo" class="form-check-input me-2 viatico-calc" style="width: 20px; height: 20px;">
                                <span class="ml-2">Almuerzo (₡5,600)</span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="d-flex align-items-center mb-3">
                                <input type="checkbox" name="aplica_cena" id="aplica_cena" class="form-check-input me-2 viatico-calc" style="width: 20px; height: 20px;">
                                <span class="ml-2">Cena (₡5,600)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <strong><i class="zmdi zmdi-car me-2"></i> 2. Transporte y Kilometraje</strong>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">¿Aplica cobro por transporte/kilometraje?</label>
                        <div class="mt-2">
                            <label class="mr-4">
                                <input type="radio" name="aplica_transporte" id="transporte_si" value="1" class="viatico-toggle"> Sí, aplica
                            </label>
                            <label>
                                <input type="radio" name="aplica_transporte" id="transporte_no" value="0" class="viatico-toggle" checked> No aplica
                            </label>
                        </div>
                    </div>

                    <div id="bloque_transporte" style="display: none; background: #f8f9fa; padding: 20px; border-radius: 8px;">
                        <h5 class="mb-3 text-muted">Calculadora de Tarifas CGR</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Año del vehículo</label>
                                <input type="number" id="v_year" name="v_year" value="2026" min="1900" max="2030" class="form-control viatico-calc">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Tipo de vehículo</label>
                                <select id="v_type" name="v_type" class="form-control viatico-calc">
                                    <option value="liviano">Vehículo liviano</option>
                                    <option value="motocicleta">Motocicleta</option>
                                    <option value="hibrido">Vehículo Híbrido</option>
                                    <option value="electrico">Vehículo Eléctrico</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3" id="group_fuel">
                                <label>Combustible</label>
                                <select id="v_fuel" name="v_fuel" class="form-control viatico-calc">
                                    <option value="gasolina">Gasolina</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="electrico">Eléctrico</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Cilindrada (cc)</label>
                                <input type="number" id="v_cc" name="v_cc" value="1600" class="form-control viatico-calc">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Carrocería</label>
                                <select id="v_body" name="v_body" class="form-control viatico-calc">
                                    <option value="sedan">Sedán</option>
                                    <option value="hatchback">Hatchback</option>
                                    <option value="suv">SUV</option>
                                    <option value="familiar">Familiar</option>
                                    <option value="pickup">Pick-up</option>
                                    <option value="rural">Rural</option>
                                    <option value="van">Van/Panel</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Doble tracción (4x4)</label>
                                <select id="v_4x4" name="v_4x4" class="form-control viatico-calc">
                                    <option value="no">No</option>
                                    <option value="si">Sí</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="text-primary font-weight-bold">Kilómetros recorridos</label>
                                <input type="number" id="v_km" name="v_km" value="0" step="0.01" class="form-control viatico-calc border-primary" style="font-size: 1.2rem;">
                            </div>
                            <div class="col-md-12">
                                <label>Enlace de Google Maps (Respaldo)</label>
                                <input type="url" name="enlace_maps" class="form-control" placeholder="https://maps.google.com/...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <strong><i class="zmdi zmdi-attachment-alt me-2"></i> 3. Respaldos y Comprobantes</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">Adjuntar Archivo de Respaldo</label>
                            <input type="file" name="archivo_comprobante" id="archivo_comprobante" class="form-control" accept=".pdf, .jpg, .jpeg, .png">
                            <small class="text-muted">Formatos permitidos: PDF, JPG, PNG. Tamaño máximo: 5MB.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-info">
                <div class="card-body bg-light text-center">
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="text-muted">Subtotal Alimentación</h5>
                            <h3 id="res_monto_alimentacion" class="text-dark">₡0,00</h3>
                        </div>
                        <div class="col-md-4 border-left border-right">
                            <h5 class="text-muted">Subtotal Transporte</h5>
                            <h3 id="res_monto_transporte" class="text-dark">₡0,00</h3>
                            <small class="text-muted"><span id="res_cat">Vehículo</span> | Tarifa: <span id="res_tarifa">0</span> ₡/km</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-info font-weight-bold">TOTAL A PAGAR</h5>
                            <h2 id="res_total_pagar" class="text-info font-weight-bold">₡0,00</h2>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="tarifa_km_oculta" id="tarifa_km_oculta" value="0">
            <input type="hidden" name="monto_transporte_oculto" id="monto_transporte_oculto" value="0">
            <input type="hidden" name="monto_alimentacion_oculto" id="monto_alimentacion_oculto" value="0">
            <input type="hidden" name="total_pagar_oculto" id="total_pagar_oculto" value="0">

            <div class="text-center mb-5">
                <button type="submit" class="btn btn-success btn-lg px-5">
                    <i class="zmdi zmdi-save"></i> Guardar Solicitud de Viáticos
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Matriz de tarifas
    const tablaTarifas = {
        rural_gasolina: [293.90, 273.00, 261.55, 255.78, 253.39, 253.05, 253.05, 250.28, 247.27, 244.84, 242.92],
        rural_diesel:   [266.91, 246.46, 235.12, 229.25, 226.66, 226.02, 226.02, 222.75, 219.56, 216.92, 214.78],
        liviano_gas_a:  [199.65, 187.53, 180.97, 177.76, 176.54, 176.52, 176.52, 175.25, 173.66, 172.41, 171.46],
        liviano_gas_b:  [254.10, 236.41, 226.70, 221.77, 219.71, 219.37, 219.37, 216.92, 214.32, 212.21, 210.52],
        liviano_diesel: [231.28, 215.98, 207.65, 203.50, 201.86, 201.72, 201.72, 199.89, 197.79, 196.10, 194.78],
        moto_gasolina:  [71.56, 69.75, 69.05, 69.01, 69.01, 69.01, 69.01, 69.01, 69.01, 69.01, 69.01],
        moto_electrica: [60.24, 53.08, 48.84, 46.35, 46.35, 46.35, 46.35, 46.35, 46.35, 46.35, 46.35],
        hibrido:        [249.30, 231.02, 220.98, 215.90, 213.79, 213.48, 213.48, 211.10, 208.54, 206.51, 204.94],
        electrico:      [190.97, 167.38, 153.77, 146.17, 142.19, 140.43, 140.01, 134.94, 130.63, 126.99, 123.98]
    };

    // 1. Lógica para Ocultar/Mostrar Transporte
    const radiosTransporte = document.querySelectorAll('.viatico-toggle');
    const bloqueTransporte = document.getElementById('bloque_transporte');

    radiosTransporte.forEach(radio => {
        radio.addEventListener('change', function() {
            if (document.getElementById('transporte_si').checked) {
                bloqueTransporte.style.display = 'block';
            } else {
                bloqueTransporte.style.display = 'none';
            }
            calcularTotales(); 
        });
    });

    // 2. Función Maestra de Cálculo
    function calcularTotales() {
        
        let totalAlimentacion = 0;
        if(document.getElementById('aplica_desayuno').checked) totalAlimentacion += 4200;
        if(document.getElementById('aplica_almuerzo').checked) totalAlimentacion += 5600;
        if(document.getElementById('aplica_cena').checked) totalAlimentacion += 5600;

        document.getElementById('res_monto_alimentacion').innerText = "₡" + totalAlimentacion.toLocaleString('es-CR', {minimumFractionDigits: 2});
        document.getElementById('monto_alimentacion_oculto').value = totalAlimentacion;

        let totalTransporte = 0;
        let tarifaFinal = 0;
        
        if (document.getElementById('transporte_si').checked) {
            const year = parseInt(document.getElementById('v_year').value) || new Date().getFullYear();
            const type = document.getElementById('v_type').value;
            const fuel = document.getElementById('v_fuel').value;
            const cc = parseInt(document.getElementById('v_cc').value) || 0;
            const body = document.getElementById('v_body').value;
            const is4x4 = document.getElementById('v_4x4').value === 'si';
            const km = parseFloat(document.getElementById('v_km').value) || 0;

            let edad = new Date().getFullYear() - year;
            if (edad < 0) edad = 0;
            if (edad > 10) edad = 10;

            let colKey = "";
            let catName = "";
            const isRural = (['rural', 'familiar', 'pickup'].includes(body) && cc > 2200 && is4x4);

            if (type === 'hibrido') {
                colKey = "hibrido"; catName = "Veh. Híbrido";
            } else if (type === 'electrico') {
                colKey = "electrico"; catName = "Veh. Eléctrico";
            } else if (type === 'motocicleta') {
                colKey = (fuel === 'electrico') ? "moto_electrica" : "moto_gasolina";
                catName = "Moto " + fuel;
            } else if (isRural) {
                colKey = (fuel === 'diesel') ? "rural_diesel" : "rural_gasolina";
                catName = "Rural " + fuel;
            } else {
                if (fuel === 'diesel') {
                    colKey = "liviano_diesel"; catName = "Liviano Diesel";
                } else if (fuel === 'electrico') {
                    colKey = "electrico"; catName = "Veh. Eléctrico";
                } else {
                    if (cc <= 1600) { colKey = "liviano_gas_a"; catName = "Gasolina (≤1600cc)"; } 
                    else { colKey = "liviano_gas_b"; catName = "Gasolina (>1600cc)"; }
                }
            }

            tarifaFinal = tablaTarifas[colKey][edad];
            totalTransporte = km * tarifaFinal;
            
            document.getElementById('res_cat').innerText = catName;
        }

        document.getElementById('res_tarifa').innerText = tarifaFinal.toLocaleString('es-CR', {minimumFractionDigits: 2});
        document.getElementById('tarifa_km_oculta').value = tarifaFinal;
        document.getElementById('res_monto_transporte').innerText = "₡" + totalTransporte.toLocaleString('es-CR', {minimumFractionDigits: 2});
        document.getElementById('monto_transporte_oculto').value = totalTransporte;

        let granTotal = totalAlimentacion + totalTransporte;
        document.getElementById('res_total_pagar').innerText = "₡" + granTotal.toLocaleString('es-CR', {minimumFractionDigits: 2});
        document.getElementById('total_pagar_oculto').value = granTotal;
    }

    const inputsCalculables = document.querySelectorAll('.viatico-calc');
    inputsCalculables.forEach(input => {
        input.addEventListener('input', calcularTotales);
        input.addEventListener('change', calcularTotales);
    });

    calcularTotales();
});
</script>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>
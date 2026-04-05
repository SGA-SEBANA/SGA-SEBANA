/**
 * AFILIADOS - BUSCAR Y GESTIONAR AFILIADOS
 * ========================================
 * Script para buscar afiliados por cédula y precarga de datos
 */

// ========================================================================
// BUSCAR AFILIADOS POR CÉDULA
// ========================================================================

document.getElementById("buscarCedula").addEventListener("keyup", function () {
  let cedula = this.value;

  // No buscar si la cédula tiene menos de 3 caracteres
  if (cedula.length < 3) return;

  // Realizar búsqueda
  fetch("/SGA-SEBANA/public/afiliados/buscar?cedula=" + cedula)
    .then((res) => res.json())
    .then((data) => {
      let select = document.getElementById("afiliadoSelect");
      select.innerHTML = '<option value="">-- Seleccione --</option>';

      // Llenar el select con los resultados
      data.forEach((afiliado) => {
        let option = document.createElement("option");
        option.value = afiliado.id;
        option.text = afiliado.nombre_completo + " (" + afiliado.cedula + ")";

        // Guardar datos en atributos para uso posterior
        option.dataset.nombre = afiliado.nombre_completo;
        option.dataset.cedula = afiliado.cedula;

        select.appendChild(option);
      });
    });
});

// ========================================================================
// CARGAR DATOS DEL AFILIADO SELECCIONADO
// ========================================================================

document
  .getElementById("afiliadoSelect")
  .addEventListener("change", function () {
    let selected = this.options[this.selectedIndex];

    // Validar que hay un valor seleccionado
    if (!selected.value) return;

    // Precarga de datos en los campos del formulario
    document.getElementById("nombreEmpleado").value = selected.dataset.nombre;
    document.getElementById("numeroEmpleado").value = selected.dataset.cedula;
  });

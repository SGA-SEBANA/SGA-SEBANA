/**
 * Busqueda de afiliados para formulario de visitas (modo admin).
 * El script se autodesactiva si no existen los elementos requeridos.
 */
(function () {
  const inputCedula = document.getElementById("buscarCedula");
  const selectAfiliado = document.getElementById("afiliadoSelect");
  const inputNombre = document.getElementById("nombreEmpleado");
  const inputNumero = document.getElementById("numeroEmpleado");

  if (!inputCedula || !selectAfiliado) {
    return;
  }

  function fillEmployeeFields() {
    if (!inputNombre || !inputNumero) {
      return;
    }

    const selected = selectAfiliado.options[selectAfiliado.selectedIndex];
    if (!selected || !selected.value) {
      return;
    }

    inputNombre.value = selected.dataset.nombre || "";
    inputNumero.value = selected.dataset.cedula || "";
  }

  inputCedula.addEventListener("keyup", function () {
    const cedula = (this.value || "").trim();

    if (cedula.length < 3) {
      return;
    }

    fetch("/SGA-SEBANA/public/afiliados/buscar?cedula=" + encodeURIComponent(cedula))
      .then(function (res) {
        return res.json();
      })
      .then(function (data) {
        selectAfiliado.innerHTML = '<option value="">-- Seleccione un afiliado --</option>';

        (data || []).forEach(function (afiliado) {
          const option = document.createElement("option");
          option.value = afiliado.id;
          option.text = (afiliado.nombre_completo || "") + " (" + (afiliado.cedula || "") + ")";
          option.dataset.nombre = afiliado.nombre_completo || "";
          option.dataset.cedula = afiliado.cedula || "";
          selectAfiliado.appendChild(option);
        });
      })
      .catch(function () {
        // Evitar romper la UI por un fallo de red.
      });
  });

  selectAfiliado.addEventListener("change", fillEmployeeFields);
})();

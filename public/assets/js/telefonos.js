/**
 * telefonos.js
 * Maneja la adición y eliminación dinámica
 * de filas de teléfono en los formularios.
 */

document.addEventListener('DOMContentLoaded', () => {

    const contenedor    = document.getElementById('contenedorTelefonos');
    const btnAgregarTel = document.getElementById('btnAgregarTel');

    // ── Agregar nueva fila de teléfono ──────────────────────
    if (btnAgregarTel) {
        btnAgregarTel.addEventListener('click', () => {

            // Clona la primera fila como plantilla
            const filaBase = contenedor.querySelector('.fila-telefono');
            const nuevaFila = filaBase.cloneNode(true);

            // Limpia el input del número
            nuevaFila.querySelector('input[name="numeros[]"]').value = '';

            // Resetea el select al primer option
            const select = nuevaFila.querySelector('select[name="tipos[]"]');
            select.selectedIndex = 0;

            contenedor.appendChild(nuevaFila);
        });
    }

    // ── Eliminar fila de teléfono ───────────────────────────
    // Usamos delegación de eventos para capturar
    // también los botones de filas recién creadas
    if (contenedor) {
        contenedor.addEventListener('click', (e) => {
            const btnEliminar = e.target.closest('.btnEliminarTel');

            if (btnEliminar) {
                const filas = contenedor.querySelectorAll('.fila-telefono');

                // Siempre deja al menos una fila
                if (filas.length > 1) {
                    btnEliminar.closest('.fila-telefono').remove();
                } else {
                    // Si es la última, solo limpia el input
                    const input = contenedor.querySelector('input[name="numeros[]"]');
                    if (input) input.value = '';
                }
            }
        });
    }

});
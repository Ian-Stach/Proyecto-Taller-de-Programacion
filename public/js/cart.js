/**
 * cart.js
 * -------
 * Maneja el envío de formularios "Añadir al carrito" sin recargar la página.
 *
 * Funciona mediante event delegation sobre document, interceptando todos
 * los formularios que tengan la clase .cart-add-form.
 *
 * Flujo por cada envío:
 *   1. Previene el submit nativo del navegador (sin reload).
 *   2. Llama a CartController@add vía fetch() con POST.
 *   3. Envía el header Accept: application/json para que el controlador
 *      y la validación de Laravel devuelvan JSON (no redirecciones).
 *   4. Muestra el resultado en el Bootstrap Toast #cart-toast:
 *        - Éxito:  fondo verde  + mensaje del servidor
 *        - Error:  fondo rojo   + primer error de validación
 *        - Fallo de red: mensaje genérico
 *   5. El botón queda deshabilitado durante la petición para evitar
 *      doble envío, y se restaura al terminar (finally).
 *
 * Dependencias:
 *   - Bootstrap JS (bootstrap.bundle.min.js) debe cargarse antes que
 *     este script porque se usa bootstrap.Toast.getOrCreateInstance().
 *   - El elemento #cart-toast debe existir en el DOM (inyectado por el
 *     layout principal dentro de @auth).
 */
document.addEventListener('DOMContentLoaded', function () {

    var toastEl   = document.getElementById('cart-toast');
    var toastBody = document.getElementById('cart-toast-body');

    // Si no hay usuario autenticado, el toast no existe en el DOM.
    if (!toastEl) return;

    /**
     * Reemplaza el contenido del .offcanvas-body del sidebar del carrito
     * con el HTML fresco devuelto por GET /cart/sidebar.
     * Se reemplaza solo el interior para no perder los event listeners
     * de Bootstrap registrados en el contenedor #carritoSidebar.
     */
    function refreshSidebar() {
        var sidebarBody = document.querySelector('#carritoSidebar .offcanvas-body');
        if (!sidebarBody) return;

        fetch('/cart/sidebar', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var temp    = document.createElement('div');
                temp.innerHTML = html;
                var newBody = temp.querySelector('.offcanvas-body');
                if (newBody) {
                    sidebarBody.innerHTML = newBody.innerHTML;
                }
            });
    }

    /**
     * Muestra el Bootstrap Toast con el mensaje dado.
     * @param {string}  message  Texto a mostrar.
     * @param {boolean} isError  true → fondo rojo; false → fondo verde.
     */
    function showToast(message, isError) {
        toastEl.classList.remove('bg-success', 'bg-danger');
        toastEl.classList.add(isError ? 'bg-danger' : 'bg-success');
        toastBody.textContent = message;
        bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 }).show();
    }

    // Event delegation: escucha cualquier submit dentro del documento.
    document.addEventListener('submit', function (e) {

        // ── AGREGAR AL CARRITO ──────────────────────────────────────────────
        var addForm = e.target.closest('.cart-add-form');
        if (addForm) {
            e.preventDefault();

            var btn          = addForm.querySelector('[type="submit"]');
            var originalText = btn.textContent;
            btn.disabled     = true;
            btn.textContent  = 'Agregando...';

            fetch(addForm.action, {
                method:  'POST',
                headers: {
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': addForm.querySelector('[name="_token"]').value,
                },
                body: new FormData(addForm),
            })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    showToast(result.data.message, false);
                    refreshSidebar();
                } else {
                    var errors     = result.data.errors || {};
                    var firstError = Object.values(errors)[0] || result.data.message || 'Error al agregar al carrito.';
                    showToast(Array.isArray(firstError) ? firstError[0] : firstError, true);
                }
            })
            .catch(function () {
                showToast('Error de conexión. Inténtalo de nuevo.', true);
            })
            .finally(function () {
                btn.disabled    = false;
                btn.textContent = originalText;
            });

            return;
        }

        // ── ELIMINAR DEL CARRITO (sidebar) ─────────────────────────────────
        var removeForm = e.target.closest('.cart-remove-form');
        if (removeForm) {
            e.preventDefault();

            var removeBtn          = removeForm.querySelector('[type="submit"]');
            removeBtn.disabled     = true;

            fetch(removeForm.action, {
                method:  'POST',          // el form envía POST con _method=DELETE
                headers: {
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': removeForm.querySelector('[name="_token"]').value,
                },
                body: new FormData(removeForm),
            })
            .then(function () {
                // CartController@remove no devuelve JSON, pero el resultado
                // no importa: siempre refrescamos el sidebar tras la petición.
                refreshSidebar();
            })
            .catch(function () {
                showToast('Error al eliminar el producto. Inténtalo de nuevo.', true);
                removeBtn.disabled = false;
            });
        }
    });
});

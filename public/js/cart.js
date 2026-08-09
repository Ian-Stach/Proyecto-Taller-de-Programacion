/**
 * cart.js
 * -------
 * Maneja el envío de formularios "Añadir al carrito" sin recargar la página.
 *
 * Funciona mediante event delegation sobre document, interceptando todos los formularios que tengan la clase .cart-add-form.
 *
 * Flujo por cada envío:
 *   1. Previene el submit nativo del navegador (sin reload).
 *   2. Llama a CartController@add vía fetch() con POST.
 *   3. Envía el header Accept: application/json para que el controlador y la validación de Laravel devuelvan JSON (no redirecciones).
 *   4. Muestra el resultado en el Bootstrap Toast #cart-toast:
 *        - Éxito:  fondo verde  + mensaje del servidor
 *        - Error:  fondo rojo   + primer error de validación
 *        - Fallo de red: mensaje genérico
 *   5. El botón queda deshabilitado durante la petición para evitar doble envío, y se restaura al terminar (finally).
 *
 * Dependencias:
 *   - Bootstrap JS (bootstrap.bundle.min.js) debe cargarse antes que este script porque se usa bootstrap.Toast.getOrCreateInstance().
 *   - El elemento #cart-toast debe existir en el DOM (inyectado por el layout principal dentro de @auth).
 */
document.addEventListener('DOMContentLoaded', function () {

    const toastEl   = document.getElementById('cart-toast');
    const toastBody = document.getElementById('cart-toast-body');

    if (!toastEl) return;     // Si no hay usuario autenticado, el toast no existe en el DOM.

    /**
     * Reemplaza el contenido del .offcanvas-body del sidebar del carritocon el HTML fresco devuelto por GET /cart/sidebar.
     * Se reemplaza solo el interior para no perder los event listeners de Bootstrap registrados en el contenedor #carritoSidebar.
     */
    async function refreshSidebar() {
        const sidebarBody = document.querySelector('#carritoSidebar .offcanvas-body');
        if (!sidebarBody) return;
        try {
            const res = await fetch('/cart/sidebar', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newBody = doc.querySelector('.offcanvas-body');
            if (newBody) sidebarBody.innerHTML = newBody.innerHTML;
        } catch (err) {
            console.error('Error al refrescar el carrito', err);
        }
    }

    /**
     * Muestra el Bootstrap Toast con el mensaje dado.
     * @param {string}  message  Texto a mostrar.
     * @param {boolean} isError  true → fondo rojo; false → fondo verde.
     */
    function showToast(message, isError = false) {
        toastEl.classList.remove('bg-success', 'bg-danger');
        toastEl.classList.add(isError ? 'bg-danger' : 'bg-success');
        toastBody.textContent = message;
        bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 }).show();
    }

    // Event delegation: escucha cualquier submit dentro del documento.
    document.addEventListener('submit', async (e) => {

        // AGREGAR AL CARRITO
        const addForm = e.target.closest('.cart-add-form');
        if (addForm) {
            e.preventDefault();

            const btn = addForm.querySelector('[type="submit"]');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.innerHTML  = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Agregando...';

            try {
                const res = await fetch(addForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': addForm.querySelector('[name="_token"]').value,
                    },
                    body: new FormData(addForm),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    showToast(data.message, false);
                    document.dispatchEvent(new CustomEvent('cart:added', { detail: data }));
                    await refreshSidebar();
                } else {
                    const errors = data.errors || {};
                    const firstError = Object.values(errors)[0] || data.message || 'Error al agregar al carrito.';
                    showToast(Array.isArray(firstError) ? firstError[0] : firstError, true);
                }
            } catch (err) {
                showToast('Error de conexión. Inténtalo de nuevo.', true);
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
            return;
        }

        const removeForm = e.target.closest('.cart-remove-form');
        if (removeForm) {
            e.preventDefault();
            const removeBtn = removeForm.querySelector('[type="submit"]');
            removeBtn.disabled = true ;
            try {
                await fetch(removeForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': removeForm.querySelector('[name="_token"]').value,
                    },
                    body: new FormData(removeForm),
                });
                document.dispatchEvent(new CustomEvent('cart:removed', { detail: { /* opcional */ } }));
                await refreshSidebar();
            } catch (err) {
                showToast('Error al eliminar el producto. Inténtalo de nuevo.', true);
                removeBtn.disabled = false;
            }
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const paginador = document.getElementById('paginador-ahorros');
    const btnAnterior = document.getElementById('pagina-anterior-ahorros');
    const btnSiguiente = document.getElementById('pagina-siguiente-ahorros');
    const tablaBody = document.getElementById('tabla-ahorros-body');

    if (!paginador || !btnAnterior || !btnSiguiente || !tablaBody) {
        return; // No hacer nada si los elementos no existen
    }

    let paginaActual = parseInt(paginador.dataset.paginaActual);
    let totalPaginas = parseInt(paginador.dataset.totalPaginas);

    // Función para formatear dinero
    const currencyFormatter = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    });

    async function cargarPagina(pagina) {
        try {
            // Ajusta esta URL a la estructura de tu proyecto
            const url = `/practica/Proyecto_dos/api/obtener_ahorros.php?pagina=${pagina}`;
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Error al cargar los datos (404 Not Found)');
            }
            const data = await response.json();

            if (data.success) {
                paginaActual = data.pagina_actual;
                totalPaginas = data.total_paginas;
                
                actualizarTabla(data.movimientos); // Usa la clave 'movimientos' del JSON
                actualizarPaginador();
            } else {
                tablaBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${data.message}</td></tr>`;
            }
        } catch (error) {
            console.error(error);
            tablaBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Error: ${error.message}</td></tr>`;
        }
    }

    function actualizarTabla(movimientos) {
        tablaBody.innerHTML = ''; 
        let nuevosDatos = '';

        if (movimientos.length === 0) {
            nuevosDatos = `<tr><td colspan="4" class="text-center">No hay movimientos en esta página.</td></tr>`;
        } else {
            movimientos.forEach(mov => {
                const fecha = new Date(mov.fecha + 'T00:00:00').toLocaleDateString('es-ES', {
                    day: '2-digit', month: '2-digit', year: 'numeric'
                });

                if (mov.tipo === 'deposito') {
                    nuevosDatos += `
                        <tr>
                            <td><span class="badge bg-success-subtle text-success-emphasis rounded-pill">Depósito</span></td>
                            <td>${escapeHTML(mov.descripcion)}</td>
                            <td>${fecha}</td>
                            <td class="fw-bold text-success">+ ${currencyFormatter.format(mov.monto)}</td>
                        </tr>
                    `;
                } else {
                    nuevosDatos += `
                        <tr>
                            <td><span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">Retiro</span></td>
                            <td>${escapeHTML(mov.descripcion)}</td>
                            <td>${fecha}</td>
                            <td class="fw-bold text-danger">- ${currencyFormatter.format(mov.monto)}</td>
                        </tr>
                    `;
                }
            });
        }
        tablaBody.innerHTML = nuevosDatos;
    }

    function actualizarPaginador() {
        btnAnterior.parentElement.classList.toggle('disabled', paginaActual <= 1);
        btnSiguiente.parentElement.classList.toggle('disabled', paginaActual >= totalPaginas);
        paginador.dataset.paginaActual = paginaActual;
    }

    btnAnterior.addEventListener('click', e => {
        e.preventDefault(); 
        if (paginaActual > 1) {
            cargarPagina(paginaActual - 1);
        }
    });

    btnSiguiente.addEventListener('click', e => {
        e.preventDefault();
        if (paginaActual < totalPaginas) {
            cargarPagina(paginaActual + 1);
        }
    });

    function escapeHTML(str) {
        if (str === null || str === undefined) return '';
        return str.toString()
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
    }
});
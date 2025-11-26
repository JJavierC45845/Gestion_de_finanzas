document.addEventListener('DOMContentLoaded', function() {
    const paginador = document.getElementById('paginador-ingresos');
    const btnAnterior = document.getElementById('pagina-anterior-ingresos');
    const btnSiguiente = document.getElementById('pagina-siguiente-ingresos');
    const tablaBody = document.getElementById('tabla-ingresos-body');

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
            const url = `/practica/Proyecto_dos/api/obtener_ingresos.php?pagina=${pagina}`;
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Error al cargar los datos (404 Not Found)');
            }
            const data = await response.json();

            if (data.success) {
                paginaActual = data.pagina_actual;
                totalPaginas = data.total_paginas;
                
                actualizarTabla(data.ingresos);
                actualizarPaginador();
            } else {
                tablaBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${data.message}</td></tr>`;
            }
        } catch (error) {
            console.error(error);
            tablaBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error: ${error.message}</td></tr>`;
        }
    }

    function actualizarTabla(ingresos) {
        tablaBody.innerHTML = ''; 
        let nuevosDatos = '';

        if (ingresos.length === 0) {
            nuevosDatos = `<tr><td colspan="5" class="text-center">No hay ingresos en esta página.</td></tr>`;
        } else {
            ingresos.forEach(ingreso => {
                const fecha = new Date(ingreso.fecha + 'T00:00:00').toLocaleDateString('es-ES', {
                    day: '2-digit', month: '2-digit', year: 'numeric'
                });

                nuevosDatos += `
                    <tr>
                        <td class="fw-bold text-success">${currencyFormatter.format(ingreso.monto)}</td>
                        <td>${escapeHTML(ingreso.categoria_nombre)}</td>
                        <td>${escapeHTML(ingreso.descripcion)}</td>
                        <td>${fecha}</td>
                        <td>
                            <button class="btn btn-modificar btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalIngreso"
                                    data-id="${ingreso.id}"
                                    data-monto="${ingreso.monto}"
                                    data-categoria_id="${ingreso.categoria_id}"
                                    data-descripcion="${escapeHTML(ingreso.descripcion)}"
                                    data-fecha="${ingreso.fecha}"
                                    data-accion="modificar">
                                Modificar
                            </button>
                            <button class="btn btn-eliminar btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar"
                                    data-id="${ingreso.id}">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                `;
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
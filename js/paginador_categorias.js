document.addEventListener('DOMContentLoaded', function() {
    
    // Función de utilidad
    function escapeHTML(str) {
        if (str === null || str === undefined) return '';
        return str.toString()
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
    }

    // --- BLOQUE 1: PAGINADOR DE INGRESOS ---
    const paginadorIngresos = document.getElementById('paginador-ingresos');
    const btnAnteriorIngresos = document.getElementById('pagina-anterior-ingresos');
    const btnSiguienteIngresos = document.getElementById('pagina-siguiente-ingresos');
    const tablaBodyIngresos = document.getElementById('tabla-categorias-ingreso-body');

    if (paginadorIngresos) {
        let paginaActualIngresos = parseInt(paginadorIngresos.dataset.paginaActual);
        let totalPaginasIngresos = parseInt(paginadorIngresos.dataset.totalPaginas);

        async function cargarPaginaIngresos(pagina) {
            const tipo = 'ingreso';
            const url = `/practica/Proyecto_dos/api/obtener_categorias.php?pagina=${pagina}&tipo=${tipo}`;
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    paginaActualIngresos = data.pagina_actual;
                    totalPaginasIngresos = data.total_paginas;
                    actualizarTablaIngresos(data.categorias);
                    actualizarPaginadorIngresos();
                } else {
                    tablaBodyIngresos.innerHTML = `<tr><td colspan="2" class="text-center text-danger">${data.message}</td></tr>`;
                }
            } catch (error) {
                tablaBodyIngresos.innerHTML = `<tr><td colspan="2" class="text-center text-danger">Error: ${error.message}</td></tr>`;
            }
        }

        function actualizarTablaIngresos(categorias) {
            tablaBodyIngresos.innerHTML = '';
            let nuevosDatos = '';
            if (categorias.length === 0) {
                nuevosDatos = `<tr><td colspan="2" class="text-center">No hay categorías.</td></tr>`;
            } else {
                categorias.forEach(cat => {
                    nuevosDatos += `
                        <tr>
                            <td>${escapeHTML(cat.nombre)}</td>
                            <td>
                                <button class="btn btn-modificar btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalModificarCategoria"
                                        data-id="${cat.id}" data-nombre="${escapeHTML(cat.nombre)}" data-tipo="ingreso">
                                    Modificar
                                </button>
                                <button class="btn btn-eliminar btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar"
                                        data-id="${cat.id}" data-tipo="ingreso">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            tablaBodyIngresos.innerHTML = nuevosDatos;
        }

        function actualizarPaginadorIngresos() {
            btnAnteriorIngresos.parentElement.classList.toggle('disabled', paginaActualIngresos <= 1);
            btnSiguienteIngresos.parentElement.classList.toggle('disabled', paginaActualIngresos >= totalPaginasIngresos);
            paginadorIngresos.dataset.paginaActual = paginaActualIngresos;
        }

        btnAnteriorIngresos.addEventListener('click', e => {
            e.preventDefault();
            if (paginaActualIngresos > 1) cargarPaginaIngresos(paginaActualIngresos - 1);
        });

        btnSiguienteIngresos.addEventListener('click', e => {
            e.preventDefault();
            if (paginaActualIngresos < totalPaginasIngresos) cargarPaginaIngresos(paginaActualIngresos + 1);
        });
    }

    // --- BLOQUE 2: PAGINADOR DE GASTOS ---
    const paginadorGastos = document.getElementById('paginador-gastos');
    const btnAnteriorGastos = document.getElementById('pagina-anterior-gastos');
    const btnSiguienteGastos = document.getElementById('pagina-siguiente-gastos');
    const tablaBodyGastos = document.getElementById('tabla-categorias-gasto-body');

    if (paginadorGastos) {
        let paginaActualGastos = parseInt(paginadorGastos.dataset.paginaActual);
        let totalPaginasGastos = parseInt(paginadorGastos.dataset.totalPaginas);

        async function cargarPaginaGastos(pagina) {
            const tipo = 'gasto';
            const url = `/practica/Proyecto_dos/api/obtener_categorias.php?pagina=${pagina}&tipo=${tipo}`;
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    paginaActualGastos = data.pagina_actual;
                    totalPaginasGastos = data.total_paginas;
                    actualizarTablaGastos(data.categorias);
                    actualizarPaginadorGastos();
                } else {
                    tablaBodyGastos.innerHTML = `<tr><td colspan="2" class="text-center text-danger">${data.message}</td></tr>`;
                }
            } catch (error) {
                tablaBodyGastos.innerHTML = `<tr><td colspan="2" class="text-center text-danger">Error: ${error.message}</td></tr>`;
            }
        }

        function actualizarTablaGastos(categorias) {
            tablaBodyGastos.innerHTML = '';
            let nuevosDatos = '';
            if (categorias.length === 0) {
                nuevosDatos = `<tr><td colspan="2" class="text-center">No hay categorías.</td></tr>`;
            } else {
                categorias.forEach(cat => {
                    nuevosDatos += `
                        <tr>
                            <td>${escapeHTML(cat.nombre)}</td>
                            <td>
                                <button class="btn btn-modificar btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalModificarCategoria"
                                        data-id="${cat.id}" data-nombre="${escapeHTML(cat.nombre)}" data-tipo="gasto">
                                    Modificar
                                </button>
                                <button class="btn btn-eliminar btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar"
                                        data-id="${cat.id}" data-tipo="gasto">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            tablaBodyGastos.innerHTML = nuevosDatos;
        }

        function actualizarPaginadorGastos() {
            btnAnteriorGastos.parentElement.classList.toggle('disabled', paginaActualGastos <= 1);
            btnSiguienteGastos.parentElement.classList.toggle('disabled', paginaActualGastos >= totalPaginasGastos);
            paginadorGastos.dataset.paginaActual = paginaActualGastos;
        }

        btnAnteriorGastos.addEventListener('click', e => {
            e.preventDefault();
            if (paginaActualGastos > 1) cargarPaginaGastos(paginaActualGastos - 1);
        });

        btnSiguienteGastos.addEventListener('click', e => {
            e.preventDefault();
            if (paginaActualGastos < totalPaginasGastos) cargarPaginaGastos(paginaActualGastos + 1);
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const paginador = document.getElementById('paginador-usuarios');
    const btnAnterior = document.getElementById('pagina-anterior');
    const btnSiguiente = document.getElementById('pagina-siguiente');
    const tablaBody = document.getElementById('tabla-usuarios-body');

    if (!paginador || !btnAnterior || !btnSiguiente || !tablaBody) {
        // Si faltan los elementos, no hacer nada (previene errores)
        return; 
    }

    let paginaActual = parseInt(paginador.dataset.paginaActual);
    let totalPaginas = parseInt(paginador.dataset.totalPaginas);

    async function cargarPagina(pagina) {
        try {
            // --- CORRECCIÓN CLAVE ---
            // Añadimos la ruta completa de tu proyecto
            const url = `/practica/Proyecto_dos/api/obtener_usuarios.php?pagina=${pagina}`;
            // -------------------------

            const response = await fetch(url);
            if (!response.ok) {
                // Esto se activa con el 404
                throw new Error('Error al cargar los datos (404 Not Found)');
            }
            const data = await response.json();

            if (data.success) {
                paginaActual = data.pagina_actual;
                totalPaginas = data.total_paginas;
                
                actualizarTabla(data.usuarios);
                actualizarPaginador();
            } else {
                console.error(data.message);
                tablaBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${data.message}</td></tr>`;
            }
        } catch (error) {
            console.error(error);
            tablaBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Error: ${error.message}</td></tr>`;
        }
    }

    function actualizarTabla(usuarios) {
        tablaBody.innerHTML = ''; 
        let nuevosDatos = '';

        if (usuarios.length === 0) {
            nuevosDatos = `<tr><td colspan="4" class="text-center">No hay usuarios en esta página.</td></tr>`;
        } else {
            usuarios.forEach(usuario => {
                const fecha = new Date(usuario.fecha_registro).toLocaleDateString('es-ES', {
                    day: '2-digit', month: '2-digit', year: 'numeric'
                });

                nuevosDatos += `
                    <tr>
                        <td style="display: none;">${usuario.id}</td>
                        <td>${escapeHTML(usuario.nombre_usuario)}</td>
                        <td>${fecha}</td>
                        <td>
                            <button class="btn btn-modificar btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalUsuario"
                                    data-id="${usuario.id}"
                                    data-nombre="${escapeHTML(usuario.nombre_usuario)}"
                                    data-accion="modificar">
                                Modificar
                            </button>
                            <button class="btn btn-eliminar btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalConfirmarEliminar"
                                    data-id="${usuario.id}">
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
        if (str === null) return '';
        return str.toString()
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
    }
});
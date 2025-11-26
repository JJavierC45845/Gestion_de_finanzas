document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const elementoError = document.getElementById('error');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenir recarga de la página

        // Ocultar errores previos
        elementoError.classList.add('d-none');

        // Crear un objeto FormData con los datos del formulario
        const formData = new FormData(loginForm);
        // Renombrar los campos para que coincidan con el backend
        formData.append('usuario', document.getElementById('usuario').value);
        formData.append('contrasena', document.getElementById('contrasena').value);

        // Enviar los datos a tu script PHP usando fetch
        fetch('api/log.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Esperar una respuesta JSON
        .then(data => {
            // Si el backend dice que el login fue exitoso...
            if (data.success) {
                // Redirigir a la página principal
                window.location.href = 'acceso_permitido.php';
            } else {
                // Si no, mostrar el mensaje de error que envió el backend
                elementoError.textContent = data.message;
                elementoError.classList.remove('d-none');
            }
        })
        .catch(error => {
            // Manejar errores de red o del servidor
            console.error('Error:', error);
            elementoError.textContent = 'Ocurrió un error de conexión. Intente de nuevo.';
            elementoError.classList.remove('d-none');
        });
    });
});
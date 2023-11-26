$(document).ready(function () {
    $('#carro').on('change', function () {
        var carroID = $(this).val();
        // Realiza una solicitud AJAX al servidor para obtener los recursos del carro seleccionado
        $.ajax({
            type: 'POST',
            url: 'actualizarVisual.php', // Reemplaza con la URL de tu script PHP
            data: {
                carroID: carroID
            },
            dataType: 'json',
            success: function (data) {
            },
            error: function (xhr, status, error) {
                // Maneja errores aquí
                console.log('Error: ' + error);
            }
        });
    });

});
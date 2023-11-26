$(document).ready(function () {



  $('#carro').on('change', function () {
    // Obtiene el valor seleccionado del carro
    var carroID = $(this).val();
    
    // Realiza la solicitud AJAX al servidor con el nuevo carroID
    $.ajax({
      url: 'actualizarVisual.php',
      type: 'POST',
      data: {
        carroID: carroID
      },
      dataType: 'json',
      success: function (data) {
        // Maneja la respuesta del servidor aquí
        let recursos = data.recursos;
        let recursosHtml = '';
        recursos.forEach(recurso => {
          recursosHtml += generarRecursosDiv(recurso);
        });
        document.getElementById('netbookContainer').innerHTML = recursosHtml;
        console.log(recursosHtml);
        console.log(data);
      },
      error: function (xhr, status, error) {
        // Maneja errores aquí
        console.log(xhr.responseText);
        if (xhr.status === 500) {
          alert('Hubo un error en el servidor: ' + xhr.responseJSON.error);
        } else {
          alert('Hubo un error al actualizar la información. Por favor, inténtalo de nuevo.');
        }
      }
    });
  });

  function generarRecursosDiv(recurso) {
    console.log(recurso.recurso_estado); // Imprime el estado del recurso
    const color = recurso.recurso_estado == 'Libre' ? '#d4edda' : '#f8d7da';
    return `
        <div class='netbook'  
             data-recurso_id='${recurso.recurso_id}' 
             data-recurso_nombre='${recurso.recurso_nombre}' 
             data-recurso_estado='${recurso.recurso_estado}' 
             data-reservado-por='${recurso.user_name}' 
             style='background-color: ${color}; width: 50px; height: 50px; margin: 10px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.15); display: flex; justify-content: center; align-items: center;'>
            <img src='netbook.png' alt='Netbook' style='width: 50%;'>
            <p>${recurso.recurso_nombre}</p>
        </div>
        `;
  }

});

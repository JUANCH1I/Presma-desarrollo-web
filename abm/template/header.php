<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../netbook/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
  <script src="visual.js"></script>
  <script src="ajaxVisual.js"></script>
  <script src="script.js"></script>
  <title>olimpiadas</title>
  <script>
    function asignarEtiqueta() {
        // Obtener el elemento select de etiquetas disponibles y asignadas
        var etiquetasDisponibles = document.getElementById("etiquetasDisponibles");
        var etiquetasAsignadas = document.getElementById("etiquetasAsignadas");

        // Recorrer las opciones seleccionadas en etiquetas disponibles
        for (var i = 0; i < etiquetasDisponibles.options.length; i++) {
          var opcion = etiquetasDisponibles.options[i];
          if (opcion.selected) {
            // Clonar la opción y agregarla a etiquetas asignadas
            var nuevaOpcion = opcion.cloneNode(true);
            etiquetasAsignadas.appendChild(nuevaOpcion);

            // Eliminar la opción de etiquetas disponibles
            opcion.remove();
            i--; // Ajustar el índice después de eliminar un elemento
          }
        }
      }

      function eliminarEtiqueta() {
        // Obtener el elemento select de etiquetas disponibles y asignadas
        var etiquetasDisponibles = document.getElementById("etiquetasDisponibles");
        var etiquetasAsignadas = document.getElementById("etiquetasAsignadas");

        // Recorrer las opciones seleccionadas en etiquetas asignadas
        for (var i = 0; i < etiquetasAsignadas.options.length; i++) {
          var opcion = etiquetasAsignadas.options[i];
          if (opcion.selected) {
            // Clonar la opción y agregarla a etiquetas disponibles
            var nuevaOpcion = opcion.cloneNode(true);
            etiquetasDisponibles.appendChild(nuevaOpcion);

            // Eliminar la opción de etiquetas asignadas
            opcion.remove();
            i--; // Ajustar el índice después de eliminar un elemento
          }
        }
      }
    $(document).ready(function() {
      
      $('#area').on('change', function() {
        var areaID = $(this).val();
        $('#carro').empty(); // Limpiar opciones anteriores

        // Realizar una petición AJAX para obtener los carros de la zona seleccionada
        $.ajax({
          type: 'POST',
          url: 'obtener_carros.php',
          data: {
            areaID: areaID
          },
          dataType: 'json',
          success: function(data) {
            $.each(data, function(key, value) {
              $('#carro').append('<option value="' + key + '">' + value + '</option>');
            });
          }
        });
      });
      $('#area').on('change', function() {
        // Obtén el valor seleccionado
        var seleccion = $(this).val();

        // Muestra o oculta el segundo select según la opción seleccionada
        if (seleccion === '0') {
          $('#carro').hide();
        } else {
          $('#carro').show();
        }
      });

      if (window.location.href.indexOf("abm.php") > -1) {
        <?php if (!empty($notification)) { ?>
          $('#returnNotificationModal').modal('show');
        <?php } ?>

        <?php if (!empty($notificacionDevolucion)) { ?>
          $('#returnDevolucionModal').modal('show');
        <?php } ?>


        $('#acceptReturn').click(function() {
          if ($('#horario').val() == null || $('#horario').val() == '') {
            alert('Por favor, elija un horario.');
          } else {
            handleReturn('accepted');
          }
        });

        $('#denyReturn').click(function() {
          handleReturn('denied');
        });
        $('#acceptDevolucion').click(function() {
          handleDevolucion('accepted');
        });

        $('#denyDevolucion').click(function() {
          handleDevolucion('denied');
        });

        let isModalOpen = false;
        let notificationId;
        let notificacionIddev;

        $('#returnNotificationModal').on('shown.bs.modal', function() {
          isModalOpen = true;
        });

        $('#returnNotificationModal').on('hidden.bs.modal', function() {
          isModalOpen = false;
        });

        $('#returnDevolucionModal').on('shown.bs.modal', function() {
          if (!isModalOpen) {
            isModalOpen = true;
          } else {
            $('#returnDevolucionModal').modal('hide');
          }
        });

        $('#returnDevolucionModal').on('hidden.bs.modal', function() {
          isModalOpen = false;
        });

        let userLink = document.querySelectorAll('.user_link');

        userLink.forEach(function(link) {
          link.addEventListener('click', function(event) {
            event.preventDefault();

            $('#userModal').modal('show');
          })
        })

        function handleReturn(status) {
          $.ajax({
            url: 'handle_return.php',
            type: 'POST',
            data: {
              status: status,
              id: notificationId, // Cambia esta línea para usar notificationId
              hora: $('#horario').val()
            },
            success: function(response) {
              $('#notificationMessage').text(response);
              $('#acceptReturn, #denyReturn').hide();
            },
            error: function(error) {
              alert('Hubo un error al manejar la devolución. Por favor, inténtalo de nuevo.');
            }
          });
        }

        function abrirModalConNombre(userName) {
          $.ajax({
            url: 'getUserLast.php', // Asegúrate de que esta es la ruta correcta a tu script PHP
            type: 'GET',
            dataType: 'json',
            data: {
              user_name: userName
            },
            success: function(data) {
              if (data.error) {
                alert('Error: ' + data.error);
              } else {
                var html = crearHTMLConPrestamos(data);
                $('#userModal .modal-body').html(html);
                $('#userModal').modal('show');
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              alert('Error al obtener los datos: ' + textStatus);
            }
          });
        }

        function crearHTMLConPrestamos(data) {
          var html = '<table class="table">';
          html += `
    <thead>
      <tr>
        <th>Nombre del Recurso</th>
        <th>Inicio del Préstamo</th>
        <th>Fin del Préstamo</th>
        <th>Fechas Extendidas</th>
      </tr>
    </thead>
    <tbody>
  `;

          data.forEach(function(prestamo) {
            html += `
      <tr>
        <td>${escaparHTML(prestamo.recurso_nombre)}</td>
        <td>${escaparHTML(prestamo.inicio_prestamo)}</td>
        <td>${escaparHTML(prestamo.horario)}</td>
        <td>${escaparHTML(prestamo.fechas_extendidas || 'N/A')}</td>
      </tr>
    `;
          });

          html += '</tbody></table>';
          return html;
        }



        // Función auxiliar para escapar caracteres especiales de HTML y prevenir inyecciones XSS
        function escaparHTML(str) {
          return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
        }


        function handleDevolucion(status) {
          $.ajax({
            url: 'handle_devolucion.php',
            type: 'POST',
            data: {
              status: status,
              id: notificationIddev,
            },
            success: function(response) {
              $('#devolucionMessage').text(response);
              $('#acceptDevolucion, #denyDevolucion').hide();
            },
            error: function(error) {
              alert('Hubo un error al manejar la devolución. Por favor, inténtalo de nuevo.');
            }
          });
        }

        const source = new EventSource('actualizar.php');
        source.onmessage = function(event) {
          const alumnos = JSON.parse(event.data);
          let html = '';
          alumnos.forEach(alumno => {
            html += generarFilaDeTabla(alumno);
          });
          document.getElementById('cuerpoDeTabla').innerHTML = html;

          // Reasigna los manejadores de eventos a los enlaces .user_link después de la actualización
          $('.user_link').off('click').on('click', function(e) {
            e.preventDefault();
            var userName = $(this).data('username');
            console.log(userName.length);
            abrirModalConNombre(userName); // Llama a la función para abrir el modal con el nombre del usuario
          });
        };

        function generarFilaDeTabla(alumno) {
          return `
      <tr>
        <td>${escaparHTML(alumno.idregistro)}</td>
        <td><a href="#" class="user_link" data-username="${escaparHTML(alumno.user_name)}">${escaparHTML(alumno.user_name)}</a></td>
        <td>${escaparHTML(alumno.inicio_prestamo)}</td>
        <td>${escaparHTML(alumno.fin_prestamo)}</td>
        <td>${escaparHTML(alumno.fechas_extendidas)}</td>
        <td>${escaparHTML(alumno.recurso_nombre)}</td>
      </tr>
    `;
        }



        let sourceModal = new EventSource('actualizarModal.php');

        sourceModal.onmessage = function(event) {
          const notificacion = JSON.parse(event.data);

          // Comprobar si el modal está abierto
          if (!isModalOpen && notificacion) {
            // Actualizar el contenido del modal
            document.getElementById('notificationMessageUser').textContent = 'Alumno: ' + notificacion.user_name;
            document.getElementById('notificationMessageResource').textContent = 'Material: ' + notificacion.recurso_nombre;
            document.getElementById('notificationMessageStart').textContent = 'Horario inicio: ' + notificacion.inicio_prestamo;

            notificationId = notificacion.idregistro; // Agrega esta línea para almacenar el id de la notificación

            $('#acceptReturn, #denyReturn').show();

            // Abrir el modal
            $('#returnNotificationModal').modal('show');
          }
        };
        let sourceDevolucion = new EventSource('actualizarDevolucion.php');

        sourceDevolucion.onmessage = function(event) {
          const notificacionDevolucion = JSON.parse(event.data);

          // Comprobar si el modal está abierto
          if (!isModalOpen && notificacionDevolucion) {
            // Actualizar el contenido del modal
            document.getElementById('devolucionMessageUser').textContent = 'Alumno: ' + notificacionDevolucion.user_name;
            document.getElementById('devolucionMessageResource').textContent = 'Material: ' + notificacionDevolucion.recurso_nombre;
            document.getElementById('devolucionMessageStart').textContent = 'Horario inicio: ' + notificacionDevolucion.inicio_prestamo;
            document.getElementById('devolucionMessageEnd').textContent = 'Horario final: ' + notificacionDevolucion.horario;

            notificationIddev = notificacionDevolucion.idregistro; // Agrega esta línea para almacenar el id de la notificación

            $('#acceptDevolucion, #denyDevolucion').show();

            // Abrir el modal
            $('#returnDevolucionModal').modal('show');
          }
        };
      };
    });
  </script>
</head>

<body>
  <nav class="sidebar locked">
    <div class="logo_items flex">
      <span class="nav_image">
        <img src="../template/logofinal.png" alt="logo_img" />
      </span>
      <span class="logo_name">Presma</span>
      <i class="bx bx-lock-alt" id="lock-icon" style="color:white" title="Unlock Sidebar"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
          <path fill="white" d="M12 2C9.243 2 7 4.243 7 7v3H6c-1.103 0-2 .897-2 2v8c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-8c0-1.103-.897-2-2-2h-1V7c0-2.757-2.243-5-5-5zm6 10l.002 8H6v-8h12zm-9-2V7c0-1.654 1.346-3 3-3s3 1.346 3 3v3H9z" />
        </svg></i>
      <i class="bx bx-x" id="sidebar-close"></i>
    </div>
    <div class="menu_container">
      <div class="menu_items">
        <ul class="menu_item">
          <li class="item">
            <a href="../netbook/visual.php" class="link flex">
              <i class="fi fi-sr-computer"></i>
              <span>Visual</span>
            </a>
          </li>
          <li class="item">
            <a href="../netbook/abm.php" class="link flex">
              <i class="fi fi-ss-laptop"></i>
              <span>Prestamos</span>
            </a>
          </li>
          <li class="item">
            <a href="../abmPersonas/abmPersonas.php" class="link flex">
              <i class="fi fi-sr-user"></i>
              <span>Usuarios</span>
            </a>
          </li>
        </ul>
      </div>
      <div class="sidebar_profile flex">
        <div class="data_text">
          <span class="name"><?php echo $_SESSION["user_name"] ?></span><a id="logout" href="/prueba d2/index.php?logout"><i id="logout" class="fi fi-rr-exit"></i></a>
        </div>
      </div>
    </div>
  </nav>
  <script src="../template/script.js"></script>
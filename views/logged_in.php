<?php
require_once("config/db.php");
$conexion = conexion();

/**$consultaSQL = "SELECT * FROM user_security WHERE user_id = :user_id";
$sentencia = $conexion->prepare($consultaSQL);
$sentencia->execute(['user_id' => $_SESSION['user_id']]);
$seguridad = $sentencia->fetch(PDO::FETCH_ASSOC);**/

header("Location: abm/netbook/visual.php");


/**if ($seguridad && $seguridad['must_change_password']) { // Añade una verificación aquí
    // Redirecciona al usuario a la página de cambio de contraseña
    header("Location: views/cambiar_password.php");
    exit;
} else {
    function escapar($html)
    {
        return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
    }
    header("Location: abm/netbook/visual.php");
}**/

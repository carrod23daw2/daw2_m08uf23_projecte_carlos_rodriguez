<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Usuario LDAP</title>
</head>
<body>
    <h1>Eliminar Usuario LDAP</h1>

    <form method="POST">
        <label for="uid">UID:</label>
        <input type="text" id="uid" name="uid" required><br>

        <label for="ou">Unidad Organizativa:</label>
        <input type="text" id="ou" name="ou" required><br>

        <button type="submit">Eliminar Usuario</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener los valores del formulario
        $uid = $_POST["uid"];
        $ou = $_POST["ou"];

        // Incluir el archivo de configuración
        $config = include('ldap_config.php');

        // Datos de configuración del servidor LDAP
        $ldap_host = $config['ldap_host'];
        $ldap_port = $config['ldap_port'];
        $ldap_dn = $config['ldap_dn'];
        $ldap_user = $config['ldap_user'];
        $ldap_password = $config['ldap_password'];

        // Construir el DN del usuario a eliminar
        $dn = "uid={$uid},ou={$ou},dc=fjeclot,dc=net";

        // Conectar al servidor LDAP
        $ldap_conn = ldap_connect($ldap_host, $ldap_port) or die("No se pudo conectar al servidor LDAP.");

        if ($ldap_conn) {
            // Establecer la versión del protocolo LDAP
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

            // Autenticarse con el servidor LDAP
            $bind = ldap_bind($ldap_conn, $ldap_user, $ldap_password);

            if ($bind) {
                // Eliminar el usuario del directorio LDAP
                $delete = ldap_delete($ldap_conn, $dn);

                if ($delete) {
                    echo "Usuario eliminado exitosamente.";
                } else {
                    echo "Error al eliminar el usuario: " . ldap_error($ldap_conn);
                }
            } else {
                echo "Fallo en la autenticación LDAP.";
            }

            // Cerrar la conexión LDAP
            ldap_close($ldap_conn);
        } else {
            echo "No se pudo conectar al servidor LDAP.";
        }
    }
    ?>
</body>
</html>


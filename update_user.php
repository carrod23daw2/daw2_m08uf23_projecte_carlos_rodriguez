<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Entrada de Usuario LDAP</title>
</head>
<body>
    <h1>Modificar Entrada de Usuario LDAP</h1>

    <form method="POST">
        <label for="uid">UID:</label>
        <input type="text" id="uid" name="uid" required><br>

        <label for="ou">Unidad Organizativa:</label>
        <input type="text" id="ou" name="ou" required><br>

        <label for="modify_cn">Modificar Nombre Completo:</label>
        <input type="checkbox" id="modify_cn" name="modify_cn" value="1">
        <input type="text" id="cn" name="cn" placeholder="Nuevo Nombre Completo"><br>

        <label for="modify_sn">Modificar Apellido:</label>
        <input type="checkbox" id="modify_sn" name="modify_sn" value="1">
        <input type="text" id="sn" name="sn" placeholder="Nuevo Apellido"><br>

        <label for="modify_givenName">Modificar Nombre:</label>
        <input type="checkbox" id="modify_givenName" name="modify_givenName" value="1">
        <input type="text" id="givenName" name="givenName" placeholder="Nuevo Nombre"><br>

        <label for="modify_title">Modificar Título:</label>
        <input type="checkbox" id="modify_title" name="modify_title" value="1">
        <input type="text" id="title" name="title" placeholder="Nuevo Título"><br>

        <!-- Agrega más campos según sea necesario -->

        <button type="submit">Modificar Entrada</button>
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

        // Construir el DN del usuario a modificar
        $dn = "uid={$uid},ou={$ou},dc=fjeclot,dc=net";

        // Atributos a modificar
        $modifications = [];

        // Verificar si se seleccionaron atributos para modificar y agregarlos a $modifications
        if (isset($_POST["modify_cn"]) && $_POST["modify_cn"] == "1") {
            $cn = $_POST["cn"];
            if (!empty($cn)) {
                $modifications[] = ldap_mod_replace("cn", $cn);
            }
        }

        if (isset($_POST["modify_sn"]) && $_POST["modify_sn"] == "1") {
            $sn = $_POST["sn"];
            if (!empty($sn)) {
                $modifications[] = ldap_mod_replace("sn", $sn);
            }
        }

        if (isset($_POST["modify_givenName"]) && $_POST["modify_givenName"] == "1") {
            $givenName = $_POST["givenName"];
            if (!empty($givenName)) {
                $modifications[] = ldap_mod_replace("givenName", $givenName);
            }
        }

        if (isset($_POST["modify_title"]) && $_POST["modify_title"] == "1") {
            $title = $_POST["title"];
            if (!empty($title)) {
                $modifications[] = ldap_mod_replace("title", $title);
            }
        }

        // Agregar más bloques similares para otros atributos

        // Conectar al servidor LDAP
        $ldap_conn = ldap_connect($ldap_host, $ldap_port) or die("No se pudo conectar al servidor LDAP.");

        if ($ldap_conn) {
            // Establecer la versión del protocolo LDAP
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

            // Autenticarse con el servidor LDAP
            $bind = ldap_bind($ldap_conn, $ldap_user, $ldap_password);

            if ($bind) {
                // Aplicar las modificaciones al usuario
                $modify = ldap_modify($ldap_conn, $dn, $modifications);

                if ($modify) {
                    echo "Entrada de usuario modificada exitosamente.";
                } else {
                    echo "Error al modificar la entrada de usuario: " . ldap_error($ldap_conn);
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


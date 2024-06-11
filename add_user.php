<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Nuevo Miembro LDAP</title>
</head>
<body>
    <h1>Añadir Nuevo Miembro LDAP</h1>

    <form method="POST">
        <label for="uid">UID:</label>
        <input type="text" id="uid" name="uid" required><br>

        <label for="ou">Unidad Organizativa:</label>
        <input type="text" id="ou" name="ou" required><br>

        <label for="cn">Nombre Completo:</label>
        <input type="text" id="cn" name="cn" required><br>

        <label for="sn">Apellido:</label>
        <input type="text" id="sn" name="sn" required><br>

        <label for="givenName">Nombre:</label>
        <input type="text" id="givenName" name="givenName" required><br>

        <label for="title">Título:</label>
        <input type="text" id="title" name="title"><br>

        <label for="telephoneNumber">Teléfono:</label>
        <input type="tel" id="telephoneNumber" name="telephoneNumber"><br>

        <label for="mobile">Móvil:</label>
        <input type="tel" id="mobile" name="mobile"><br>

        <label for="postalAddress">Dirección Postal:</label>
        <input type="text" id="postalAddress" name="postalAddress"><br>

        <label for="loginShell">Shell de Inicio de Sesión:</label>
        <input type="text" id="loginShell" name="loginShell"><br>

        <label for="gidNumber">Número de GID:</label>
        <input type="text" id="gidNumber" name="gidNumber"><br>

        <label for="uidNumber">Número de UID:</label>
        <input type="text" id="uidNumber" name="uidNumber"><br>

        <label for="homeDirectory">Directorio de Inicio:</label>
        <input type="text" id="homeDirectory" name="homeDirectory"><br>

        <label for="description">Descripción:</label><br>
        <textarea id="description" name="description" rows="4" cols="50"></textarea><br>

        <button type="submit">Añadir Miembro</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener los valores del formulario
        $uid = $_POST["uid"];
        $ou = $_POST["ou"];
        $cn = $_POST["cn"];
        $sn = $_POST["sn"];
        $givenName = $_POST["givenName"];
        $title = $_POST["title"];
        $telephoneNumber = $_POST["telephoneNumber"];
        $mobile = $_POST["mobile"];
        $postalAddress = $_POST["postalAddress"];
        $loginShell = $_POST["loginShell"];
        $gidNumber = $_POST["gidNumber"];
        $uidNumber = $_POST["uidNumber"];
        $homeDirectory = $_POST["homeDirectory"];
        $description = $_POST["description"];

        // Incluir el archivo de configuración
        $config = include('ldap_config.php');

        // Datos de configuración del servidor LDAP
        $ldap_host = $config['ldap_host'];
        $ldap_port = $config['ldap_port'];
        $ldap_dn = $config['ldap_dn'];
        $ldap_user = $config['ldap_user'];
        $ldap_password = $config['ldap_password'];

        // Construir el DN del nuevo usuario
        $dn = "uid={$uid},ou={$ou},dc=fjeclot,dc=net";

        // Atributos del nuevo usuario
        $attributes = [
            "objectClass" => ["top", "person", "organizationalPerson", "inetOrgPerson", "posixAccount", "shadowAccount"],
            "uid" => $uid,
            "cn" => $cn,
            "sn" => $sn,
            "givenName" => $givenName,
            "title" => $title,
            "telephoneNumber" => $telephoneNumber,
            "mobile" => $mobile,
            "postalAddress" => $postalAddress,
            "loginShell" => $loginShell,
            "gidNumber" => $gidNumber,
            "uidNumber" => $uidNumber,
            "homeDirectory" => $homeDirectory,
            "description" => $description
        ];

        // Conectar al servidor LDAP
        $ldap_conn = ldap_connect($ldap_host, $ldap_port) or die("No se pudo conectar al servidor LDAP.");

        if ($ldap_conn) {
            // Establecer la versión del protocolo LDAP
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

            // Autenticarse con el servidor LDAP
            $bind = ldap_bind($ldap_conn, $ldap_user, $ldap_password);

            if ($bind) {
                // Añadir el nuevo usuario al directorio LDAP
                $add = ldap_add($ldap_conn, $dn, $attributes);

                if ($add) {
                    echo "Nuevo usuario añadido exitosamente.";
                } else {
                    echo "Error al añadir el nuevo usuario: " . ldap_error($ldap_conn);
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


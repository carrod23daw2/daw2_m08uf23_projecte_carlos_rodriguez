<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios LDAP</title>
</head>
<body>
    <h1>Lista de Usuarios LDAP</h1>

    <?php
    // Incluir el archivo de configuración
    $config = include('ldap_config.php');

    // Datos de configuración del servidor LDAP
    $ldap_host = $config['ldap_host'];
    $ldap_port = $config['ldap_port'];
    $ldap_dn = $config['ldap_dn'];
    $ldap_user = $config['ldap_user'];
    $ldap_password = $config['ldap_password'];

    // Conectar al servidor LDAP
    $ldap_conn = ldap_connect($ldap_host, $ldap_port) or die("No se pudo conectar al servidor LDAP.");

    if ($ldap_conn) {
        // Establecer la versión del protocolo LDAP
        ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        // Autenticarse con el servidor LDAP
        $bind = ldap_bind($ldap_conn, $ldap_user, $ldap_password);

        if ($bind) {
            // Filtro de búsqueda para obtener todos los usuarios
            echo $ldap_dn;
            $search_filter = "(objectClass=inetOrgPerson)";
            $search_base = "dc=fjeclot,dc=net"; // Eliminar la unidad organizativa de la base de búsqueda
            $result = ldap_search($ldap_conn, $search_base, $search_filter);

            if (!$result) {
                die ("Error en la búsqueda: " . ldap_error($ldap_conn));
            }

            // Obtener los datos de la búsqueda
            $entries = ldap_get_entries($ldap_conn, $result);

            if ($entries["count"] > 0) {
                // Mostrar los resultados
                echo "<h2>Detalles de los Usuarios</h2>";
                echo "<table border='1'>";
                echo "<tr><th>UID</th><th>Unidad Organizativa</th><th>Apellido (SN)</th></tr>";
                for ($i = 0; $i < $entries["count"]; $i++) {
                    $uid = isset($entries[$i]['uid'][0]) ? htmlspecialchars($entries[$i]['uid'][0]) : '';
                    $ou = isset($entries[$i]['ou'][0]) ? htmlspecialchars($entries[$i]['ou'][0]) : '';
                    $sn = isset($entries[$i]['sn'][0]) ? htmlspecialchars($entries[$i]['sn'][0]) : '';
                    echo "<tr>";
                    echo "<td>{$uid}</td>";
                    echo "<td>{$ou}</td>";
                    echo "<td>{$sn}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No se encontró ningún usuario en el servidor LDAP.";
            }
        } else {
            echo "Fallo en la autenticación LDAP.";
        }

        // Cerrar la conexión LDAP
        ldap_close($ldap_conn);
    } else {
        echo "No se pudo conectar al servidor LDAP.";
    }
    ?>
</body>
</html>


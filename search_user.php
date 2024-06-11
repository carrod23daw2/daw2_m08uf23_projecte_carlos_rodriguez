<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Usuario LDAP</title>
</head>
<body>
    <h1>Buscar Usuario LDAP</h1>

    <form method="GET">
        <label for="uid">UID:</label>
        <input type="text" id="uid" name="uid" required>
        <br>
        <label for="ou">Unidad Organizativa:</label>
        <input type="text" id="ou" name="ou" required>
        <br>
        <button type="submit">Buscar</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // Obtener los valores del formulario
        $uid = $_GET["uid"];
        $ou = $_GET["ou"];

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
                // Filtro de búsqueda para obtener el usuario por UID y unidad organizativa
                $search_filter = "(&(uid={$uid}))"; // Filtro de búsqueda para buscar por UID
                $search_base = "ou={$ou},dc=fjeclot,dc=net";
                $result = ldap_search($ldap_conn, $search_base, $search_filter);

                   echo $search_filter;
                   echo $search_base;
                if (!$result) {
                    die ("Error en la búsqueda: " . ldap_error($ldap_conn));
                }

                // Obtener los datos de la búsqueda
                $entries = ldap_get_entries($ldap_conn, $result);

                if ($entries["count"] > 0) {
                    // Mostrar los resultados
                    echo "<h2>Detalles del Usuario</h2>";
                    echo "<p><strong>UID:</strong> {$uid}</p>";
                    echo "<p><strong>Unidad Organizativa:</strong> {$ou}</p>";
                    echo "<p><strong>Apellido (SN):</strong> {$entries[0]['sn'][0]}</p>";
                    echo "<p><strong>Nombre:</strong> {$entries[0]['givenname'][0]}</p>";
                    echo "<p><strong>Dirección:</strong> {$entries[0]['postaladdress'][0]}</p>";
                    echo "<p><strong>Teléfono Móvil:</strong> {$entries[0]['mobile'][0]}</p>";
                    echo "<p><strong>Teléfono Fijo:</strong> {$entries[0]['telephonenumber'][0]}</p>";
                    echo "<p><strong>Puesto:</strong> {$entries[0]['title'][0]}</p>";
                    echo "<p><strong>Descripción:</strong> {$entries[0]['description'][0]}</p>";
                    echo "<p><strong>uidNumber:</strong> {$entries[0]['uidNumber'][0]}</p>";
                    echo "<p><strong>gidNumber:</strong> {$entries[0]['gidNumber'][0]}</p>";
                    echo "<p><strong>Shell:</strong> {$entries[0]['loginShell'][0]}</p>";
                    echo "<p><strong>Directori:</strong> {$entries[0]['homeDirectory'][0]}</p>";
                    
                } else {
                    echo "No se encontró ningún usuario con UID '{$uid}' en la unidad organizativa '{$ou}'.";
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


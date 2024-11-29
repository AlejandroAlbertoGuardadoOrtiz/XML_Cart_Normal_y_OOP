<?php
// Function to check if a user is already connected
///////////////////////////////////////////////////////
function isUserConnected($username, $connections)
{
    // Recorre todas las conexiones en el archivo XML
    foreach ($connections->connection as $connection) {
        if ($connection->user == $username) {
            // Verifica si la conexión es válida, es decir, está dentro de los últimos 5 minutos
            $currentTime = time();                                                                  // Tiempo actual en segundos
            $connectionTime = strtotime($connection->date);                               // Tiempo de la conexión en segundos
            $expirationTime = $connectionTime + (5 * 60);                                           // Tiempo de expiración de 5 minutos

            if ($currentTime < $expirationTime) {
                return true;                                                                        // El usuario ya está conectado y la conexión es válida
            }
        }
    }
    return false; // El usuario no está conectado o la conexión ha expirado
}

// Function to write a connection to the connection.xml file
///////////////////////////////////////////////////////
function writeConnection($username)
{
    // Carga el archivo XML de conexiones existentes o crea un nuevo documento XML si no existe
    if (file_exists('connection.xml')) {
        $connections = simplexml_load_file('connection.xml');
    } else {
        $connections = new SimpleXMLElement('<connections></connections>');
    }
    // Crea una nueva entrada de conexión en el XML
    $connection = $connections->addChild('connection');
    $connection->addChild('user', $username);
    $connection->addChild('date', date('Y-m-d H:i:s'));             // Fecha y hora actual.

    // Guarda las conexiones actualizadas en el archivo connection.xml
    $connections->asXML('connection.xml');
}

////////////////////////////////////////////////////////////////
// Verifica si se han proporcionado el nombre de usuario y la contraseña en la URL
if (isset($_GET['username']) && isset($_GET['password'])) {
    $username = $_GET['username'];
    $password = $_GET['password'];

    // Carga el archivo user.xml para verificar las credenciales del usuario
    $users = simplexml_load_file('xmldb/user.xml');

    // Recorre cada usuario en el archivo XML para encontrar una coincidencia
    foreach ($users->user as $user) {
        if ($user->username == $username && $user->password == $password) {
            // Verifica si el usuario ya está conectado
            $connections = simplexml_load_file('connection.xml');
            if (!isUserConnected($username, $connections)) {
                // Si no está conectado, guarda la nueva conexión en el archivo connection.xml
                writeConnection($username);
                echo "La conexión ha sido exitosa para: $username";
            } else {
                echo "El usuario: $username ya está conectado.";
            }
            exit(); // Detiene la ejecución después de una conexión exitosa
        }
    }
    echo "Invalido username y/o password. <br>";                                                // Mensaje de error si las credenciales no coinciden
} else {
    echo "username y/o password no son correctos o no se ha introducido en la URL <br>";        // Error si faltan credenciales en la URL
}
?>

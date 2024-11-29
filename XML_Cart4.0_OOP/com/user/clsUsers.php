<?php

// Clase "clsUsers" para gestionar usuarios y sus conexiones al sistema.
class clsUsers
{
    private $username; // Almacena el nombre del usuario.
    private $password; // Almacena la contraseña del usuario.
    private $usersFilePath = 'xmldb/user.xml'; // Ruta al archivo XML que contiene los usuarios.
    private $connectionsFilePath = 'connection.xml'; // Ruta al archivo XML que gestiona las conexiones activas.
    private $users; // Almacena todos los usuarios cargados del XML.
    private $connections; // Almacena todas las conexiones activas cargadas del XML.

    // Constructor: Inicializa la clase con un nombre de usuario y una contraseña.
    public function __construct($username, $password)
    {
        $this->username = $username; // Asigna el nombre de usuario.
        $this->password = $password; // Asigna la contraseña.

        // Carga los datos de los archivos XML una sola vez.
        $this->loadXmlData();
    }

    // Método privado: Carga los usuarios y las conexiones desde los archivos XML.
    private function loadXmlData()
    {
        $this->users = $this->loadXml($this->usersFilePath, '<users></users>');
        $this->connections = $this->loadXml($this->connectionsFilePath, '<connections></connections>');
    }

    // Método privado: Carga un archivo XML o crea uno vacío si no existe.
    private function loadXml($filePath, $emptyXml)
    {
        if (file_exists($filePath)) {
            return simplexml_load_file($filePath);
        }
        return new SimpleXMLElement($emptyXml);
    }

    // Método privado: Verifica si el usuario ya tiene una conexión activa.
    private function isUserConnected()
    {
        $currentTime = time(); // Obtiene la hora actual en segundos.
        foreach ($this->connections->connection as $connection) {
            if ($connection->user == $this->username) {
                $connectionTime = strtotime($connection->date); // Convierte la fecha de la conexión a timestamp.
                $expirationTime = $connectionTime + (5 * 60); // Define la expiración de la conexión en 5 minutos.
                if ($currentTime < $expirationTime) {
                    return true; // Si la conexión aún es válida.
                }
            }
        }
        return false; // No se encontró una conexión activa válida.
    }

    // Método privado: Escribe una nueva conexión para el usuario en el archivo XML.
    private function writeConnection()
    {
        $connection = $this->connections->addChild('connection');
        $connection->addChild('user', $this->username); // Almacena el nombre del usuario.
        $connection->addChild('date', date('Y-m-d H:i:s')); // Almacena la fecha y hora actuales.
        $this->connections->asXML($this->connectionsFilePath); // Guarda los cambios en el archivo XML.
    }

    // Método público: Valida las credenciales del usuario y establece una conexión si son correctas.
    public function validateAndConnectUser()
    {
        foreach ($this->users->user as $user) {
            if ($user->username == $this->username && password_verify($this->password, (string) $user->password)) {
                // Si el usuario no tiene una conexión activa, registra una nueva conexión.
                if (!$this->isUserConnected()) {
                    $this->writeConnection(); // Registra la nueva conexión.
                }
                return true; // Las credenciales son válidas y la conexión se ha establecido.
            }
        }
        return false; // Devuelve `false` si las credenciales son inválidas.
    }
}
?>

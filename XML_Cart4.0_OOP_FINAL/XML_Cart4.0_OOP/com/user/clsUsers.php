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
    // Al instanciar la clase, se cargan los usuarios y las conexiones de los archivos XML.
    public function __construct($username, $password)
    {
        $this->username = $username; // Asigna el nombre de usuario.
        $this->password = $password; // Asigna la contraseña.

        // Carga los datos de los archivos XML una sola vez al crear la clase.
        $this->loadXmlData();
    }

    // Método privado: Carga los usuarios y las conexiones desde los archivos XML.
    // Si los archivos no existen, se crean instancias vacías de los datos.
    private function loadXmlData()
    {
        $this->users = $this->loadXml($this->usersFilePath, '<users></users>');
        $this->connections = $this->loadXml($this->connectionsFilePath, '<connections></connections>');
    }

    // Método privado: Carga un archivo XML o crea uno vacío si no existe.
    // Este método se usa para cargar tanto los usuarios como las conexiones.
    private function loadXml($filePath, $emptyXml)
    {
        if (file_exists($filePath)) {
            return simplexml_load_file($filePath); // Si el archivo existe, lo carga como un objeto SimpleXMLElement.
        }
        return new SimpleXMLElement($emptyXml); // Si el archivo no existe, crea un nuevo XML vacío.
    }

    // Método privado: Verifica si el usuario ya tiene una conexión activa.
    // La conexión se considera activa si está registrada en los últimos 5 minutos.
    private function isUserConnected()
    {
        $currentTime = time(); // Obtiene la hora actual en segundos desde la época Unix.
        foreach ($this->connections->connection as $connection) {

            if ($connection->user == $this->username) {
                // Verifica si el usuario ya está registrado y calcula el tiempo de expiración de su conexión (5 minutos).
                $connectionTime = strtotime($connection->date); // Convierte la fecha de la conexión a timestamp.
                $expirationTime = $connectionTime + (5 * 60); // Define la expiración de la conexión en 5 minutos.
                if ($currentTime < $expirationTime) {
                    return true; // Si la conexión aún es válida (dentro de los 5 minutos), retorna true.
                }
            }
        }
        return false; // No se encontró una conexión activa válida, retorna false.
    }

    // Método privado: Escribe una nueva conexión para el usuario en el archivo XML.
    // Crea un nuevo nodo en el archivo de conexiones con el usuario y la fecha actual.
    private function writeConnection()
    {
        $connection = $this->connections->addChild('connection'); // Agrega un nodo de conexión.
        $connection->addChild('user', $this->username); // Almacena el nombre de usuario en el nodo.
        $connection->addChild('date', date('Y-m-d H:i:s')); // Almacena la fecha y hora actuales de la conexión.
        $this->connections->asXML($this->connectionsFilePath); // Guarda el XML de conexiones actualizado en el archivo.
    }

    // Método público: Valida las credenciales del usuario y establece una conexión si son correctas.
    // Este método verifica si el usuario existe en el archivo XML de usuarios y si la contraseña es correcta.
    public function validateAndConnectUser()
    {
        foreach ($this->users->user as $user) {
            
            // Si el nombre de usuario coincide y la contraseña es válida (usando password_verify para seguridad).
            if ($user->username == $this->username && password_verify($this->password, (string) $user->password)) {
                // Si el usuario no tiene una conexión activa, registra una nueva conexión.
                if (!$this->isUserConnected()) {
                    $this->writeConnection(); // Registra la nueva conexión en el archivo XML.
                }
                return true; // Las credenciales son válidas y la conexión se ha establecido.
            }
        }
        return false; // Si las credenciales no son válidas, devuelve false.
    }
}
?>
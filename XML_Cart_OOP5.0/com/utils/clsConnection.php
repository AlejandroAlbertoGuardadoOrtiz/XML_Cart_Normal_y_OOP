<?php
/*
 *  By: Alejandro Guardado
 */
class clsConnection
{
    private $file_connection = 'connection.xml';
    private $connectionXML;

    public function __construct()
    {
        $this->loadFile();

    }

    public function checkUserConnected($username)
    {
        foreach ($this->connectionXML->xpath("/connections/connection/username") as $user) {
            if ((string) $user == $username) {
                //echo "Usuario conectado";
                return true;
            }
        }
        //echo "Usuario no conectado";
        return false;
    }
    public function writeConnection($username, $password)
    {
        $newUser = $this->connectionXML->addChild('connection');
        $newUser->addChild('username', $username);
        $newUser->addChild('password', $password);
        $newUser->addChild('date', date('Y-m-d H:i:s'));

        $this->save();
    }

    private function save()
    {
        $this->connectionXML->asXML($this->file_connection);
    }


    public function loadFile()
    {
        if (file_exists($this->file_connection)) {
            $this->connectionXML = simplexml_load_file($this->file_connection);
            //echo "Existe el fichero connection.xml";

        } else {
            //echo "No existe el fichero connection.xml";
            $this->connectionXML = new SimpleXMLElement('<connections></connections>');
        }
    }

    public function ViewConnection()
    {

        header("Content-Type: text/xml");
        //print_r($this->connectionXML);
        echo $this->connectionXML->asXML();

    }
}
?>
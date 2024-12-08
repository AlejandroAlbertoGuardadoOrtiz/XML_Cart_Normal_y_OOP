<?php
/*
 *  By: Alejandro Guardado
 */
class clsUsers
{
    // private $file_connection = 'connection.xml';
    private $file_user = 'xmldb/user.xml';
    private $userXML;
    private $username;
    private $password;

    public function __construct()
    {
        $this->loadFile();

    }
    /*
    public function registerUser($username, $password)
    {
        $newUser = $this->userXML->addChild('user');
        $newUser->addChild('username', $username);
        $newUser->addChild('password', $password);
        $this->userXML->asXML($this->file_connection);
    }
    */
    public function ExistUserPassword($username, $password)
    {
        foreach ($this->userXML->xpath("/users/user/username") as $user) {
            if ((string) $user == $username) {
                foreach ($this->userXML->xpath("/users/user/password") as $pass) {
                    if ((string) $pass == $password) {
                        //echo "Usuario y contraseña correctos";
                        return true;
                    }
                }
            }
        }
        //echo "Usuario o contraseña incorrectos";
        return false;
    }
    private function loadFile()
    {
        if (file_exists($this->file_user)) {
            $this->userXML = simplexml_load_file($this->file_user);
            //echo "Existe el fichero user.xml";

        } else {
            //echo "No existe el fichero user.xml";
            $this->userXML = new SimpleXMLElement('<users></users>');
        }
    }

    public function viewUser()
    {
        //header("Content-Type: text/xml");
        //print_r($this->userXML);
        //echo $this->userXML->asXML();

        
    }

} // FIN clsUsers.
?>
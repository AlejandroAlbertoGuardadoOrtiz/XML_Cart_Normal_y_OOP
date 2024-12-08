<?php
    /*
    *  By: Alejandro Guardado
    */
    class clsCatalog
    {

        private $file_catalog = 'xmldb/catalog.xml';
        private $catalogXML;

        public function __construct()
        {
            $this->loadCatalog();
        }

        public function ExistsProduct($id_product)
        {
            foreach ($this->catalogXML->xpath("/catalog/product/id") as $product) {
                if ((int) $product == $id_product) {
                    // echo "Producto encontrado";
                    return true;
                }
            }
            //echo "Producto no encontrado";
            return false;
        }

        public function loadCatalog()
        {
            if (file_exists($this->file_catalog)) {
                $this->catalogXML = simplexml_load_file($this->file_catalog);
                // print_r($xml);

            } else {
                $this->catalogXML = new SimpleXMLElement('<catalog></catalog>');
            }
        }

        public function viewCatalog()
        {

            //header("Content-Type: text/xml");
            //print_r($this->catalogXML);
            //echo $this->catalogXML->asXML();

        }
    }// FIN clsCatalog.
?>
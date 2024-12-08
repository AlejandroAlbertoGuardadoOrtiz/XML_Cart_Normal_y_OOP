<?php
include_once 'clsProduct.php';

class clsCatalog {
    private $catalogFilePath = 'xmldb/catalog.xml';
    private $catalog;

    public function __construct($catalogFilePath) {
        $this->catalogFilePath = $catalogFilePath;
        $this->catalog = $this->loadCatalog();
    }

    private function loadCatalog() {

        if (file_exists($this->catalogFilePath)) {
            return simplexml_load_file($this->catalogFilePath);
        } else {
            return new SimpleXMLElement('<catalog></catalog>');
        }
    }

    public function getProductPrice($id_product) {

        foreach ($this->catalog->product as $product) {
            if ((string) $product->id === (string) $id_product) {

                $clsProduct = new clsProduct((string) $product->id, (string) $product->name, (float) $product->price, (int) $product->quantity);
                return $clsProduct->getPrice();
            }
        }
        return 0;
    }

    public function productExists($id_product, $quantity) {

        foreach ($this->catalog->product as $product) {
            if ((string) $product->id === (string) $id_product) {

                $clsProduct = new clsProduct((string) $product->id, (string) $product->name, (float) $product->price, (int) $product->quantity);
                return $clsProduct->getQuantity() >= $quantity;
            }
        }
        return false;
    }

    public function getCatalog() {
        return $this->catalog;
    }

    private function addCatalog( $id, $name, $price, $quantity) {

        $product2 = new clsProduct($id, $name, $price, $quantity);
        
        $product = $this->catalog->addChild('product');
        $product->addChild('id', $product2->getId());
        $product->addChild('name', $product2->getName());
        $product->addChild('price', $product2-> getPrice());
        $product->addChild('quantity', $product2->getQuantity());

        $this->catalog->asXML($this->catalogFilePath);
    }

    public function getProductById($id_product) {
            
            foreach ($this->catalog->product as $product) {
                if ((string) $product->id === (string) $id_product) {
    
                    $clsProduct = new clsProduct((string) $product->id, (string) $product->name, (float) $product->price, (int) $product->quantity);
                    return $clsProduct;
                }
            }
            return null;
    }
}
?>
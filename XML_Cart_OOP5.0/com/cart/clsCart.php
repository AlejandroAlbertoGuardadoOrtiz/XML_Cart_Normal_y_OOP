<?php
/*
 *  By: Alejandro Guardado
 */
class clsCart
{

    private $file_cart = "xmldb/cart.xml";
    private $cartXML;

    public function __construct()
    {
        $this->load();
    }

    private function save()
    {
        $this->cartXML->asXML($this->file_cart);
    }
    /*
    public function removeCart($id_product)
    {
        foreach ($this->cartXML->product_item as $item) {
            
            if ((string) $item->id_product == $id_product) {
                $dom = dom_import_simplexml($item); // Convierte el SimpleXMLElement en DOMElement
                $dom->parentNode->removeChild($dom); // Elimina el nodo del documento XML
                $this->save(); // Guarda el XML actualizado
                return true; // Producto eliminado
            }
        }
        return false; // Producto no encontrado
    }
    */

    public function removeCart($id_product)
    {
        // Verificar si el producto existe en el carrito
        if (!$this->ExistProduct($id_product)) {
            // Si el producto no existe, retornar un mensaje indicando que no se puede eliminar
            return "El producto con ID '{$id_product}' no existe en el carrito y no se puede eliminar.";
        }

        // Buscar y eliminar los nodos con el valor de id_product dado
        foreach ($this->cartXML->xpath("//product[id_product='$id_product']") as $node) {
            // Eliminar el nodo encontrado
            unset($node[0]); // Eliminar el nodo de SimpleXML
        }

        // Guardar el XML actualizado
        $this->save();

        // Retornar un mensaje indicando que el producto ha sido eliminado del carrito
        return "El producto con ID '{$id_product}' ha sido eliminado del carrito.";
    }

    public function add($product, $quantity)
    {
        $newItem = $this->cartXML->addChild('product');
        $newItem->addChild('id_product', $product);
        $newItem->addChild('quantity', $quantity);

        /*
        $item_price = $this->cartXML->addChild('price_item');
        $item_price->addChild('price', '0');
        $item_price->addChild('currency', 'EU');
        */

        $this->save();

    }

    public function load()
    {
        if (file_exists($this->file_cart)) {
            $this->cartXML = simplexml_load_file($this->file_cart);
            //echo "Existe el fichero cart.xml";

        } else {
            //echo "No existe el fichero cart.xml";
            $this->cartXML = new SimpleXMLElement('<cart></cart>');
        }
    }

    private function ExistProduct($id_product)
    {
        foreach ($this->cartXML->product as $product) {
            if ((string) $product->id_product == $id_product) {
                return true;
            }
        }
        return false;
    }

    public function View()
    {

        header("Content-Type: text/xml");
        //print_r($this->cartXML);
        echo $this->cartXML->asXML();

    }
} // FIN clsCart.
?>
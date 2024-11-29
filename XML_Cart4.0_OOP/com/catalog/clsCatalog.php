<?php
class clsCatalog
{
    // Ruta del archivo XML donde se almacena el catálogo de productos.
    private $catalogFilePath = 'xmldb/catalog.xml';
    private $catalog; // Propiedad para almacenar el catálogo cargado.

    // Constructor: Carga el catálogo una vez.
    public function __construct()
    {
        $this->catalog = $this->loadCatalog(); // Carga el catálogo al instanciar la clase.
    }

    // Método privado que carga el catálogo desde el archivo XML.
    // Si el archivo no existe, crea un nuevo catálogo vacío.
    private function loadCatalog()
    {
        if (file_exists($this->catalogFilePath)) {
            // Si el archivo XML existe, lo carga como un objeto SimpleXMLElement.
            return simplexml_load_file($this->catalogFilePath);
        } else {
            // Si el archivo no existe, crea un objeto XML con la raíz <catalog>.
            return new SimpleXMLElement('<catalog></catalog>');
        }
    }

    // Método público que obtiene el precio de un producto por su ID.
    public function getProductPrice($id_product)
    {
        // Recorre los productos del catálogo.
        foreach ($this->catalog->product as $product) {
            // Busca un producto con el ID solicitado.
            if ((string) $product->id === (string) $id_product) {
                return (float) $product->price; // Retorna el precio del producto si lo encuentra.
            }
        }

        return 0; // Retorna 0 si el producto no se encuentra.
    }

    // Método público que verifica si un producto existe en el catálogo 
    // y si tiene suficiente stock disponible para la cantidad solicitada.
    public function productExists($id_product, $quantity)
    {
        // Recorre los productos para verificar la existencia y el stock.
        foreach ($this->catalog->product as $product) {
            if ((string) $product->id === (string) $id_product) {
                // Retorna true si la cantidad solicitada está disponible en el stock.
                return ((int) $product->quantity >= $quantity);
            }
        }

        return false; // Retorna false si el producto no existe o el stock es insuficiente.
    }

    // Método público que devuelve todo el catálogo.
    public function getCatalog()
    {
        return $this->catalog; // Retorna el catálogo almacenado en la propiedad.
    }
}
?>

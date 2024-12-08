<?php
// Funciones de manejo del catálogo de productos

// Cargar el catálogo desde el archivo XML
function loadCatalog() {
    // Carga el archivo XML de catálogo y lo devuelve como un objeto XML
    return simplexml_load_file("xmldb/catalog.xml");
}

// Guardar el catálogo actualizado en el archivo XML
function saveCatalog($catalog) {
    // Guarda el objeto XML del catálogo en el archivo XML
    $catalog->asXML("xmldb/catalog.xml");
}

// Verificar si el producto existe y tiene stock suficiente
function productExists($id_product, $quantity) {
    // Carga el catálogo desde el archivo XML
    $catalog = loadCatalog();

    // Recorre cada producto en el catálogo
    foreach ($catalog->product as $product) {
        // Comprueba si el ID del producto coincide y si hay suficiente stock
        if ((string)$product->id === (string)$id_product && (int)$product->quantity >= $quantity) {       // Con (string) se convierte a cadena y con (int) a entero.
            return true;                                                                                  // El producto existe y tiene suficiente stock.
        }
    }

    // Si el producto no se encuentra o no tiene suficiente stock, devuelve false
    return false;
}

// Actualizar el stock de un producto en el catálogo después de añadirlo al carrito
function updateCatalogStock($id_product, $quantity) {                                               // La variable  $id_product y $quantity se pasan como parámetros.
    // Carga el catálogo desde el archivo XML
    $catalog = loadCatalog();

    // Recorre cada producto en el catálogo
    foreach ($catalog->product as $product) {
        // Encuentra el producto correspondiente por su ID
        if ((string)$product->id === (string)$id_product) {
            // Resta la cantidad indicada del stock del producto
            $product->quantity -= $quantity;

            // Guarda el catálogo actualizado en el archivo XML
            saveCatalog($catalog);
            break;                                                                                          // Termina el bucle después de actualizar el producto.
        }
    }
}
?>

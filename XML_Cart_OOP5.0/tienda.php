<?php
// Incluyo los archivos necesarios para el funcionamiento del carrito.
include_once("com/cart/cart.php");
include_once("com/user/users.php");
include_once("com/catalog/catalog.php");

// Título principal de la tienda
echo "<h1> Tienda principal</h1>";

// Enlace para ir al catálogo.
echo "<a href='catalogo.php'>Ir al catálogo</a>";

// Cargar el catálogo
$catalog = loadCatalog();

// Mostrar el catálogo de productos
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Cantidad</th></tr>";

foreach ($catalog->product as $product) {
    echo "<tr>";
    echo "<td>{$product->id}</td>";
    echo "<td>{$product->name}</td>";
    echo "<td>\${$product->price}</td>";
    echo "<td>{$product->quantity}</td>";
    echo "</tr>";
}

echo "</table>";

viewCart();


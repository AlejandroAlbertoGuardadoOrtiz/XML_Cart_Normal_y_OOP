<?php

// Incluir los archivos necesarios para el manejo del carrito, usuarios y catálogo.

include_once("com/cart/cart.php");      // Funciones de manejo del carrito de compras
include_once("com/user/users.php");     // Funciones para la gestión de usuarios
include_once("com/catalog/catalog.php"); // Funciones para manejar el catálogo de productos

// Título de la página del catálogo
echo "<h1>Catálogo</h1>";

// Enlace para volver a la tienda principal
echo '<a href="main_oop.php">Volver a la tienda</a>' . "<br><br>";

// Cargar el catálogo de productos desde el archivo XML
$catalog = loadCatalog();

// Mostrar el catálogo en una tabla HTML

echo "<table border='1'>";                                                  // Comienza la tabla con un borde para mejor visualización
echo "<tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Cantidad</th></tr>"; // Cabecera de la tabla

// Recorrer cada producto en el catálogo y mostrar sus detalles en la tabla.
foreach ($catalog->product as $product) {
    echo "<tr>";
    echo "<td>{$product->id}</td>";       // Muestra el ID del producto
    echo "<td>{$product->name}</td>";     // Muestra el nombre del producto
    echo "<td>\${$product->price}</td>";  // Muestra el precio del producto
    echo "<td>{$product->quantity}</td>"; // Muestra la cantidad disponible en stock
    echo "</tr>";
}

echo "</table>"; // Finaliza la tabla

?>
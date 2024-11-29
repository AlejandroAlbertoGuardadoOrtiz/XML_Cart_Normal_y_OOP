<?php

// Incluye archivos necesarios para la gestión de usuarios, carrito y catálogo de productos
include_once('com/user/users.php');
include_once('com/cart/cart.php');
include_once('com/catalog/catalog.php');

// Título de la página principal
echo "<h1>Tienda Principal</h1>";

// Enlace para ver el catálogo de productos
echo '<a href="catalogo.php">Ver Catálogo</a>' . "<br><br>";

// Enlaces para ver el carrito con o sin un código promocional
echo '<a href="main.php?action=viewCart&promoCode=PROMO10">Ver Carrito con PROMO10</a>' . "<br><br>";
echo '<a href="main.php?action=viewCart">Ver Carrito sin Promoción</a>' . "<br><br>";

// Verifica si hay una acción especificada en la URL
if (isset($_GET['action'])) {
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING); // Toma la acción especificada
    $promoCode = filter_input(INPUT_GET, 'promoCode', FILTER_SANITIZE_STRING); // Toma el código de promoción si está presente

    // Realiza una acción dependiendo de lo especificado en la URL
    switch ($action) {
        case 'viewCart': // Mostrar el carrito
            viewCart($promoCode); // Llama a la función para ver el carrito con o sin promoción
            break;
        case 'addToCart': // Añadir un producto al carrito
            $id_product = filter_input(INPUT_GET, 'id_product', FILTER_VALIDATE_INT); // Obtiene el ID del producto de la URL
            $quantity = filter_input(INPUT_GET, 'quantity', FILTER_VALIDATE_INT); // Obtiene la cantidad de la URL
            if ($id_product !== false && $quantity !== false) {
                addCart($id_product, $quantity); // Añade el producto al carrito
            } else {
                echo "ID de producto o cantidad no válidos.<br>";
            }
            break;
        case 'removeFromCart': // Eliminar un producto del carrito
            $id_product = filter_input(INPUT_GET, 'id_product', FILTER_VALIDATE_INT); // Obtiene el ID del producto a eliminar
            if ($id_product !== false) {
                removeFromCart($id_product); // Elimina el producto del carrito
            } else {
                echo "ID de producto no válido.<br>";
            }
            break;
        default:
            echo "Acción no válida.<br>"; // Mensaje de error si la acción no es válida
            break;
    }
}

// Enlaces de ejemplo para añadir o eliminar productos del carrito
echo '<a href="main.php?action=addToCart&id_product=1&quantity=2">Añadir Producto 1 (Cantidad: 1) al Carrito</a>' . "<br><br>";
echo '<a href="main.php?action=addToCart&id_product=2&quantity=1">Añadir Producto 2 (Cantidad: 1) al Carrito</a>' . "<br><br>";
echo '<a href="main.php?action=removeFromCart&id_product=1">Eliminar Producto 1 del Carrito</a>' . "<br><br>";
echo '<a href="main.php?action=removeFromCart&id_product=2">Eliminar Producto 2 del Carrito</a>' . "<br><br>";
?>
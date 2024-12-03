<?php
// Incluye archivos necesarios para la gestión de usuarios, carrito y catálogo de productos
include_once('com/user/users.php');
include_once('com/cart/cart.php');
include_once('com/catalog/catalog.php');

// Verifica si hay una acción especificada en la URL
if (isset($_GET['action'])) {
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING); // Toma la acción especificada
    $promoCode = filter_input(INPUT_GET, 'promoCode', FILTER_SANITIZE_STRING); // Toma el código de promoción si está presente

    // Realiza una acción dependiendo de lo especificado en la URL
    switch ($action) {
        case 'viewCart': // Mostrar el carrito
            $cartOutput = viewCart($promoCode); // Llama a la función para ver el carrito con o sin promoción
            break;
        case 'addToCart': // Añadir un producto al carrito
            $id_product = filter_input(INPUT_GET, 'id_product', FILTER_VALIDATE_INT); // Obtiene el ID del producto de la URL
            $quantity = filter_input(INPUT_GET, 'quantity', FILTER_VALIDATE_INT); // Obtiene la cantidad de la URL
            if ($id_product !== false && $quantity !== false) {
                $cartOutput = addCart($id_product, $quantity); // Añade el producto al carrito
            } else {
                $cartOutput = "ID de producto o cantidad no válidos.<br>";
            }
            break;
        case 'removeFromCart': // Eliminar un producto del carrito
            $id_product = filter_input(INPUT_GET, 'id_product', FILTER_VALIDATE_INT); // Obtiene el ID del producto a eliminar
            if ($id_product !== false) {
                $cartOutput = removeFromCart($id_product); // Elimina el producto del carrito
            } else {
                $cartOutput = "ID de producto no válido.<br>";
            }
            break;
        default:
            $cartOutput = "Acción no válida.<br>"; // Mensaje de error si la acción no es válida
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Tienda Principal</title>
</head>
<body>
    <h1>Tienda Principal</h1>

    <form method="GET" action="catalogo.php">
        <button type="submit">Ver Catálogo</button>
    </form><br><br>

    <form method="GET" action="main.php">
        <input type="hidden" name="action" value="viewCart">
        <input type="hidden" name="promoCode" value="PROMO10">
        <button type="submit">Ver Carrito con PROMO10</button>
    </form><br><br>

    <form method="GET" action="main.php">
        <input type="hidden" name="action" value="viewCart">
        <button type="submit">Ver Carrito sin Promoción</button>
    </form><br><br>

    <?php
    // Muestra el resultado de las acciones realizadas
    if (isset($cartOutput)) {
        echo $cartOutput;
    }
    ?>

    <form method="GET" action="main.php">
        <input type="hidden" name="action" value="addToCart">
        <input type="hidden" name="id_product" value="1">
        <input type="hidden" name="quantity" value="2">
        <button type="submit">Añadir Producto 1 (Cantidad: 2) al Carrito</button>
    </form><br><br>

    <form method="GET" action="main.php">
        <input type="hidden" name="action" value="addToCart">
        <input type="hidden" name="id_product" value="2">
        <input type="hidden" name="quantity" value="1">
        <button type="submit">Añadir Producto 2 (Cantidad: 1) al Carrito</button>
    </form><br><br>

    <form method="GET" action="main.php">
        <input type="hidden" name="action" value="removeFromCart">
        <input type="hidden" name="id_product" value="1">
        <button type="submit">Eliminar Producto 1 del Carrito</button>
    </form><br><br>

    <form method="GET" action="main.php">
        <input type="hidden" name="action" value="removeFromCart">
        <input type="hidden" name="id_product" value="2">
        <button type="submit">Eliminar Producto 2 del Carrito</button>
    </form><br><br>
</body>
</html>
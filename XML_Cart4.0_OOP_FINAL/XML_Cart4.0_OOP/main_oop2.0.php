<?php

// Se incluyen las clases necesarias para usuarios, carrito y catálogo.
include_once('com/user/clsUsers.php');
include_once('com/cart/clsCart.php');
include_once('com/catalog/clsCatalog.php');

// Variables globales
$isLoggedIn = false;
$username = '';
$cartItems = [];
$promoCode = '';

// Verifica si un producto existe y tiene suficiente stock.
function productExists($id_product, $quantity)
{
    $catalog = new clsCatalog('com/catalog/catalog.xml');
    return $catalog->productExists($id_product, $quantity);
}

// Obtiene el precio de un producto específico.
function getProductPrice($id_product)
{
    $catalog = new clsCatalog('com/catalog/catalog.xml');
    return $catalog->getProductPrice($id_product);
}

// Actualiza o crea el archivo XML con los datos del usuario y registra fecha y hora.
function updateConnectionXML($username, $password)
{
    $filePath = 'connection.xml';
    $currentDateTime = date('Y-m-d H:i:s'); // Obtiene la fecha y hora actuales.

    // Prepara los datos del usuario en formato XML
    $userData = "<user>\n";
    $userData .= "  <username>$username</username>\n";
    $userData .= "  <password>$password</password>\n";
    $userData .= "  <date>$currentDateTime</date>\n";
    $userData .= "</user>\n";

    // Escribe los datos al final del archivo (si existe), o crea uno nuevo si no existe.
    if (file_exists($filePath)) {
        $xml = simplexml_load_file($filePath);
        $newUser = $xml->addChild('user');
        $newUser->addChild('username', $username);
        $newUser->addChild('password', $password);
        $newUser->addChild('date', $currentDateTime);
        $xml->asXML($filePath);
    } else {
        $xmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<connections>\n$userData</connections>";
        file_put_contents($filePath, $xmlContent);
    }
}

// Gestiona las acciones realizadas por el usuario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Maneja el inicio de sesión.
    if (isset($_POST['action']) && $_POST['action'] === 'Login') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $isLoggedIn = true;
        updateConnectionXML($username, $password);
    }

    // Acción para agregar productos al carrito
    if (isset($_POST['action']) && $_POST['action'] === 'addToCart') {
        $id_product = $_POST['id_product'];
        $quantity = $_POST['quantity'];
        $price = getProductPrice($id_product);
        addToCart($id_product, $quantity, $price);
    }
}

// Maneja las acciones GET.
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id_product = $_GET['id_product'] ?? null;
    $quantity = $_GET['quantity'] ?? 1; // Cantidad por defecto a 1 si no se especifica

    switch ($action) {
        case 'removeFromCart':
            removeFromCart($id_product);
            break;
        case 'checkProductExists':
            $exists = productExists($id_product, $quantity);
            echo $exists ? "El producto con ID $id_product existe y tiene suficiente stock." : "El producto con ID $id_product no existe o no tiene suficiente stock.";
            break;
    }
}

// Función para añadir un producto al carrito.
function addToCart($id_product, $quantity, $price)
{
    global $cartItems;
    if (isset($GLOBALS['promoCode']) && $GLOBALS['promoCode'] === 'PROMO10') {
        $price *= 0.9; // Aplica un descuento del 10%
    }
    $cart = new clsCart();
    $cart->addCart($id_product, $quantity, $price);
    $cartItems[] = ['id_product' => $id_product, 'quantity' => $quantity, 'price' => $price];
}

// Función para eliminar un producto del carrito.
function removeFromCart($id_product)
{
    global $cartItems;
    $cart = new clsCart();
    $cart->removeFromCart($id_product);
    $cartItems = array_filter($cartItems, function ($item) use ($id_product) {
        return $item['id_product'] !== $id_product;
    });
}

// Renderiza el HTML de la página.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <h1>Tienda Principal</h1>

    <?php if ($isLoggedIn): ?>
        <p>Bienvenido, <?php echo htmlspecialchars($username); ?>. Has iniciado sesión correctamente.</p>
    <?php endif; ?>

    <!-- Formulario de inicio de sesión -->
    <form method="POST" action="main_oop2.0.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" name="action" value="Login">
    </form><br><br>

    <!-- Botón para acceder al catálogo -->
    <form method="GET" action="catalogo.php">
        <button type="submit">Ver Catálogo</button>
    </form><br><br>

    <!-- Botón para eliminar el producto del carrito -->
    <form method="GET" action="main_oop2.0.php">
        <input type="hidden" name="action" value="removeFromCart">
        <input type="hidden" name="id_product" value="1">
        <button type="submit">Eliminar Producto del Carrito</button>
    </form><br><br>

    <!-- Formulario para comprobar si un producto existe -->
    <form method="GET" action="main_oop2.0.php">
        <input type="hidden" name="action" value="checkProductExists">
        <label for="id_product">ID del Producto:</label>
        <input type="text" name="id_product" id="id_product" required>
        <button type="submit">Comprobar Producto</button>
    </form><br><br>

    <!-- Formulario para agregar productos al carrito -->
    <form method="POST" action="main_oop2.0.php">
        <input type="hidden" name="action" value="addToCart">
        <label for="id_product_add">ID del Producto:</label>
        <input type="text" name="id_product" id="id_product_add" required>
        <label for="quantity_add">Cantidad:</label>
        <input type="number" name="quantity" id="quantity_add" value="1" required>
        <label for="promoCode">Código de Promoción:</label>
        <input type="text" name="promoCode" id="promoCode">
        <button type="submit">Agregar al Carrito</button>
    </form><br><br>

    <!-- Sección para ver los productos añadidos al carrito -->
    <h2>Productos en el Carrito</h2>
    <table>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
        </tr>
        <?php foreach ($cartItems as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['id_product']); ?></td>
                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                <td><?php echo htmlspecialchars($item['price']); ?> €</td>
                <td><?php echo $item['quantity'] * $item['price']; ?> €</td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
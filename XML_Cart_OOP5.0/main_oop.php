<?php
include_once('com/user/clsUsers.php');
include_once('com/cart/clsCart.php');
include_once('com/catalog/clsCatalog.php');
include_once('com/utils/clsConnection.php');

$cart = new clsCart();
$catalog = new clsCatalog();
$users = new clsUsers();
$connection = new clsConnection();

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add':
            if (isset($_GET['id_product']) && isset($_GET['quantity'])) {
                $id_product = $_GET['id_product'];
                $quantity = $_GET['quantity'];
                $cart->add($id_product, $quantity);
            }
            break;

        case 'remove':
            if (isset($_GET['id_product'])) {
                $id_product = $_GET['id_product'];
                $cart->removeCart($id_product);
                //echo "Producto $id_product eliminado";
            } else {
                echo "No id_product";
            }
            break;

        case 'view':
            $cart->View();
            exit; // Asegúrate de salir para no enviar contenido HTML después

        case 'viewCatalog':
            $catalog->viewCatalog();
            exit; // Asegúrate de salir para no enviar contenido HTML después

        case 'viewUser':
            $users->viewUser();
            exit; // Asegúrate de salir para no enviar contenido HTML después

        case 'checkUserConnected':
            if (isset($_GET['user'])) {
                $user = $_GET['user'];
                $connection->checkUserConnected($user);
                echo "Usuario $user conectado";
            } else {
                echo "No user";
            }
            break;

        case 'writeConnection':
            if (isset($_GET['user']) && isset($_GET['password'])) {
                $user = $_GET['user'];
                $password = $_GET['password'];
                $connection->writeConnection($user, $password);
            } else {
                echo "No user or password";
            }
            break;

        default:
            echo "No action";
            break;
    }
} else {
    //echo "No action";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Carrito, Usuarios y Catálogo</title>
</head>
<body>
    <h1>Gestión de Carrito, Usuarios y Catálogo</h1>

    <form method="GET" action="main_oop.php">
        <input type="hidden" name="action" value="add">
        <label for="id_product">ID del Producto:</label>
        <input type="text" name="id_product" id="id_product" required>
        <label for="quantity">Cantidad:</label>
        <input type="number" name="quantity" id="quantity" required>
        <button type="submit">AddCart</button>
    </form>

    <form method="GET" action="main_oop.php">
        <input type="hidden" name="action" value="remove">
        <label for="id_product_remove">ID del Producto:</label>
        <input type="text" name="id_product" id="id_product_remove" required>
        <button type="submit">Remove Cart</button>
    </form>

    <form method="GET" action="main_oop.php">
        <input type="hidden" name="action" value="view">
        <button type="submit">Ver Carrito</button>
    </form>

    <form method="GET" action="catalogo.php">
        <input type="hidden" name="action" value="viewCatalog">
        <button type="submit">Ver Catálogo</button>
    </form>

    <form method="GET" action="connection.xml">
        <input type="hidden" name="action" value="viewUser">
        <button type="submit">View Connections</button>
    </form>

    <form method="GET" action="main_oop.php">
        <input type="hidden" name="action" value="writeConnection">
        <label for="user_write">Usuario:</label>
        <input type="text" name="user" id="user_write" required>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">LogIn</button>
    </form>
</body>
</html>
<?php

// Se incluyen las clases necesarias para usuarios, carrito y catálogo.
include_once('com/user/clsUsers.php');
include_once('com/cart/clsCart.php');
include_once('com/catalog/clsCatalog.php');

// Clase principal que gestiona la página de inicio.
class MainPage
{
    private $isLoggedIn = false;
    private $username = '';
    private $cartItems = [];
    private $promoCode = '';

    // Método principal que renderiza toda la página.
    public function render()
    {
        $this->handleActions(); // Gestiona las acciones realizadas por el usuario.
        $this->renderHTML(); // Renderiza el HTML de la página.
    }

    // Verifica si un producto existe y tiene suficiente stock.
    public function productExists($id_product, $quantity)
    {
        $catalog = new clsCatalog();
        return $catalog->productExists($id_product, $quantity);
    }

    // Obtiene el precio de un producto específico.
    public function getProductPrice($id_product)
    {
        $catalog = new clsCatalog();
        return $catalog->getProductPrice($id_product);
    }

    // Gestiona las acciones realizadas por el usuario.
    private function handleActions()
    {
        // Maneja el inicio de sesión.
        if (isset($_POST['action']) && $_POST['action'] === 'Login') {
            $this->username = $_POST['username'];
            $password = $_POST['password'];

            $this->isLoggedIn = true;

            // Generar archivo XML con los datos del usuario
            $this->updateConnectionXML($this->username, $password);
        }

        // Maneja las acciones GET.
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            $id_product = $_GET['id_product'] ?? null;
            $quantity = $_GET['quantity'] ?? 1; // Cantidad por defecto a 1 si no se especifica
            $this->promoCode = $_GET['promoCode'] ?? ''; // Código de promoción

            switch ($action) {
                case 'removeFromCart':
                    $this->removeFromCart($id_product);
                    break;
                case 'checkProductExists':
                    $exists = $this->productExists($id_product, $quantity);
                    if ($exists) {
                        echo "El producto con ID $id_product existe y tiene suficiente stock.";
                    } else {
                        echo "El producto con ID $id_product no existe o no tiene suficiente stock.";
                    }
                    break;
                case 'addToCart':
                    $price = $this->getProductPrice($id_product);
                    $this->addToCart($id_product, $quantity, $price);
                    echo "Producto añadido al carrito.";
                    break;
                case 'updateCart':
                    $new_quantity = $_GET['new_quantity'] ?? 1;
                    $this->updateCart($id_product, $new_quantity);
                    echo 'Cantidad actualizada en el carrito';
                    break;
            }
        }
    }
    private function updateCart($id_product, $new_quantity)
    {
        $cart = new clsCart();
        $cart->updateQuantity($id_product, $new_quantity);
        foreach ($this->cartItems as &$item) {
            if ($item['id_product'] == $id_product) {
                $item['quantity'] = $new_quantity;
            }
        }
    }

    // Actualiza o crea el archivo XML con los datos del usuario y registra fecha y hora.
    private function updateConnectionXML($username, $password)
    {
        $filePath = 'connection.xml';
        $currentDateTime = date('Y-m-d H:i:s'); // Obtiene la fecha y hora actuales.

        // Carga el XML existente o crea uno nuevo si no existe.
        if (file_exists($filePath)) {
            $xml = new DOMDocument();
            $xml->load($filePath);
            $root = $xml->documentElement;
        } else {
            $xml = new DOMDocument('1.0', 'UTF-8');
            $xml->formatOutput = true;
            $root = $xml->createElement('connections');
            $xml->appendChild($root);
        }

        // Crear un nuevo nodo para el usuario.
        $userNode = $xml->createElement('user');
        $usernameNode = $xml->createElement('username', htmlspecialchars($username));
        $passwordNode = $xml->createElement('password', htmlspecialchars($password));
        $dateNode = $xml->createElement('date', $currentDateTime);

        // Añadir los nodos al usuario.
        $userNode->appendChild($usernameNode);
        $userNode->appendChild($passwordNode);
        $userNode->appendChild($dateNode);

        // Añadir el nodo del usuario al documento.
        $root->appendChild($userNode);

        // Guardar el XML en un archivo.
        $xml->save($filePath);
    }

    // Renderiza el HTML de la página.
    private function renderHTML()
    {
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

            <?php if ($this->isLoggedIn): ?>
                <p>Bienvenido, <?php echo htmlspecialchars($this->username); ?>. Has iniciado sesión correctamente.</p>
            <?php endif; ?>

            <!-- Formulario de inicio de sesión -->
            <form method="POST" action="main_oop.php">
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

            <!-- Botón para eliminar el producto 1 del carrito -->
            <form method="GET" action="main_oop.php">
                <input type="hidden" name="action" value="removeFromCart">
                <input type="hidden" name="id_product" value="1">
                <button type="submit">Eliminar Producto añadido del Carrito</button>
            </form><br><br>

            <!-- Formulario para comprobar si un producto existe -->
            <form method="GET" action="main_oop.php">
                <input type="hidden" name="action" value="checkProductExists">
                <label for="id_product">ID del Producto:</label>
                <input type="text" name="id_product" id="id_product" required>
                <button type="submit">Comprobar Producto</button>
            </form><br><br>

            <!-- Formulario para agregar productos al carrito -->
            <form method="GET" action="main_oop.php">
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
        
                <?php foreach ($this->cartItems as $item): ?>
                <?php endforeach; ?>
                

            <!-- Tabla para mostrar los detalles de los productos en el carrito -->
            <table>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($this->cartItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id_product']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['price']); ?> €</td>
                        <td><?php echo $item['quantity'] * $item['price']; ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <!-- Sección para mostrar el total a pagar -->
            <!--<h2>Total a Pagar</h2>-->
            <!--<p>
                <?php
                $total = 0;
                foreach ($this->cartItems as $item) {
                    $total += $item['quantity'] * $item['price'];
                }

                // Instancia de la clase clsCart
                $cart = new clsCart();

                if ($this->promoCode && !$cart->applyDiscount($this->promoCode)) {
                    echo "Código promocional inválido.";
                }

                echo "Total: $" . number_format($total, 2);
                ?>
            </p> -->

            <!-- Instancia de la clase clsCart -->
            <?php $cart = new clsCart(); ?>

            <!-- Sección para mostrar el resumen del carrito -->
            <h2>Resumen del Carrito</h2>
            <p>Subtotal: <?php echo $cart->getSubtotal(); ?> €</p>
            <p>Impuestos (IVA): <?php echo $cart->calculateTax(); ?> €</p>
            <p>Envío: <?php echo $cart->calculateShipping(); ?> €</p>
            <p>Total Final: <?php echo $cart->calculateTotalWithExtras(); ?> €</p>
        </body>

        </html>
        <?php
    }

    // Añade un producto al carrito.
    private function addToCart($id_product, $quantity, $price)
    {
        if ($this->promoCode === 'PROMO10') {
            $price *= 0.9; // Aplica un descuento del 10%
        }
        $cart = new clsCart();
        $cart->addCart($id_product, $quantity, $price);
        $this->cartItems[] = ['id_product' => $id_product, 'quantity' => $quantity, 'price' => $price];
    }

    // Elimina un producto del carrito.
    private function removeFromCart($id_product)
    {
        $cart = new clsCart();
        $cart->removeFromCart($id_product);
        $this->cartItems = array_filter($this->cartItems, function ($item) use ($id_product) {
            return $item['id_product'] !== $id_product;
        });
    }
} // Fin de la clase MainPage.

// Lógica principal: crea una instancia de MainPage y renderiza la página.
$page = new MainPage();
$page->render();

?>
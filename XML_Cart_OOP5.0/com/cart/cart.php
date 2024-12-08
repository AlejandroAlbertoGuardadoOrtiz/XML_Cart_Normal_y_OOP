<?php
/* --------------------------------------------------------------------  Funciones para manejar el carrito -------------------------------------------------------------------- */

// Carga el carrito desde el archivo XML.
function loadCart() {
    // Abre y carga el archivo XML que contiene la información del carrito.
    return simplexml_load_file('xmldb/cart.xml'); // Devuelve el objeto XML cargado.
}

// Guarda el carrito actualizado en el archivo XML.
function saveCart($cart) {
    // Convierte el objeto XML del carrito en un archivo XML actualizado.
    $cart->asXML('xmldb/cart.xml'); // Sobrescribe el archivo existente.
}

// Función para obtener el precio de un producto desde el archivo XML.
function getPriceFromXML($id_product) {
    $cartXML = loadCart(); // Carga el carrito desde el archivo XML.
    foreach ($cartXML->product_item as $product) {
        // Compara el ID del producto actual con el proporcionado.
        if ((int)$product->id_product === (int)$id_product) {                                // Con el (int) se convierte a entero.
            // Devuelve el precio del producto si encuentra una coincidencia.
            return (float)$product->price_item->price;                                       // Con el (float) se convierte a decimal.
        }
    }
}

// Añadir un producto al carrito.
function addCart($id_product, $quantity) {
    // Carga el carrito actual desde las cookies o inicia un carrito vacío si no existe.
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];     // El signo de interrogación es para validar si existe, además se convierte a array asociativo.

    // Obtiene el precio del producto desde el archivo XML.
    $price = getPriceFromXML($id_product);
    if ($price === null) { 
        // Si el precio no existe en el archivo XML, muestra un mensaje de error.
        echo "El producto con ID $id_product no tiene un precio asignado en cart.xml.<br>";
        return;
    }

    // Si el producto ya está en el carrito, incrementa la cantidad.
    if (isset($cart[$id_product])) {
        $cart[$id_product]['quantity'] += $quantity;
    } else {
        // Si el producto no está en el carrito, lo añade como un nuevo elemento.
        $cart[$id_product] = [
            'id_product' => $id_product,
            'quantity' => $quantity,
            'price' => $price // Asigna el precio del producto.
        ];
    }

    // Guarda el carrito actualizado en una cookie con una duración de 30 días.
    setcookie('cart', json_encode($cart), time() + (86400 * 30), '/');
    echo 'Producto añadido al carrito <br>'; // Mensaje de confirmación.
}

// Función para ver el contenido del carrito.
function viewCart($promoCode = null) {
    // Carga el carrito desde las cookies.
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];     // El signo de interrogación es para validar si existe.

    if (empty($cart)) { 
        // Si el carrito está vacío, muestra un mensaje.
        echo "El carrito está vacío.<br>";
        return;
    }

    $subtotal = 0; // Inicializa el subtotal.

    // Muestra la tabla con el contenido del carrito.
    echo "<h2>Contenido del Carrito</h2>";
    echo "<table>";
    echo "<tr><th>Nombre</th><th>Cantidad</th><th>Precio Individual</th><th>Costo Total</th></tr>";

    foreach ($cart as $productItem) {
        // Valida que el producto tenga las claves necesarias.
        if (!isset($productItem['id_product'], $productItem['quantity'], $productItem['price'])) {
            echo "<tr><td colspan='4'>Producto inválido en el carrito.</td></tr>";
            continue;
        }

        // Calcula el costo total de cada producto y lo suma al subtotal.
        $totalCost = $productItem['quantity'] * $productItem['price'];
        $subtotal += $totalCost;

        // Muestra los datos del producto en la tabla.
        echo "<tr>";
        echo "<td>{$productItem['id_product']}</td>";
        echo "<td>{$productItem['quantity']}</td>";
        echo "<td>{$productItem['price']} €</td>";
        echo "<td>{$totalCost} €</td>";
        echo "</tr>";
    }

    echo "</table>";

    $discount = 0; // Inicializa el descuento.
    if ($promoCode === "PROMO10") { 
        // Aplica un descuento del 10% si el código promocional es válido.
        $discount = $subtotal * 0.10;
    }

    $total = $subtotal - $discount; // Calcula el total después del descuento.

    // Muestra los totales.
    echo "<p>Subtotal: {$subtotal} €</p>";
    echo "<p>Descuento: {$discount} €</p>";
    echo "<p>Total: {$total} €</p>";
}

// Función eliminar un producto del carrito.
function removeFromCart($id_product) {
    // Carga el carrito desde las cookies.
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

    if (isset($cart[$id_product])) { 
        // Si el producto existe en el carrito, lo elimina.
        unset($cart[$id_product]);
        setcookie('cart', json_encode($cart), time() + (86400 * 30), '/');
        echo "Producto eliminado del carrito con éxito.<br>";
    } else {
        // Si el producto no está en el carrito, muestra un mensaje.
        echo "El producto no está en el carrito, no se puede eliminar.<br>";
    }
}

// Actualizar la cantidad de un producto en el carrito.
function updateCart($id_product, $quantity) {
    // Carga el carrito desde las cookies.
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];     // El signo de interrogación es para validar si existe.

    if (isset($cart[$id_product])) { 
        // Si el producto existe en el carrito, actualiza su cantidad.
        $cart[$id_product]['quantity'] = $quantity;
        setcookie('cart', json_encode($cart), time() + (86400 * 30), '/');
        echo "Cantidad del producto actualizada con éxito.<br>";
    } else {
        // Si el producto no está en el carrito, muestra un mensaje.
        echo "El producto no está en el carrito.<br>";
    }
}
?>

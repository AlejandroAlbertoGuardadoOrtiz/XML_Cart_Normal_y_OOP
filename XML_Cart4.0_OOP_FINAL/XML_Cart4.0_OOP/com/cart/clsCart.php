<?php
// Clase clsCart: Representa y gestiona las operaciones del carrito de compras.
class clsCart
{
    private $cart;      // Array que almacena los productos en el carrito.
    private $subtotal;  // Subtotal del costo del carrito.
    private $discount;  // Descuento aplicado al carrito.
    private $total;     // Total final del carrito tras aplicar el descuento.
    private $cartFilePath = 'xmldb/cart.xml'; // Ruta al archivo XML del carrito.
    private $taxRate = 0.21; // 21% IVA
    private $shippingCost = 5.99; // Costo fijo de envio.

    private $validPromoCodes = [
        'PROMO10' => 0.10,
        'SUMMER15' => 0.15,
        'BLACKFRIDAY' => 0.20
    ];

    // Constructor: Inicializa el carrito y sus valores.
    public function __construct()
    {
        // Carga el carrito desde una cookie, o lo inicializa vacío si no existe.
        $this->cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
        $this->subtotal = 0; // Inicializa el subtotal.
        $this->discount = 0; // Inicializa el descuento.
        $this->total = 0; // Inicializa el total.
    }

    public function calculateTax()
    {
        return $this->subtotal * $this->taxRate;
    }

    public function calculateShipping()
    {
        return $this->shippingCost; // Podría basarse en peso o ubicación en proyectos avanzados.
    }

    public function calculateTotalWithExtras()
    {
        return $this->subtotal + $this->calculateTax() + $this->calculateShipping();
    }

    // Guarda el carrito en un archivo XML y en una cookie.
    private function save()
    {
        // Crear un nuevo objeto XML con la raíz <cart>.
        $xml = new SimpleXMLElement('<cart></cart>');

        // Añadir cada producto del carrito al XML.
        foreach ($this->cart as $item) {
            $product = $xml->addChild('product');
            $product->addChild('id', $item['id_product']);
            $product->addChild('quantity', $item['quantity']);
            $product->addChild('price', isset($item['price']) ? $item['price'] : 0); // Proporciona un valor predeterminado si 'price' no existe
        }

        // Guardar el objeto XML en el archivo especificado.
        $xml->asXML($this->cartFilePath);

        // Guardar también en la cookie.
        setcookie('cart', json_encode($this->cart), time() + (86400 * 30), "/");
    }

    // Muestra el contenido del carrito y aplica descuentos si se proporciona un código promocional.
    public function viewCart($promoCode = null)
    {
        // Si el carrito está vacío, devuelve un mensaje.
        if (empty($this->cart)) {
            return "El carrito está vacío.<br>";
        }

        // Genera la tabla con los productos y calcula el subtotal.
        $output = $this->displayCartContents();
        $this->applyDiscount($promoCode); // Aplica el descuento según el código promocional.
        $this->calculateTotal(); // Calcula el total final después del descuento.
        $output .= $this->displayTotals(); // Añade el resumen de precios.

        return $output; // Devuelve el contenido del carrito con los totales.
    }

    // Genera una tabla con los productos del carrito y calcula el subtotal.
    private function displayCartContents()
    {
        $output = "<h2>Contenido del Carrito</h2>";
        $output .= "<table>";
        $output .= "<tr><th>Nombre</th><th>Cantidad</th><th>Precio Individual</th><th>Costo Total</th></tr>";

        foreach ($this->cart as $productItem) {
            // Verifica que el producto tenga los datos necesarios.
            $totalCost = $productItem['quantity'] * $productItem['price'];
            $this->subtotal += $totalCost;

            // Añade una fila a la tabla con los detalles del producto.
            $output .= "<tr>";
            $output .= "<td>{$productItem['id_product']}</td>"; // ID del producto.
            $output .= "<td>{$productItem['quantity']}</td>"; // Cantidad.
            $output .= "<td>{$productItem['price']} €</td>"; // Precio por unidad.
            $output .= "<td>{$totalCost} €</td>"; // Costo total.
            $output .= "</tr>";
        }

        $output .= "</table>";
        return $output; // Devuelve la tabla con los productos.
    }

    // Aplica un descuento al carrito basado en un código promocional.
    private function applyDiscount($promoCode)
    {
        if (isset($this->validPromoCodes[$promoCode])) {
            $this->discount = $this->subtotal * $this->validPromoCodes[$promoCode];
        } else {
            $this->discount = 0; // No descuento si el código no es válido.
        }
    }

    // Calcula el total del carrito después de aplicar el descuento.
    private function calculateTotal()
    {
        $this->total = $this->subtotal - $this->discount; // Calcula el total final.
    }

    // Muestra el resumen del subtotal, descuento y total.
    private function displayTotals()
    {
        $output = "<h3>Resumen del Carrito</h3>";
        $output .= "<p>Subtotal: {$this->subtotal} €</p>"; // Subtotal.
        $output .= "<p>Descuento: {$this->discount} €</p>"; // Descuento aplicado.
        $output .= "<p>Total: {$this->total} €</p>"; // Total final.
        return $output; // Devuelve el resumen.
    }

    // Añade un producto al carrito, o incrementa su cantidad si ya existe.
    public function addCart($id_product, $quantity, $price)
    {
        // Busca el producto en el carrito.
        foreach ($this->cart as &$productItem) {
            if ($productItem['id_product'] == $id_product) {
                $productItem['quantity'] += $quantity; // Incrementa la cantidad.
                $this->save(); // Guarda el carrito actualizado.
                return "Producto añadido al carrito.<br>";
            }
        }

        // Si el producto no estaba en el carrito, lo añade.
        $this->cart[] = [
            'id_product' => $id_product, // ID del producto.
            'quantity' => $quantity, // Cantidad.
            'price' => $price // Precio unitario.
        ];

        $this->save(); // Guarda el carrito actualizado.
        return "Producto añadido al carrito.<br>";
    }

    // Elimina un producto del carrito por su ID.
    public function removeFromCart($id_product)
    {
        foreach ($this->cart as $key => $productItem) {
            if ($productItem['id_product'] == $id_product) { // Si encuentra el producto.
                unset($this->cart[$key]); // Lo elimina del carrito.
                $this->save(); // Guarda el carrito actualizado.
                return "Producto eliminado del carrito.<br>";
            }
        }
        return "Producto no encontrado en el carrito.<br>"; // Si no lo encuentra.
    }

    // ----- Permite a los usuarios modificar las cantidades de productos en el carrito.
    public function updateQuantity($id_product, $new_quantity)
    {
        foreach ($this->cart as &$item) {
            if ($item['id_product'] == $id_product) {
                $item['quantity'] = $new_quantity; // Actualiza cantidad.
                $this->save(); // Guarda los cambios.
                return 'Cantidad actualizada para el producto {$id_product}.';

            }

        }
        return 'Producto no encontrado en el carrito.';
    }


    public function getSubtotal()
    {
        $subtotal = 0;

        foreach ($this->cart as $item) {
            $price = isset($item['price']) ? $item['price'] : 0; // Proporciona un valor predeterminado si 'price' no existe
            $subtotal += $item['quantity'] * $price;
        }

        return $subtotal;
    }

    function getProductPrice($id_product)
{
    $catalog = new clsCatalog('xmldb/catalog.xml');
    return $catalog->getProductPrice($id_product);
}

}
?>
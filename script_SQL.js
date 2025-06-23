document.addEventListener("DOMContentLoaded", () => {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const cartItemsContainer = document.getElementById("cart-items");
    const totalPriceContainer = document.getElementById("total-price");

    function updateCart() {
        localStorage.setItem("cart", JSON.stringify(cart));
        if (cartItemsContainer && totalPriceContainer) {
            cartItemsContainer.innerHTML = "";
            let total = 0;
            cart.forEach((item, index) => {
                const li = document.createElement("li");
                li.innerHTML = `${item.name} - ${item.price.toFixed(2)}€ <button class='remove-item' data-index='${index}'>❌</button>`;
                cartItemsContainer.appendChild(li);
                total += item.price * item.quantity;
            });
            totalPriceContainer.textContent = total.toFixed(2);
        }
    }

    if (cartItemsContainer) {
        cartItemsContainer.addEventListener("click", (event) => {
            if (event.target.classList.contains("remove-item")) {
                const index = event.target.getAttribute("data-index");
                cart.splice(index, 1);
                updateCart();
            }
        });
    }

    document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", () => {
            const name = button.getAttribute("data-name");
            const price = parseFloat(button.getAttribute("data-price"));
            cart.push({ name, price, quantity: 1 });
            updateCart();
            alert(`${name} ajouté au panier !`);
        });
    });

    updateCart();
});

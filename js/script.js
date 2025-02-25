document.addEventListener("DOMContentLoaded", function () {

    // ===1) NAVBAR TOGGLE (Optional)

    const navToggle = document.querySelector(".nav-toggle");
    const navLinks = document.querySelector(".nav-links");
  
    if (navToggle && navLinks) {
      navToggle.addEventListener("click", function () {

        navLinks.classList.toggle("show-nav");
  
        navToggle.classList.toggle("active");
      });
    }
  
    // ===2) CONFIRMATION BEFORE DELETING
    const deleteLinks = document.querySelectorAll(
      'a[href*="delete_product.php"], a[href*="remove"]'
    );
    deleteLinks.forEach((link) => {
      link.addEventListener("click", function (event) {
        const confirmDelete = confirm(
          "Jeste li sigurni da želite obrisati ovaj proizvod?"
        );
        if (!confirmDelete) {
          event.preventDefault();
        }
      });
    });
  
    // ===3) DYNAMIC TOTAL PRICE IN CART
    const quantityInputs = document.querySelectorAll('input[type="number"]');
  
    quantityInputs.forEach((input) => {
      input.addEventListener("input", function () {
        const productCard = this.closest(".product-card");
        if (!productCard) return;
  
        // e.g., <p class="product-price">25.00 KM</p>
        const priceElement = productCard.querySelector(".product-price");
        // e.g., <span class="product-total">Total here</span>
        const totalElement = productCard.querySelector(".product-total");
        if (!priceElement || !totalElement) return;
  
        const price = parseFloat(priceElement.textContent.replace("KM", "").trim());
        const quantity = parseInt(this.value, 10) || 0;
  
        totalElement.textContent = (price * quantity).toFixed(2) + " KM";
      });
    });
  
    // ==4) BUTTON CLICK ANIMATION
    const buttons = document.querySelectorAll("button");
    buttons.forEach((button) => {
      button.addEventListener("click", function () {
        this.style.transform = "scale(0.95)";
        setTimeout(() => {
          this.style.transform = "scale(1)";
        }, 200);
      });
    });
  
    // == 5) PRODUCT FORM VALIDATION

    const productForm = document.querySelector("form");
    if (productForm) {
      productForm.addEventListener("submit", function (event) {
        const nameInput = this.querySelector('input[name="name"]');
        const priceInput = this.querySelector('input[name="price"]');
        const descriptionInput = this.querySelector('textarea[name="description"]');
  
        if (!nameInput || !priceInput || !descriptionInput) {
          return;
        }
  
        if (
          !nameInput.value.trim() ||
          parseFloat(priceInput.value) <= 0 ||
          !descriptionInput.value.trim()
        ) {
          alert("Sva polja su obavezna i moraju biti ispravno popunjena.");
          event.preventDefault();
        }
      });
    }
  
  });
  
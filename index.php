<?php
session_start();

require 'db/db_connection.php';
?>

<?php include 'header.php'; ?> 

<section class="hero" id="home">
  <div class="hero-overlay">
    <div class="hero-content">
      <h1>Dobrodošli na Solarni Info Sistem</h1>
      <p>Najefikasnija solarna rješenja za vaš dom i poslovni prostor</p>
      <a href="#products" class="btn-primary">Pogledaj Proizvode</a>
    </div>
  </div>
</section>

<section class="user-session">
  <div class="container">
    <?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])): ?>
      <div class="auth-links">
        <a href="login.php">Prijava</a> | 
        <a href="register.php">Registracija</a>
      </div>
    <?php elseif (isset($_SESSION['admin_id'])): ?>
      <p>Prijavljeni ste kao administrator.</p>
      <a href="dashboard.php">Idi na Admin Panel</a> | 
      <a href="logout.php">Odjava</a>
      <?php exit; // Administratori ne trebaju vidjeti proizvode ?>
    <?php else: ?>
      <?php if ($_SESSION['role'] === 'klijent'): ?>
        <p>Prijavljeni ste kao klijent.</p>
        <a href="user_profile.php">Moj Profil</a> | 
        <a href="logout.php">Odjava</a>
      <?php elseif ($_SESSION['role'] === 'kompanija'): ?>
        <p>Prijavljeni ste kao kompanija.</p>
        <a href="company_dashboard.php">Kompanijski Panel</a> | 
        <a href="logout.php">Odjava</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<section class="products" id="products">
  <div class="container">
    <h2>Ponuda Proizvoda</h2>
    <?php
    // Fetch products
    $products_query = "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY created_at DESC";
    $products = $conn->query($products_query);
    ?>
    <?php if ($products->num_rows > 0): ?>
      <div class="product-grid">
        <?php while ($product = $products->fetch_assoc()): ?>
          <div class="product-card">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p><?= htmlspecialchars($product['description']) ?></p>
            <p><strong><?= number_format($product['price'], 2) ?> KM</strong></p>
            <form method="POST" action="shopping_cart.php">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
              <label for="quantity_<?= $product['id'] ?>">Količina:</label>
              <input 
                type="number" 
                name="quantity" 
                id="quantity_<?= $product['id'] ?>" 
                min="1" 
                value="1" 
                required 
              />
              <button type="submit">Dodaj u Korpu</button>
            </form>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p>Trenutno nema proizvoda u ponudi.</p>
    <?php endif; ?>

    <div class="cart-link">
      <a href="shopping_cart.php" class="btn-secondary">Pogledajte svoju korpu</a>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>  
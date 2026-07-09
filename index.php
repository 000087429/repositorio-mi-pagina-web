<?php
require_once __DIR__ . '/config.php';

$user = getCurrentUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pizzeria Trejo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header class="hero">
    <nav class="nav">
      <a href="#" class="brand">Pizzeria Trejo</a>
      <div class="nav-actions">
        <?php if ($user): ?>
          <span class="greeting">Hola, <?= htmlspecialchars($user['full_name']) ?></span>
          <?php if (isAdmin()): ?>
            <a href="dashboard.php" class="btn btn-secondary nav-link">Ir al dashboard</a>
          <?php endif; ?>
          <a href="logout.php" class="nav-link">Cerrar sesión</a>
        <?php else: ?>
          <a href="#login" class="nav-link">Iniciar sesión</a>
        <?php endif; ?>
      </div>
    </nav>

    <div class="hero-content">
      <div class="hero-text">
        <p class="eyebrow">Pizza artesanal • entregas rápidas</p>
        <h1>La pizza que se siente como un plan perfecto.</h1>
        <p class="subtitle">Ingredientes frescos, corteza dorada y un sabor que conquista desde la primera porción.</p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#destacados">Ver menú</a>
          <a class="btn btn-secondary" href="https://wa.me/5549523978" target="_blank" rel="noopener">Pedir por WhatsApp</a>
        </div>
      </div>

      <div class="hero-side">
        <?php if ($flash): ?>
          <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <?php if ($user): ?>
          <div class="card auth-card">
            <h3>Bienvenido</h3>
            <p>Tu acceso quedó listo. Puedes continuar navegando o entrar al panel si eres administrador.</p>
          </div>
        <?php else: ?>
          <div id="login" class="card auth-card">
            <h3>Accede a tu cuenta</h3>
            <form action="login.php" method="post" class="auth-form">
              <label>
                Correo electrónico
                <input type="email" name="email" required>
              </label>
              <label>
                Contraseña
                <input type="password" name="password" required>
              </label>
              <button type="submit" class="btn btn-primary full-width">Entrar</button>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main>
    <section class="about">
      <div class="section-heading">
        <p class="eyebrow">Nuestra propuesta</p>
        <h2>Una pizzeria joven, sencilla y con personalidad.</h2>
      </div>
      <p>En Pizzeria Trejo combinamos técnica, ingredientes de calidad y una estética moderna para crear pizzas que se disfrutan con amigos, familia o en una noche especial.</p>
    </section>

    <section id="destacados" class="featured">
      <div class="section-heading">
        <p class="eyebrow">Más pedidos</p>
        <h2>Los favoritos de la casa</h2>
      </div>

      <div class="cards">
        <article class="card">
          <img src="https://images.unsplash.com/photo-1574071318508-1cdbab80d002?auto=format&fit=crop&w=900&q=80" alt="Pizza Pepperoni">
          <h3>Pepperoni Fire</h3>
          <p>Mozzarella, pepperoni crujiente y salsa especial.</p>
        </article>

        <article class="card">
          <img src="https://images.unsplash.com/photo-1542834369-f10ebf06d3e0?auto=format&fit=crop&w=900&q=80" alt="Pizza clásica de la casa">
          <h3>La Clásica</h3>
          <p>Tomate, albahaca fresca y queso derretido en cada bocado.</p>
        </article>

        <article class="card">
          <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=900&q=80" alt="Pizza Premium">
          <h3>Premium</h3>
          <p>Jamón, champiñones y un toque de sabor intenso.</p>
        </article>
      </div>
    </section>

    <section class="cta-section">
      <h2>Haz tu pedido y vive la mejor pizza de la ciudad.</h2>
      <a class="btn btn-primary" href="https://wa.me/5549523978" target="_blank" rel="noopener">Pedir ahora</a>
    </section>
  </main>

  <footer>
    <p>© <span id="year"></span> Pizzeria Trejo • Instagram: @pizzeria_trejo</p>
  </footer>

  <script src="script.js"></script>
</body>
</html>

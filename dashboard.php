<?php
require_once __DIR__ . '/config.php';

if (!isAdmin()) {
    setFlash('error', 'Necesitas permisos de administrador para ver este panel.');
    redirect('index.php');
}

$pdo = getDb();
$flash = getFlash();
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $status = $_POST['status'] ?? 'active';

    if ($fullName === '' || $email === '') {
        setFlash('error', 'Nombre y correo son obligatorios.');
        redirect('dashboard.php');
    }

    if ($userId) {
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $current = $stmt->fetch();

        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET full_name = ?, email = ?, password_hash = ?, role = ?, status = ? WHERE id = ?');
            $stmt->execute([$fullName, $email, $passwordHash, $role, $status, $userId]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET full_name = ?, email = ?, role = ?, status = ? WHERE id = ?');
            $stmt->execute([$fullName, $email, $role, $status, $userId]);
        }

        setFlash('success', 'Usuario actualizado correctamente.');
    } else {
        $passwordHash = password_hash($password !== '' ? $password : 'pizzeria123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$fullName, $email, $passwordHash, $role, $status]);
        setFlash('success', 'Usuario creado correctamente.');
    }

    redirect('dashboard.php');
}

if (isset($_GET['delete'])) {
    $deleteId = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($deleteId) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$deleteId]);
        setFlash('success', 'Usuario eliminado.');
    }
    redirect('dashboard.php');
}

$usersStmt = $pdo->query('SELECT id, full_name, email, role, status, created_at FROM users ORDER BY created_at DESC');
$users = $usersStmt->fetchAll();

$today = date('Y-m-d');
$salesStmt = $pdo->prepare('SELECT id, customer_name, total_amount, sale_date, description FROM sales WHERE sale_date = ? ORDER BY id DESC');
$salesStmt->execute([$today]);
$dailySales = $salesStmt->fetchAll();
$dailyTotal = array_sum(array_column($dailySales, 'total_amount'));

$selectedUser = null;
if ($editId) {
    $selectedStmt = $pdo->prepare('SELECT id, full_name, email, role, status FROM users WHERE id = ?');
    $selectedStmt->execute([$editId]);
    $selectedUser = $selectedStmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard • Pizzeria Trejo</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <main class="dashboard-shell">
    <section class="dashboard-card">
      <div class="dashboard-header">
        <div>
          <p class="eyebrow">Panel administrativo</p>
          <h1>Gestión de usuarios y ventas</h1>
        </div>
        <a class="btn btn-secondary" href="index.php">Volver a la landing</a>
      </div>

      <?php if ($flash): ?>
        <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
      <?php endif; ?>

      <div class="stats-grid">
        <article class="stat-card">
          <span class="stat-label">Ventas hoy</span>
          <strong>$<?= number_format($dailyTotal, 2, ',', '.') ?></strong>
        </article>
        <article class="stat-card">
          <span class="stat-label">Pedidos hoy</span>
          <strong><?= count($dailySales) ?></strong>
        </article>
        <article class="stat-card">
          <span class="stat-label">Usuarios registrados</span>
          <strong><?= count($users) ?></strong>
        </article>
      </div>

      <div class="dashboard-grid">
        <section class="panel">
          <h2><?= $selectedUser ? 'Editar usuario' : 'Agregar nuevo usuario' ?></h2>
          <form action="dashboard.php" method="post" class="stacked-form">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars((string) ($selectedUser['id'] ?? '')) ?>">
            <label>
              Nombre completo
              <input type="text" name="full_name" value="<?= htmlspecialchars($selectedUser['full_name'] ?? '') ?>" required>
            </label>
            <label>
              Correo electrónico
              <input type="email" name="email" value="<?= htmlspecialchars($selectedUser['email'] ?? '') ?>" required>
            </label>
            <label>
              Contraseña <?= $selectedUser ? '(dejar vacía para conservar)' : '' ?>
              <input type="password" name="password">
            </label>
            <label>
              Rol
              <select name="role">
                <option value="user" <?= (($selectedUser['role'] ?? 'user') === 'user') ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= (($selectedUser['role'] ?? 'user') === 'admin') ? 'selected' : '' ?>>Administrador</option>
              </select>
            </label>
            <label>
              Estado
              <select name="status">
                <option value="active" <?= (($selectedUser['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= (($selectedUser['status'] ?? 'active') === 'inactive') ? 'selected' : '' ?>>Inactivo</option>
              </select>
            </label>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </form>
        </section>

        <section class="panel">
          <h2>Usuarios</h2>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Correo</th>
                  <th>Rol</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $item): ?>
                  <tr>
                    <td><?= htmlspecialchars($item['full_name']) ?></td>
                    <td><?= htmlspecialchars($item['email']) ?></td>
                    <td><?= htmlspecialchars($item['role']) ?></td>
                    <td>
                      <a href="dashboard.php?edit=<?= (int) $item['id'] ?>">Editar</a>
                      <a href="dashboard.php?delete=<?= (int) $item['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <section class="panel sales-panel">
        <h2>Ventas de hoy</h2>
        <?php if ($dailySales): ?>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Cliente</th>
                  <th>Monto</th>
                  <th>Descripción</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($dailySales as $sale): ?>
                  <tr>
                    <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                    <td>$<?= number_format((float) $sale['total_amount'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($sale['description']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p>Aún no hay ventas registradas para hoy.</p>
        <?php endif; ?>
      </section>
    </section>
  </main>
</body>
</html>

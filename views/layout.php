<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/king.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>Finanzas</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler"
                aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand" href="/<?= $_ENV['APP_NAME'] ?>/">
                <img src="<?= asset('./images/king.png') ?>" width="35px" alt="logo">
                Finanzas
            </a>

            <div class="collapse navbar-collapse" id="navbarToggler">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <!-- Inicio -->
                    <li class="nav-item">
                        <a class="nav-link" href="/<?= $_ENV['APP_NAME'] ?>/">
                            <i class="bi bi-house-fill me-1"></i>Inicio
                        </a>
                    </li>

                    <!-- Cuentas -->
                    <li class="nav-item">
                        <a class="nav-link" href="/<?= $_ENV['APP_NAME'] ?>/cuentas">
                            <i class="bi bi-wallet me-1"></i>Cuentas
                        </a>
                    </li>

                    <!-- Movimientos -->
                    <li class="nav-item">
                        <a class="nav-link" href="/<?= $_ENV['APP_NAME'] ?>/movimientos">
                            <i class="bi bi-arrow-left-right me-1"></i>Movimientos
                        </a>
                    </li>

                    <!-- Gastos Fijos -->
                    <li class="nav-item">
                        <a class="nav-link" href="/<?= $_ENV['APP_NAME'] ?>/gastos_fijos">
                            <i class="bi bi-calendar-check me-1"></i>Gastos Fijos
                        </a>
                    </li>

                    <!-- Deudas -->
                    <li class="nav-item">
                        <a class="nav-link" href="/<?= $_ENV['APP_NAME'] ?>/deudas">
                            <i class="bi bi-credit-card me-1"></i>Deudas
                        </a>
                    </li>

                    <!-- Configuración (dropdown) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-1"></i>Configuración
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li>
                                <a class="dropdown-item" href="/<?= $_ENV['APP_NAME'] ?>/bancos">
                                    <i class="bi bi-bank2 me-2"></i>Bancos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/<?= $_ENV['APP_NAME'] ?>/categorias">
                                    <i class="bi bi-tags me-2"></i>Categorías
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>

                <div class="col-lg-1 d-grid mb-lg-0 mb-2">
                    <a href="/menu/" class="btn btn-danger btn-sm">
                        <i class="bi bi-arrow-bar-left"></i> MENÚ
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar"
            role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">
        <?php echo $contenido; ?>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:xx-small; font-weight: bold;">
                    Base<?= date('Y') ?> &copy;
                </p>
            </div>
        </div>
    </div>
</body>

</html>

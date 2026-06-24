<!-- KPIs -->
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-primary rounded-circle p-2 me-2">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </span>
                    <span class="text-muted small">Saldo Total</span>
                </div>
                <h4 class="mb-0 fw-bold" id="kpiSaldoTotal">--</h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-success rounded-circle p-2 me-2">
                        <i class="bi bi-arrow-down-circle fs-5"></i>
                    </span>
                    <span class="text-muted small">Ingresos del Mes</span>
                </div>
                <h4 class="mb-0 fw-bold text-success" id="kpiIngresos">--</h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-danger rounded-circle p-2 me-2">
                        <i class="bi bi-arrow-up-circle fs-5"></i>
                    </span>
                    <span class="text-muted small">Gastos del Mes</span>
                </div>
                <h4 class="mb-0 fw-bold text-danger" id="kpiGastos">--</h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-warning rounded-circle p-2 me-2">
                        <i class="bi bi-credit-card-2-back fs-5"></i>
                    </span>
                    <span class="text-muted small">Deuda Pendiente</span>
                </div>
                <h4 class="mb-0 fw-bold text-warning" id="kpiDeuda">--</h4>
            </div>
        </div>
    </div>

</div>

<!-- Graficas fila 1 -->
<div class="row g-3 mb-4">

    <!-- Ingresos vs Gastos (barras) -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <span class="fw-semibold"><i class="bi bi-bar-chart-line me-1"></i>Ingresos vs Gastos — 6 meses</span>
            </div>
            <div class="card-body">
                <canvas id="chartTendencia" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Gastos por categoria (dona) -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <span class="fw-semibold"><i class="bi bi-pie-chart me-1"></i>Gastos por Categoria</span>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartCategorias" height="220"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- Fila 2: Saldos + Deudas + Ultimos movimientos -->
<div class="row g-3">

    <!-- Saldos por cuenta -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <span class="fw-semibold"><i class="bi bi-bank me-1"></i>Saldos por Cuenta</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="listaCuentas"></ul>
            </div>
        </div>
    </div>

    <!-- Deudas pendientes -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <span class="fw-semibold"><i class="bi bi-credit-card-2-back me-1"></i>Deudas Pendientes</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="listaDeudas"></ul>
            </div>
        </div>
    </div>

    <!-- Ultimos movimientos -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <span class="fw-semibold"><i class="bi bi-clock-history me-1"></i>Ultimos Movimientos</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="listaUltimos"></ul>
            </div>
        </div>
    </div>

</div>

<script>
    const RUTA_APP = '/<?= $_ENV['APP_NAME'] ?>';
</script>
<script src="<?= asset('build/js/inicio.js') ?>"></script>

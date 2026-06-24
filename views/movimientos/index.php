<div class="row mb-3">
    <div class="col">
        <h4><i class="bi bi-arrow-left-right me-2"></i><?= $titulo ?></h4>
    </div>
    <div class="col text-end">
        <button class="btn btn-primary btn-sm" id="btnNuevo">
            <i class="bi bi-plus-circle me-1"></i>Nuevo Movimiento
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="row g-2 mb-3" id="panelFiltros">
    <div class="col-auto">
        <label class="form-label mb-0 small">Desde</label>
        <input type="date" class="form-control form-control-sm" id="filtroDesde">
    </div>
    <div class="col-auto">
        <label class="form-label mb-0 small">Hasta</label>
        <input type="date" class="form-control form-control-sm" id="filtroHasta">
    </div>
    <div class="col-auto">
        <label class="form-label mb-0 small">Tipo</label>
        <select class="form-select form-select-sm" id="filtroTipo">
            <option value="">Todos</option>
            <option value="ingreso">Ingreso</option>
            <option value="gasto">Gasto</option>
            <option value="transferencia">Transferencia</option>
        </select>
    </div>
    <div class="col-auto">
        <label class="form-label mb-0 small">Cuenta</label>
        <select class="form-select form-select-sm" id="filtroCuenta">
            <option value="">Todas</option>
        </select>
    </div>
    <div class="col-auto d-flex align-items-end">
        <button class="btn btn-secondary btn-sm" id="btnFiltrar">
            <i class="bi bi-funnel me-1"></i>Filtrar
        </button>
    </div>
    <div class="col-auto d-flex align-items-end">
        <button class="btn btn-outline-secondary btn-sm" id="btnLimpiar">
            <i class="bi bi-x-circle me-1"></i>Limpiar
        </button>
    </div>
</div>

<!-- Totales resumen -->
<div class="row g-2 mb-3" id="panelTotales">
    <div class="col-auto">
        <span class="badge bg-success fs-6" id="totalIngresos">Ingresos: Q 0.00</span>
    </div>
    <div class="col-auto">
        <span class="badge bg-danger fs-6" id="totalGastos">Gastos: Q 0.00</span>
    </div>
    <div class="col-auto">
        <span class="badge bg-primary fs-6" id="totalTransferencias">Transferencias: Q 0.00</span>
    </div>
</div>

<div class="row">
    <div class="col">
        <table id="datatable" class="table table-striped table-hover table-sm w-100"></table>
    </div>
</div>

<!-- ============================================================
     MODAL: Nuevo Movimiento
============================================================ -->
<div class="modal fade" id="modalMovimiento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    <i class="bi bi-arrow-left-right me-2"></i>Nuevo Movimiento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formMovimiento">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="mov_tipo" name="mov_tipo">
                            <option value="">-- Selecciona --</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="gasto">Gasto</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm"
                            id="mov_fecha" name="mov_fecha">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Monto <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0.01"
                                class="form-control form-control-sm"
                                id="mov_monto" name="mov_monto">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cuenta Origen <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm"
                            id="mov_cuenta_origen_id" name="mov_cuenta_origen_id">
                            <option value="">-- Selecciona cuenta --</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3" id="wrapCuentaDestino" style="display:none">
                        <label class="form-label">Cuenta Destino <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm"
                            id="mov_cuenta_destino_id" name="mov_cuenta_destino_id">
                            <option value="">-- Selecciona cuenta --</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3" id="wrapCategoria">
                        <label class="form-label">Categoria</label>
                        <select class="form-select form-select-sm"
                            id="mov_categoria_id" name="mov_categoria_id">
                            <option value="">-- Sin categoria --</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Descripcion <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm"
                            id="mov_descripcion" name="mov_descripcion"
                            placeholder="Ej: Pago de renta">
                    </div>
                </div>

            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formMovimiento" id="btnCrear" class="btn btn-primary btn-sm">
                    Guardar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoader"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const RUTA_APP = '/<?= $_ENV['APP_NAME'] ?>';
</script>
<script src="<?= asset('build/js/movimientos/index.js') ?>"></script>

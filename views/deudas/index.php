<div class="row mb-3">
    <div class="col">
        <h4><i class="bi bi-credit-card me-2"></i><?= $titulo ?></h4>
    </div>
    <div class="col text-end">
        <button class="btn btn-primary btn-sm" id="btnNuevo">
            <i class="bi bi-plus-circle me-1"></i>Nueva Deuda
        </button>
    </div>
</div>

<div class="row">
    <div class="col">
        <table id="datatable" class="table table-striped table-hover table-sm w-100"></table>
    </div>
</div>

<!-- Modal CRUD -->
<div class="modal fade" id="modalDeuda" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    <i class="bi bi-credit-card me-2"></i>Nueva Deuda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formDeuda">
                <input type="hidden" id="deu_id" name="deu_id">

                <div class="mb-3">
                    <label for="deu_descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" id="deu_descripcion" name="deu_descripcion"
                        placeholder="Ej: Préstamo vehículo">
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="deu_monto_total" class="form-label">Monto total <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="deu_monto_total" name="deu_monto_total" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="deu_monto_pagado" class="form-label">Monto pagado</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="deu_monto_pagado" name="deu_monto_pagado" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="deu_cuota_mensual" class="form-label">Cuota mensual <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="deu_cuota_mensual" name="deu_cuota_mensual" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="deu_fecha_inicio" class="form-label">Fecha inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="deu_fecha_inicio" name="deu_fecha_inicio">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="deu_fecha_fin_est" class="form-label">Fecha fin estimada</label>
                        <input type="date" class="form-control form-control-sm" id="deu_fecha_fin_est" name="deu_fecha_fin_est">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deu_cuenta_id" class="form-label">Cuenta de pago <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="deu_cuenta_id" name="deu_cuenta_id">
                        <option value="">-- Selecciona cuenta --</option>
                    </select>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnModificar" class="btn btn-warning btn-sm">
                    Modificar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderModificar"></span>
                </button>
                <button type="submit" form="formDeuda" id="btnCrear" class="btn btn-primary btn-sm">
                    Guardar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoader"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Abonar -->
<div class="modal fade" id="modalAbonar" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Registrar Abono</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formAbonar">
                <input type="hidden" id="abonar_deu_id" name="deu_id">
                <p class="mb-1 fw-bold" id="abonarDescripcion"></p>
                <p class="mb-3 text-muted small">
                    Pendiente: <span id="abonarPendiente" class="text-danger fw-bold"></span>
                </p>
                <div class="mb-3">
                    <label for="abonar_monto" class="form-label">Monto <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm"
                            id="abonar_monto" name="monto">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="abonar_fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="abonar_fecha" name="fecha">
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnAbonar" class="btn btn-success btn-sm">
                    Confirmar abono <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderAbonar"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const RUTA_APP = '/<?= $_ENV['APP_NAME'] ?>';
</script>
<script src="<?= asset('build/js/deudas/index.js') ?>"></script>

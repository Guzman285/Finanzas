<div class="row mb-3">
    <div class="col">
        <h4><i class="bi bi-calendar-check me-2"></i><?= $titulo ?></h4>
    </div>
    <div class="col text-end">
        <button class="btn btn-primary btn-sm" id="btnNuevo">
            <i class="bi bi-plus-circle me-1"></i>Nuevo Gasto Fijo
        </button>
    </div>
</div>

<div class="row">
    <div class="col">
        <table id="datatable" class="table table-striped table-hover table-sm w-100"></table>
    </div>
</div>

<!-- Modal CRUD -->
<div class="modal fade" id="modalGastoFijo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    <i class="bi bi-calendar-check me-2"></i>Nuevo Gasto Fijo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formGastoFijo">
                <input type="hidden" id="gf_id" name="gf_id">

                <div class="mb-3">
                    <label for="gf_descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" id="gf_descripcion" name="gf_descripcion"
                        placeholder="Ej: Alquiler">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gf_monto_estimado" class="form-label">Monto estimado <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="gf_monto_estimado" name="gf_monto_estimado" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gf_dia_pago" class="form-label">Día de pago <span class="text-danger">*</span></label>
                        <input type="number" min="1" max="31" class="form-control form-control-sm"
                            id="gf_dia_pago" name="gf_dia_pago" placeholder="1-31">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="gf_cuenta_id" class="form-label">Cuenta <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="gf_cuenta_id" name="gf_cuenta_id">
                        <option value="">-- Selecciona cuenta --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="gf_categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="gf_categoria_id" name="gf_categoria_id">
                        <option value="">-- Selecciona categoría --</option>
                    </select>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnModificar" class="btn btn-warning btn-sm">
                    Modificar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderModificar"></span>
                </button>
                <button type="submit" form="formGastoFijo" id="btnCrear" class="btn btn-primary btn-sm">
                    Guardar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoader"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pagar -->
<div class="modal fade" id="modalPagar" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formPagar">
                <input type="hidden" id="pagar_gf_id" name="gf_id">

                <p class="mb-2 text-muted" id="pagarDescripcion"></p>

                <div class="mb-3">
                    <label for="pagar_cuenta_id" class="form-label">Cuenta / Tarjeta <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="pagar_cuenta_id" name="cuenta_id">
                        <option value="">-- Selecciona cuenta --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="pagar_monto" class="form-label">Monto real <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm"
                            id="pagar_monto" name="monto">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="pagar_fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="pagar_fecha" name="fecha">
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnPagar" class="btn btn-success btn-sm">
                    Confirmar pago <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderPagar"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const RUTA_APP = '/<?= $_ENV['APP_NAME'] ?>';
</script>
<script src="<?= asset('build/js/gastos_fijos/index.js') ?>"></script>

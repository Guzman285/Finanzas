<div class="row mb-3">
    <div class="col">
        <h4><i class="bi bi-bank me-2"></i><?= $titulo ?></h4>
    </div>
    <div class="col text-end">
        <button class="btn btn-primary btn-sm" id="btnNuevo">
            <i class="bi bi-plus-circle me-1"></i>Nueva Cuenta
        </button>
    </div>
</div>

<div class="row">
    <div class="col">
        <table id="datatableCuentas" class="table table-striped table-hover table-sm w-100">
        </table>
    </div>
</div>

<div class="modal fade" id="modalCuenta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId"><i class="bi bi-bank me-2"></i>Nueva Cuenta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formCuenta">
                <input type="hidden" id="cta_id" name="cta_id">

                <div class="mb-3">
                    <label for="cta_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" id="cta_nombre" name="cta_nombre"
                        placeholder="Ej: Banco Industrial">
                </div>

                <div class="mb-3">
                    <label for="cta_tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="cta_tipo" name="cta_tipo">
                        <option value="">-- Selecciona --</option>
                        <option value="monetaria">Monetaria</option>
                        <option value="ahorro">Ahorro</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta_debito">Tarjeta Débito</option>
                        <option value="tarjeta_credito">Tarjeta Crédito</option>
                    </select>
                </div>

                <div class="mb-3 d-none" id="divBanco">
                    <label for="cta_banco_id" class="form-label">Banco al que pertenece <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="cta_banco_id" name="cta_banco_id">
                        <option value="">-- Selecciona banco --</option>
                    </select>
                </div>

                <div class="mb-3 d-none" id="divNumero">
                    <label for="cta_numero" class="form-label">Número de cuenta</label>
                    <input type="text" class="form-control form-control-sm" id="cta_numero" name="cta_numero"
                        placeholder="Ej: 001-123456-7890">
                </div>

                <div class="mb-3">
                    <label for="cta_saldo" class="form-label">Saldo inicial <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                            id="cta_saldo" name="cta_saldo" placeholder="0.00">
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnModificar" class="btn btn-warning btn-sm">
                    Modificar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderModificar"></span>
                </button>
                <button type="submit" form="formCuenta" id="btnCrear" class="btn btn-primary btn-sm">
                    Guardar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoader"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const RUTA_APP = '/<?= $_ENV['APP_NAME'] ?>';
</script>
<script src="<?= asset('build/js/cuentas/index.js') ?>"></script>

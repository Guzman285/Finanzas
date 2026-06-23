<div class="row mb-3">
    <div class="col">
        <h4><i class="bi bi-bank me-2"></i><?= $titulo ?></h4>
    </div>
    <div class="col text-end">
        <button class="btn btn-primary btn-sm" id="btnNuevo">
            <i class="bi bi-plus-circle me-1"></i>Nuevo Banco
        </button>
    </div>
</div>

<div class="row">
    <div class="col">
        <table id="datatableBancos" class="table table-striped table-hover table-sm w-100">
        </table>
    </div>
</div>

<div class="modal fade" id="modalBanco" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId"><i class="bi bi-bank me-2"></i>Nuevo Banco</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formBanco">
                <input type="hidden" id="ban_id" name="ban_id">

                <div class="mb-3">
                    <label for="ban_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" id="ban_nombre" name="ban_nombre"
                        placeholder="Ej: Banco Industrial">
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnModificar" class="btn btn-warning btn-sm">
                    Modificar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderModificar"></span>
                </button>
                <button type="submit" form="formBanco" id="btnCrear" class="btn btn-primary btn-sm">
                    Guardar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoader"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const RUTA_APP = '/<?= $_ENV['APP_NAME'] ?>';
</script>
<script src="<?= asset('build/js/bancos/index.js') ?>"></script>

<div class="row mb-3">
    <div class="col">
        <h4><i class="bi bi-tags me-2"></i><?= $titulo ?></h4>
    </div>
    <div class="col text-end">
        <button class="btn btn-primary btn-sm" id="btnNuevo">
            <i class="bi bi-plus-circle me-1"></i>Nueva Categoría
        </button>
    </div>
</div>

<div class="row">
    <div class="col">
        <table id="datatableCategorias" class="table table-striped table-hover table-sm w-100">
        </table>
    </div>
</div>

<div class="modal fade" id="modalCategoria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId"><i class="bi bi-tags me-2"></i>Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formCategoria">
                <input type="hidden" id="cat_id" name="cat_id">

                <div class="mb-3">
                    <label for="cat_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" id="cat_nombre" name="cat_nombre"
                        placeholder="Ej: Alimentación">
                </div>

                <div class="mb-3">
                    <label for="cat_tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="cat_tipo" name="cat_tipo">
                        <option value="">-- Selecciona --</option>
                        <option value="ingreso">Ingreso</option>
                        <option value="gasto">Gasto</option>
                        <option value="neutro">Neutro</option>
                    </select>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnModificar" class="btn btn-warning btn-sm">
                    Modificar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderModificar"></span>
                </button>
                <button type="submit" form="formCategoria" id="btnCrear" class="btn btn-primary btn-sm">
                    Guardar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoader"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const RUTA_APP = '/<?= $_ENV['APP_NAME'] ?>';
</script>
<script src="<?= asset('build/js/categorias/index.js') ?>"></script>

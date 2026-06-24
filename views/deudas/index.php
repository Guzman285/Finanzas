<div class="row mb-3">
    <div class="col">
        <h4><i class="bi bi-credit-card-2-back me-2"></i><?= $titulo ?></h4>
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

<!-- ============================================================
     MODAL: Crear / Editar Deuda
============================================================ -->
<div class="modal fade" id="modalDeuda" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    <i class="bi bi-credit-card-2-back me-2"></i>Nueva Deuda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formDeuda">
                <input type="hidden" id="deu_id" name="deu_id">

                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm"
                            id="deu_descripcion" name="deu_descripcion" placeholder="Ej: Préstamo personal">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Entidad / Banco</label>
                        <input type="text" class="form-control form-control-sm"
                            id="deu_entidad" name="deu_entidad" placeholder="Ej: Banco Industrial">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="deu_tipo" name="deu_tipo">
                            <option value="fija">Fija</option>
                            <option value="revolving">Revolving / Tarjeta</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Monto Total</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="deu_monto_total" name="deu_monto_total" value="0">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Cuota Mensual</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="deu_cuota_mensual" name="deu_cuota_mensual" value="0">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Límite Crédito</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="deu_limite_credito" name="deu_limite_credito">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tasa Interés (%)</label>
                        <input type="number" step="0.0001" min="0" class="form-control form-control-sm"
                            id="deu_tasa_interes" name="deu_tasa_interes" value="0">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Día Corte</label>
                        <input type="number" min="1" max="31" class="form-control form-control-sm"
                            id="deu_dia_corte" name="deu_dia_corte">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Día Pago</label>
                        <input type="number" min="1" max="31" class="form-control form-control-sm"
                            id="deu_dia_pago" name="deu_dia_pago">
                    </div>
                    <div class="col-md-5 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                id="deu_descuento_nomina" name="deu_descuento_nomina" value="1">
                            <label class="form-check-label" for="deu_descuento_nomina">
                                Descuento nómina
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cuenta <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="deu_cuenta_id" name="deu_cuenta_id">
                            <option value="">-- Selecciona cuenta --</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm"
                            id="deu_fecha_inicio" name="deu_fecha_inicio">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha Fin Est.</label>
                        <input type="date" class="form-control form-control-sm"
                            id="deu_fecha_fin_est" name="deu_fecha_fin_est">
                    </div>
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

<!-- ============================================================
     MODAL: Registrar Pago
============================================================ -->
<div class="modal fade" id="modalPago" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formPago">
                <input type="hidden" id="pago_deu_id" name="deu_id">
                <p class="mb-2 fw-bold" id="pagoDescripcion"></p>

                <div class="mb-3">
                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="pago_fecha" name="dm_fecha">
                </div>
                <div class="mb-3">
                    <label class="form-label">Monto Total Pago <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm"
                            id="pago_monto_total" name="dm_monto_total">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Abono a Capital</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                            id="pago_abono_capital" name="dm_abono_capital" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Intereses</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                            id="pago_interes" name="dm_interes" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm"
                        id="pago_descripcion" name="dm_descripcion">
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnPago" class="btn btn-success btn-sm">
                    Registrar Pago <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderPago"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL: Registrar Consumo (revolving)
============================================================ -->
<div class="modal fade" id="modalConsumo" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cart-plus me-2"></i>Registrar Consumo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formConsumo">
                <input type="hidden" id="consumo_deu_id" name="deu_id">
                <p class="mb-2 fw-bold" id="consumoDescripcion"></p>

                <div class="mb-3">
                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="consumo_fecha" name="dm_fecha">
                </div>
                <div class="mb-3">
                    <label class="form-label">Monto <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm"
                            id="consumo_monto" name="dm_monto_total">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm"
                        id="consumo_descripcion" name="dm_descripcion">
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnConsumo" class="btn btn-primary btn-sm">
                    Registrar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderConsumo"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL: Ajuste de Saldo
============================================================ -->
<div class="modal fade" id="modalAjuste" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-sliders me-2"></i>Ajustar Saldo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formAjuste">
                <input type="hidden" id="ajuste_deu_id" name="deu_id">
                <p class="mb-2 fw-bold" id="ajusteDescripcion"></p>

                <div class="mb-3">
                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="ajuste_fecha" name="dm_fecha">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nuevo Saldo Total <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                            id="ajuste_monto" name="dm_monto_total">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Motivo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm"
                        id="ajuste_descripcion" name="dm_descripcion">
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnAjuste" class="btn btn-warning btn-sm">
                    Aplicar Ajuste <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderAjuste"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL: Historial de Movimientos
============================================================ -->
<div class="modal fade" id="modalMovimientos" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history me-2"></i>Movimientos &mdash; <span id="movDeudaNombre"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table id="datatableMovimientos" class="table table-striped table-hover table-sm w-100"></table>
            </div>
        </div>
    </div>
</div>

<script>
    const RUTA_APP = '/<?= $_ENV['APP_NAME'] ?>';
</script>
<script src="<?= asset('build/js/deudas/index.js') ?>"></script>

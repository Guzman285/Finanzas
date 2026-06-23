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

<!-- ============================================================ -->
<!-- Modal CRUD -->
<!-- ============================================================ -->
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

                <!-- Fila 1: descripción + entidad -->
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="deu_descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="deu_descripcion" name="deu_descripcion"
                            placeholder="Ej: Extrafin BAC, Visa Cuotas BI">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="deu_entidad" class="form-label">Entidad</label>
                        <input type="text" class="form-control form-control-sm" id="deu_entidad" name="deu_entidad"
                            placeholder="Ej: BAC Credomatic">
                    </div>
                </div>

                <!-- Fila 2: tipo + cuenta -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="deu_tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="deu_tipo" name="deu_tipo">
                            <option value="fija">Fija (extrafin, préstamo, visacuotas)</option>
                            <option value="revolving">Revolving (tarjeta de crédito)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="deu_cuenta_id" class="form-label">Cuenta de pago <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="deu_cuenta_id" name="deu_cuenta_id">
                            <option value="">-- Selecciona --</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="deu_cuota_mensual" class="form-label">Pago mínimo / Cuota</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="deu_cuota_mensual" name="deu_cuota_mensual" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- Fila 3: montos -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="deu_monto_total" class="form-label">Saldo actual <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="deu_monto_total" name="deu_monto_total" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3" id="wrapMontoPagado">
                        <label for="deu_monto_pagado" class="form-label">Monto pagado</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="deu_monto_pagado" name="deu_monto_pagado" placeholder="0.00">
                        </div>
                        <div class="form-text">Solo para deudas fijas</div>
                    </div>
                    <div class="col-md-4 mb-3" id="wrapLimite">
                        <label for="deu_limite_credito" class="form-label">Límite de crédito</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="deu_limite_credito" name="deu_limite_credito" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- Fila 4: tasa + días corte/pago -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="deu_tasa_interes" class="form-label">Tasa interés mensual (%)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.0001" min="0" max="100" class="form-control form-control-sm"
                                id="deu_tasa_interes" name="deu_tasa_interes" placeholder="0.0000">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">Referencial, del estado de cuenta</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="deu_dia_corte" class="form-label">Día de corte</label>
                        <input type="number" min="1" max="31" class="form-control form-control-sm"
                            id="deu_dia_corte" name="deu_dia_corte" placeholder="Ej: 15">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="deu_dia_pago" class="form-label">Día límite de pago</label>
                        <input type="number" min="1" max="31" class="form-control form-control-sm"
                            id="deu_dia_pago" name="deu_dia_pago" placeholder="Ej: 5">
                    </div>
                </div>

                <!-- Fila 5: fechas -->
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

                <!-- Descuento nómina -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="deu_descuento_nomina" name="deu_descuento_nomina" value="1">
                    <label class="form-check-label" for="deu_descuento_nomina">Se descuenta de nómina</label>
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

<!-- ============================================================ -->
<!-- Modal Pago (con desglose capital + interés) -->
<!-- ============================================================ -->
<div class="modal fade" id="modalPago" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formPago">
                <input type="hidden" id="pago_deu_id" name="deu_id">
                <div class="alert alert-secondary py-2 mb-3">
                    <strong id="pagoDescripcion"></strong>
                    <div class="small mt-1">
                        Saldo pendiente: <span id="pagoPendiente" class="text-danger fw-bold"></span>
                        &nbsp;|&nbsp; Pago mínimo: <span id="pagoCuota" class="text-primary fw-bold"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="pago_fecha" class="form-label">Fecha del pago <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="pago_fecha" name="dm_fecha">
                </div>
                <div class="mb-3">
                    <label for="pago_descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" id="pago_descripcion" name="dm_descripcion"
                        placeholder="Ej: Pago mínimo junio 2026">
                </div>
                <div class="mb-3">
                    <label for="pago_monto_total" class="form-label">Monto total pagado <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm"
                            id="pago_monto_total" name="dm_monto_total" placeholder="0.00">
                    </div>
                    <div class="form-text">Lo que salió de tu cuenta bancaria</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="pago_abono_capital" class="form-label">Abono a capital</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="pago_abono_capital" name="dm_abono_capital" placeholder="0.00">
                        </div>
                        <div class="form-text">Lo que bajó la deuda (del estado de cuenta)</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pago_interes" class="form-label">Intereses pagados</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                id="pago_interes" name="dm_interes" placeholder="0.00">
                        </div>
                        <div class="form-text">Lo que fue a intereses (del estado de cuenta)</div>
                    </div>
                </div>
                <div class="alert alert-info py-2 small" id="alertDesglose" style="display:none">
                    <i class="bi bi-info-circle me-1"></i>
                    Capital + Interés = <strong id="desgloseSuma">Q 0.00</strong>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnPago" class="btn btn-success btn-sm">
                    Confirmar pago <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderPago"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- Modal Consumo -->
<!-- ============================================================ -->
<div class="modal fade" id="modalConsumo" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cart-plus me-2"></i>Registrar Consumo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="modal-body" id="formConsumo">
                <input type="hidden" id="consumo_deu_id" name="deu_id">
                <p class="mb-3 fw-bold" id="consumoDescripcion"></p>
                <div class="mb-3">
                    <label for="consumo_fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="consumo_fecha" name="dm_fecha">
                </div>
                <div class="mb-3">
                    <label for="consumo_descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" id="consumo_descripcion" name="dm_descripcion"
                        placeholder="Ej: Compra supermercado">
                </div>
                <div class="mb-3">
                    <label for="consumo_monto" class="form-label">Monto <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Q</span>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm"
                            id="consumo_monto" name="dm_monto_total" placeholder="0.00">
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnConsumo" class="btn btn-warning btn-sm">
                    Registrar <span class="spinner-grow spinner-grow-sm ms-2 d-none" id="spanLoaderConsumo"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- Modal Historial -->
<!-- ============================================================ -->
<div class="modal fade" id="modalHistorial" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Historial: <span id="historialTitulo"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table id="datatableHistorial" class="table table-striped table-hover table-sm w-100"></table>
            </div>
        </div>
    </div>
</div>

<script>
    const RUTA_APP = '/<?= $_ENV['APP_NAME'] ?>';
</script>
<script src="<?= asset('build/js/deudas/index.js') ?>"></script>

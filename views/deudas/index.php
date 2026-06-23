<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-credit-card-2-back me-2"></i><?= $titulo ?></h4>
    <button id="btnNuevo" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-lg me-1"></i>Nueva Deuda
    </button>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-2">
      <table id="datatable" class="table table-sm table-hover w-100"></table>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Crear / Editar Deuda
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalDeuda" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitleId">Nueva Deuda</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formDeuda">
        <div class="modal-body">
          <input type="hidden" name="deu_id" id="deu_id">

          <!-- Fila 1 -->
          <div class="row g-2 mb-2">
            <div class="col-12 col-md-7">
              <label class="form-label form-label-sm">Descripción *</label>
              <input type="text" name="deu_descripcion" id="deu_descripcion"
                class="form-control form-control-sm" required>
            </div>
            <div class="col-12 col-md-5">
              <label class="form-label form-label-sm">Entidad / Banco</label>
              <input type="text" name="deu_entidad" id="deu_entidad"
                class="form-control form-control-sm">
            </div>
          </div>

          <!-- Fila 2 -->
          <div class="row g-2 mb-2">
            <div class="col-6 col-md-3">
              <label class="form-label form-label-sm">Tipo *</label>
              <select name="deu_tipo" id="deu_tipo" class="form-select form-select-sm" required>
                <option value="fija">Fija</option>
                <option value="revolving">Revolving / Tarjeta</option>
              </select>
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label form-label-sm">Monto Total</label>
              <input type="number" name="deu_monto_total" id="deu_monto_total"
                class="form-control form-control-sm" step="0.01" min="0" value="0">
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label form-label-sm">Cuota Mensual</label>
              <input type="number" name="deu_cuota_mensual" id="deu_cuota_mensual"
                class="form-control form-control-sm" step="0.01" min="0" value="0">
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label form-label-sm">Límite Crédito</label>
              <input type="number" name="deu_limite_credito" id="deu_limite_credito"
                class="form-control form-control-sm" step="0.01" min="0">
            </div>
          </div>

          <!-- Fila 3 -->
          <div class="row g-2 mb-2">
            <div class="col-6 col-md-3">
              <label class="form-label form-label-sm">Tasa Interés (%)</label>
              <input type="number" name="deu_tasa_interes" id="deu_tasa_interes"
                class="form-control form-control-sm" step="0.0001" min="0" value="0">
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label form-label-sm">Día de Corte</label>
              <input type="number" name="deu_dia_corte" id="deu_dia_corte"
                class="form-control form-control-sm" min="1" max="31">
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label form-label-sm">Día de Pago</label>
              <input type="number" name="deu_dia_pago" id="deu_dia_pago"
                class="form-control form-control-sm" min="1" max="31">
            </div>
            <div class="col-6 col-md-3">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox"
                  name="deu_descuento_nomina" id="deu_descuento_nomina" value="1">
                <label class="form-check-label form-label-sm" for="deu_descuento_nomina">
                  Descuento nómina
                </label>
              </div>
            </div>
          </div>

          <!-- Fila 4 -->
          <div class="row g-2 mb-2">
            <div class="col-12 col-md-4">
              <label class="form-label form-label-sm">Cuenta *</label>
              <select name="deu_cuenta_id" id="deu_cuenta_id"
                class="form-select form-select-sm" required>
                <option value="">-- Selecciona cuenta --</option>
              </select>
            </div>
            <div class="col-6 col-md-4">
              <label class="form-label form-label-sm">Fecha Inicio *</label>
              <input type="date" name="deu_fecha_inicio" id="deu_fecha_inicio"
                class="form-control form-control-sm" required>
            </div>
            <div class="col-6 col-md-4">
              <label class="form-label form-label-sm">Fecha Fin Est.</label>
              <input type="date" name="deu_fecha_fin_est" id="deu_fecha_fin_est"
                class="form-control form-control-sm">
            </div>
          </div>
        </div><!-- /modal-body -->

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>

          <button type="submit" id="btnCrear" class="btn btn-primary btn-sm">
            <span id="spanLoader" class="spinner-border spinner-border-sm me-1"></span>
            Guardar
          </button>

          <button type="button" id="btnModificar" class="btn btn-warning btn-sm">
            <span id="spanLoaderModificar" class="spinner-border spinner-border-sm me-1"></span>
            Modificar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Registrar Pago
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalPago" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registrar Pago</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formPago">
        <div class="modal-body">
          <input type="hidden" name="deu_id" id="pago_deu_id">
          <p class="fw-bold mb-3" id="pagoDescripcion"></p>

          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label form-label-sm">Fecha *</label>
              <input type="date" name="dm_fecha" id="pago_fecha"
                class="form-control form-control-sm" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label form-label-sm">Monto Total Pago *</label>
              <input type="number" name="dm_monto_total" id="pago_monto_total"
                class="form-control form-control-sm" step="0.01" min="0.01" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label form-label-sm">Abono a Capital</label>
              <input type="number" name="dm_abono_capital" id="pago_abono_capital"
                class="form-control form-control-sm" step="0.01" min="0" value="0">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label form-label-sm">Intereses</label>
              <input type="number" name="dm_interes" id="pago_interes"
                class="form-control form-control-sm" step="0.01" min="0" value="0">
            </div>
            <div class="col-12">
              <label class="form-label form-label-sm">Descripción *</label>
              <input type="text" name="dm_descripcion" id="pago_descripcion"
                class="form-control form-control-sm" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnPago" class="btn btn-success btn-sm">
            <span id="spanLoaderPago" class="spinner-border spinner-border-sm me-1"></span>
            Registrar Pago
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Registrar Consumo (revolving)
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalConsumo" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registrar Consumo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formConsumo">
        <div class="modal-body">
          <input type="hidden" name="deu_id" id="consumo_deu_id">
          <p class="fw-bold mb-3" id="consumoDescripcion"></p>
          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label form-label-sm">Fecha *</label>
              <input type="date" name="dm_fecha" id="consumo_fecha"
                class="form-control form-control-sm" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label form-label-sm">Monto *</label>
              <input type="number" name="dm_monto_total" id="consumo_monto"
                class="form-control form-control-sm" step="0.01" min="0.01" required>
            </div>
            <div class="col-12">
              <label class="form-label form-label-sm">Descripción *</label>
              <input type="text" name="dm_descripcion" id="consumo_descripcion"
                class="form-control form-control-sm" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnConsumo" class="btn btn-primary btn-sm">
            <span id="spanLoaderConsumo" class="spinner-border spinner-border-sm me-1"></span>
            Registrar Consumo
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Ajuste de Saldo
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalAjuste" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajustar Saldo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAjuste">
        <div class="modal-body">
          <input type="hidden" name="deu_id" id="ajuste_deu_id">
          <p class="fw-bold mb-3" id="ajusteDescripcion"></p>
          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label form-label-sm">Fecha *</label>
              <input type="date" name="dm_fecha" id="ajuste_fecha"
                class="form-control form-control-sm" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label form-label-sm">Nuevo Saldo Total *</label>
              <input type="number" name="dm_monto_total" id="ajuste_monto"
                class="form-control form-control-sm" step="0.01" min="0" required>
            </div>
            <div class="col-12">
              <label class="form-label form-label-sm">Motivo *</label>
              <input type="text" name="dm_descripcion" id="ajuste_descripcion"
                class="form-control form-control-sm" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnAjuste" class="btn btn-warning btn-sm">
            <span id="spanLoaderAjuste" class="spinner-border spinner-border-sm me-1"></span>
            Aplicar Ajuste
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Historial de Movimientos
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalMovimientos" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Movimientos — <span id="movDeudaNombre"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table id="datatableMovimientos" class="table table-sm table-hover w-100"></table>
      </div>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

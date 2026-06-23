import { Modal } from "bootstrap";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./../lenguaje";
import { Toast, confirmacion, validarFormulario } from "./../funciones";

// ── Referencias DOM ───────────────────────────────────────────
const formDeuda           = document.querySelector("#formDeuda");
const modalEl             = document.querySelector("#modalDeuda");
const modalBS             = new Modal(modalEl);
const modalPagoEl         = document.querySelector("#modalPago");
const modalBSPago         = new Modal(modalPagoEl);
const modalConsumoEl      = document.querySelector("#modalConsumo");
const modalBSConsumo      = new Modal(modalConsumoEl);
const modalHistorialEl    = document.querySelector("#modalHistorial");
const modalBSHistorial    = new Modal(modalHistorialEl);

const spanLoader          = document.getElementById("spanLoader");
const btnCrear            = document.getElementById("btnCrear");
const spanLoaderModificar = document.getElementById("spanLoaderModificar");
const btnModificar        = document.getElementById("btnModificar");
const spanLoaderPago      = document.getElementById("spanLoaderPago");
const btnPago             = document.getElementById("btnPago");
const spanLoaderConsumo   = document.getElementById("spanLoaderConsumo");
const btnConsumo          = document.getElementById("btnConsumo");
const modalTitleId        = document.getElementById("modalTitleId");
const btnNuevo            = document.getElementById("btnNuevo");
const selectCuenta        = document.getElementById("deu_cuenta_id");
const selectTipo          = document.getElementById("deu_tipo");

// Desglose pago
const inputCapital  = document.getElementById("pago_abono_capital");
const inputInteres  = document.getElementById("pago_interes");
const inputTotal    = document.getElementById("pago_monto_total");
const alertDesglose = document.getElementById("alertDesglose");
const desgloseSuma  = document.getElementById("desgloseSuma");

spanLoader.classList.add("d-none");
spanLoaderModificar.classList.add("d-none");
spanLoaderPago.classList.add("d-none");
spanLoaderConsumo.classList.add("d-none");
btnModificar.style.display = "none";
btnModificar.disabled = true;

// ── Mostrar/ocultar campos según tipo ────────────────────────
const toggleCamposTipo = () => {
  const es_revolving = selectTipo.value === "revolving";
  document.getElementById("wrapLimite").style.display       = es_revolving ? "" : "none";
  document.getElementById("wrapMontoPagado").style.display  = es_revolving ? "none" : "";
};
select Tipo.addEventListener("change", toggleCamposTipo);
toggleCamposTipo();

// ── Calcular desglose en tiempo real ─────────────────────────
const actualizarDesglose = () => {
  const capital = parseFloat(inputCapital.value) || 0;
  const interes = parseFloat(inputInteres.value) || 0;
  const suma    = capital + interes;
  desgloseSuma.textContent = `Q ${suma.toFixed(2)}`;
  alertDesglose.style.display = (capital > 0 || interes > 0) ? "" : "none";
};
inputCapital.addEventListener("input", actualizarDesglose);
inputInteres.addEventListener("input", actualizarDesglose);

// ── DataTable principal ───────────────────────────────────────
let datatable = new DataTable("#datatable", {
  language: lenguaje,
  data: null,
  columns: [
    { title: "No.", width: "2%", render: (d, t, r, m) => m.row + 1 },
    {
      title: "DESCRIPCIÓN",
      data: "deu_descripcion",
      render: (d, t, row) => {
        const badge = row.deu_tipo === "revolving"
          ? `<span class="badge bg-info text-dark ms-1">Tarjeta</span>`
          : `<span class="badge bg-secondary ms-1">Fija</span>`;
        return `${d} ${badge}`;
      }
    },
    { title: "ENTIDAD", data: "deu_entidad", defaultContent: "-" },
    {
      title: "SALDO",
      data: "deu_saldo_pendiente",
      render: (d) => {
        const v = parseFloat(d);
        const cls = v > 0 ? "text-danger fw-bold" : "text-success fw-bold";
        return `<span class="${cls}">Q ${v.toFixed(2)}</span>`;
      },
    },
    {
      title: "CUOTA / MÍN.",
      data: "deu_cuota_mensual",
      render: (d) => `Q ${parseFloat(d).toFixed(2)}`,
    },
    {
      title: "CORTE",
      data: "deu_dia_corte",
      render: (d) => d ? `Día ${d}` : "-",
    },
    {
      title: "PAGO",
      data: "deu_dia_pago",
      render: (d) => d ? `Día ${d}` : "-",
    },
    { title: "CUENTA", data: "cuenta_nombre" },
    {
      title: "PROGRESO",
      data: "deu_monto_total",
      searchable: false,
      render: (d, t, row) => {
        if (row.deu_tipo === "revolving") {
          // Revolving: muestra saldo vs límite
          const limite  = parseFloat(row.deu_limite_credito) || 0;
          const saldo   = parseFloat(row.deu_saldo_pendiente) || 0;
          if (limite <= 0) return `<span class="text-muted small">Sin límite</span>`;
          const uso = Math.min(100, Math.round((saldo / limite) * 100));
          const cls = uso >= 90 ? "bg-danger" : uso >= 60 ? "bg-warning" : "bg-success";
          return `<div class="progress" style="height:14px;min-width:80px">
            <div class="progress-bar ${cls}" style="width:${uso}%">${uso}% usado</div>
          </div>`;
        } else {
          // Fija: muestra % pagado
          const total  = parseFloat(row.deu_monto_total)  || 0;
          const pagado = parseFloat(row.deu_monto_pagado) || 0;
          const pct    = total > 0 ? Math.min(100, Math.round((pagado / total) * 100)) : 0;
          const cls    = pct >= 100 ? "bg-success" : pct >= 50 ? "bg-warning" : "bg-danger";
          return `<div class="progress" style="height:14px;min-width:80px">
            <div class="progress-bar ${cls}" style="width:${pct}%">${pct}%</div>
          </div>`;
        }
      },
    },
    {
      title: "Acciones",
      data: "deu_id",
      width: "12%",
      searchable: false,
      render: (data, type, row) => `
        <div class='text-center'>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-success btn-sm rounded-circle registrar-pago"
            title='Registrar pago'
            data-deu-id='${data}'
            data-descripcion='${row.deu_descripcion}'
            data-cuota='${row.deu_cuota_mensual}'
            data-pendiente='${row.deu_saldo_pendiente}'>
            <i class='bi bi-cash-coin'></i>
          </button>
          ${row.deu_tipo === 'revolving' ? `
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-warning btn-sm rounded-circle registrar-consumo"
            title='Registrar consumo'
            data-deu-id='${data}'
            data-descripcion='${row.deu_descripcion}'>
            <i class='bi bi-cart-plus'></i>
          </button>` : ''}
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-info btn-sm rounded-circle ver-historial"
            title='Ver historial'
            data-deu-id='${data}'
            data-descripcion='${row.deu_descripcion}'>
            <i class='bi bi-clock-history'></i>
          </button>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-secondary btn-sm rounded-circle editar"
            title='Modificar'
            data-deu-id='${data}'
            data-descripcion='${row.deu_descripcion}'
            data-entidad='${row.deu_entidad ?? ""}'
            data-tipo='${row.deu_tipo}'
            data-total='${row.deu_monto_total}'
            data-pagado='${row.deu_monto_pagado}'
            data-cuota='${row.deu_cuota_mensual}'
            data-limite='${row.deu_limite_credito ?? ""}'
            data-tasa='${row.deu_tasa_interes}'
            data-corte='${row.deu_dia_corte ?? ""}'
            data-pago-dia='${row.deu_dia_pago ?? ""}'
            data-inicio='${row.deu_fecha_inicio}'
            data-fin='${row.deu_fecha_fin_est ?? ""}'
            data-cuenta='${row.deu_cuenta_id}'
            data-nomina='${row.deu_descuento_nomina}'>
            <i class='bi bi-pencil-fill'></i>
          </button>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-danger btn-sm rounded-circle eliminar"
            data-deu-id='${data}'
            title='Eliminar'>
            <i class='bi bi-x-lg'></i>
          </button>
        </div>`,
    },
  ],
});

// ── DataTable historial ───────────────────────────────────────
let datatableHistorial = new DataTable("#datatableHistorial", {
  language: lenguaje,
  data: null,
  order: [[1, 'desc']],
  columns: [
    {
      title: "TIPO",
      data: "dm_tipo",
      render: (d) => {
        const cfg = {
          pago:    { cls: 'bg-success',   icon: 'bi-cash-coin',    label: 'Pago' },
          consumo: { cls: 'bg-warning text-dark', icon: 'bi-cart-plus', label: 'Consumo' },
          interes: { cls: 'bg-danger',    icon: 'bi-percent',      label: 'Interés' },
          ajuste:  { cls: 'bg-secondary', icon: 'bi-tools',        label: 'Ajuste' },
        };
        const c = cfg[d] || cfg.ajuste;
        return `<span class="badge ${c.cls}"><i class="bi ${c.icon} me-1"></i>${c.label}</span>`;
      }
    },
    { title: "FECHA",        data: "dm_fecha" },
    { title: "DESCRIPCIÓN",  data: "dm_descripcion" },
    { title: "MONTO PAGADO", data: "dm_monto_total",   render: (d) => `Q ${parseFloat(d).toFixed(2)}` },
    {
      title: "ABONO CAPITAL",
      data: "dm_abono_capital",
      render: (d) => {
        const v = parseFloat(d);
        return v > 0 ? `<span class="text-success fw-bold">Q ${v.toFixed(2)}</span>` : '-';
      }
    },
    {
      title: "INTERESES",
      data: "dm_interes",
      render: (d) => {
        const v = parseFloat(d);
        return v > 0 ? `<span class="text-danger">Q ${v.toFixed(2)}</span>` : '-';
      }
    },
    { title: "CUENTA", data: "cuenta_nombre", defaultContent: "-" },
  ],
});

// ── Cargar cuentas ────────────────────────────────────────────
const cargarCuentas = async () => {
  const r    = await fetch(`${RUTA_APP}/API/cuentas/buscar`, { headers: { "X-Requested-With": "fetch" } });
  const data = await r.json();
  if (data.codigo == 1) {
    selectCuenta.innerHTML = '<option value="">-- Selecciona --</option>';
    data.datos.forEach((c) => {
      selectCuenta.innerHTML += `<option value="${c.cta_id}">${c.cta_nombre}</option>`;
    });
  }
};

// ── Buscar deudas ─────────────────────────────────────────────
const buscarApi = async () => {
  try {
    const r    = await fetch(`${RUTA_APP}/API/deudas/buscar`, { headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    datatable.clear().draw();
    if (data.codigo == 1) datatable.rows.add(data.datos).draw();
    else Toast.fire({ icon: "info", title: data.mensaje });
  } catch (e) { console.log(e); }
};
buscarApi();

// ── Guardar ───────────────────────────────────────────────────
const guardarApi = async (e) => {
  e.preventDefault();
  spanLoader.classList.remove("d-none");
  btnCrear.disabled = true;

  const excepciones = ["deu_id", "deu_monto_pagado", "deu_fecha_fin_est", "deu_entidad",
    "deu_limite_credito", "deu_tasa_interes", "deu_dia_corte", "deu_dia_pago"];

  if (!validarFormulario(formDeuda, excepciones)) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoader.classList.add("d-none");
    btnCrear.disabled = false;
    return;
  }

  try {
    const body = new FormData(formDeuda);
    body.delete("deu_id");
    // checkbox descuento_nomina: si no está marcado no lo envía FormData
    if (!body.get("deu_descuento_nomina")) body.set("deu_descuento_nomina", "0");
    const r    = await fetch(`${RUTA_APP}/API/deudas/guardar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { formDeuda.reset(); toggleCamposTipo(); modalBS.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoader.classList.add("d-none");
  btnCrear.disabled = false;
};

// ── Modificar ─────────────────────────────────────────────────
const modificarApi = async () => {
  spanLoaderModificar.classList.remove("d-none");
  btnModificar.disabled = true;

  const excepciones = ["deu_monto_pagado", "deu_fecha_fin_est", "deu_entidad",
    "deu_limite_credito", "deu_tasa_interes", "deu_dia_corte", "deu_dia_pago"];

  if (!validarFormulario(formDeuda, excepciones)) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoaderModificar.classList.add("d-none");
    btnModificar.disabled = false;
    return;
  }

  try {
    const body = new FormData(formDeuda);
    if (!body.get("deu_descuento_nomina")) body.set("deu_descuento_nomina", "0");
    const r    = await fetch(`${RUTA_APP}/API/deudas/modificar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { formDeuda.reset(); toggleCamposTipo(); modalBS.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoaderModificar.classList.add("d-none");
  btnModificar.disabled = false;
};

// ── Eliminar ──────────────────────────────────────────────────
const eliminarApi = async (e) => {
  const { deuId } = e.currentTarget.dataset;
  const ok = await confirmacion("¿Eliminar esta deuda?", "warning", "Sí, eliminar");
  if (!ok) return;

  try {
    const body = new FormData();
    body.append("deu_id", deuId);
    const r    = await fetch(`${RUTA_APP}/API/deudas/eliminar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) buscarApi();
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }
};

// ── Abrir modal pago ──────────────────────────────────────────
const abrirPago = (e) => {
  const { deuId, descripcion, cuota, pendiente } = e.currentTarget.dataset;
  document.getElementById("pago_deu_id").value          = deuId;
  document.getElementById("pagoDescripcion").textContent = descripcion;
  document.getElementById("pagoPendiente").textContent   = `Q ${parseFloat(pendiente).toFixed(2)}`;
  document.getElementById("pagoCuota").textContent       = `Q ${parseFloat(cuota).toFixed(2)}`;
  document.getElementById("pago_fecha").value            = new Date().toISOString().slice(0, 10);
  document.getElementById("pago_monto_total").value      = cuota;
  document.getElementById("pago_abono_capital").value    = "";
  document.getElementById("pago_interes").value          = "";
  alertDesglose.style.display = "none";
  modalBSPago.show();
};

// ── Confirmar pago ────────────────────────────────────────────
const pagoApi = async () => {
  spanLoaderPago.classList.remove("d-none");
  btnPago.disabled = true;

  const formPago = document.getElementById("formPago");
  if (!validarFormulario(formPago, ["dm_abono_capital", "dm_interes"])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoaderPago.classList.add("d-none");
    btnPago.disabled = false;
    return;
  }

  const capital = parseFloat(inputCapital.value) || 0;
  const interes = parseFloat(inputInteres.value) || 0;
  const total   = parseFloat(inputTotal.value)   || 0;

  if (capital + interes > total + 0.01) {
    Toast.fire({ icon: "warning", title: "Capital + Interés no puede superar el monto total" });
    spanLoaderPago.classList.add("d-none");
    btnPago.disabled = false;
    return;
  }

  try {
    const body = new FormData(formPago);
    const r    = await fetch(`${RUTA_APP}/API/deudas/pago`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { modalBSPago.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoaderPago.classList.add("d-none");
  btnPago.disabled = false;
};

// ── Abrir modal consumo ───────────────────────────────────────
const abrirConsumo = (e) => {
  const { deuId, descripcion } = e.currentTarget.dataset;
  document.getElementById("consumo_deu_id").value          = deuId;
  document.getElementById("consumoDescripcion").textContent = descripcion;
  document.getElementById("consumo_fecha").value            = new Date().toISOString().slice(0, 10);
  document.getElementById("consumo_monto").value            = "";
  document.getElementById("consumo_descripcion").value      = "";
  modalBSConsumo.show();
};

// ── Confirmar consumo ─────────────────────────────────────────
const consumoApi = async () => {
  spanLoaderConsumo.classList.remove("d-none");
  btnConsumo.disabled = true;

  const formConsumo = document.getElementById("formConsumo");
  if (!validarFormulario(formConsumo, [])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoaderConsumo.classList.add("d-none");
    btnConsumo.disabled = false;
    return;
  }

  try {
    const body = new FormData(formConsumo);
    const r    = await fetch(`${RUTA_APP}/API/deudas/consumo`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { modalBSConsumo.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoaderConsumo.classList.add("d-none");
  btnConsumo.disabled = false;
};

// ── Ver historial ─────────────────────────────────────────────
const verHistorial = async (e) => {
  const { deuId, descripcion } = e.currentTarget.dataset;
  document.getElementById("historialTitulo").textContent = descripcion;
  datatableHistorial.clear().draw();
  modalBSHistorial.show();

  try {
    const r    = await fetch(`${RUTA_APP}/API/deudas/movimientos?deu_id=${deuId}`, { headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) datatableHistorial.rows.add(data.datos).draw();
    else Toast.fire({ icon: "info", title: data.mensaje });
  } catch (e) { console.log(e); }
};

// ── Asignar valores al editar ─────────────────────────────────
const asignarValores = async (e) => {
  const d = e.currentTarget.dataset;
  formDeuda.deu_id.value              = d.deuId;
  formDeuda.deu_descripcion.value     = d.descripcion;
  formDeuda.deu_entidad.value         = d.entidad;
  formDeuda.deu_tipo.value            = d.tipo;
  formDeuda.deu_monto_total.value     = d.total;
  formDeuda.deu_monto_pagado.value    = d.pagado;
  formDeuda.deu_cuota_mensual.value   = d.cuota;
  formDeuda.deu_limite_credito.value  = d.limite;
  formDeuda.deu_tasa_interes.value    = d.tasa;
  formDeuda.deu_dia_corte.value       = d.corte;
  formDeuda.deu_dia_pago.value        = d.pagiaDia;
  formDeuda.deu_fecha_inicio.value    = d.inicio;
  formDeuda.deu_fecha_fin_est.value   = d.fin;
  formDeuda.deu_descuento_nomina.checked = d.nomina == "1";

  toggleCamposTipo();
  await cargarCuentas();
  formDeuda.deu_cuenta_id.value = d.cuenta;

  modalTitleId.innerHTML     = '<i class="bi bi-credit-card me-2"></i>Modificar Deuda';
  btnCrear.style.display     = "none";
  btnModificar.style.display = "";
  btnCrear.disabled          = true;
  btnModificar.disabled      = false;
  modalBS.show();
};

// ── btnNuevo ──────────────────────────────────────────────────
btnNuevo.addEventListener("click", async () => {
  formDeuda.reset();
  formDeuda.deu_id.value = "";
  toggleCamposTipo();
  await cargarCuentas();
  modalTitleId.innerHTML     = '<i class="bi bi-credit-card me-2"></i>Nueva Deuda';
  btnCrear.style.display     = "";
  btnModificar.style.display = "none";
  btnCrear.disabled          = false;
  btnModificar.disabled      = true;
  modalBS.show();
});

// ── Eventos formularios ───────────────────────────────────────
formDeuda.addEventListener("submit", guardarApi);
btnModificar.addEventListener("click", modificarApi);
btnPago.addEventListener("click", pagoApi);
btnConsumo.addEventListener("click", consumoApi);

// ── Delegación de eventos en DataTable ───────────────────────
document.querySelector("#datatable").addEventListener("click", (e) => {
  const btn = e.target.closest("button");
  if (!btn) return;
  if (btn.classList.contains("registrar-pago"))    abrirPago(    { currentTarget: btn });
  if (btn.classList.contains("registrar-consumo")) abrirConsumo( { currentTarget: btn });
  if (btn.classList.contains("ver-historial"))     verHistorial( { currentTarget: btn });
  if (btn.classList.contains("editar"))            asignarValores({ currentTarget: btn });
  if (btn.classList.contains("eliminar"))          eliminarApi(  { currentTarget: btn });
});

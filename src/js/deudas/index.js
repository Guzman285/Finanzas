import { Modal } from "bootstrap";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./../lenguaje";
import { Toast, confirmacion, validarFormulario } from "./../funciones";

// ── Elementos del DOM ──────────────────────────────────────
const formDeuda         = document.querySelector("#formDeuda");
const modalEl           = document.querySelector("#modalDeuda");
const modalBS           = new Modal(modalEl);
const modalPagoEl       = document.querySelector("#modalPago");
const modalBSPago       = new Modal(modalPagoEl);
const modalConsumoEl    = document.querySelector("#modalConsumo");
const modalBSConsumo    = new Modal(modalConsumoEl);
const modalAjusteEl     = document.querySelector("#modalAjuste");
const modalBSAjuste     = new Modal(modalAjusteEl);
const modalMovEl        = document.querySelector("#modalMovimientos");
const modalBSMov        = new Modal(modalMovEl);

const spanLoader          = document.getElementById("spanLoader");
const btnCrear            = document.getElementById("btnCrear");
const spanLoaderModificar = document.getElementById("spanLoaderModificar");
const btnModificar        = document.getElementById("btnModificar");
const spanLoaderPago      = document.getElementById("spanLoaderPago");
const btnPago             = document.getElementById("btnPago");
const spanLoaderConsumo   = document.getElementById("spanLoaderConsumo");
const btnConsumo          = document.getElementById("btnConsumo");
const spanLoaderAjuste    = document.getElementById("spanLoaderAjuste");
const btnAjuste           = document.getElementById("btnAjuste");
const btnNuevo            = document.getElementById("btnNuevo");
const modalTitleId        = document.getElementById("modalTitleId");
const selectCuenta        = document.getElementById("deu_cuenta_id");

// Estados iniciales
spanLoader.classList.add("d-none");
spanLoaderModificar.classList.add("d-none");
spanLoaderPago.classList.add("d-none");
spanLoaderConsumo.classList.add("d-none");
spanLoaderAjuste.classList.add("d-none");
btnModificar.style.display = "none";
btnModificar.disabled      = true;

// ── DataTable principal ────────────────────────────────────
let datatable = new DataTable("#datatable", {
  language: lenguaje,
  data: null,
  columns: [
    { title: "No.",   width: "2%", render: (d, t, r, m) => m.row + 1 },
    { title: "DESCRIPCIÓN",  data: "deu_descripcion" },
    { title: "ENTIDAD",      data: "deu_entidad",    defaultContent: "—" },
    { title: "TIPO",         data: "deu_tipo",       width: "7%" },
    {
      title: "SALDO PEND.",
      data: "deu_saldo_pendiente",
      render: (d) => `Q ${parseFloat(d).toFixed(2)}`,
    },
    {
      title: "CUOTA",
      data: "deu_cuota_mensual",
      render: (d) => `Q ${parseFloat(d).toFixed(2)}`,
    },
    { title: "CUENTA",       data: "cuenta_nombre" },
    { title: "FIN EST.",     data: "deu_fecha_fin_est",  defaultContent: "—" },
    {
      title: "Acciones",
      data: "deu_id",
      width: "18%",
      searchable: false,
      render: (data, type, row) => {
        const esTarjeta = row.deu_tipo === "revolving";
        return `
          <div class='text-center d-flex gap-1 justify-content-center'>
            <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
              class="btn btn-success btn-sm rounded-circle pagar"
              title='Registrar pago'
              data-deu-id='${data}'
              data-descripcion='${row.deu_descripcion}'
              data-cuota='${row.deu_cuota_mensual}'>
              <i class='bi bi-cash-coin'></i>
            </button>
            ${esTarjeta ? `
            <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
              class="btn btn-info btn-sm rounded-circle consumo"
              title='Registrar consumo'
              data-deu-id='${data}'
              data-descripcion='${row.deu_descripcion}'>
              <i class='bi bi-cart-plus'></i>
            </button>` : ""}
            <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
              class="btn btn-secondary btn-sm rounded-circle ajustar"
              title='Ajustar saldo'
              data-deu-id='${data}'
              data-descripcion='${row.deu_descripcion}'
              data-saldo='${row.deu_saldo_pendiente}'>
              <i class='bi bi-sliders'></i>
            </button>
            <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
              class="btn btn-dark btn-sm rounded-circle movimientos"
              title='Ver movimientos'
              data-deu-id='${data}'
              data-descripcion='${row.deu_descripcion}'>
              <i class='bi bi-clock-history'></i>
            </button>
            <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
              class="btn btn-warning btn-sm rounded-circle editar"
              title='Modificar'
              data-row='${JSON.stringify(row).replace(/'/g, "&apos;")}'
              data-deu-id='${data}'>
              <i class='bi bi-pencil-fill'></i>
            </button>
            <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
              class="btn btn-danger btn-sm rounded-circle eliminar"
              title='Eliminar'
              data-deu-id='${data}'>
              <i class='bi bi-x-lg'></i>
            </button>
          </div>`;
      },
    },
  ],
});

// ── DataTable movimientos ──────────────────────────────────
let datatableMov = new DataTable("#datatableMovimientos", {
  language: lenguaje,
  data: null,
  columns: [
    { title: "No.",        width: "3%", render: (d, t, r, m) => m.row + 1 },
    { title: "FECHA",      data: "dm_fecha",       width: "10%" },
    { title: "TIPO",       data: "dm_tipo",        width: "8%" },
    { title: "DESCRIPCIÓN", data: "dm_descripcion" },
    { title: "TOTAL",      data: "dm_monto_total",   render: (d) => `Q ${parseFloat(d).toFixed(2)}` },
    { title: "CAPITAL",    data: "dm_abono_capital", render: (d) => `Q ${parseFloat(d).toFixed(2)}` },
    { title: "INTERÉS",    data: "dm_interes",       render: (d) => `Q ${parseFloat(d).toFixed(2)}` },
    { title: "CUENTA",     data: "cuenta_nombre",    defaultContent: "—" },
  ],
});

// ── Cargar cuentas ─────────────────────────────────────────
const cargarCuentas = async () => {
  const r    = await fetch(`${RUTA_APP}/API/cuentas/buscar`, { headers: { "X-Requested-With": "fetch" } });
  const data = await r.json();
  if (data.codigo == 1) {
    selectCuenta.innerHTML = '<option value="">-- Selecciona cuenta --</option>';
    data.datos.forEach((c) => {
      selectCuenta.innerHTML += `<option value="${c.cta_id}">${c.cta_nombre}</option>`;
    });
  }
};

// ── Buscar deudas ──────────────────────────────────────────
const buscarApi = async () => {
  try {
    const r    = await fetch(`${RUTA_APP}/API/deudas/buscar`, { headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    datatable.clear().draw();
    if (data.codigo == 1) {
      datatable.rows.add(data.datos).draw();
    } else {
      Toast.fire({ icon: "info", title: data.mensaje });
    }
  } catch (e) { console.log(e); }
};
buscarApi();

// ── Guardar ────────────────────────────────────────────────
const guardarApi = async (e) => {
  e.preventDefault();
  spanLoader.classList.remove("d-none");
  btnCrear.disabled = true;

  if (!validarFormulario(formDeuda, ["deu_id"])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoader.classList.add("d-none");
    btnCrear.disabled = false;
    return;
  }

  try {
    const body = new FormData(formDeuda);
    body.delete("deu_id");
    if (!document.getElementById("deu_descuento_nomina").checked) body.set("deu_descuento_nomina", 0);
    const r    = await fetch(`${RUTA_APP}/API/deudas/guardar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { formDeuda.reset(); modalBS.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoader.classList.add("d-none");
  btnCrear.disabled = false;
};

// ── Modificar ──────────────────────────────────────────────
const modificarApi = async (e) => {
  e.preventDefault();
  spanLoaderModificar.classList.remove("d-none");
  btnModificar.disabled = true;

  if (!validarFormulario(formDeuda, [])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoaderModificar.classList.add("d-none");
    btnModificar.disabled = false;
    return;
  }

  try {
    const body = new FormData(formDeuda);
    if (!document.getElementById("deu_descuento_nomina").checked) body.set("deu_descuento_nomina", 0);
    const r    = await fetch(`${RUTA_APP}/API/deudas/modificar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { formDeuda.reset(); modalBS.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoaderModificar.classList.add("d-none");
  btnModificar.disabled = false;
};

// ── Eliminar ───────────────────────────────────────────────
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

// ── Pago ───────────────────────────────────────────────────
const abrirPago = (e) => {
  const { deuId, descripcion, cuota } = e.currentTarget.dataset;
  document.getElementById("pago_deu_id").value       = deuId;
  document.getElementById("pagoDescripcion").textContent = descripcion;
  document.getElementById("pago_monto_total").value  = cuota;
  document.getElementById("pago_abono_capital").value = cuota;
  document.getElementById("pago_interes").value      = "0";
  document.getElementById("pago_fecha").value        = new Date().toISOString().slice(0, 10);
  document.getElementById("pago_descripcion").value  = `Pago ${descripcion}`;
  modalBSPago.show();
};

const pagoApi = async (e) => {
  e.preventDefault();
  spanLoaderPago.classList.remove("d-none");
  btnPago.disabled = true;

  try {
    const body = new FormData(document.getElementById("formPago"));
    const r    = await fetch(`${RUTA_APP}/API/deudas/pago`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { modalBSPago.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoaderPago.classList.add("d-none");
  btnPago.disabled = false;
};

// ── Consumo ────────────────────────────────────────────────
const abrirConsumo = (e) => {
  const { deuId, descripcion } = e.currentTarget.dataset;
  document.getElementById("consumo_deu_id").value          = deuId;
  document.getElementById("consumoDescripcion").textContent = descripcion;
  document.getElementById("consumo_fecha").value           = new Date().toISOString().slice(0, 10);
  document.getElementById("consumo_monto").value           = "";
  document.getElementById("consumo_descripcion").value     = "";
  modalBSConsumo.show();
};

const consumoApi = async (e) => {
  e.preventDefault();
  spanLoaderConsumo.classList.remove("d-none");
  btnConsumo.disabled = true;

  try {
    const body = new FormData(document.getElementById("formConsumo"));
    const r    = await fetch(`${RUTA_APP}/API/deudas/consumo`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { modalBSConsumo.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoaderConsumo.classList.add("d-none");
  btnConsumo.disabled = false;
};

// ── Ajuste ─────────────────────────────────────────────────
const abrirAjuste = (e) => {
  const { deuId, descripcion, saldo } = e.currentTarget.dataset;
  document.getElementById("ajuste_deu_id").value          = deuId;
  document.getElementById("ajusteDescripcion").textContent = descripcion;
  document.getElementById("ajuste_monto").value           = saldo;
  document.getElementById("ajuste_fecha").value           = new Date().toISOString().slice(0, 10);
  document.getElementById("ajuste_descripcion").value     = "";
  modalBSAjuste.show();
};

const ajusteApi = async (e) => {
  e.preventDefault();
  spanLoaderAjuste.classList.remove("d-none");
  btnAjuste.disabled = true;

  try {
    const body = new FormData(document.getElementById("formAjuste"));
    const r    = await fetch(`${RUTA_APP}/API/deudas/ajustar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { modalBSAjuste.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoaderAjuste.classList.add("d-none");
  btnAjuste.disabled = false;
};

// ── Movimientos ────────────────────────────────────────────
const abrirMovimientos = async (e) => {
  const { deuId, descripcion } = e.currentTarget.dataset;
  document.getElementById("movDeudaNombre").textContent = descripcion;
  datatableMov.clear().draw();
  modalBSMov.show();

  try {
    const r    = await fetch(`${RUTA_APP}/API/deudas/movimientos?deu_id=${deuId}`, { headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) datatableMov.rows.add(data.datos).draw();
    else Toast.fire({ icon: "info", title: data.mensaje });
  } catch (e) { console.log(e); }
};

// ── Asignar valores al editar ──────────────────────────────
const asignarValores = async (e) => {
  const row = JSON.parse(e.currentTarget.dataset.row.replace(/&apos;/g, "'"));

  formDeuda.deu_id.value              = row.deu_id;
  formDeuda.deu_descripcion.value     = row.deu_descripcion;
  formDeuda.deu_entidad.value         = row.deu_entidad ?? "";
  formDeuda.deu_tipo.value            = row.deu_tipo;
  formDeuda.deu_monto_total.value     = row.deu_monto_total;
  formDeuda.deu_cuota_mensual.value   = row.deu_cuota_mensual;
  formDeuda.deu_limite_credito.value  = row.deu_limite_credito ?? "";
  formDeuda.deu_tasa_interes.value    = row.deu_tasa_interes;
  formDeuda.deu_dia_corte.value       = row.deu_dia_corte ?? "";
  formDeuda.deu_dia_pago.value        = row.deu_dia_pago ?? "";
  formDeuda.deu_fecha_inicio.value    = row.deu_fecha_inicio;
  formDeuda.deu_fecha_fin_est.value   = row.deu_fecha_fin_est ?? "";
  document.getElementById("deu_descuento_nomina").checked = row.deu_descuento_nomina == 1;

  await cargarCuentas();
  formDeuda.deu_cuenta_id.value = row.deu_cuenta_id;

  modalTitleId.textContent   = "Modificar Deuda";
  btnCrear.style.display     = "none";
  btnModificar.style.display = "";
  btnCrear.disabled          = true;
  btnModificar.disabled      = false;
  spanLoader.classList.add("d-none");
  spanLoaderModificar.classList.add("d-none");

  modalEl.removeEventListener("show.bs.modal", resetearModal);
  modalBS.show();
  modalEl.addEventListener("show.bs.modal", resetearModal);
};

// ── Resetear modal ─────────────────────────────────────────
const resetearModal = () => {
  modalTitleId.textContent   = "Nueva Deuda";
  btnCrear.style.display     = "";
  btnModificar.style.display = "none";
  btnCrear.disabled          = false;
  btnModificar.disabled      = true;
  spanLoader.classList.add("d-none");
  spanLoaderModificar.classList.add("d-none");
  formDeuda.reset();
};

// ── Eventos ────────────────────────────────────────────────
formDeuda.addEventListener("submit",  guardarApi);
btnModificar.addEventListener("click", modificarApi);

document.getElementById("formPago").addEventListener("submit",    pagoApi);
document.getElementById("formConsumo").addEventListener("submit", consumoApi);
document.getElementById("formAjuste").addEventListener("submit",  ajusteApi);

btnNuevo.addEventListener("click", async () => {
  resetearModal();
  await cargarCuentas();
  modalBS.show();
});

modalEl.addEventListener("show.bs.modal", resetearModal);

datatable.on("click", ".editar",      asignarValores);
datatable.on("click", ".eliminar",    eliminarApi);
datatable.on("click", ".pagar",       abrirPago);
datatable.on("click", ".consumo",     abrirConsumo);
datatable.on("click", ".ajustar",     abrirAjuste);
datatable.on("click", ".movimientos", abrirMovimientos);

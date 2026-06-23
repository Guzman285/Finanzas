import { Modal } from "bootstrap";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./../lenguaje";
import { Toast, confirmacion, validarFormulario } from "./../funciones";

const formDeuda           = document.querySelector("#formDeuda");
const modalEl             = document.querySelector("#modalDeuda");
const modalBS             = new Modal(modalEl);
const modalAbonarEl       = document.querySelector("#modalAbonar");
const modalBSAbonar       = new Modal(modalAbonarEl);
const spanLoader          = document.getElementById("spanLoader");
const btnCrear            = document.getElementById("btnCrear");
const spanLoaderModificar = document.getElementById("spanLoaderModificar");
const btnModificar        = document.getElementById("btnModificar");
const spanLoaderAbonar    = document.getElementById("spanLoaderAbonar");
const btnAbonar           = document.getElementById("btnAbonar");
const modalTitleId        = document.getElementById("modalTitleId");
const btnNuevo            = document.getElementById("btnNuevo");
const selectCuenta        = document.getElementById("deu_cuenta_id");

spanLoader.classList.add("d-none");
spanLoaderModificar.classList.add("d-none");
spanLoaderAbonar.classList.add("d-none");
btnModificar.style.display = "none";
btnModificar.disabled = true;

// ── DataTable ────────────────────────────────────────────────
let datatable = new DataTable("#datatable", {
  language: lenguaje,
  data: null,
  columns: [
    { title: "No.", width: "2%", render: (d, t, r, m) => m.row + 1 },
    { title: "DESCRIPCIÓN", data: "deu_descripcion" },
    {
      title: "TOTAL",
      data: "deu_monto_total",
      render: (d) => `Q ${parseFloat(d).toFixed(2)}`,
    },
    {
      title: "PAGADO",
      data: "deu_monto_pagado",
      render: (d) => `Q ${parseFloat(d).toFixed(2)}`,
    },
    {
      title: "PENDIENTE",
      data: "deu_saldo_pendiente",
      render: (d) => {
        const v = parseFloat(d);
        const cls = v > 0 ? "text-danger fw-bold" : "text-success fw-bold";
        return `<span class="${cls}">Q ${v.toFixed(2)}</span>`;
      },
    },
    {
      title: "CUOTA",
      data: "deu_cuota_mensual",
      render: (d) => `Q ${parseFloat(d).toFixed(2)}`,
    },
    { title: "CUENTA", data: "cuenta_nombre" },
    {
      title: "PROGRESO",
      data: "deu_monto_total",
      searchable: false,
      render: (d, t, row) => {
        const pct = row.deu_monto_total > 0
          ? Math.min(100, Math.round((row.deu_monto_pagado / row.deu_monto_total) * 100))
          : 0;
        const cls = pct >= 100 ? "bg-success" : pct >= 50 ? "bg-warning" : "bg-danger";
        return `<div class="progress" style="height:14px;min-width:80px">
          <div class="progress-bar ${cls}" style="width:${pct}%">${pct}%</div>
        </div>`;
      },
    },
    {
      title: "Acciones",
      data: "deu_id",
      width: "10%",
      searchable: false,
      render: (data, type, row) => `
        <div class='text-center'>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-success btn-sm rounded-circle abonar"
            title='Registrar abono'
            data-deu-id='${data}'
            data-descripcion='${row.deu_descripcion}'
            data-cuota='${row.deu_cuota_mensual}'
            data-pendiente='${row.deu_saldo_pendiente}'>
            <i class='bi bi-cash-coin'></i>
          </button>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-warning btn-sm rounded-circle editar"
            title='Modificar'
            data-deu-id='${data}'
            data-descripcion='${row.deu_descripcion}'
            data-total='${row.deu_monto_total}'
            data-pagado='${row.deu_monto_pagado}'
            data-cuota='${row.deu_cuota_mensual}'
            data-inicio='${row.deu_fecha_inicio}'
            data-fin='${row.deu_fecha_fin_est ?? ""}'
            data-cuenta='${row.deu_cuenta_id}'>
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

// ── Cargar cuentas ────────────────────────────────────────────
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

// ── Buscar ────────────────────────────────────────────────────
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

  if (!validarFormulario(formDeuda, ["deu_id", "deu_monto_pagado", "deu_fecha_fin_est"])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoader.classList.add("d-none");
    btnCrear.disabled = false;
    return;
  }

  try {
    const body = new FormData(formDeuda);
    body.delete("deu_id");
    const r    = await fetch(`${RUTA_APP}/API/deudas/guardar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { formDeuda.reset(); modalBS.hide(); buscarApi(); }
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

  if (!validarFormulario(formDeuda, ["deu_fecha_fin_est"])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoaderModificar.classList.add("d-none");
    btnModificar.disabled = false;
    return;
  }

  try {
    const body = new FormData(formDeuda);
    const r    = await fetch(`${RUTA_APP}/API/deudas/modificar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { formDeuda.reset(); modalBS.hide(); buscarApi(); }
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

// ── Abonar ────────────────────────────────────────────────────
const abrirAbonar = (e) => {
  const { deuId, descripcion, cuota, pendiente } = e.currentTarget.dataset;
  document.getElementById("abonar_deu_id").value        = deuId;
  document.getElementById("abonarDescripcion").textContent = descripcion;
  document.getElementById("abonarPendiente").textContent  = `Q ${parseFloat(pendiente).toFixed(2)}`;
  document.getElementById("abonar_monto").value          = cuota;
  document.getElementById("abonar_fecha").value          = new Date().toISOString().slice(0, 10);
  modalBSAbonar.show();
};

const abonarApi = async () => {
  spanLoaderAbonar.classList.remove("d-none");
  btnAbonar.disabled = true;

  try {
    const body = new FormData(document.getElementById("formAbonar"));
    const r    = await fetch(`${RUTA_APP}/API/deudas/abonar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { modalBSAbonar.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoaderAbonar.classList.add("d-none");
  btnAbonar.disabled = false;
};

// ── Asignar valores al editar ─────────────────────────────────
const asignarValores = async (e) => {
  const { deuId, descripcion, total, pagado, cuota, inicio, fin, cuenta } = e.currentTarget.dataset;
  formDeuda.deu_id.value            = deuId;
  formDeuda.deu_descripcion.value   = descripcion;
  formDeuda.deu_monto_total.value   = total;
  formDeuda.deu_monto_pagado.value  = pagado;
  formDeuda.deu_cuota_mensual.value = cuota;
  formDeuda.deu_fecha_inicio.value  = inicio;
  formDeuda.deu_fecha_fin_est.value = fin;

  await cargarCuentas();
  formDeuda.deu_cuenta_id.value = cuenta;

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

// ── Resetear modal ────────────────────────────────────────────
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

// ── Eventos ───────────────────────────────────────────────────
formDeuda.addEventListener("submit", guardarApi);
btnNuevo.addEventListener("click", async () => {
  resetearModal();
  await cargarCuentas();
  modalBS.show();
});
btnModificar.addEventListener("click", modificarApi);
btnAbonar.addEventListener("click", abonarApi);
modalEl.addEventListener("show.bs.modal", resetearModal);
datatable.on("click", ".editar",   asignarValores);
datatable.on("click", ".eliminar", eliminarApi);
datatable.on("click", ".abonar",   abrirAbonar);

import { Modal } from "bootstrap";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./../lenguaje";
import { Toast, confirmacion, validarFormulario } from "./../funciones";

const formGF              = document.querySelector("#formGastoFijo");
const modalEl             = document.querySelector("#modalGastoFijo");
const modalBS             = new Modal(modalEl);
const modalPagarEl        = document.querySelector("#modalPagar");
const modalBSPagar        = new Modal(modalPagarEl);
const spanLoader          = document.getElementById("spanLoader");
const btnCrear            = document.getElementById("btnCrear");
const spanLoaderModificar = document.getElementById("spanLoaderModificar");
const btnModificar        = document.getElementById("btnModificar");
const spanLoaderPagar     = document.getElementById("spanLoaderPagar");
const btnPagar            = document.getElementById("btnPagar");
const modalTitleId        = document.getElementById("modalTitleId");
const btnNuevo            = document.getElementById("btnNuevo");
const selectCategoria     = document.getElementById("gf_categoria_id");
const selectPagarCuenta   = document.getElementById("pagar_cuenta_id");
const infoCuentaDiv       = document.getElementById("infoCuentaSeleccionada");

spanLoader.classList.add("d-none");
spanLoaderModificar.classList.add("d-none");
spanLoaderPagar.classList.add("d-none");
btnModificar.style.display = "none";
btnModificar.disabled = true;

// Almacena los datos de cuentas para mostrar info al seleccionar
let _cuentasPago = [];

// ── DataTable ──────────────────────────────────────────────
let datatable = new DataTable("#datatable", {
  language: lenguaje,
  data: null,
  columns: [
    { title: "No.", width: "2%", render: (d, t, r, m) => m.row + 1 },
    { title: "DESCRIPCIÓN", data: "gf_descripcion" },
    {
      title: "MONTO EST.",
      data: "gf_monto_estimado",
      render: (d) => `Q ${parseFloat(d).toFixed(2)}`,
    },
    { title: "DÍA PAGO", data: "gf_dia_pago", width: "6%" },
    { title: "CATEGORÍA", data: "categoria_nombre" },
    {
      title: "Acciones",
      data: "gf_id",
      width: "12%",
      searchable: false,
      render: (data, type, row) => `
        <div class='text-center'>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-success btn-sm rounded-circle pagar"
            title='Registrar pago'
            data-gf-id='${data}'
            data-descripcion='${row.gf_descripcion}'
            data-monto='${row.gf_monto_estimado}'>
            <i class='bi bi-cash-coin'></i>
          </button>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-warning btn-sm rounded-circle editar"
            title='Modificar'
            data-gf-id='${data}'
            data-descripcion='${row.gf_descripcion}'
            data-monto='${row.gf_monto_estimado}'
            data-dia='${row.gf_dia_pago}'
            data-categoria='${row.gf_categoria_id}'>
            <i class='bi bi-pencil-fill'></i>
          </button>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-danger btn-sm rounded-circle eliminar"
            data-gf-id='${data}'
            title='Eliminar'>
            <i class='bi bi-x-lg'></i>
          </button>
        </div>`,
    },
  ],
});

// ── Cargar categorías ──────────────────────────────────────
const cargarCategorias = async () => {
  const r    = await fetch(`${RUTA_APP}/API/categorias/buscar`, { headers: { "X-Requested-With": "fetch" } });
  const data = await r.json();
  if (data.codigo == 1) {
    selectCategoria.innerHTML = '<option value="">-- Selecciona categoría --</option>';
    data.datos
      .filter((c) => c.cat_tipo === "gasto")
      .forEach((c) => {
        selectCategoria.innerHTML += `<option value="${c.cat_id}">${c.cat_nombre}</option>`;
      });
  }
};

// ── Buscar ─────────────────────────────────────────────────
const buscarApi = async () => {
  try {
    const r    = await fetch(`${RUTA_APP}/API/gastos_fijos/buscar`, { headers: { "X-Requested-With": "fetch" } });
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

  if (!validarFormulario(formGF, ["gf_id"])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoader.classList.add("d-none");
    btnCrear.disabled = false;
    return;
  }

  try {
    const body = new FormData(formGF);
    body.delete("gf_id");
    const r    = await fetch(`${RUTA_APP}/API/gastos_fijos/guardar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { formGF.reset(); modalBS.hide(); buscarApi(); }
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

  if (!validarFormulario(formGF, ["gf_id"])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoaderModificar.classList.add("d-none");
    btnModificar.disabled = false;
    return;
  }

  try {
    const body = new FormData(formGF);
    const r    = await fetch(`${RUTA_APP}/API/gastos_fijos/modificar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { formGF.reset(); modalBS.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoaderModificar.classList.add("d-none");
  btnModificar.disabled = false;
};

// ── Eliminar ───────────────────────────────────────────────
const eliminarApi = async (e) => {
  const { gfId } = e.currentTarget.dataset;
  const ok = await confirmacion("¿Eliminar este gasto fijo?", "warning", "Sí, eliminar");
  if (!ok) return;

  try {
    const body = new FormData();
    body.append("gf_id", gfId);
    const r    = await fetch(`${RUTA_APP}/API/gastos_fijos/eliminar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) buscarApi();
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }
};

// ── Cargar cuentas para pago (muestra disponible real) ─────
const cargarCuentasPago = async () => {
  try {
    const r    = await fetch(`${RUTA_APP}/API/cuentas/buscar`, { headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) {
      _cuentasPago = data.datos;
      selectPagarCuenta.innerHTML = '<option value="">-- Selecciona cuenta --</option>';
      data.datos.forEach((c) => {
        const saldo   = parseFloat(c.cta_saldo);
        const limite  = parseFloat(c.cta_limite_credito ?? 0);
        let disponible, label;

        if (c.cta_tipo === "tarjeta_credito") {
          disponible = limite - saldo;
          label = `${c.cta_nombre} (TC — disponible: Q ${disponible.toFixed(2)})`;
        } else {
          disponible = saldo;
          label = `${c.cta_nombre} (Q ${disponible.toFixed(2)})`;
        }

        // Solo mostrar si tiene fondos/crédito disponible
        if (disponible > 0) {
          selectPagarCuenta.innerHTML += `<option value="${c.cta_id}">${label}</option>`;
        }
      });
    }
  } catch (e) { console.log(e); }
};

// Muestra info de la cuenta seleccionada debajo del select
const mostrarInfoCuenta = () => {
  const id     = selectPagarCuenta.value;
  const cuenta = _cuentasPago.find((c) => c.cta_id == id);
  if (!cuenta || !infoCuentaDiv) return;

  if (cuenta.cta_tipo === "tarjeta_credito") {
    const limite     = parseFloat(cuenta.cta_limite_credito ?? 0);
    const usado      = parseFloat(cuenta.cta_saldo);
    const disponible = limite - usado;
    infoCuentaDiv.innerHTML = `
      <span class="text-warning"><i class="bi bi-credit-card me-1"></i>Tarjeta de crédito</span> &mdash;
      Límite: <strong>Q ${limite.toFixed(2)}</strong> |
      Usado: <strong>Q ${usado.toFixed(2)}</strong> |
      Disponible: <strong class="text-success">Q ${disponible.toFixed(2)}</strong>`;
  } else {
    infoCuentaDiv.innerHTML = `
      <span class="text-primary"><i class="bi bi-bank me-1"></i>Saldo disponible:</span>
      <strong>Q ${parseFloat(cuenta.cta_saldo).toFixed(2)}</strong>`;
  }
};

if (selectPagarCuenta) {
  selectPagarCuenta.addEventListener("change", mostrarInfoCuenta);
}

// ── Abrir modal pagar ──────────────────────────────────────
const abrirPagar = async (e) => {
  const { gfId, descripcion, monto } = e.currentTarget.dataset;
  document.getElementById("pagar_gf_id").value           = gfId;
  document.getElementById("pagarDescripcion").textContent = descripcion;
  document.getElementById("pagar_monto").value            = monto;
  document.getElementById("pagar_fecha").value            = new Date().toISOString().slice(0, 10);
  if (infoCuentaDiv) infoCuentaDiv.innerHTML = "";

  await cargarCuentasPago();
  modalBSPagar.show();
};

// ── Confirmar pago ─────────────────────────────────────────
const pagarApi = async () => {
  spanLoaderPagar.classList.remove("d-none");
  btnPagar.disabled = true;

  if (!selectPagarCuenta.value) {
    Toast.fire({ icon: "warning", title: "Debe seleccionar una cuenta para pagar" });
    spanLoaderPagar.classList.add("d-none");
    btnPagar.disabled = false;
    return;
  }

  const body = new FormData(document.getElementById("formPagar"));
  try {
    const r    = await fetch(`${RUTA_APP}/API/gastos_fijos/pagar`, { method: "POST", body, headers: { "X-Requested-With": "fetch" } });
    const data = await r.json();
    if (data.codigo == 1) { modalBSPagar.hide(); buscarApi(); }
    else console.log(data.detalle);
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) { console.log(e); }

  spanLoaderPagar.classList.add("d-none");
  btnPagar.disabled = false;
};

// ── Asignar valores al editar ──────────────────────────────
const asignarValores = async (e) => {
  const { gfId, descripcion, monto, dia, categoria } = e.currentTarget.dataset;
  formGF.gf_id.value             = gfId;
  formGF.gf_descripcion.value    = descripcion;
  formGF.gf_monto_estimado.value = monto;
  formGF.gf_dia_pago.value       = dia;

  await cargarCategorias();
  formGF.gf_categoria_id.value = categoria;

  modalTitleId.textContent   = "Modificar Gasto Fijo";
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
  modalTitleId.textContent   = "Nuevo Gasto Fijo";
  btnCrear.style.display     = "";
  btnModificar.style.display = "none";
  btnCrear.disabled          = false;
  btnModificar.disabled      = true;
  spanLoader.classList.add("d-none");
  spanLoaderModificar.classList.add("d-none");
  formGF.reset();
};

// ── Eventos ────────────────────────────────────────────────
formGF.addEventListener("submit", guardarApi);
btnNuevo.addEventListener("click", async () => {
  resetearModal();
  await cargarCategorias();
  modalBS.show();
});
btnModificar.addEventListener("click", modificarApi);
btnPagar.addEventListener("click", pagarApi);
modalEl.addEventListener("show.bs.modal", resetearModal);
datatable.on("click", ".editar",   asignarValores);
datatable.on("click", ".eliminar", eliminarApi);
datatable.on("click", ".pagar",    abrirPagar);

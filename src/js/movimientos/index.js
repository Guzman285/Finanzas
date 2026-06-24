import { Modal } from "bootstrap";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./../lenguaje";
import { Toast, confirmacion, validarFormulario } from "./../funciones";

// ── Elementos ──────────────────────────────────────────────────────────────
const formMovimiento   = document.querySelector("#formMovimiento");
const modalElement     = document.querySelector("#modalMovimiento");
const modalBS          = new Modal(modalElement);
const spanLoader       = document.getElementById("spanLoader");
const btnCrear         = document.getElementById("btnCrear");
const btnNuevo         = document.getElementById("btnNuevo");
const btnFiltrar       = document.getElementById("btnFiltrar");
const btnLimpiar       = document.getElementById("btnLimpiar");
const selTipo          = document.getElementById("mov_tipo");
const selCuentaOrigen  = document.getElementById("mov_cuenta_origen_id");
const selCuentaDestino = document.getElementById("mov_cuenta_destino_id");
const selCategoria     = document.getElementById("mov_categoria_id");
const wrapDestino      = document.getElementById("wrapCuentaDestino");
const wrapCategoria    = document.getElementById("wrapCategoria");
const filtroDesde      = document.getElementById("filtroDesde");
const filtroHasta      = document.getElementById("filtroHasta");
const filtroTipo       = document.getElementById("filtroTipo");
const filtroCuenta     = document.getElementById("filtroCuenta");
const totalIngresos     = document.getElementById("totalIngresos");
const totalGastos       = document.getElementById("totalGastos");
const totalTransf       = document.getElementById("totalTransferencias");

spanLoader.classList.add("d-none");

// ── DataTable ──────────────────────────────────────────────────────────────
let datatable = new DataTable("#datatable", {
  language: lenguaje,
  data: null,
  order: [[3, "desc"]],
  columns: [
    {
      title: "No.",
      width: "2%",
      render: (_, __, ___, meta) => meta.row + 1,
    },
    {
      title: "TIPO",
      data: "mov_tipo",
      width: "8%",
      render: (data) => {
        const cfg = {
          ingreso:      { cls: "success",  icon: "bi-arrow-down-circle-fill",  label: "Ingreso" },
          gasto:        { cls: "danger",   icon: "bi-arrow-up-circle-fill",    label: "Gasto" },
          transferencia:{ cls: "primary",  icon: "bi-arrow-left-right",        label: "Transf." },
        };
        const c = cfg[data] || { cls: "secondary", icon: "bi-question", label: data };
        return `<span class="badge bg-${c.cls}"><i class="bi ${c.icon} me-1"></i>${c.label}</span>`;
      },
    },
    { title: "DESCRIPCION", data: "mov_descripcion" },
    { title: "FECHA",       data: "mov_fecha",       width: "9%" },
    {
      title: "MONTO",
      data: "mov_monto",
      width: "10%",
      className: "text-end",
      render: (data, type, row) => {
        const cls = row.mov_tipo === "ingreso" ? "text-success fw-bold" : row.mov_tipo === "gasto" ? "text-danger fw-bold" : "";
        return `<span class="${cls}">Q ${parseFloat(data).toLocaleString("es-GT", { minimumFractionDigits: 2 })}</span>`;
      },
    },
    { title: "CUENTA ORIGEN",  data: "cuenta_origen",  defaultContent: "--" },
    {
      title: "CUENTA DESTINO",
      data: "cuenta_destino",
      defaultContent: "--",
      render: (data) => data ?? "--",
    },
    { title: "CATEGORIA", data: "categoria", defaultContent: "--", render: (d) => d ?? "--" },
    {
      title: "Acciones",
      data: "mov_id",
      width: "7%",
      searchable: false,
      render: (data, type, row) =>
        `<div class='text-center'>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-danger btn-sm rounded-circle eliminar"
            data-codigo='${data}'
            data-tipo='${row.mov_tipo}'
            data-monto='${row.mov_monto}'
            title='Eliminar y revertir saldo'>
            <i class='bi bi-trash-fill'></i>
          </button>
        </div>`,
    },
  ],
});

// ── Catalogos ──────────────────────────────────────────────────────────────
let cuentas = [];
let categorias = [];

const cargarCatalogos = async () => {
  try {
    const res  = await fetch(`${RUTA_APP}/API/movimientos/catalogos`, {
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await res.json();
    if (data.codigo !== 1) return;

    cuentas    = data.cuentas    || [];
    categorias = data.categorias || [];

    const optCuenta = cuentas
      .map((c) => `<option value="${c.cta_id}">${c.cta_nombre} (Q ${parseFloat(c.cta_saldo).toLocaleString("es-GT", { minimumFractionDigits: 2 })})</option>`)
      .join("");

    selCuentaOrigen.innerHTML  = `<option value="">-- Selecciona cuenta --</option>${optCuenta}`;
    selCuentaDestino.innerHTML = `<option value="">-- Selecciona cuenta --</option>${optCuenta}`;
    filtroCuenta.innerHTML     = `<option value="">Todas</option>${cuentas.map((c) => `<option value="${c.cta_id}">${c.cta_nombre}</option>`).join("")}`;

    poblarCategorias("");
  } catch (e) {
    console.error(e);
  }
};

const poblarCategorias = (tipo) => {
  const filtradas = tipo === "transferencia"
    ? categorias.filter((c) => c.cat_tipo === "neutro")
    : tipo === "ingreso"
    ? categorias.filter((c) => c.cat_tipo === "ingreso" || c.cat_tipo === "neutro")
    : tipo === "gasto"
    ? categorias.filter((c) => c.cat_tipo === "gasto" || c.cat_tipo === "neutro")
    : categorias;

  selCategoria.innerHTML =
    `<option value="">-- Sin categoria --</option>` +
    filtradas.map((c) => `<option value="${c.cat_id}">${c.cat_nombre}</option>`).join("");
};

// ── Buscar ─────────────────────────────────────────────────────────────────
const buscarApi = async () => {
  try {
    const params = new URLSearchParams();
    if (filtroDesde.value)  params.set("desde",  filtroDesde.value);
    if (filtroHasta.value)  params.set("hasta",  filtroHasta.value);
    if (filtroTipo.value)   params.set("tipo",   filtroTipo.value);
    if (filtroCuenta.value) params.set("cuenta", filtroCuenta.value);

    const res  = await fetch(`${RUTA_APP}/API/movimientos/buscar?${params}`, {
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await res.json();

    datatable.clear().draw();

    if (data.codigo === 1) {
      datatable.rows.add(data.datos).draw();
      calcularTotales(data.datos);
    } else {
      Toast.fire({ icon: "info", title: data.mensaje });
      calcularTotales([]);
    }
  } catch (e) {
    console.error(e);
  }
};

const calcularTotales = (datos) => {
  let ing = 0, gas = 0, tra = 0;
  datos.forEach((r) => {
    const m = parseFloat(r.mov_monto) || 0;
    if (r.mov_tipo === "ingreso")       ing += m;
    else if (r.mov_tipo === "gasto")    gas += m;
    else if (r.mov_tipo === "transferencia") tra += m;
  });
  const fmt = (v) => `Q ${v.toLocaleString("es-GT", { minimumFractionDigits: 2 })}`;
  totalIngresos.textContent      = `Ingresos: ${fmt(ing)}`;
  totalGastos.textContent        = `Gastos: ${fmt(gas)}`;
  totalTransf.textContent        = `Transferencias: ${fmt(tra)}`;
};

// ── Guardar ────────────────────────────────────────────────────────────────
const guardarApi = async (e) => {
  e.preventDefault();
  spanLoader.classList.remove("d-none");
  btnCrear.disabled = true;

  const excluir = ["mov_cuenta_destino_id", "mov_categoria_id"];
  if (!validarFormulario(formMovimiento, excluir)) {
    Toast.fire({ icon: "warning", title: "Revise la informacion ingresada" });
    spanLoader.classList.add("d-none");
    btnCrear.disabled = false;
    return;
  }

  try {
    const body = new FormData(formMovimiento);
    const res  = await fetch(`${RUTA_APP}/API/movimientos/guardar`, {
      method: "POST",
      body,
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await res.json();

    if (data.codigo == 1) {
      formMovimiento.reset();
      modalBS.hide();
      buscarApi();
    } else {
      console.log(data.detalle);
    }
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) {
    console.error(e);
  }

  spanLoader.classList.add("d-none");
  btnCrear.disabled = false;
};

// ── Eliminar ───────────────────────────────────────────────────────────────
const eliminarApi = async (e) => {
  const { codigo, tipo, monto } = e.currentTarget.dataset;
  const confirm = await confirmacion(
    `Eliminar este movimiento revertira el saldo de la cuenta (Q ${parseFloat(monto).toLocaleString("es-GT", { minimumFractionDigits: 2 })}).\n¿Desea continuar?`,
    "warning",
    "Si, eliminar"
  );
  if (!confirm) return;

  try {
    const body = new FormData();
    body.append("mov_id", codigo);
    const res  = await fetch(`${RUTA_APP}/API/movimientos/eliminar`, {
      method: "POST",
      body,
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await res.json();
    if (data.codigo == 1) buscarApi();
    Toast.fire({ icon: data.codigo == 1 ? "success" : "error", title: data.mensaje });
  } catch (e) {
    console.error(e);
  }
};

// ── Cambio de tipo: mostrar/ocultar campos ─────────────────────────────────
selTipo.addEventListener("change", () => {
  const tipo = selTipo.value;
  if (tipo === "transferencia") {
    wrapDestino.style.display  = "";
    wrapCategoria.style.display = "none";
  } else {
    wrapDestino.style.display  = "none";
    wrapCategoria.style.display = "";
  }
  poblarCategorias(tipo);
});

// ── Resetear modal ─────────────────────────────────────────────────────────
const resetearModal = () => {
  formMovimiento.reset();
  wrapDestino.style.display   = "none";
  wrapCategoria.style.display = "";
  spanLoader.classList.add("d-none");
  btnCrear.disabled = false;
  poblarCategorias("");
};

// ── Eventos ────────────────────────────────────────────────────────────────
btnNuevo.addEventListener("click",   () => { resetearModal(); modalBS.show(); });
btnFiltrar.addEventListener("click", buscarApi);
btnLimpiar.addEventListener("click", () => {
  filtroDesde.value = "";
  filtroHasta.value = "";
  filtroTipo.value  = "";
  filtroCuenta.value = "";
  buscarApi();
});
formMovimiento.addEventListener("submit", guardarApi);
modalElement.addEventListener("show.bs.modal", resetearModal);
datatable.on("click", ".eliminar", eliminarApi);

// ── Inicializar ────────────────────────────────────────────────────────────
cargarCatalogos();
buscarApi();

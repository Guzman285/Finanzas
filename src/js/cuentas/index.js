import { Modal } from "bootstrap";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./../lenguaje";
import { Toast, confirmacion, validarFormulario } from "./../funciones";

const formCuenta          = document.querySelector("#formCuenta");
const modalElement        = document.querySelector("#modalCuenta");
const modalBSCuenta       = new Modal(modalElement);
const spanLoader          = document.getElementById("spanLoader");
const btnCrear            = document.getElementById("btnCrear");
const spanLoaderModificar = document.getElementById("spanLoaderModificar");
const btnModificar        = document.getElementById("btnModificar");
const modalTitleId        = document.getElementById("modalTitleId");
const btnNuevo            = document.getElementById("btnNuevo");
const selectTipo          = document.getElementById("cta_tipo");
const divBanco            = document.getElementById("divBanco");
const selectBanco         = document.getElementById("cta_banco_id");
const divNumero           = document.getElementById("divNumero");
const lblCtaSaldo         = document.getElementById("lbl_cta_saldo");

spanLoader.classList.add("d-none");
spanLoaderModificar.classList.add("d-none");
btnModificar.style.display = "none";
btnModificar.disabled = true;

// ── DataTable ──────────────────────────────────────────────
let datatableCuentas = new DataTable("#datatableCuentas", {
  language: lenguaje,
  data: null,
  columns: [
    {
      title: "No.",
      width: "2%",
      render: (data, type, row, meta) => meta.row + 1,
    },
    { title: "CUENTA", data: "cta_nombre" },
    {
      title: "TIPO",
      data: "cta_tipo",
      render: (data) => {
        const tipos = {
          monetaria:       '<span class="badge bg-primary">Monetaria</span>',
          ahorro:          '<span class="badge bg-success">Ahorro</span>',
          efectivo:        '<span class="badge bg-secondary">Efectivo</span>',
          tarjeta_debito:  '<span class="badge bg-info text-dark">T. Débito</span>',
          tarjeta_credito: '<span class="badge bg-warning text-dark">T. Crédito</span>',
        };
        return tipos[data] ?? data;
      },
    },
    {
      title: "BANCO",
      data: "banco_nombre",
      render: (data) => data ?? "—",
    },
    {
      title: "NO. CUENTA",
      data: "cta_numero",
      render: (data) => data ?? "—",
    },
    {
      title: "SALDO",
      data: "cta_saldo",
      render: (data) => `Q ${parseFloat(data).toFixed(2)}`,
    },
    {
      title: "Acciones",
      data: "cta_id",
      width: "10%",
      searchable: false,
      render: (data, type, row) => `
        <div class='text-center'>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-warning btn-sm rounded-circle editar"
            title='Modificar'
            data-codigo='${data}'
            data-nombre='${row.cta_nombre}'
            data-tipo='${row.cta_tipo}'
            data-saldo='${row.cta_saldo}'
            data-numero='${row.cta_numero ?? ""}'
            data-banco='${row.cta_banco_id ?? ""}'>
            <i class='bi bi-pencil-fill'></i>
          </button>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-danger btn-sm rounded-circle eliminar"
            data-codigo='${data}'
            title='Eliminar'>
            <i class='bi bi-x-lg'></i>
          </button>
        </div>`,
    },
  ],
});

// ── Mostrar/ocultar campos según tipo ──────────────────────
const actualizarCamposTipo = async (tipo, bancoId = "") => {
  if (tipo !== "" && tipo !== "efectivo") {
    divBanco.classList.remove("d-none");
    divNumero.classList.remove("d-none");
    await cargarBancos();
    selectBanco.value = bancoId;
  } else {
    divBanco.classList.add("d-none");
    divNumero.classList.add("d-none");
    selectBanco.value = "";
  }

  if (lblCtaSaldo) {
    if (tipo === "tarjeta_credito") {
      lblCtaSaldo.innerHTML = 'Límite de crédito <span class="text-danger">*</span>';
    } else {
      lblCtaSaldo.innerHTML = 'Saldo inicial <span class="text-danger">*</span>';
    }
  }
};

selectTipo.addEventListener("change", () => actualizarCamposTipo(selectTipo.value));

// ── Cargar bancos en el select ─────────────────────────────
const cargarBancos = async () => {
  try {
    const respuesta = await fetch(`${RUTA_APP}/API/bancos/buscar`, {
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    if (data.codigo == 1) {
      selectBanco.innerHTML = '<option value="">-- Selecciona banco --</option>';
      data.datos.forEach((b) => {
        selectBanco.innerHTML += `<option value="${b.ban_id}">${b.ban_nombre}</option>`;
      });
    }
  } catch (error) {
    console.log(error);
  }
};

// ── Buscar ─────────────────────────────────────────────────
const buscarApi = async () => {
  try {
    const respuesta = await fetch(`${RUTA_APP}/API/cuentas/buscar`, {
      method: "GET",
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { datos, mensaje, codigo } = data;
    datatableCuentas.clear().draw();
    if (codigo == 1) {
      datatableCuentas.rows.add(datos).draw();
    } else {
      Toast.fire({ icon: "info", title: mensaje });
    }
  } catch (error) {
    console.log(error);
  }
};
buscarApi();

// ── Guardar ────────────────────────────────────────────────
const guardarApi = async (e) => {
  e.preventDefault();
  spanLoader.classList.remove("d-none");
  btnCrear.disabled = true;

  const excluir = ["cta_id", "cta_numero"];
  const tipo = selectTipo.value;
  if (tipo === "efectivo" || tipo === "") excluir.push("cta_banco_id");

  if (!validarFormulario(formCuenta, excluir)) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoader.classList.add("d-none");
    btnCrear.disabled = false;
    return;
  }

  try {
    const body = new FormData(formCuenta);
    body.delete("cta_id");
    const respuesta = await fetch(`${RUTA_APP}/API/cuentas/guardar`, {
      method: "POST",
      body,
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { codigo, mensaje, detalle } = data;

    if (codigo == 1) {
      formCuenta.reset();
      modalBSCuenta.hide();
      buscarApi();
    } else {
      console.log(detalle);
    }

    Toast.fire({ icon: codigo == 1 ? "success" : "error", title: mensaje });
  } catch (error) {
    console.log(error);
  }

  spanLoader.classList.add("d-none");
  btnCrear.disabled = false;
};

// ── Modificar ──────────────────────────────────────────────
const modificarApi = async (e) => {
  e.preventDefault();
  spanLoaderModificar.classList.remove("d-none");
  btnModificar.disabled = true;

  const tipo = selectTipo.value;
  const excluir = ["cta_numero"];
  if (tipo === "efectivo" || tipo === "") excluir.push("cta_banco_id");

  if (!validarFormulario(formCuenta, excluir)) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoaderModificar.classList.add("d-none");
    btnModificar.disabled = false;
    return;
  }

  try {
    const body = new FormData(formCuenta);
    const respuesta = await fetch(`${RUTA_APP}/API/cuentas/modificar`, {
      method: "POST",
      body,
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { codigo, mensaje, detalle } = data;

    if (codigo == 1) {
      formCuenta.reset();
      modalBSCuenta.hide();
      buscarApi();
    } else {
      console.log(detalle);
    }

    Toast.fire({ icon: codigo == 1 ? "success" : "error", title: mensaje });
  } catch (error) {
    console.log(error);
  }

  spanLoaderModificar.classList.add("d-none");
  btnModificar.disabled = false;
};

// ── Eliminar ───────────────────────────────────────────────
const eliminarApi = async (e) => {
  const { codigo } = e.currentTarget.dataset;
  const confirm = await confirmacion(
    "¿Está seguro que desea eliminar esta cuenta?",
    "warning",
    "Sí, eliminar"
  );
  if (!confirm) return;

  try {
    const body = new FormData();
    body.append("cta_id", codigo);
    const respuesta = await fetch(`${RUTA_APP}/API/cuentas/eliminar`, {
      method: "POST",
      body,
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { codigo: cod, mensaje, detalle } = data;

    if (cod == 1) {
      formCuenta.reset();
      buscarApi();
    } else {
      console.log(detalle);
    }

    Toast.fire({ icon: cod == 1 ? "success" : "error", title: mensaje });
  } catch (error) {
    console.log(error);
  }
};

// ── Asignar valores al editar ──────────────────────────────
const asignarValores = async (e) => {
  const { codigo, nombre, tipo, saldo, banco, numero } = e.currentTarget.dataset;
  formCuenta.cta_id.value     = codigo;
  formCuenta.cta_nombre.value = nombre;
  formCuenta.cta_saldo.value  = saldo;
  formCuenta.cta_tipo.value   = tipo;
  formCuenta.cta_numero.value = numero;

  await actualizarCamposTipo(tipo, banco);

  modalTitleId.textContent   = "Modificar cuenta";
  btnCrear.style.display     = "none";
  btnModificar.style.display = "";
  btnCrear.disabled          = true;
  btnModificar.disabled      = false;
  spanLoader.classList.add("d-none");
  spanLoaderModificar.classList.add("d-none");

  modalElement.removeEventListener("show.bs.modal", resetearModal);
  modalBSCuenta.show();
  modalElement.addEventListener("show.bs.modal", resetearModal);
};

// ── Resetear modal ─────────────────────────────────────────
const resetearModal = () => {
  modalTitleId.textContent   = "Nueva Cuenta";
  btnCrear.style.display     = "";
  btnModificar.style.display = "none";
  btnCrear.disabled          = false;
  btnModificar.disabled      = true;
  spanLoader.classList.add("d-none");
  spanLoaderModificar.classList.add("d-none");
  divBanco.classList.add("d-none");
  divNumero.classList.add("d-none");
  if (lblCtaSaldo) {
    lblCtaSaldo.innerHTML = 'Saldo inicial <span class="text-danger">*</span>';
  }
  formCuenta.reset();
};

// ── Eventos ────────────────────────────────────────────────
formCuenta.addEventListener("submit", guardarApi);
btnNuevo.addEventListener("click", () => modalBSCuenta.show());
btnModificar.addEventListener("click", modificarApi);
modalElement.addEventListener("show.bs.modal", resetearModal);
datatableCuentas.on("click", ".editar", asignarValores);
datatableCuentas.on("click", ".eliminar", eliminarApi);

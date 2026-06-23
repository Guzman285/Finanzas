import { Modal } from "bootstrap";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./../lenguaje";
import { Toast, confirmacion, validarFormulario } from "./../funciones";

const formBanco           = document.querySelector("#formBanco");
const modalElement        = document.querySelector("#modalBanco");
const modalBSBanco        = new Modal(modalElement);
const spanLoader          = document.getElementById("spanLoader");
const btnCrear            = document.getElementById("btnCrear");
const spanLoaderModificar = document.getElementById("spanLoaderModificar");
const btnModificar        = document.getElementById("btnModificar");
const modalTitleId        = document.getElementById("modalTitleId");
const btnNuevo            = document.getElementById("btnNuevo");

spanLoader.classList.add("d-none");
spanLoaderModificar.classList.add("d-none");
btnModificar.style.display = "none";
btnModificar.disabled = true;

// ── DataTable ──────────────────────────────────────────────
let datatableBancos = new DataTable("#datatableBancos", {
  language: lenguaje,
  data: null,
  columns: [
    {
      title: "No.",
      width: "2%",
      render: (data, type, row, meta) => meta.row + 1,
    },
    { title: "BANCO", data: "ban_nombre" },
    {
      title: "Acciones",
      data: "ban_id",
      width: "20%",
      searchable: false,
      render: (data, type, row) => `
        <div class='text-center'>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-warning btn-sm rounded-circle editar"
            title='Modificar'
            data-codigo='${data}'
            data-nombre='${row.ban_nombre}'>
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

// ── Buscar ─────────────────────────────────────────────────
const buscarApi = async () => {
  try {
    const respuesta = await fetch(`${RUTA_APP}/API/bancos/buscar`, {
      method: "GET",
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { datos, mensaje, codigo } = data;
    datatableBancos.clear().draw();
    if (codigo == 1) {
      datatableBancos.rows.add(datos).draw();
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

  if (!validarFormulario(formBanco, ["ban_id"])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoader.classList.add("d-none");
    btnCrear.disabled = false;
    return;
  }

  try {
    const body = new FormData(formBanco);
    body.delete("ban_id");
    const respuesta = await fetch(`${RUTA_APP}/API/bancos/guardar`, {
      method: "POST",
      body,
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { codigo, mensaje, detalle } = data;

    if (codigo == 1) {
      formBanco.reset();
      modalBSBanco.hide();
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

  if (!validarFormulario(formBanco, ["ban_id"])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoaderModificar.classList.add("d-none");
    btnModificar.disabled = false;
    return;
  }

  try {
    const body = new FormData(formBanco);
    const respuesta = await fetch(`${RUTA_APP}/API/bancos/modificar`, {
      method: "POST",
      body,
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { codigo, mensaje, detalle } = data;

    if (codigo == 1) {
      formBanco.reset();
      modalBSBanco.hide();
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
    "¿Está seguro que desea eliminar este banco?",
    "warning",
    "Sí, eliminar"
  );
  if (!confirm) return;

  try {
    const body = new FormData();
    body.append("ban_id", codigo);
    const respuesta = await fetch(`${RUTA_APP}/API/bancos/eliminar`, {
      method: "POST",
      body,
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { codigo: cod, mensaje, detalle } = data;

    if (cod == 1) buscarApi();
    else console.log(detalle);

    Toast.fire({ icon: cod == 1 ? "success" : "error", title: mensaje });
  } catch (error) {
    console.log(error);
  }
};

// ── Asignar valores al editar ──────────────────────────────
// El modal se abre manualmente para evitar que show.bs.modal
// dispare resetearModal y pise los valores asignados
const asignarValores = (e) => {
  const { codigo, nombre } = e.currentTarget.dataset;
  formBanco.ban_id.value     = codigo;
  formBanco.ban_nombre.value = nombre;

  modalTitleId.textContent   = "Modificar banco";
  btnCrear.style.display     = "none";
  btnModificar.style.display = "";
  btnCrear.disabled          = true;
  btnModificar.disabled      = false;
  spanLoader.classList.add("d-none");
  spanLoaderModificar.classList.add("d-none");

  // Usamos el evento interno para saltarnos resetearModal
  modalElement.removeEventListener("show.bs.modal", resetearModal);
  modalBSBanco.show();
  modalElement.addEventListener("show.bs.modal", resetearModal);
};

// ── Resetear modal ─────────────────────────────────────────
const resetearModal = () => {
  modalTitleId.textContent   = "Nuevo Banco";
  btnCrear.style.display     = "";
  btnModificar.style.display = "none";
  btnCrear.disabled          = false;
  btnModificar.disabled      = true;
  spanLoader.classList.add("d-none");
  spanLoaderModificar.classList.add("d-none");
  formBanco.reset();
};

// ── Eventos ────────────────────────────────────────────────
formBanco.addEventListener("submit", guardarApi);
btnNuevo.addEventListener("click", () => { resetearModal(); modalBSBanco.show(); });
btnModificar.addEventListener("click", modificarApi);
modalElement.addEventListener("show.bs.modal", resetearModal);
datatableBancos.on("click", ".editar", asignarValores);
datatableBancos.on("click", ".eliminar", eliminarApi);

import { Modal } from "bootstrap";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./../lenguaje";
import { Toast, confirmacion, validarFormulario } from "./../funciones";

const formCategoria       = document.querySelector("#formCategoria");
const modalElement        = document.querySelector("#modalCategoria");
const modalBSCategoria    = new Modal(modalElement);
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
let datatableCategorias = new DataTable("#datatableCategorias", {
  language: lenguaje,
  data: null,
  columns: [
    {
      title: "No.",
      width: "2%",
      render: (data, type, row, meta) => meta.row + 1,
    },
    { title: "CATEGORÍA", data: "cat_nombre" },
    {
      title: "TIPO",
      data: "cat_tipo",
      render: (data) => {
        const tipos = {
          ingreso: '<span class="badge bg-success">Ingreso</span>',
          gasto:   '<span class="badge bg-danger">Gasto</span>',
          neutro:  '<span class="badge bg-secondary">Neutro</span>',
        };
        return tipos[data] ?? data;
      },
    },
    {
      title: "Acciones",
      data: "cat_id",
      width: "20%",
      searchable: false,
      render: (data, type, row) => `
        <div class='text-center'>
          <button style='min-width:31px;max-width:32px;min-height:31px;max-height:32px'
            class="btn btn-warning btn-sm rounded-circle editar"
            title='Modificar'
            data-codigo='${data}'
            data-nombre='${row.cat_nombre}'
            data-tipo='${row.cat_tipo}'>
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
    const respuesta = await fetch(`${RUTA_APP}/API/categorias/buscar`, {
      method: "GET",
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { datos, mensaje, codigo } = data;
    datatableCategorias.clear().draw();
    if (codigo == 1) {
      datatableCategorias.rows.add(datos).draw();
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

  if (!validarFormulario(formCategoria, ["cat_id"])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoader.classList.add("d-none");
    btnCrear.disabled = false;
    return;
  }

  try {
    const body = new FormData(formCategoria);
    body.delete("cat_id");
    const respuesta = await fetch(`${RUTA_APP}/API/categorias/guardar`, {
      method: "POST",
      body,
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { codigo, mensaje, detalle } = data;

    if (codigo == 1) {
      formCategoria.reset();
      modalBSCategoria.hide();
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

  if (!validarFormulario(formCategoria, [])) {
    Toast.fire({ icon: "warning", title: "Revise la información ingresada" });
    spanLoaderModificar.classList.add("d-none");
    btnModificar.disabled = false;
    return;
  }

  try {
    const body = new FormData(formCategoria);
    const respuesta = await fetch(`${RUTA_APP}/API/categorias/modificar`, {
      method: "POST",
      body,
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await respuesta.json();
    const { codigo, mensaje, detalle } = data;

    if (codigo == 1) {
      formCategoria.reset();
      modalBSCategoria.hide();
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
    "¿Está seguro que desea eliminar esta categoría?",
    "warning",
    "Sí, eliminar"
  );
  if (!confirm) return;

  try {
    const body = new FormData();
    body.append("cat_id", codigo);
    const respuesta = await fetch(`${RUTA_APP}/API/categorias/eliminar`, {
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
const asignarValores = (e) => {
  const { codigo, nombre, tipo } = e.currentTarget.dataset;
  formCategoria.cat_id.value     = codigo;
  formCategoria.cat_nombre.value = nombre;
  formCategoria.cat_tipo.value   = tipo;

  modalTitleId.textContent   = "Modificar categoría";
  btnCrear.style.display     = "none";
  btnModificar.style.display = "";
  btnCrear.disabled          = true;
  btnModificar.disabled      = false;
  spanLoader.classList.add("d-none");
  spanLoaderModificar.classList.add("d-none");

  modalElement.removeEventListener("show.bs.modal", resetearModal);
  modalBSCategoria.show();
  modalElement.addEventListener("show.bs.modal", resetearModal);
};

// ── Resetear modal ─────────────────────────────────────────
const resetearModal = () => {
  modalTitleId.textContent   = "Nueva Categoría";
  btnCrear.style.display     = "";
  btnModificar.style.display = "none";
  btnCrear.disabled          = false;
  btnModificar.disabled      = true;
  spanLoader.classList.add("d-none");
  spanLoaderModificar.classList.add("d-none");
  formCategoria.reset();
};

// ── Eventos ────────────────────────────────────────────────
formCategoria.addEventListener("submit", guardarApi);
btnNuevo.addEventListener("click", () => { resetearModal(); modalBSCategoria.show(); });
btnModificar.addEventListener("click", modificarApi);
modalElement.addEventListener("show.bs.modal", resetearModal);
datatableCategorias.on("click", ".editar", asignarValores);
datatableCategorias.on("click", ".eliminar", eliminarApi);

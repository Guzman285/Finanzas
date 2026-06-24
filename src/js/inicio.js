import { Dropdown } from "bootstrap";
import Chart from "chart.js/auto";

// ── Helpers ──────────────────────────────────────────────────────────────
const Q = (v) =>
  "Q " + parseFloat(v || 0).toLocaleString("es-GT", { minimumFractionDigits: 2 });

const TIPO_CFG = {
  ingreso:      { cls: "success", icon: "bi-arrow-down-circle-fill" },
  gasto:        { cls: "danger",  icon: "bi-arrow-up-circle-fill" },
  transferencia:{ cls: "primary", icon: "bi-arrow-left-right" },
};

const CUENTA_ICON = {
  monetaria:       "bi-bank",
  ahorro:          "bi-piggy-bank",
  efectivo:        "bi-cash",
  tarjeta_debito:  "bi-credit-card",
  tarjeta_credito: "bi-credit-card-2-back",
};

const PALETTE = [
  "#0d6efd","#198754","#dc3545","#ffc107",
  "#0dcaf0","#6f42c1","#fd7e14","#20c997",
  "#6c757d","#d63384",
];

// ── Graficas ─────────────────────────────────────────────────────────────
let chartTendencia  = null;
let chartCategorias = null;

const renderTendencia = (datos) => {
  if (chartTendencia) chartTendencia.destroy();
  chartTendencia = new Chart(document.getElementById("chartTendencia"), {
    type: "bar",
    data: {
      labels: datos.map((d) => d.label),
      datasets: [
        {
          label: "Ingresos",
          data: datos.map((d) => d.ingresos),
          backgroundColor: "rgba(25,135,84,0.75)",
          borderRadius: 4,
        },
        {
          label: "Gastos",
          data: datos.map((d) => d.gastos),
          backgroundColor: "rgba(220,53,69,0.75)",
          borderRadius: 4,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: { legend: { position: "top" } },
      scales: {
        y: {
          ticks: {
            callback: (v) => "Q " + v.toLocaleString("es-GT"),
          },
        },
      },
    },
  });
};

const renderCategorias = (datos) => {
  if (chartCategorias) chartCategorias.destroy();
  if (!datos.length) return;
  chartCategorias = new Chart(document.getElementById("chartCategorias"), {
    type: "doughnut",
    data: {
      labels: datos.map((d) => d.categoria),
      datasets: [{
        data: datos.map((d) => parseFloat(d.total)),
        backgroundColor: PALETTE.slice(0, datos.length),
        hoverOffset: 6,
      }],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: "bottom", labels: { boxWidth: 12 } },
        tooltip: {
          callbacks: {
            label: (ctx) => ` ${Q(ctx.raw)}`,
          },
        },
      },
    },
  });
};

// ── Listas ─────────────────────────────────────────────────────────────────
const renderCuentas = (cuentas) => {
  const el = document.getElementById("listaCuentas");
  if (!cuentas.length) {
    el.innerHTML = `<li class="list-group-item text-muted small">Sin cuentas registradas</li>`;
    return;
  }
  el.innerHTML = cuentas
    .map(
      (c) => `
      <li class="list-group-item d-flex justify-content-between align-items-center py-2">
        <span>
          <i class="bi ${CUENTA_ICON[c.cta_tipo] || 'bi-bank'} me-2 text-primary"></i>
          <span class="small">${c.cta_nombre}</span>
        </span>
        <span class="fw-bold small ${parseFloat(c.cta_saldo) < 0 ? 'text-danger' : ''}">${Q(c.cta_saldo)}</span>
      </li>`
    )
    .join("");
};

const renderDeudas = (deudas) => {
  const el = document.getElementById("listaDeudas");
  if (!deudas.length) {
    el.innerHTML = `<li class="list-group-item text-muted small">Sin deudas pendientes</li>`;
    return;
  }
  el.innerHTML = deudas
    .map((d) => {
      const pct = d.deu_monto_total > 0
        ? Math.round((d.deu_monto_pagado / d.deu_monto_total) * 100)
        : 0;
      return `
      <li class="list-group-item py-2">
        <div class="d-flex justify-content-between">
          <span class="small fw-semibold">${d.deu_descripcion}</span>
          <span class="small text-danger fw-bold">${Q(d.saldo_pendiente)}</span>
        </div>
        <div class="progress mt-1" style="height:5px">
          <div class="progress-bar bg-warning" style="width:${pct}%"></div>
        </div>
        <div class="d-flex justify-content-between mt-1">
          <span class="text-muted" style="font-size:0.7rem">${d.cuenta}</span>
          <span class="text-muted" style="font-size:0.7rem">Cuota: ${Q(d.deu_cuota_mensual)}</span>
        </div>
      </li>`;
    })
    .join("");
};

const renderUltimos = (movs) => {
  const el = document.getElementById("listaUltimos");
  if (!movs.length) {
    el.innerHTML = `<li class="list-group-item text-muted small">Sin movimientos recientes</li>`;
    return;
  }
  el.innerHTML = movs
    .map((m) => {
      const cfg = TIPO_CFG[m.mov_tipo] || { cls: "secondary", icon: "bi-question" };
      return `
      <li class="list-group-item py-2">
        <div class="d-flex justify-content-between align-items-center">
          <span>
            <i class="bi ${cfg.icon} text-${cfg.cls} me-1"></i>
            <span class="small">${m.mov_descripcion}</span>
          </span>
          <span class="small fw-bold text-${cfg.cls}">${Q(m.mov_monto)}</span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted" style="font-size:0.7rem">${m.cuenta_origen ?? ''}</span>
          <span class="text-muted" style="font-size:0.7rem">${m.mov_fecha}</span>
        </div>
      </li>`;
    })
    .join("");
};

// ── Cargar dashboard ──────────────────────────────────────────────────
const cargarDashboard = async () => {
  try {
    const res  = await fetch(`${RUTA_APP}/API/dashboard`, {
      headers: { "X-Requested-With": "fetch" },
    });
    const data = await res.json();
    if (data.codigo !== 1) return;

    // KPIs
    document.getElementById("kpiSaldoTotal").textContent = Q(data.saldo_total);
    document.getElementById("kpiIngresos").textContent   = Q(data.ingresos_mes);
    document.getElementById("kpiGastos").textContent     = Q(data.gastos_mes);
    document.getElementById("kpiDeuda").textContent      = Q(data.total_deuda);

    // Graficas
    renderTendencia(data.tendencia);
    renderCategorias(data.gastos_cat);

    // Listas
    renderCuentas(data.cuentas);
    renderDeudas(data.deudas);
    renderUltimos(data.ultimos);
  } catch (e) {
    console.error(e);
  }
};

cargarDashboard();

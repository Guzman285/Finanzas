<?php

namespace Controllers;

use MVC\Router;
use Model\Cuenta;
use Model\Movimiento;
use Model\Deuda;
use Exception;
use DateTime;

class AppController
{
    public static function index(Router $router)
    {
        $router->render('pages/index', [
            'titulo' => 'Dashboard'
        ]);
    }

    // GET /API/dashboard
    public static function dashboardAPI()
    {
        getHeadersApi();
        try {
            // ── Saldos por cuenta ────────────────────────────────────────────
            $cuentas = Cuenta::fetchArray("
                SELECT cta_id, cta_nombre, cta_tipo, cta_saldo
                FROM cuentas
                WHERE cta_situacion = 1
                ORDER BY cta_nombre
            ");

            $saldo_total = array_sum(array_column($cuentas, 'cta_saldo'));

            // ── Movimientos del mes actual ────────────────────────────────────
            $hoy        = date('Y-m-d');
            $inicio_mes = date('Y-m-01');

            $mov_mes = Movimiento::fetchArray("
                SELECT mov_tipo, SUM(mov_monto) AS total
                FROM movimientos
                WHERE mov_situacion = 1
                  AND mov_fecha >= TO_DATE('$inicio_mes', '%Y-%m-%d')
                  AND mov_fecha <= TO_DATE('$hoy', '%Y-%m-%d')
                GROUP BY mov_tipo
            ");

            $ingresos_mes       = 0;
            $gastos_mes         = 0;
            $transferencias_mes = 0;
            foreach ($mov_mes as $r) {
                if ($r['mov_tipo'] === 'ingreso')           $ingresos_mes       = (float)$r['total'];
                elseif ($r['mov_tipo'] === 'gasto')         $gastos_mes         = (float)$r['total'];
                elseif ($r['mov_tipo'] === 'transferencia') $transferencias_mes = (float)$r['total'];
            }

            // ── Gastos por categoria (mes actual) ────────────────────────────
            $gastos_cat = Movimiento::fetchArray("
                SELECT
                    COALESCE(cat.cat_nombre, 'Sin categoria') AS categoria,
                    SUM(m.mov_monto) AS total
                FROM movimientos m
                LEFT JOIN categorias cat ON m.mov_categoria_id = cat.cat_id
                WHERE m.mov_situacion = 1
                  AND m.mov_tipo = 'gasto'
                  AND m.mov_fecha >= TO_DATE('$inicio_mes', '%Y-%m-%d')
                  AND m.mov_fecha <= TO_DATE('$hoy', '%Y-%m-%d')
                GROUP BY COALESCE(cat.cat_nombre, 'Sin categoria')
                ORDER BY total DESC
            ");

            // ── Ingresos vs Gastos ultimos 6 meses ───────────────────────────
            $tendencia = [];
            for ($i = 5; $i >= 0; $i--) {
                $dt = new DateTime('first day of this month');
                $dt->modify("-$i month");
                $ini   = $dt->format('Y-m-d');
                $label = $dt->format('M Y');
                $dt->modify('last day of this month');
                $fin   = $dt->format('Y-m-d');

                $r_ing = Movimiento::fetchArray("
                    SELECT COALESCE(SUM(mov_monto), 0) AS t
                    FROM movimientos
                    WHERE mov_situacion = 1 AND mov_tipo = 'ingreso'
                      AND mov_fecha >= TO_DATE('$ini', '%Y-%m-%d')
                      AND mov_fecha <= TO_DATE('$fin', '%Y-%m-%d')
                ");
                $r_gas = Movimiento::fetchArray("
                    SELECT COALESCE(SUM(mov_monto), 0) AS t
                    FROM movimientos
                    WHERE mov_situacion = 1 AND mov_tipo = 'gasto'
                      AND mov_fecha >= TO_DATE('$ini', '%Y-%m-%d')
                      AND mov_fecha <= TO_DATE('$fin', '%Y-%m-%d')
                ");

                $tendencia[] = [
                    'label'    => $label,
                    'ingresos' => (float)($r_ing[0]['t'] ?? 0),
                    'gastos'   => (float)($r_gas[0]['t'] ?? 0),
                ];
            }

            // ── Deudas pendientes ─────────────────────────────────────────────
            $deudas = Deuda::fetchArray("
                SELECT
                    d.deu_descripcion,
                    d.deu_tipo,
                    ROUND(d.deu_monto_total - d.deu_monto_pagado, 2) AS saldo_pendiente,
                    d.deu_cuota_mensual,
                    d.deu_monto_total,
                    d.deu_monto_pagado,
                    c.cta_nombre AS cuenta
                FROM deudas d
                INNER JOIN cuentas c ON d.deu_cuenta_id = c.cta_id
                WHERE d.deu_situacion = 1
                  AND (d.deu_monto_total - d.deu_monto_pagado) > 0
                ORDER BY saldo_pendiente DESC
            ");

            $total_deuda = array_sum(array_column($deudas, 'saldo_pendiente'));

            // ── Ultimos 10 movimientos ────────────────────────────────────────
            $ultimos = Movimiento::fetchArray("
                SELECT
                    m.mov_id,
                    m.mov_tipo,
                    m.mov_descripcion,
                    m.mov_monto,
                    m.mov_fecha,
                    co.cta_nombre  AS cuenta_origen,
                    cat.cat_nombre AS categoria
                FROM movimientos m
                LEFT JOIN cuentas    co  ON m.mov_cuenta_origen_id = co.cta_id
                LEFT JOIN categorias cat ON m.mov_categoria_id     = cat.cat_id
                WHERE m.mov_situacion = 1
                ORDER BY m.mov_fecha DESC, m.mov_id DESC
                FETCH FIRST 10 ROWS ONLY
            ");

            echo json_encode([
                'codigo'             => 1,
                'saldo_total'        => $saldo_total,
                'ingresos_mes'       => $ingresos_mes,
                'gastos_mes'         => $gastos_mes,
                'transferencias_mes' => $transferencias_mes,
                'total_deuda'        => $total_deuda,
                'cuentas'            => $cuentas,
                'gastos_cat'         => $gastos_cat,
                'tendencia'          => $tendencia,
                'deudas'             => $deudas,
                'ultimos'            => $ultimos,
            ]);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al cargar dashboard', 'detalle' => $e->getMessage()]);
        }
    }
}

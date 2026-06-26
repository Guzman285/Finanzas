<?php

namespace Controllers;

use MVC\Router;
use Model\Movimiento;
use Model\Cuenta;
use Model\Categoria;
use Exception;

class MovimientoController
{
    public static function index(Router $router)
    {
        $router->render('movimientos/index', [
            'titulo' => 'Control de Movimientos'
        ]);
    }

    // ─── GET /API/movimientos/buscar ──────────────────────────────────────────
    public static function buscarAPI()
    {
        getHeadersApi();
        try {
            $desde  = trim($_GET['desde']   ?? '');
            $hasta  = trim($_GET['hasta']   ?? '');
            $tipo   = trim($_GET['tipo']    ?? '');
            $cuenta = (int)($_GET['cuenta'] ?? 0);

            $where = "WHERE m.mov_situacion = 1";
            if ($desde)  $where .= " AND m.mov_fecha >= '$desde'";
            if ($hasta)  $where .= " AND m.mov_fecha <= '$hasta'";
            if ($tipo)   $where .= " AND m.mov_tipo = '$tipo'";
            if ($cuenta) $where .= " AND (m.mov_cuenta_origen_id = $cuenta OR m.mov_cuenta_destino_id = $cuenta)";

            $datos = Movimiento::fetchArray("
                SELECT
                    m.mov_id,
                    m.mov_tipo,
                    m.mov_descripcion,
                    m.mov_monto,
                    m.mov_fecha,
                    m.mov_cuenta_origen_id,
                    m.mov_cuenta_destino_id,
                    m.mov_categoria_id,
                    m.mov_gasto_fijo_id,
                    m.mov_deuda_id,
                    co.cta_nombre   AS cuenta_origen,
                    cd.cta_nombre   AS cuenta_destino,
                    cat.cat_nombre  AS categoria
                FROM movimientos m
                LEFT JOIN cuentas    co  ON m.mov_cuenta_origen_id  = co.cta_id
                LEFT JOIN cuentas    cd  ON m.mov_cuenta_destino_id = cd.cta_id
                LEFT JOIN categorias cat ON m.mov_categoria_id      = cat.cat_id
                $where
                ORDER BY m.mov_fecha DESC, m.mov_id DESC
            ");

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => count($datos) . ' movimiento/s encontrado/s',
                'datos'   => $datos
            ]);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al buscar', 'detalle' => $e->getMessage()]);
        }
    }

    // ─── POST /API/movimientos/guardar ────────────────────────────────────────
    public static function guardarAPI()
    {
        getHeadersApi();
        try {
            $tipo        = trim($_POST['mov_tipo']              ?? '');
            $descripcion = trim($_POST['mov_descripcion']       ?? '');
            $monto       = (float)($_POST['mov_monto']          ?? 0);
            $fecha       = trim($_POST['mov_fecha']             ?? '');
            $cta_origen  = (int)($_POST['mov_cuenta_origen_id'] ?? 0);

            if (!$tipo || !$descripcion || $monto <= 0 || !$fecha || !$cta_origen) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }

            $cta_destino = (int)($_POST['mov_cuenta_destino_id'] ?? 0);

            if ($tipo === 'transferencia') {
                if (!$cta_destino) {
                    echo json_encode(['codigo' => 0, 'mensaje' => 'La transferencia requiere cuenta destino']);
                    return;
                }
                if ($cta_origen === $cta_destino) {
                    echo json_encode(['codigo' => 0, 'mensaje' => 'Origen y destino no pueden ser la misma cuenta']);
                    return;
                }
            }

            if (empty($_POST['mov_cuenta_destino_id'])) $_POST['mov_cuenta_destino_id'] = null;
            if (empty($_POST['mov_categoria_id']))       $_POST['mov_categoria_id']      = null;
            if (empty($_POST['mov_gasto_fijo_id']))      $_POST['mov_gasto_fijo_id']     = null;
            if (empty($_POST['mov_deuda_id']))           $_POST['mov_deuda_id']          = null;

            // Obtener tipo de cuenta origen para validar fondos
            $cuentaOrigen = Cuenta::fetchFirst("
                SELECT cta_id, cta_tipo, cta_saldo, cta_limite_credito
                FROM cuentas WHERE cta_id = {$cta_origen}
            ");

            if (!$cuentaOrigen) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Cuenta origen no encontrada']);
                return;
            }

            // Validar fondos disponibles antes de registrar
            if (in_array($tipo, ['gasto', 'transferencia'])) {
                if ($cuentaOrigen['cta_tipo'] === 'tarjeta_credito') {
                    $disponible = (float)$cuentaOrigen['cta_limite_credito'] - (float)$cuentaOrigen['cta_saldo'];
                    if ($monto > $disponible) {
                        echo json_encode(['codigo' => 0, 'mensaje' => 'Crédito insuficiente. Disponible: Q ' . number_format($disponible, 2)]);
                        return;
                    }
                } else {
                    if ($monto > (float)$cuentaOrigen['cta_saldo']) {
                        echo json_encode(['codigo' => 0, 'mensaje' => 'Saldo insuficiente. Disponible: Q ' . number_format((float)$cuentaOrigen['cta_saldo'], 2)]);
                        return;
                    }
                }
            }

            $db = \Model\ActiveRecord::getDB();

            $db->prepare("
                INSERT INTO movimientos
                    (mov_id, mov_tipo, mov_descripcion, mov_monto, mov_fecha,
                     mov_cuenta_origen_id, mov_cuenta_destino_id,
                     mov_categoria_id, mov_gasto_fijo_id, mov_deuda_id)
                VALUES
                    (seq_movimientos.nextval, :tipo, :desc, :monto, :fecha,
                     :co, :cd, :cat, :gf, :deu)
            ")->execute([
                ':tipo'  => $_POST['mov_tipo'],
                ':desc'  => $_POST['mov_descripcion'],
                ':monto' => $_POST['mov_monto'],
                ':fecha' => $_POST['mov_fecha'],
                ':co'    => $_POST['mov_cuenta_origen_id'],
                ':cd'    => $_POST['mov_cuenta_destino_id'],
                ':cat'   => $_POST['mov_categoria_id'],
                ':gf'    => $_POST['mov_gasto_fijo_id'],
                ':deu'   => $_POST['mov_deuda_id'],
            ]);

            // Ajustar saldo según tipo de cuenta y tipo de movimiento
            if ($tipo === 'ingreso') {
                if ($cuentaOrigen['cta_tipo'] === 'tarjeta_credito') {
                    // Ingreso en TC = abono/pago de deuda: reduce saldo utilizado
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo - $monto WHERE cta_id = $cta_origen");
                } else {
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo + $monto WHERE cta_id = $cta_origen");
                }
            } elseif ($tipo === 'gasto') {
                if ($cuentaOrigen['cta_tipo'] === 'tarjeta_credito') {
                    // Gasto en TC = incrementa deuda (saldo utilizado sube)
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo + $monto WHERE cta_id = $cta_origen");
                } else {
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo - $monto WHERE cta_id = $cta_origen");
                }
            } elseif ($tipo === 'transferencia') {
                // Transferencia: origen siempre pierde (TC pierde disponible, banco pierde saldo)
                if ($cuentaOrigen['cta_tipo'] === 'tarjeta_credito') {
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo + $monto WHERE cta_id = $cta_origen");
                } else {
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo - $monto WHERE cta_id = $cta_origen");
                }
                // Cuenta destino siempre recibe saldo normal
                $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo + $monto WHERE cta_id = $cta_destino");
            }

            echo json_encode(['codigo' => 1, 'mensaje' => 'Movimiento registrado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    // ─── POST /API/movimientos/eliminar ───────────────────────────────────────
    public static function eliminarAPI()
    {
        getHeadersApi();
        try {
            $mov_id = (int)($_POST['mov_id'] ?? 0);
            if (!$mov_id) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'ID requerido']);
                return;
            }

            $mov = Movimiento::fetchFirst("
                SELECT mov_id, mov_tipo, mov_monto, mov_cuenta_origen_id, mov_cuenta_destino_id
                FROM movimientos WHERE mov_id = $mov_id AND mov_situacion = 1
            ");

            if (!$mov) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Movimiento no encontrado']);
                return;
            }

            $db    = \Model\ActiveRecord::getDB();
            $monto = (float)$mov['mov_monto'];
            $co    = (int)$mov['mov_cuenta_origen_id'];
            $cd    = (int)$mov['mov_cuenta_destino_id'];
            $tipo  = $mov['mov_tipo'];

            // Obtener tipo de cuenta origen para revertir correctamente
            $cuentaOrigen = Cuenta::fetchFirst("
                SELECT cta_tipo FROM cuentas WHERE cta_id = {$co}
            ");
            $esTc = $cuentaOrigen && $cuentaOrigen['cta_tipo'] === 'tarjeta_credito';

            if ($tipo === 'ingreso') {
                // Revertir ingreso: si era TC, sube la deuda; si era banco, resta saldo
                if ($esTc) {
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo + $monto WHERE cta_id = $co");
                } else {
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo - $monto WHERE cta_id = $co");
                }
            } elseif ($tipo === 'gasto') {
                // Revertir gasto: si era TC, baja la deuda; si era banco, suma saldo
                if ($esTc) {
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo - $monto WHERE cta_id = $co");
                } else {
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo + $monto WHERE cta_id = $co");
                }
            } elseif ($tipo === 'transferencia' && $cd) {
                if ($esTc) {
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo - $monto WHERE cta_id = $co");
                } else {
                    $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo + $monto WHERE cta_id = $co");
                }
                $db->exec("UPDATE cuentas SET cta_saldo = cta_saldo - $monto WHERE cta_id = $cd");
            }

            $db->exec("UPDATE movimientos SET mov_situacion = 0 WHERE mov_id = $mov_id");

            echo json_encode(['codigo' => 1, 'mensaje' => 'Movimiento eliminado y saldo revertido']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    // ─── GET /API/movimientos/catalogos ──────────────────────────────────────
    public static function catalogosAPI()
    {
        getHeadersApi();
        try {
            $cuentas = Cuenta::fetchArray("
                SELECT
                    cta_id,
                    cta_nombre,
                    cta_tipo,
                    cta_saldo,
                    cta_limite_credito
                FROM cuentas WHERE cta_situacion = 1 ORDER BY cta_nombre
            ");
            $categorias = Categoria::fetchArray("
                SELECT cat_id, cat_nombre, cat_tipo
                FROM categorias WHERE cat_situacion = 1 ORDER BY cat_tipo, cat_nombre
            ");

            echo json_encode([
                'codigo'     => 1,
                'cuentas'    => $cuentas,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al cargar catalogos', 'detalle' => $e->getMessage()]);
        }
    }
}

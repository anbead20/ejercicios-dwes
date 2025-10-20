<?php

include 'lib/function.php';

session_start();

$mesActual = date('n');
$anioActual = date('Y');
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('d-m-Y');
$msgErrorTarea = "";

if (!isset($_SESSION['tareas'])) {
    $_SESSION['tareas'] = array();
}

if (isset($_POST['nueva'])) {
    if (empty($_POST['tarea'])) {
        $msgErrorTarea = "<span>La tarea no puede estar vacía</span>";
    } else {
        $_SESSION['tareas'][] = array(
            'fecha' => clearData($_POST['fecha']),
            'tarea' => clearData($_POST['tarea'])
        );
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mes'], $_POST['anio'])) {
    $_SESSION['mes']  = (int)($_POST['mes']);
    $_SESSION['anio'] = (int)($_POST['anio']);
}


$nombresMes = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
];

$mes  = isset($_SESSION['mes']) ? $_SESSION['mes'] : date('n');
$anio = isset($_SESSION['anio']) ? $_SESSION['anio'] : date('Y');

$festivosNacionales = ['01-01', '06-01', '17-04', '18-04', '19-04', '20-04', '01-05', '15-08', '13-10', '01-11', '06-12', '08-12', '25-12'];
$festivosAndalucia  = ['28-02', '17-04'];
$festivosLocales    = ['08-09', '24-10'];

if ($mes < 1 || $mes > 12 || $anio < 1900 || $anio > 2100) {
    die("Mes o año inválido.");
}

function obtenerDiasFestivos(array $festivos, int $mes): array
{
    $dias = [];
    foreach ($festivos as $f) {
        $diaF = (int)substr($f, 0, 2);
        $mesF = (int)substr($f, 3, 2);
        if ($mesF === $mes) $dias[] = $diaF;
    }
    return $dias;
}

$numeroDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
$primerDiaMes = date('w', mktime(0, 0, 0, $mes, 1, $anio));
$numeroHuecos = ($primerDiaMes + 6) % 7; // Ajuste para que lunes = 0

$festivosNacMes  = obtenerDiasFestivos($festivosNacionales, $mes);
$festivosAutoMes = obtenerDiasFestivos($festivosAndalucia, $mes);
$festivosLocMes  = obtenerDiasFestivos($festivosLocales, $mes);

$tareas = $_SESSION['tareas'];

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <style>
        <?php include 'styles/styles.css' ?>
    </style>
</head>

<body>
    <h1>Selecciona Mes y Año</h1>
    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <label for="mes">Mes:</label>
        <select name="mes" id="mes" required>
            <?php foreach ($nombresMes as $num => $nombre): ?>
                <option value="<?= $num ?>" <?= $num == $mesActual ? 'selected' : '' ?>><?= $nombre ?></option>
            <?php endforeach; ?>
        </select>

        <label for="anio">Año:</label>
        <input type="number" name="anio" id="anio" value="<?= $anioActual ?>" min="1900" max="2100" required>

        <input type="submit" name="submit" value="Mostrar Calendario">
    </form>
    <h1>Calendario <?= $nombresMes[$mes] ?> <?= $anio ?></h1>
    <p>
        <span><span style="color: darkred;">■</span> Festivo nacional</span><br>
        <span><span style="color: purple;">■</span> Festivo autonómico (Andalucía)</span><br>
        <span><span style="color: orangered;">■</span> Festivo local (Córdoba)</span><br>
        <span><span style="color: red;">■</span> Domingo</span>
    </p>
    <table>
        <tr>
            <th>L</th>
            <th>M</th>
            <th>X</th>
            <th>J</th>
            <th>V</th>
            <th>S</th>
            <th>D</th>
        </tr>
        <?php
        echo "<tr>";
        for ($i = 0; $i < $numeroHuecos; $i++) echo "<td></td>";

        $diaSemana = $numeroHuecos;
        for ($dia = 1; $dia <= $numeroDiasMes; $dia++) {
            $clase = "";
            $strfecha = "$dia-$mes-$anio";
            if (in_array($dia, $festivosNacMes)) $clase = "festivo-nacional";
            elseif (in_array($dia, $festivosAutoMes)) $clase = "festivo-autonomico";
            elseif (in_array($dia, $festivosLocMes)) $clase = "festivo-local";
            elseif ($diaSemana % 7 == 6) $clase = "domingo";

            echo "<td class='$clase'>
           <a href=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "?fecha=$strfecha\">$dia</a>
           </td>";
            $diaSemana++;
            if ($diaSemana % 7 == 0) echo "</tr><tr>";
        }

        while ($diaSemana % 7 != 0) {
            echo "<td></td>";
            $diaSemana++;
        }
        echo "</tr>";
        ?>
    </table>
    <?php
    echo '<h2><a href="save.php">Tareas:</a></h2>';
    echo "Fecha: $fecha<br>";
    echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '?fecha=' . $fecha . '" method="POST">';
    echo '<input type="text" name="tarea" value="">';
    echo $msgErrorTarea;
    echo '<input type="hidden" name="fecha" value="' . $fecha . '">';
    echo '<br>';
    echo '<input type="submit" value="Añadir" name="nueva">';
    echo '</form><br>';
    foreach ($tareas as $clave => $valor) {
        if ($valor["fecha"] == $fecha) {
            echo $valor["tarea"] . ' <a href="del.php?id=' . $clave . '">🗑️</a><br>';
        }
    }
    ?>
</body>

</html>
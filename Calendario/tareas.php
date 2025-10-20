<?php

session_start();

$dia   = isset($_GET['dia']) ? (int)$_GET['dia'] : 0;
$mes   = isset($_GET['mes']) ? (int)$_GET['mes'] : 0;
$anio  = isset($_GET['anio']) ? (int)$_GET['anio'] : 0;

if ($dia < 1 || $mes < 1 || $mes > 12 || $anio < 1900 || $anio > 2100) {
    die("Fecha inválida.");
}

// Leer tareas del día
$tareasDia = [];
if (file_exists('Tareas/tareas.txt')) {
    $lineas = file('Tareas/tareas.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        list($fecha, $tarea) = explode('|', $linea);
        list($y, $m, $d) = explode('-', $fecha);
        if ((int)$d === $dia && (int)$m === $mes && (int)$y === $anio) {
            $tareasDia[] = $tarea;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar tarea <?= "$dia/$mes/$anio" ?></title>
</head>

<body>
    <h1>Tareas para el <?= "$dia/$mes/$anio" ?></h1>

    <?php if (!empty($tareasDia)): ?>
        <ul>
            <?php foreach ($tareasDia as $tarea): ?>
                <li><?= htmlspecialchars($tarea) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay tareas para este día.</p>
    <?php endif; ?>

    <h2>Agregar nueva tarea</h2>
    <form method="post" action="guardar_tarea.php">
        <input type="hidden" name="dia" value="<?= $dia ?>">
        <input type="hidden" name="mes" value="<?= $mes ?>">
        <input type="hidden" name="anio" value="<?= $anio ?>">

        <label for="tarea">Tarea:</label>
        <input type="text" name="tarea" id="tarea" required>
        <br><br>
        <input type="submit" value="Guardar tarea">
    </form>
    <br>
    <a href="calendario.php">Volver al calendario</a>
</body>

</html>
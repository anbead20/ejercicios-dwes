<?php

session_start();

$dia   = $_POST['dia'];
$mes   = $_POST['mes'];
$anio  = $_POST['anio'];
$tarea = trim($_POST['tarea']);

if (!$tarea) exit("La tarea no puede estar vacía.");

$file = 'Tareas/tareas.txt';
file_put_contents($file, "$anio-$mes-$dia|$tarea\n", FILE_APPEND);

echo "Tarea guardada correctamente.<br>";
echo "<a href='calendario.php'>Volver al calendario</a>";

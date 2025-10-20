<?php

session_start();

if (!isset($_SESSION['tareas']) or !isset($_GET['id'])) {
    header('Location: index.php');
}

$id = $_GET['id'];
$fecha = $_SESSION['tareas'][$id]['fecha'];
unset($_SESSION['tareas'][$id]);
header("Location: index.php?fecha=$fecha");
exit;

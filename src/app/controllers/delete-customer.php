<?php

include_once('../db/db_connection.php');

$user_id = (int)$_GET['id'];

if (!$user_id) {
    header('Location: ../pages/homepage.php?errors[update]=true');
    die;
}

// Delete user and associated entities
$sql_sottoscrizione = "DELETE FROM sottoscrizione WHERE id_cliente = " . (int)$user_id;
$sql_cliente = "DELETE FROM cliente WHERE id = " . (int)$user_id;

$result = true;

$result &= (bool)mysqli_query($db_connection, $sql_sottoscrizione);
$result &= (bool)mysqli_query($db_connection, $sql_cliente);

mysqli_close($db_connection);

if (!$result) {
    header('Location: ../pages/homepage.php?errors[delete]=true');
    die;
} else {
    header('Location: ../pages/logout.php');
    die;
}

?>

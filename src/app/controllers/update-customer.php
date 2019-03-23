<?php

include_once('../db/db_connection.php');

$user_id = (int)$_GET['id'];

if (!$user_id) {
    header('Location: ../pages/homepage.php?errors[update]=true');
    die;
}


$username = trim($_POST['username']);
$password = md5(trim($_POST['password']));
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$birthday = trim($_POST['birthday']);
$plan = trim($_POST['plan']);
$sport_option = isset($_POST['sport_option']) ? 1 : 0;
$address = $_POST['address'] != '' ? $_POST['address'] : null;
$num_civ = $_POST['num_civ'] != '' ? $_POST['num_civ'] : null;
$city = $_POST['city'] != '' ? $_POST['city'] : null;

$sql_cliente = "UPDATE cliente c SET 
        c.password = '" . $password . "', 
        c.nome = '" . $first_name . "', 
        c.cognome = '" . $last_name . "', 
        c.data_nascita = '" . $birthday . "', 
        c.via = '" . $address . "',
        c.numero_civico = '" . $num_civ . "',
        c.citta = '" . $city . "' 
        WHERE c.id = " . (int)$user_id;
$result_cliente = mysqli_query($db_connection, $sql_cliente);


if((int)$plan!==0) {
    $sql_sottoscrizione = "UPDATE sottoscrizione s SET 
        s.data_attivazione = '" . date('Y-m-d') . "', 
        s.data_termine = '" . date('Y-m-d', strtotime('+1 month')) . "', 
        s.opzione_sport = '" . $sport_option . "',
        s.id_piano = '" . (int)$plan . "' 
        WHERE s.id_cliente = " . (int)$user_id;

    $result_sottoscrizione = mysqli_query($db_connection, $sql_sottoscrizione);
}



mysqli_close($db_connection);




if (((int)$plan!==0 && ($result_cliente && $result_sottoscrizione)) || ((int)$plan==0 && $result_cliente)) {
    header('Location: ../pages/homepage.php?confirmations[update]=true');
    die;
} else {
    header('Location: ../pages/homepage.php?errors[update]=true');
    die;
}

?>

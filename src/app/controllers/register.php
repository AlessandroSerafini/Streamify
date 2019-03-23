<?php

include_once('../db/db_connection.php');


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

// Check if username already exists
$check_username_sql = "SELECT * FROM cliente c WHERE c.username = '" . $username . "'";
$check_username_result = mysqli_query($db_connection, $check_username_sql);


if (mysqli_num_rows($check_username_result) > 0) {
    header('Location: ../pages/register.php?errors[username_already_exists]=true');
    mysqli_close($db_connection);
    die;
} else {
    $sql_cliente = "INSERT INTO cliente(username, password, nome, cognome, data_nascita, via, numero_civico, citta)
			VALUES('" . $username . "', '" . $password . "', '" . $first_name . "', '" . $last_name . "', '" . $birthday . "', '" . $address . "', '" . $num_civ . "', '" . $city . "');";
    $result_cliente = mysqli_query($db_connection, $sql_cliente);
    $id_cliente = mysqli_insert_id($db_connection);

    $sql_sottoscrizione = "INSERT INTO sottoscrizione(data_attivazione, data_termine, opzione_sport, id_cliente, id_piano)
			VALUES('" . date('Y-m-d') . "', '" . date('Y-m-d', strtotime('+1 month')) . "', '" . (int)$sport_option . "', '" . $id_cliente . "', '" . $plan . "');";
    $result_sottoscrizione = mysqli_query($db_connection, $sql_sottoscrizione);
    mysqli_close($db_connection);

    if ($result_cliente && $result_sottoscrizione) {
        header('Location: ../pages/login.php?confirmations[registration]=true');
        die;
    } else {
        header('Location: ../pages/register.php?errors[registration]=true');
        die;
    }
}

?>

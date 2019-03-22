<?php
require '../partials/header.php';


if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    die;
}

$errors = isset($_GET['errors']) ? $_GET['errors'] : array();
$confirmations = isset($_GET['confirmations']) ? $_GET['confirmations'] : array();

// Get user by session email
$user_sql = "SELECT * FROM cliente c WHERE c.username = '" . $_SESSION['username'] . "'";

$user_result = mysqli_query($db_connection, $user_sql);

if (!$user_result) {
    header('Location: logout.php');
    die;
} else {
    $user = $user_result->fetch_object();
}



// Get user plan
    $plan_sql = "SELECT s.data_attivazione, s.data_termine, s.opzione_sport, p.id, p.numero_dispositivi, p.qualita_definizione FROM sottoscrizione s
LEFT JOIN piano p ON p.id = s.id_piano
WHERE s.id_cliente = " . (int)$user->id;

$plan_result = mysqli_query($db_connection, $plan_sql);
$plan = $plan_result->fetch_object();
?>


<br />
<strong>UTENTE</strong>
    <div><?php echo $user->username; ?></div>
    <div><?php echo $user->nome; ?></div>
    <div><?php echo $user->cognome; ?></div>
    <div><?php echo $user->data_nascita; ?></div>
    <div><?php echo $user->via; ?></div>
    <div><?php echo $user->numero_civico; ?></div>
    <div><?php echo $user->citta; ?></div>

<br /><br />
<strong>PIANO</strong>
<div><?php echo $plan->id; ?></div>
<div><?php echo $plan->data_attivazione; ?></div>
<div><?php echo $plan->data_termine; ?></div>
<div><?php echo $plan->opzione_sport; ?></div>
<div><?php echo $plan->numero_dispositivi; ?></div>
<div><?php echo $plan->qualita_definizione; ?></div>



<?php
require '../partials/footer.php';
?>

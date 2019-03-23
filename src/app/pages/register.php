<?php
require '../partials/header.php';

if (isset($_SESSION['username'])) {
    header('Location: homepage.php');
    die;
}

$errors = isset($_GET['errors']) ? $_GET['errors'] : array();


// Get plans
$plan_sql = "SELECT * FROM piano p";
$plans = $db_connection->query($plan_sql);
if ($plans->num_rows === 0) {
    die('Servizio offline: al momento non ci sono piani.');
}

?>


<?php if ($errors && array_key_exists('username_already_exists', $errors) && $errors['username_already_exists']) { ?>
    <div>Lo username inserito è già stato utilizzato</div>
<?php } ?>

<?php if ($errors && array_key_exists('registration', $errors) && $errors['registration']) { ?>
    <div>C'è stato un errore durante la registrazione, riprovare più tardi</div>
<?php } ?>


<form action="../controllers/register.php" method="post">
    username*
    <input name="username"
           type="text"
           maxlength="50"
           id="username"
           required>
    password*
    <input name="password"
           type="password"
           id="password"
           required>
    nome*
    <input name="first_name"
           type="text"
           maxlength="50"
           id="first_name"
           required>
    cognome*
    <input name="last_name"
           type="text"
           maxlength="50"
           id="last_name"
           required>
    data di nascita*
    <input name="birthday"
           type="date"
           id="birthday"
           required>
    via
    <input name="address"
           type="text"
           maxlength="50"
           id="address">
    numero civico
    <input name="num_civ"
           type="number"
           id="num_civ">
    città
    <input name="city"
           type="text"
           maxlength="50"
           id="city">
    piano *
    <select name="plan"
            id="plan">
        <?php
        while($plan = $plans->fetch_assoc()) {
            echo '<option value="' . (int)$plan['id'] . '">Piano ' . $plan['id'] . '</option>';
        }
        ?>
    </select>

    <input type="checkbox"
           name="sport_option"
           value="1"> Opzione sport


    <button type="submit">Registrati</button>
</form>
<a href="login.php">Hai un'account? Vai al login</a>

<?php
require '../partials/footer.php';
?>

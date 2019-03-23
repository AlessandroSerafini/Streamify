<?php
require '../partials/header.php';

if (isset($_SESSION['username'])) {
    header('Location: homepage.php');
    die;
}

$errors = isset($_GET['errors']) ? $_GET['errors'] : array();
$confirmations = isset($_GET['confirmations']) ? $_GET['confirmations'] : array();
?>

<?php if ($errors && array_key_exists('bad_credentials', $errors) && $errors['bad_credentials']) { ?>
    <div>Credenziali errate</div>
<?php } ?>

<?php if ($confirmations && array_key_exists('registration', $confirmations) && $confirmations['registration']) { ?>
    <div>Registrazione effettuata! Accedi con i tuoi dati</div>
<?php } ?>

    <form action="../controllers/login.php" method="post">
        username*
        <input name="username"
               type="text"
               id="username"
               required>
        password*
        <input name="password"
               type="password"
               id="password"
               required>
        <button type="submit">Login</button>
    </form>
    <a href="register.php">Non hai un'account? Registrati</a>

<?php
require '../partials/footer.php';
?>

<?php
require '../partials/header.php';

if (isset($_SESSION['username'])) {
    header('Location: homepage.php');
    die;
}

$errors = isset($_GET['errors']) ? $_GET['errors'] : array();
$confirmations = isset($_GET['confirmations']) ? $_GET['confirmations'] : array();
?>


<div class="container">
    <div class="login-wrapper">
        <h1 class="page-title center">Hi! Let's Streamify</h1>

        <?php if ($confirmations && array_key_exists('registration', $confirmations) && $confirmations['registration']) { ?>
            <div class="alert alert-success">Registrazione effettuata! Accedi con i tuoi dati</div>
        <?php } ?>

        <form action="../controllers/login.php"
              method="post">
            <div class="form-group">
                <input name="username"
                       class="form-control"
                       placeholder="username*"
                       type="text"
                       id="username"
                       required>
            </div>
            <div class="form-group">
                <input name="password"
                       class="form-control"
                       placeholder="password*"
                       type="password"
                       id="password"
                       required>
            </div>

            <?php if ($errors && array_key_exists('bad_credentials', $errors) && $errors['bad_credentials']) { ?>
                <div class="alert alert-danger">Credenziali errate</div>
            <?php } ?>

            <button type="submit"
                    class="btn btn-primary full">Login
            </button>
        </form>
        <small>Non hai un'account?</small>
        <a href="register.php"
           class="btn btn-outline">Registrati</a>
    </div>
</div>

<?php
require '../partials/footer.php';
?>

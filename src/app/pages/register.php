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

<div class="container">
    <div class="login-wrapper">
        <h1 class="page-title center">Crea un account</h1>

        <form action="../controllers/register.php" method="post">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <input name="username"
                               type="text"
                               placeholder="username*"
                               class="form-control"
                               maxlength="50"
                               id="username"
                               required>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <input name="password"
                               type="password"
                               class="form-control"
                               placeholder="password*"
                               id="password"
                               required>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <input name="first_name"
                               type="text"
                               maxlength="50"
                               class="form-control"
                               id="first_name"
                               placeholder="nome*"
                               required>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <input name="last_name"
                               type="text"
                               maxlength="50"
                               class="form-control"
                               id="last_name"
                               placeholder="cognome*"
                               required>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <input name="birthday"
                               type="date"
                               id="birthday"
                               class="form-control"
                               placeholder="data di nascita*"
                               required>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <input name="address"
                               type="text"
                               maxlength="50"
                               class="form-control"
                               placeholder="via"
                               id="address">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <input name="num_civ"
                               type="number"
                               class="form-control"
                               placeholder="numero civico"
                               id="num_civ">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <input name="city"
                               type="text"
                               maxlength="50"
                               class="form-control"
                               placeholder="città"
                               id="city">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <select name="plan"
                                class="form-control"
                                id="plan">
                            <?php
                            while ($plan = $plans->fetch_assoc()) {
                                echo '<option value="' . (int)$plan['id'] . '">Piano ' . $plan['id'] . '</option>';
                            }
                            ?>
                        </select>

                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <input type="checkbox"
                               name="sport_option"
                               value="1"> Opzione sport

                    </div>
                </div>
            </div>


            <?php if ($errors && array_key_exists('username_already_exists', $errors) && $errors['username_already_exists']) { ?>
                <div class="alert alert-danger">Lo username inserito è già stato utilizzato</div>
            <?php } ?>

            <?php if ($errors && array_key_exists('registration', $errors) && $errors['registration']) { ?>
                <div class="alert alert-danger">C'è stato un errore durante la registrazione, riprovare più tardi</div>
            <?php } ?>

            <button type="submit"
                    class="btn btn-primary full">Registrati
            </button>
        </form>
        <small>Hai un'account?</small>
        <a href="login.php"
           class="btn btn-outline">Accedi</a>
    </div>
</div>

<?php
require '../partials/footer.php';
?>

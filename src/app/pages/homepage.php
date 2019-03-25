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
$my_plan_sql = "SELECT s.data_attivazione, s.data_termine, s.opzione_sport, p.id, p.numero_dispositivi, p.qualita_definizione FROM sottoscrizione s
LEFT JOIN piano p ON p.id = s.id_piano
WHERE s.id_cliente = " . (int)$user->id;
$my_plan_result = mysqli_query($db_connection, $my_plan_sql);
$my_plan = $my_plan_result->fetch_object();

$is_plan_valid = date("Y-m-d") <= $my_plan->data_termine;


if (!$is_plan_valid) {
// Get all plans
    $all_plans_sql = "SELECT * FROM piano p";
    $all_plans = $db_connection->query($all_plans_sql);
}


// Get plan contents
$contents_sql = "SELECT cm.id, cm.nome, cm.data, cm.sport, cm.trasmissione, cm.durata, cm.regia, cm.anno_uscita, cm.genere, cm.lingua_parlato, cm.lingua_sottotitolazione FROM contenuto_multimediale cm
LEFT JOIN inclusione i ON i.id_contenuto_multimediale = cm.id
WHERE i.id_piano = " . (int)$my_plan->id . "
AND (cm.stand_alone<>1
OR cm.stand_alone IS NULL)";
if (!(bool)$my_plan->opzione_sport) {
    $contents_sql .= " AND cm.sport IS NULL";
}
$contents = $db_connection->query($contents_sql);


// Get stand-alone sport contents
$stand_alone_sql = "SELECT * FROM contenuto_multimediale WHERE stand_alone = 1";
$stand_alone_contents = $db_connection->query($stand_alone_sql);
?>


<div class="container">
    <h1 class="page-title center">Ciao <?php echo $user->nome ?>!</h1>


    <div class="row personal-wrapper">
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
            <?php

            if ($errors && array_key_exists('delete', $errors) && $errors['delete']) {
                ?>
                <div class="alert alert-danger">C'è stato un errore durante l'eliminazione dei dati, riprovare più
                    tardi
                </div>
                <?php
            }
            if ($errors && array_key_exists('generic', $errors) && $errors['generic']) {
                ?>
                <div class="alert alert-danger">C'è stato un errore, riprovare più tardi</div><?php
            }
            ?>

            <?php if ($errors && array_key_exists('update', $errors) && $errors['update']) {
                ?>
                <div class="alert alert-danger">C'è stato un errore durante l'aggiornamento, riprovare più tardi
                </div><?php
            }


            if ($confirmations && array_key_exists('update', $confirmations) && $confirmations['update']) {
                ?>
                <div class="alert alert-success">Dati aggiornati con successo!</div><?php
            }
            ?>
            <form action="../controllers/update-customer.php?id=<?php echo $user->id; ?>" method="post">

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <input name="username"
                                   type="text"
                                   class="form-control"
                                   placeholder="username*"
                                   maxlength="50"
                                   value="<?php echo $user->username; ?>"
                                   id="username"
                                   disabled
                                   required>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <input name="password"
                                   type="password"
                                   placeholder="password*"
                                   id="password"
                                   class="form-control"
                                   required>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <input name="first_name"
                                   type="text"
                                   maxlength="50"
                                   placeholder="nome*"
                                   class="form-control"
                                   id="first_name"
                                   value="<?php echo $user->nome; ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <input name="last_name"
                                   type="text"
                                   class="form-control"
                                   placeholder="cognome*"
                                   id="last_name"
                                   value="<?php echo $user->cognome; ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <input name="birthday"
                                   type="date"
                                   id="birthday"
                                   class="form-control"
                                   value="<?php echo $user->data_nascita; ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <input name="address"
                                   type="text"
                                   maxlength="50"
                                   placeholder="via"
                                   class="form-control"
                                   value="<?php echo $user->via; ?>"
                                   id="address">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <input name="num_civ"
                                   type="number"
                                   placeholder="numero civico"
                                   class="form-control"
                                   value="<?php echo $user->numero_civico; ?>"
                                   id="num_civ">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <input name="city"
                                   class="form-control"
                                   type="text"
                                   maxlength="50"
                                   placeholder="città"
                                   value="<?php echo $user->citta; ?>"
                                   id="city">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <select name="plan"
                                    class="form-control"
                                    id="plan"
                                <?php if ($is_plan_valid) {
                                    echo " disabled";
                                } ?>>
                                <option value="<?php echo (int)$my_plan->id; ?>" selected>
                                    Piano <?php echo (int)$my_plan->id; ?></option>
                                <?php if (!$is_plan_valid) {
                                    while ($plan = $all_plans->fetch_assoc()) {
                                        if ((int)$plan['id'] !== (int)$my_plan->id) { ?>
                                            <option value="<?php echo (int)$plan['id']; ?>">
                                                Piano <?php echo (int)$plan['id']; ?></option>
                                        <?php }
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <input type="checkbox"
                                   name="sport_option"
                                <?php
                                if ($is_plan_valid) {
                                    echo " disabled='disabled' value='" . $my_plan->opzione_sport . "'";
                                }
                                if ((bool)$my_plan->opzione_sport) echo ' checked'; ?>> Opzione sport
                            <?php
                            if ($is_plan_valid) {
                                ?>
                                <input type="hidden" id="sport_option" name="sport_option"
                                       value="<?php echo $my_plan->opzione_sport; ?>"/>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                </div>


                <div class="cb">
                    <button type="submit"
                            class="btn btn-primary pull-left">Aggiorna dati personali
                    </button>
                    <a class="btn-danger pull-right"
                       style="position: relative; top: 30px;"
                       href="../controllers/delete-customer.php?id=<?php echo $user->id; ?>">
                        Elimina account
                    </a>
                </div>

            </form>
            <p>oppure</p>
            <a class="btn btn-outline btn-small"
               href="logout.php">Logout</a>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
            <div class="box">
                <h3>Dettagli piano</h3>
                <div><strong>Piano: </strong><?php echo $my_plan->id; ?></div>
                <div><strong>Attivazione: </strong><?php echo $my_plan->data_attivazione; ?></div>
                <div><strong>Termine: </strong><?php echo $my_plan->data_termine; ?></div>
                <div><strong>Pacchetto sport: </strong><?php echo $my_plan->opzione_sport; ?></div>
                <div><strong>Numero dispositivi: </strong><?php echo $my_plan->numero_dispositivi; ?></div>
                <div><strong>Qualità: </strong><?php echo $my_plan->qualita_definizione; ?></div>
            </div>
        </div>
    </div>


    <br/>


    <?php

    if ($is_plan_valid) {
        ?>
        <h1 class="page-title center">Contenuti multimediali</h1>
        <div class="row contents-list">
            <?php
            if ($contents->num_rows > 0) {
                while ($content = $contents->fetch_assoc()) {
                    $is_sport_content = $content['sport'] !== null;
                    ?>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div class="cover<?php if ($is_sport_content) echo " sport"; ?>">
                            <i class="fa fa-<?php echo ($is_sport_content) ? 'futbol' : 'film'; ?>"></i>
                            <div class="info">
                                <?php if ($content['durata'] !== null) { ?>
                                    <strong> <?php echo $content['durata']; ?>"</strong>
                                <?php }
                                if ($content['trasmissione'] !== null) { ?>
                                    <i class="fa fa-<?php echo ($content['trasmissione'] == 'live') ? 'broadcast-tower' : 'history'; ?>"></i>
                                <?php } ?>
                            </div>
                            <footer>
                                <h3> <?php echo $content['nome']; ?></h3>
                                <?php
                                if ($content['genere'] !== null) { ?>
                                    <h4><?php echo $content['genere']; ?></h4>
                                <?php }
                                if ($content['sport'] !== null) { ?>
                                    <h4><?php echo $content['sport']; ?></h4>
                                <?php }
                                ?>
                            </footer>
                        </div>
                        <ul class="additional-info">
                            <?php if ($content['data'] !== null) { ?>
                                <li>
                                    Data:<strong> <?php echo $content['data']; ?></strong>
                                </li>
                            <?php }


                            if ($content['regia'] !== null) { ?>
                                <li>Regia:<strong> <?php echo $content['regia']; ?></strong></li>
                            <?php }
                            if ($content['anno_uscita'] !== null) { ?>
                                <li>Anno di uscita:<strong> <?php echo $content['anno_uscita']; ?></strong></li>
                            <?php }
                            if ($content['lingua_parlato'] !== null) { ?>
                                <li>Lingua parlato:<strong> <?php echo $content['lingua_parlato']; ?>
                                </strong>
                                </li><?php }
                            if ($content['lingua_sottotitolazione'] !== null) { ?>
                                <li>Lingua
                                    sottotitolazione:<strong> <?php echo $content['lingua_sottotitolazione']; ?>
                                    </strong>
                                </li>
                            <?php }
                            if (!$is_sport_content) {
                                // Get user plan
                                $actor_sql = "SELECT a.id, r.protagonista FROM attore a
                            LEFT JOIN riferimento r ON a.id = r.id_attore
                            WHERE r.id_contenuto_multimediale = " . (int)$content['id'];
                                $actors = $db_connection->query($actor_sql);
                                if ($actors->num_rows > 0) {
                                    ?>
                                    Attori:
                                    <ul>
                                        <?php
                                        while ($actor = $actors->fetch_assoc()) {
                                            ?>
                                            <li>
                                                <?php if ((bool)$actor['protagonista']) echo "<strong>"; ?>
                                                    <?php echo "Attore " . $actor['id']; ?>
                                                    <?php if ((bool)$actor['protagonista']) echo "</strong>"; ?>
                                            </li>
                                            <?php
                                        }
                                        ?>

                                    </ul>
                                    <?php
                                }
                            } ?>
                        </ul>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <br/><br/>


        <h1 class="page-title center">Extra sport</h1>
        <div class="row contents-list">
            <?php
            if ($stand_alone_contents->num_rows > 0) {
                while ($content = $stand_alone_contents->fetch_assoc()) {
                    ?>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div class="cover sport">
                            <i class="fa fa-futbol"></i>
                            <div class="info">
                                <?php
                                if ($content['trasmissione'] !== null) { ?>
                                    <i class="fa fa-<?php echo ($content['trasmissione'] == 'live') ? 'broadcast-tower' : 'history'; ?>"></i>
                                <?php } ?>
                            </div>
                            <footer>
                                <h3> <?php echo $content['nome']; ?></h3>
                                <?php
                                if ($content['sport'] !== null) { ?>
                                    <h4><?php echo $content['sport']; ?></h4>
                                <?php }
                                ?>
                            </footer>
                        </div>
                        <ul class="additional-info">
                            <?php if ($content['data'] !== null) { ?>
                                <li>
                                    Data:<strong> <?php echo $content['data']; ?></strong>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php
                }
            }
            ?>
        </div>


        <ul>
            <?php
            if ($stand_alone_contents->num_rows > 0) {
                while ($stand_alone_content = $stand_alone_contents->fetch_assoc()) {
                    ?>
                    <li>
                        Nome:<strong> <?php echo $stand_alone_content['nome']; ?></strong>
                        <ul>
                            <?php if ($stand_alone_content['data'] !== null) { ?>
                                <li>Data:<strong> <?php echo $stand_alone_content['data']; ?></strong></li><?php }
                            if ($stand_alone_content['sport'] !== null) { ?>
                                <li>Sport:<strong> <?php echo $stand_alone_content['sport']; ?></strong></li><?php }
                            if ($stand_alone_content['trasmissione'] !== null) { ?>
                                <li>Trasmissione:<strong> <?php echo $stand_alone_content['trasmissione']; ?></strong>
                                </li><?php } ?>

                        </ul>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>

        <?php
    } else {
        ?>
        <div class="alert alert-danger">Il tuo piano è scaduto! Non Puoi vedere i contenuti multimediali</div>
        <?php
    }
    ?>


</div>


<?php

require '../partials/footer.php';
?>

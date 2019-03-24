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


if ($errors && array_key_exists('delete', $errors) && $errors['delete']) {
    ?>
    <div>C'è stato un errore durante l'eliminazione dei dati, riprovare più tardi</div>
    <?php
}
if ($errors && array_key_exists('generic', $errors) && $errors['generic']) {
    ?>
    <div>C'è stato un errore, riprovare più tardi</div><?php
}
?>

<strong>Dati personali:</strong>
<?php if ($errors && array_key_exists('update', $errors) && $errors['update']) {
    ?>
    <div>C'è stato un errore durante l'aggiornamento, riprovare più tardi</div><?php
}


if ($confirmations && array_key_exists('update', $confirmations) && $confirmations['update']) {
    ?>
    <div>Dati aggiornati con successo!</div><?php
}
?>


<div class="container">

    <div>


        <form action="../controllers/update-customer.php?id=<?php echo $user->id; ?>" method="post">


            username*
            <input name="username"
                   type="text"
                   maxlength="50"
                   value="<?php echo $user->username; ?>"
                   id="username"
                   disabled
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
                   value="<?php echo $user->nome; ?>"
                   required>
            cognome*
            <input name="last_name"
                   type="text"
                   maxlength="50"
                   id="last_name"
                   value="<?php echo $user->cognome; ?>"
                   required>
            data di nascita*
            <input name="birthday"
                   type="date"
                   id="birthday"
                   value="<?php echo $user->data_nascita; ?>"
                   required>
            via
            <input name="address"
                   type="text"
                   maxlength="50"
                   value="<?php echo $user->via; ?>"
                   id="address">
            numero civico
            <input name="num_civ"
                   type="number"
                   value="<?php echo $user->numero_civico; ?>"
                   id="num_civ">
            città
            <input name="city"
                   type="text"
                   maxlength="50"
                   value="<?php echo $user->citta; ?>"
                   id="city">
            piano *
            <select name="plan"
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
            <button type="submit">Aggiorna dati personali</button>

        </form>

        <br/><br/>
        <a href="logout.php">Logout</a>
        <br/>
        <a href="../controllers/delete-customer.php?id=<?php echo $user->id; ?>">Elimina
            account</a>


        <br/><br/>
        <strong>PIANO</strong>
        <div><?php echo "Piano " . $my_plan->id; ?></div>
        <div><?php echo $my_plan->data_attivazione; ?></div>
        <div><?php echo $my_plan->data_termine; ?></div>
        <div><?php echo $my_plan->opzione_sport; ?></div>
        <div><?php echo $my_plan->numero_dispositivi; ?></div>
        <div><?php echo $my_plan->qualita_definizione; ?></div>

    </div>


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
                    <div class="col-xs-12 col-sm-4">
                        <div class="cover">
                            <?php
                            if ($content['durata'] !== null) { ?>
                                <div class="duration"><strong> <?php echo $content['durata']; ?>"</strong></div>
                            <?php }
                            ?>
                        </div>
                        <?php if ($is_sport_content) {
                            echo "(SPORT)";
                        } ?>
                        Nome:<strong> <?php echo $content['nome']; ?></strong>
                        <ul>
                            <?php if ($content['data'] !== null) { ?>
                                <li>
                                    Data:<strong> <?php echo $content['data']; ?></strong>
                                </li>
                            <?php }
                            if ($content['sport'] !== null) { ?>
                                <li>Sport:<strong> <?php echo $content['sport']; ?></strong></li>
                            <?php }
                            if ($content['trasmissione'] !== null) { ?>
                                <li>Trasmissione:<strong> <?php echo $content['trasmissione']; ?></strong></li>
                            <?php }

                            if ($content['regia'] !== null) { ?>
                                <li>Regia:<strong> <?php echo $content['regia']; ?></strong></li>
                            <?php }
                            if ($content['anno_uscita'] !== null) { ?>
                                <li>Anno di uscita:<strong> <?php echo $content['anno_uscita']; ?></strong></li>
                            <?php }
                            if ($content['genere'] !== null) { ?>
                                <li>Genere:<strong> <?php echo $content['genere']; ?></strong></li>
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
                                $actor_sql = "SELECT a.id FROM attore a
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
                                            <li><?php echo "Attore " . $actor['id']; ?></li>
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
        <strong>EVENTI SPORTIVI ACQUISTABILI A PARTE</strong>
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
        <div>Il tuo piano è scaduto! Non Puoi vedere i contenuti multimediali</div>
        <?php
    }
    ?>


</div>


<?php

require '../partials/footer.php';
?>

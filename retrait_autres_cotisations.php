<?php
session_start();
if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])) {
    header('Location: ./login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Administration — Retrait des cotisations</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <style>
        /* Custom select-box styles */
        .select-box {
            position: relative;
            width: 100%;
        }

        .select-box .select-option {
            cursor: pointer;
        }

        .select-box .content {
            background: #fff;
            position: absolute;
            color: #000;
            border-radius: 7px;
            margin-top: 5px;
            width: 100%;
            z-index: 999;
            display: none;
            border: 1px solid #ccc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .select-box.active .content {
            display: block;
        }

        .content input[type="text"] {
            margin: 10px;
        }

        .options {
            max-height: 200px;
            overflow-y: auto;
            padding: 0;
            margin: 0;
        }

        .options li {
            padding: 10px 15px;
            list-style: none;
            font-size: 14px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }

        .options li:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main-panel">
            <?php include 'mainheader.php'; ?>

            <div class="container">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Retrait des cotisations</h3>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <a href="liste_retrait_autres_cotisations" class="btn btn-danger btn-round">Annuler</a>
                        </div>
                    </div>

                    <?php
                    // SweetAlert messages
                    if (isset($_SESSION['message'])) {
                        $msg = addslashes($_SESSION['message']);
                        echo "
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Succès',
                                    text: '{$msg}',
                                    confirmButtonText: 'OK'
                                });
                            });
                        </script>
                        ";
                    } elseif (isset($_SESSION['error'])) {
                        $msg = addslashes($_SESSION['error']);
                        echo "
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: '{$msg}',
                                    confirmButtonText: 'OK'
                                });
                            });
                        </script>
                        ";
                    }
                    unset($_SESSION['message'], $_SESSION['error']);
                    ?>

                    <div class="row row-card-no-pd d-flex justify-content-center">
                        <div class="col-md-8">
                            <div class="card p-4">

                                <form action="./traitement/retrait_autre_cotisation.php" method="post" class="row g-3">
                                    <div class="col-md-12">
                                        <label for="motif" class="form-label">Motif du retrait</label>
                                        <input type="text" class="form-control" id="motif" name="motif" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="dateretrait" class="form-label">Date de retrait</label>
                                        <input type="date" class="form-control" id="dateretrait" name="dateretrait"
                                            value="<?= date("Y-m-d") ?>" disabled>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="montant" class="form-label">Montant à retirer</label>
                                        <input type="number" class="form-control" id="montant" name="montant" required min="0">
                                    </div>

                                    <div class="col-md-12 select-box">
                                        <label for="soValue" class="form-label">Cotisation concernée</label>
                                        <input type="text" class="form-control select-option" id="soValue"
                                            placeholder="Sélectionner une cotisation" readonly name="cotcon" style="background: #fff;">
                                        <input type="hidden" id="idm" name="id_cot_con">
                                        <div class="content">
                                            <input type="text" id="optionSearch" class="form-control mb-3"
                                                placeholder="Rechercher">
                                            <ul class="options">
                                                <?php
                                                require './traitement/connect.php';
                                                $pst = $con->prepare('SELECT * FROM autres_cotisations');
                                                $pst->execute();
                                                $res = $pst->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($res as $rs) {
                                                    echo '<li data-id="' . htmlspecialchars($rs['id_ac']) . '">' . htmlspecialchars($rs['motif_acotisation']) . '</li>';
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>

                                    <?php
                                    // Vérifier s’il y a au moins une cotisation
                                    $count = count($res);
                                    ?>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary"
                                            <?= ($count > 0 ? '' : 'disabled') ?>>Retirer maintenant</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Scripts en bas pour de meilleures performances -->
            <script src="assets/js/core/jquery-3.7.1.min.js"></script>
            <script src="assets/js/core/popper.min.js"></script>
            <script src="assets/js/core/bootstrap.min.js"></script>
            <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
            <script src="assets/js/plugin/chart.js/chart.min.js"></script>
            <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
            <script src="assets/js/plugin/chart-circle/circles.min.js"></script>
            <script src="assets/js/plugin/datatables/datatables.min.js"></script>
            <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
            <script src="assets/js/plugin/jsvectormap/world.js"></script>

            <!-- SweetAlert -->
            <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

            <!-- Ton JS perso pour le select custom -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const selectBox = document.querySelector('.select-box');
                    const selectOptionInput = document.querySelector('.select-option');
                    const soValueInput = document.querySelector('#soValue');
                    const hiddenIdInput = document.querySelector('#idm');
                    const searchInput = document.querySelector('#optionSearch');
                    const optionsList = document.querySelector('.options');
                    const optionItems = document.querySelectorAll('.options li');

                    // Ouvre/ferme le menu
                    selectOptionInput.addEventListener('click', function() {
                        selectBox.classList.toggle('active');
                        searchInput.value = '';
                        filterOptions('');
                        searchInput.focus();
                    });

                    // Quand on clique sur une option
                    optionItems.forEach(function(item) {
                        item.addEventListener('click', function() {
                            const text = this.textContent.trim();
                            const id = this.getAttribute('data-id');
                            soValueInput.value = text;
                            hiddenIdInput.value = id;
                            selectBox.classList.remove('active');
                        });
                    });

                    // Filtrer les options au fur et à mesure que l’utilisateur tape
                    searchInput.addEventListener('input', function() {
                        const filter = this.value.toUpperCase();
                        filterOptions(filter);
                    });

                    function filterOptions(filter) {
                        optionItems.forEach(function(item) {
                            const txt = item.textContent || item.innerText;
                            if (txt.toUpperCase().indexOf(filter) > -1) {
                                item.style.display = '';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    }

                    // Fermer le select si on clique à l'extérieur
                    document.addEventListener('click', function(event) {
                        const isClickInside = selectBox.contains(event.target);
                        if (!isClickInside) {
                            selectBox.classList.remove('active');
                        }
                    });
                });
            </script>

        </div>
    </div>
</body>

</html>

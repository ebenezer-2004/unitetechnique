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
    <title>Administration</title>
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
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Select2 CSS local -->
    <link href="assets/select2/select2.min.css" rel="stylesheet" />

    <style>
      /* Optionnel : style pour avoir un bon rendu Select2 avec Bootstrap */
      .select2-container .select2-selection--multiple {
          min-height: 38px;
          border: 1px solid #ced4da;
          border-radius: 4px;
      }
      .select2-container .select2-selection--single {
          height: 38px;
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
                            <h3 class="fw-bold mb-3">Enregistrement des cotisations</h3>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <a href="liste_participants_autre_cotisations.php" class="btn btn-danger btn-round">Annuler</a>
                        </div>
                    </div>

                    <?php
                    if (isset($_SESSION['message'])) {
                        echo '<div class="alert alert-success">'.$_SESSION['message'].'</div>';
                    } elseif (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                    }
                    unset($_SESSION['message'], $_SESSION['error']);
                    ?>

                    <div class="row row-card-no-pd d-flex justify-content-center">
                        <div class="col-md-12">
                            <div class="card centered">
                                <form class="row g-3" action="./traitement/add_Autre.php" method="post">
                                    <div class="col-md-12">
                                        <label for="montant" class="form-label">Montant (Optionnel)</label>
                                        <input type="number" class="form-control" id="montant" name="montant" min="100">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="datepayement" class="form-label">Date payement</label>
                                        <input type="text" class="form-control" id="datepayement" name="datepayement"
                                            value="<?= date("Y-m-d") ?>" readonly="true">
                                    </div>

                                    <!-- Sélection multiple des participants -->
                                    <div class="col-md-12">
                                        <label for="idm" class="form-label">Participant(s)</label>
                                        <select id="idm" name="idm[]" class="form-control select2-participants" multiple="multiple" required>
                                            <?php
                                            require './traitement/connect.php';
                                            $pst = $con->prepare('SELECT * FROM membres ORDER BY nom, prenom');
                                            $pst->execute();
                                            $res = $pst->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($res as $rs) {
                                                $nomComplet = htmlspecialchars($rs['nom'] . ' ' . $rs['prenom']);
                                                echo "<option value=\"{$rs['id']}\">{$nomComplet}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Sélection de la cotisation concernée -->
                                    <div class="col-md-12">
                                        <label for="idcot" class="form-label">Cotisation Concernée</label>
                                        <select id="idcot" name="idcot" class="form-control select2-cotisation" required>
                                            <option value="">Sélectionnez une cotisation</option>
                                            <?php
                                            require './traitement/connect.php';
                                            $pst2 = $con->prepare('SELECT * FROM autres_cotisations ORDER BY id_ac DESC');
                                            $pst2->execute();
                                            $res2 = $pst2->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($res2 as $rs2) {
                                                $motif = htmlspecialchars($rs2['motif_acotisation']);
                                                echo "<option value=\"{$rs2['id_ac']}\">{$motif}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Ajouter maintenant</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Scripts JS -->
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
            <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>
            <script src="assets/js/kaiadmin.min.js"></script>
            <script src="assets/js/setting-demo.js"></script>
            <script src="assets/js/demo.js"></script>

            <!-- Select2 JS local -->
            <script src="assets/select2/select2.min.js"></script>

            <script>
              $(document).ready(function() {
                // Initialiser select2 sur le champ participants (multi)
                $('#idm').select2({
                  placeholder: "Sélectionnez un ou plusieurs participants",
                  allowClear: true,
                  width: '100%'
                });

                // Initialiser select2 sur le champ cotisation (simple)
                $('#idcot').select2({
                  placeholder: "Sélectionnez une cotisation",
                  allowClear: true,
                  width: '100%'
                });
              });
            </script>

        </div>
    </div>
</body>

</html>

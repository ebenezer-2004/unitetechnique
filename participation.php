<?php
session_start();
if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])) {
  header('Location: ./login.php');
  exit;
}

$mois = [
  1 => "Janvier",
  2 => "Février",
  3 => "Mars",
  4 => "Avril",
  5 => "Mai",
  6 => "Juin",
  7 => "Juillet",
  8 => "Aout",
  9 => "Septembre",
  10 => "Octobre",
  11 => "Novembre",
  12 => "Décembre",
];

$mois_actuelle = (int) date('n');
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
    /* Pour améliorer le rendu multi-select et alignement */
    .select2-container--default .select2-selection--multiple {
      min-height: 38px;
      border-radius: 4px;
      border: 1px solid #ced4da;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <?php include 'sidebar.php' ?>
    <div class="main-panel">
      <?php include 'mainheader.php' ?>

      <div class="container">
        <div class="page-inner">
          <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
              <h3 class="fw-bold mb-3">Enregistrement des mensualités</h3>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
              <a href="mensualites.php" class="btn btn-danger btn-round">Annuler</a>
            </div>
          </div>

          <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
          <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
          <?php endif; 
          unset($_SESSION['message'], $_SESSION['error']);
          ?>

          <div class="row row-card-no-pd d-flex justify-content-center">
            <div class="col-md-12">
              <div class="card centered">
                <form class="row g-3" action="./traitement/addMensualite.php" method="post">
                  
                  <div class="col-md-12">
                    <label for="montant" class="form-label">Montant</label>
                    <input type="number" class="form-control" id="montant" name="montant" min="200" value="200" required>
                  </div>

                  <div class="col-md-12">
                    <label for="datepayement" class="form-label">Date paiement</label>
                    <input type="text" class="form-control" id="datepayement" name="datepayement" value="<?= date("Y-m-d") ?>" readonly>
                  </div>

                  <div class="col-md-12">
                    <label for="id_mois" class="form-label">Mois</label>
                    <select name="id_mois" id="id_mois" class="form-control select2-single" required>
                      <?php foreach ($mois as $key => $value): ?>
                        <option value="<?= $key ?>" <?= $key === $mois_actuelle ? "selected" : "" ?>>
                          <?= $value ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="col-md-12">
                    <label for="idm" class="form-label">Participant(s)</label>
                    <select id="idm" name="idm[]" class="form-control select2-multiple" multiple="multiple" required>
                      <?php
                      require './traitement/connect.php';
                      $pst = $con->prepare('SELECT * FROM membres ORDER BY nom,prenom');
                      $pst->execute();
                      $res = $pst->fetchAll(PDO::FETCH_ASSOC);

                      foreach ($res as $rs) {
                        $nomComplet = htmlspecialchars($rs['nom'] . ' ' . $rs['prenom']);
                        echo "<option value=\"{$rs['id']}\">{$nomComplet}</option>";
                      }
                      ?>
                    </select>
                    <small class="form-text text-muted">Vous pouvez sélectionner plusieurs membres (Ctrl+clic ou Cmd+clic)</small>
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

      <!-- JS scripts -->
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
          // Select simple (mois)
          $('.select2-single').select2({
            placeholder: "Sélectionnez un mois",
            minimumResultsForSearch: Infinity, // cache la recherche car peu d'options
            width: '100%'
          });

          // Select multiple (membres)
          $('.select2-multiple').select2({
            placeholder: "Sélectionnez un ou plusieurs membres",
            width: '100%'
          });
        });
      </script>

    </div>
  </div>
</body>

</html>

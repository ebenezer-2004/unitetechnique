<?php
session_start();
if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])) {
  $location = './login.php';
  header('location:'.$location);
}


require './traitement/connect.php';

$parpage = 5;
$current = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;


$queryCount = "SELECT COUNT(*) as nb FROM retraits_evenements JOIN autres_cotisations ON autres_cotisations.id_ac=retraits_evenements.id_cot";

$pst = $con->prepare($queryCount);
$pst->execute();
$nbm = $pst->fetch(PDO::FETCH_ASSOC)['nb'];

$offset = ($current - 1) * $parpage;
$pages = ceil($nbm / $parpage);


$query = "SELECT * FROM retraits_evenements JOIN autres_cotisations ON autres_cotisations.id_ac=retraits_evenements.id_cot ORDER BY id_rc DESC";
// $query .= " LIMIT :offset, :parpage";

$pst = $con->prepare($query);
// $pst->bindValue(':offset', $offset, PDO::PARAM_INT);
// $pst->bindValue(':parpage', $parpage, PDO::PARAM_INT);
$pst->execute();
$values = $pst->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Administration</title>
  <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
  <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

  <!-- Fonts and icons -->
  <script src="assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      google: {
        families: ["Public Sans:300,400,500,600,700"]
      },
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
              <h3 class="fw-bold mb-3">Gestion des retraits</h3>

            </div>
            <div class="ms-md-auto py-2 py-md-0">
              <a href="retrait_autres_cotisations.php" class="btn btn-primary btn-round">Nouveau</a>
            </div>
          </div>
          <?php
          if (isset($_SESSION['message'])) {
            ?>
          <div class="alert alert-success">
            <?= $_SESSION['message'] ?>
          </div>

          <?php

          } elseif (isset($_SESSION['error'])) {
            ?>
          <div class="alert alert-danger">
            <?= $_SESSION['error'] ?>
          </div>
          <?php }
          unset($_SESSION['message']);
          unset($_SESSION['error'])

            ?>
          <div class="row row-card-no-pd d-flex justify-content-center">
            <div class="col-sm-6 col-md-3">
              <div class="card card-stats card-round">
                <div class="card-body">
                  <div class="row">
                    <div class="col-5">
                      <div class="icon-big text-center">
                        <i class="icon-pie-chart text-warning"></i>
                      </div>
                    </div>
                    <?php include './statistique.php' ?>
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-body">
                          <!-- Table responsive pour les grands écrans -->
                          <div class="table-responsive d-none d-md-block">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                              <thead>
                                <tr>
                                  <th>MOTIF</th>
                                  <th>Cotisation Concernée</th>
                                  <th>MONTANT RETIRE</th>
                                  <th>DATE RETRAIT</th>
                                  <th>Actions</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php if ($values) {
              foreach ($values as $value) { ?>
                                <tr>
                                  <td><?= $value['description'] ?></td>
                                  <td><?= $value['motif_acotisation'] ?></td>
                                  <td><?= $value['montant'] ?></td>
                                  <td><?= date_format(date_create($value['date_retrait']), "d-m-Y") ?></td>
                                  <td>
                                    <div class="btn-group" role="group" aria-label="Actions">
                                      <!-- Afficher le bouton Voir seulement si l'ID est disponible -->
                                      <?php if (!empty($value['id_rc'])) { ?>

                                      <?php } ?>
                                      <a href="./modifierRetrait.php?id=<?= $value['id_rc'] ?>"
                                        class="btn btn-outline-warning" title="Modifier">
                                        <i class="far fa-edit" style="font-size: 20px;"></i>
                                      </a>
                                      <a href="./deleteRetrait.php?id=<?= $value['id_rc'] ?>"
                                        class="btn btn-outline-danger" title="Supprimer"
                                        onclick="return confirm('Voulez-vous vraiment effectuer la suppression?')">
                                        <i class="far fa-trash-alt"></i>
                                      </a>
                                    </div>
                                  </td>
                                </tr>
                                <?php }
            } else { ?>
                                <tr>
                                  <td colspan="5" class="text-center">Aucune information disponible</td>
                                </tr>
                                <?php } ?>
                              </tbody>
                            </table>
                          </div>

                          <!-- Version mobile : transformation en cartes -->
                          <div class="d-block d-md-none">
                            <?php if ($values) {
          foreach ($values as $value) { ?>
                            <div class="card mb-3">
                              <div class="card-body">
                                <h5 class="card-title"><?= $value['description'] ?></h5>
                                <p class="card-text">Cotisation Concernée: <?= $value['motif_acotisation'] ?></p>
                                <p class="card-text">Montant retiré: <?= $value['montant'] ?></p>
                                <p class="card-text">Date de retrait:
                                  <?= date_format(date_create($value['date_retrait']), "d-m-Y") ?></p>
                                <div class="btn-group" role="group" aria-label="Actions">
                                  <!-- Afficher le bouton Voir seulement si l'ID est disponible -->
                                  <?php if (!empty($value['id_rc'])) { ?>

                                  <?php } ?>
                                  <a href="./modifierRetrait.php?id=<?= $value['id_rc'] ?>"
                                    class="btn btn-outline-warning" title="Modifier">
                                    <i class="far fa-edit" style="font-size: 20px;"></i>
                                  </a>
                                  <a href="./deleteRetrait.php?id=<?= $value['id_rc'] ?>" class="btn btn-outline-danger"
                                    title="Supprimer"
                                    onclick="return confirm('Voulez-vous vraiment effectuer la suppression?')">
                                    <i class="far fa-trash-alt"></i>
                                  </a>
                                </div>
                              </div>
                            </div>
                            <?php }
        } else { ?>
                            <p class="text-center">Aucune information disponible</p>
                            <?php } ?>
                          </div>

                       

                        </div>
                      </div>
                    </div>

                  </div>

                </div>
              </div>

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
               <script>
      $(document).ready(function () {
        $("#basic-datatables").DataTable({});

        $("#multi-filter-select").DataTable({
          pageLength: 5,
          initComplete: function () {
            this.api()
              .columns()
              .every(function () {
                var column = this;
                var select = $(
                  '<select class="form-select"><option value=""></option></select>'
                )
                  .appendTo($(column.footer()).empty())
                  .on("change", function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column
                      .search(val ? "^" + val + "$" : "", true, false)
                      .draw();
                  });

                column
                  .data()
                  .unique()
                  .sort()
                  .each(function (d, j) {
                    select.append(
                      '<option value="' + d + '">' + d + "</option>"
                    );
                  });
              });
          },
        });

        // Add Row
        $("#add-row").DataTable({
          pageLength: 5,
        });

        var action =
          '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

        $("#addRowButton").click(function () {
          $("#add-row")
            .dataTable()
            .fnAddData([
              $("#addName").val(),
              $("#addPosition").val(),
              $("#addOffice").val(),
              action,
            ]);
          $("#addRowModal").modal("hide");
        });
      });
    </script>
              

</body>

</html>
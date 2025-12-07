<?php
function renderLayout($content, $script) {
    ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>E-JurnalGuru</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />

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

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="style1.css" />
    <!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  </head>
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar sidebar-style-2" data-background-color="light">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="light">
            <a href="index.php" class="logo">
              <img
                src="assets/img/image-removebg-preview.png"
                alt="navbar brand"
                class="navbar-brand"
                height="40"
              />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right" style="background: rgba(131,132,138,.89)"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left" style="background: rgba(131,132,138,.89)"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt" style="background: rgba(131,132,138,.89)"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item">
                <a
                  href="index.php"
                >
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <?php if($_SESSION['role']=="Guru") : ?>
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-graduation-cap"></i>
                </span>
                <h4 class="text-section">Guru</h4>
              </li>
              <li class="nav-item">
                <a href="form_jurnal.php">
                  <i class="fas fa-file-invoice"></i>
                  <p>Form Jurnal</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="riwayat_jurnal.php">
                  <i class="fas fa-book-open"></i>
                  <p>Riwayat Jurnal</p>
                </a>
              </li>
              <?php endif; ?>
              <?php if($_SESSION['role']=="Pengawas") : ?>
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-eye"></i>
                </span>
                <h4 class="text-section">Pengawas</h4>
              </li>
              <li class="nav-item">
                <a href="validasi.php">
                  <i class="fas fa-check-circle"></i>
                  <p>Validasi Jurnal</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="rekapitulasi.php">
                  <i class="fas fa-book"></i>
                  <p>Rekapitulasi</p>
                </a>
              </li>
              <?php endif; ?>
              <?php if($_SESSION['role']=="Admin") : ?>
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-headset"></i>
                </span>
                <h4 class="text-section">Admin</h4>
              </li>
              <li class="nav-item">
                <a href="daftarpengguna.php">
                  <i class="fas fa-book-reader"></i>
                  <p>Daftar Pengguna</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="datasekolah.php">
                  <i class="fas fa-school"></i>
                  <p>Data Sekolah</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="datasiswa.php">
                  <i class="fas fa-user-graduate"></i>
                  <p>Data Siswa</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="laporan.php">
                  <i class="fas fa-print"></i>
                  <p>Laporan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="statistik.php">
                  <i class="fas fa-chart-bar"></i>
                  <p>Statistik</p>
                </a>
              </li>
              <?php endif; ?>
              <li class="nav-section">
                <h4 class="text-section"></h4>
              </li>
              <li class="nav-item">
                <a href="tampilanprofil.php">
                  <i class="fas fa-user-circle"></i>
                  <p>Profil</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="logout.php">
                  <i class="fas fa-sign-out-alt"></i>
                  <p>Logout</p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->

      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="light">
              <a href="index.php" class="logo">
                <img
                  src="assets/img/image-removebg-preview.png"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right" style="color: rgba(131,132,138,.89)"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left" style="color: rgba(131,132,138,.89)"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt" style="color: rgba(131,132,138,.89)"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>
          <!-- Navbar Header -->
          <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">
              <nav
                class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
              >
              <h3 style="color: black">Halo, <?= $_SESSION['user']['nama_user'] ?>!</h3>
              </nav>

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li
                  class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none"
                >
                  <a
                    class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"
                    href="#"
                    role="button"
                    aria-expanded="false"
                    aria-haspopup="true"
                  >
                    <i class="fa fa-search"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                      <div class="input-group">
                        <input
                          type="text"
                          placeholder="Search ..."
                          class="form-control"
                        />
                      </div>
                    </form>
                  </ul>
                </li>
                

                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    
                    <div class="avatar-sm">
                      <i class="fas fa-user" style="font-size: 25px;"></i> <!-- Perbesar ikon -->
                    </div>
                    
                      <span class="profile-username" style="font-size: 12px;"> <!-- Perbesar teks -->
                        <span class="op-7"><?= $_SESSION['role'] ?></span>
                      </span>
                    
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                            <i class="fas fa-user"></i>

                          </div>
                          <div class="u-text">
                            <h4><?= $_SESSION['user']['nama_user'] ?></h4>
                            <p class="text-muted"><?= $_SESSION['user']['email'] ?></p>
                            <a
                              href="tampilanprofil.php"
                              class="btn btn-xs btn-secondary btn-sm"
                              >View Profile</a
                            >
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>
        
        <?= $content ?>

        <!-- Footer -->
        <footer class="footer">
          <div class="container-fluid d-flex justify-content-between">
            <nav class="pull-left">
              <ul class="nav">
                <li class="nav-item">
                  <a class="nav-link" href="#"> Help </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#"> Licenses </a>
                </li>
              </ul>
            </nav>
            <div class="copyright">
              2025, made by Kelompok 6
            </div>
            <div>
              For "Rekayasa Perangkat Lunak" Project.
            </div>
          </div>
        </footer>
      </div>
    </div>

    <!--   Core JS Files   -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="assets/js/plugin/jsvectormap/world.js"></script>

    <!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Kaiadmin JS -->
    <script src="assets/js/kaiadmin.min.js"></script>

    <!-- Javascript -->
    <?= $script ?>
  </body>
</html>
    <?php
}
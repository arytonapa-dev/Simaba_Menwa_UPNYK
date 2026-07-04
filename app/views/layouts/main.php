<?php
/**
 * Main Layout Shell (Authenticated views)
 * Bootstrap 5 (SIMBA v3.0)
 */
$currentUser = Auth::user();
$roleId = $currentUser['role_id'] ?? 0;
$fullName = $currentUser['full_name'] ?? 'Pengguna';
$username = $currentUser['username'] ?? '';
$photo = $currentUser['photo'] ?? '';
$profileImg = (!empty($photo) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/uploads/profil/' . $photo)) 
              ? 'uploads/profil/' . $photo 
              : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($fullName);

$currentController = $_GET['controller'] ?? 'dashboard';
$currentAction = $_GET['action'] ?? 'index';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $title ?? 'SIMBA | Admin Dashboard' ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css?v=<?= time() ?>" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            background-color: #fff;
            border-right: 1px solid #E5E7EB;
            overflow-y: auto;
            transition: all 0.3s;
        }
        
        .main-wrapper {
            margin-left: 280px;
            min-height: 100vh;
        }
        
        .main-content {
            margin-left: 0 !important;
            margin-top: 0 !important;
            padding: 96px 24px 32px 24px !important;
        }
        
        .topbar {
            height: 72px;
            background-color: #fff;
            border-bottom: 1px solid #E5E7EB;
            position: fixed;
            top: 0;
            right: 0;
            left: 280px;
            z-index: 1030;
        }
        
        .nav-link.active {
            background-color: rgba(46, 125, 50, 0.1);
            color: #2E7D32 !important;
            border-left: 4px solid #2E7D32;
            font-weight: 600;
        }
        
        .nav-link {
            color: #40493d;
            border-radius: 0 0.5rem 0.5rem 0;
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
        }
        
        .nav-link:hover:not(.active) {
            background-color: #f1f3ff;
        }
        
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0 !important;
            }
            .topbar {
                left: 0;
            }
        }
    </style>
</head>
<body>

<!-- SideNavBar -->
<aside class="sidebar d-flex flex-column py-4" id="sidebar">
    <div class="px-4 mb-4 d-flex align-items-center gap-3">
        <div class="bg-success rounded p-2 d-flex align-items-center justify-content-center">
            <i class="fa-solid fa-medal fs-4"></i>
        </div>
        <div>
            <h1 class="font-headline fw-bold text-success mb-0 fs-4">SIMBA</h1>
            <p class="text-muted small fw-bold mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">Military Logistics</p>
        </div>
    </div>
    
    <?php if ($roleId == ROLE_ANGGOTA): ?>
    <div class="px-3 mb-4">
        <a href="index.php?controller=peminjaman&action=ajukan" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2 fw-bold shadow-sm">
            <i class="fa-solid fa-plus"></i> New Request
        </a>
    </div>
    <?php endif; ?>
    
    <nav class="flex-grow-1 px-2 overflow-y-auto">
        <?php
        function renderSidebarItem($href, $icon, $label, $isActive) {
            $activeClass = $isActive ? 'active' : '';
            return '<a href="'.$href.'" class="nav-link text-decoration-none d-flex align-items-center gap-3 '.$activeClass.'">
                        <i class="fa-solid fa-'.$icon.' fa-fw"></i>
                        <span>'.$label.'</span>
                    </a>';
        }
        
        echo renderSidebarItem('index.php?controller=dashboard&action=index', 'chart-pie', 'Overview', $currentController === 'dashboard');
        
        if ($roleId == ROLE_ADMIN) {
            echo renderSidebarItem('index.php?controller=user&action=index', 'users', 'User Management', $currentController === 'user');
            echo renderSidebarItem('index.php?controller=kategori&action=index', 'tags', 'Kategori', $currentController === 'kategori');
            echo renderSidebarItem('index.php?controller=bidang&action=index', 'sitemap', 'Bidang', $currentController === 'bidang');
            echo renderSidebarItem('index.php?controller=audit&action=index', 'clock-rotate-left', 'Audit Trail', $currentController === 'audit');
        } elseif ($roleId == ROLE_OPERATOR) {
            echo renderSidebarItem('index.php?controller=barang&action=index', 'boxes-stacked', 'Inventory', $currentController === 'barang');
            echo renderSidebarItem('index.php?controller=unit&action=index', 'cube', 'Units', $currentController === 'unit');
            echo renderSidebarItem('index.php?controller=peminjaman&action=verifikasiList', 'right-left', 'Transactions', $currentController === 'peminjaman' || $currentController === 'pengembalian');
            echo renderSidebarItem('index.php?controller=laporan&action=index', 'file-lines', 'Reports', $currentController === 'laporan');
        } elseif ($roleId == ROLE_ANGGOTA) {
            echo renderSidebarItem('index.php?controller=peminjaman&action=ajukan', 'hand-holding-hand', 'Request Loan', $currentController === 'peminjaman' && $currentAction === 'ajukan');
            echo renderSidebarItem('index.php?controller=pengembalian&action=ajukan', 'rotate-left', 'Request Return', $currentController === 'pengembalian');
            echo renderSidebarItem('index.php?controller=peminjaman&action=riwayat', 'clock-rotate-left', 'History', $currentController === 'peminjaman' && $currentAction === 'riwayat');
        } elseif ($roleId == ROLE_DANSAT) {
            echo renderSidebarItem('index.php?controller=peminjaman&action=verifikasiKritisList', 'clipboard-check', 'Approvals', $currentController === 'peminjaman');
            echo renderSidebarItem('index.php?controller=laporan&action=index', 'file-lines', 'Reports', $currentController === 'laporan');
        }
        ?>
    </nav>
    
    <div class="px-2 mt-auto pt-3 border-top mx-3">
        <a href="index.php?controller=auth&action=logout" onclick="return confirm('Apakah Anda yakin ingin keluar?');" class="nav-link text-decoration-none text-danger d-flex align-items-center gap-3 mt-1">
            <i class="fa-solid fa-right-from-bracket fa-fw"></i>
            <span class="fw-bold">Log Out</span>
        </a>
    </div>
</aside>

<!-- Main Content Canvas -->
<div class="main-wrapper d-flex flex-column">
    
    <!-- TopNavBar -->
    <header class="topbar d-flex justify-content-between align-items-center px-4 shadow-sm m-0">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-md-none" id="sidebarToggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <nav class="d-none d-md-flex align-items-center gap-2 small text-muted">
                <span>SIMBA</span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
                <span class="fw-bold text-success text-capitalize"><?= $currentController ?></span>
            </nav>
        </div>
        
        <div class="d-flex align-items-center gap-4">
            <div class="d-flex align-items-center gap-3">
                <a href="index.php?controller=notifikasi" class="btn btn-light rounded-circle position-relative p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; text-decoration: none;">
                    <i class="fa-regular fa-bell fs-5 text-dark"></i>
                    <?php 
                    require_once dirname(dirname(__DIR__)) . '/models/Notifikasi.php';
                    $notifModel = new Notifikasi();
                    $unreadCount = $notifModel->getUnreadCount($_SESSION['user_id'] ?? 0);
                    if ($unreadCount > 0): 
                    ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem;">
                            <?= $unreadCount > 99 ? '99+' : $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <div class="vr mx-1" style="height: 24px;"></div>
                
                <a href="index.php?controller=user&action=profil" class="d-flex align-items-center gap-3 cursor-pointer text-decoration-none" id="userProfileBtn">
                    <div class="text-end d-none d-sm-block">
                        <p class="mb-0 fw-bold small lh-1 text-dark"><?= htmlspecialchars($fullName) ?></p>
                        <p class="mb-0 text-muted text-uppercase fw-bold" style="font-size: 0.65rem;"><?= ROLE_NAMES[$roleId] ?? 'USER' ?></p>
                    </div>
                    <img class="rounded-circle object-fit-cover border border-2 border-success hover-zoom" src="<?= $profileImg ?>" width="40" height="40" alt="Profile">
                </a>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="main-content flex-grow-1 p-4">
        <div class="container-fluid max-w-7xl px-0">
            <?= $content ?>
        </div>
    </main>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Global fix for Bootstrap Modals inside transformed elements
        // Move all modals to the end of the body to prevent z-index freeze issues
        document.querySelectorAll('.modal').forEach(function(modal) {
            document.body.appendChild(modal);
        });

        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        
        if(sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
    });
</script>
</body>
</html>

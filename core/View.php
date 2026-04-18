<?php
class View {
    public static function renderAuth($viewPath, $data = []) {
        $ENTITY = getEntityLabel();
        $APP_NAME = getSetting('app_name', 'The Digital Curator');
        extract($data);
        require_once "views/layouts/header.php";
        require_once "views/{$viewPath}.php";
        require_once "views/layouts/footer.php";
    }

    public static function renderRaw($viewPath, $data = []) {
        $ENTITY = getEntityLabel();
        $APP_NAME = getSetting('app_name', 'The Digital Curator');
        extract($data);
        require_once "views/{$viewPath}.php";
    }

    public static function renderPublic($viewPath, $data = []) {
        $ENTITY = getEntityLabel();
        $APP_NAME = getSetting('app_name', 'The Digital Curator');
        extract($data);
        require_once "views/layouts/public_header.php";
        require_once "views/{$viewPath}.php";
        require_once "views/layouts/public_footer.php";
    }

    public static function renderAdmin($viewPath, $data = []) {
        $ENTITY = getEntityLabel();
        $APP_NAME = getSetting('app_name', 'The Digital Curator');
        extract($data);
        require_once "views/layouts/header.php";
        require_once "views/layouts/admin_sidebar.php";
        
        echo '<div class="ml-[260px] min-h-screen flex flex-col">';
        require_once "views/layouts/admin_topbar.php";
        
        if (isset($_SESSION['is_active_periode']) && $_SESSION['is_active_periode'] === false) {
            echo '<div class="bg-amber-100 border-l-4 border-amber-500 text-amber-700 p-4 sticky top-16 z-30" role="alert">
                    <p class="font-bold"><span class="material-symbols-outlined text-sm align-middle mr-1">warning</span> Mode Arsip (Read-Only)</p>
                    <p class="text-sm">Anda sedang melihat data dari periode masa lalu. Beberapa fitur manajemen dinonaktifkan.</p>
                  </div>';
        }

        
        require_once "views/{$viewPath}.php";
        
        require_once "views/layouts/admin_footer.php";
        echo '</div>';
        
        require_once "views/layouts/footer.php";
    }
}

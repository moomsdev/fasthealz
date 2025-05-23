<?php

use Duplicator\Core\Controllers\ControllersManager;

defined('ABSPATH') || defined('DUPXABSPATH') || exit;
wp_enqueue_script('dup-handlebars');
require_once(DUPLICATOR_PLUGIN_PATH . '/classes/utilities/class.u.scancheck.php');
require_once(DUPLICATOR_PLUGIN_PATH . '/classes/class.io.php');

$installer_files = DUP_Server::getInstallerFiles();
$package_name    = (isset($_GET['package'])) ?  esc_html($_GET['package']) : '';
$abs_path        = duplicator_get_abs_path();

// For auto detect archive file name logic
if (empty($package_name)) {
    $installer_file_path = $abs_path . '/' . 'installer.php';
    if (file_exists($installer_file_path)) {
        $installer_file_data = file_get_contents($installer_file_path);
        if (preg_match("/const ARCHIVE_FILENAME	 = '(.*?)';/", $installer_file_data, $match)) {
            $temp_archive_file      = esc_html($match[1]);
            $temp_archive_file_path = $abs_path . '/' . $temp_archive_file;
            if (file_exists($temp_archive_file_path)) {
                $package_name = $temp_archive_file;
            }
        }
    }
}

$package_path = empty($package_name) ? '' : $abs_path . '/' . $package_name;
$txt_found    = __('File Found: Unable to remove', 'duplicator');
$txt_removed  = __('Removed', 'duplicator');
$nonce        = wp_create_nonce('duplicator_cleanup_page');
$section      = (isset($_GET['section'])) ? $_GET['section'] : '';

if ($section == "info" || $section == '') {
    $_GET['action'] = isset($_GET['action']) ? $_GET['action'] : '';

    switch ($_GET['action']) {
        case 'tmp-cache':
            if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'duplicator_cleanup_page')) {
                exit; // Get out of here bad nounce!
            }
            DUP_Package::tempFileCleanup(true);
            ?>
            <div id="message" class="notice notice-success is-dismissible  dup-wpnotice-box">
                <p><b><?php _e('Build cache removed.', 'duplicator'); ?></b></p>
            </div>
            <?php
            break;
    }
}
$actionUrl = ControllersManager::getMenuLink(
    ControllersManager::TOOLS_SUBMENU_SLUG,
    'diagnostics',
    null,
    ['section' => 'info']
);
?>

<form id="dup-settings-form" action="<?php echo esc_url($actionUrl); ?>" method="post">
    <?php
    wp_nonce_field('duplicator_settings_page', '_wpnonce', false);
    include_once 'inc.data.php';
    include_once 'inc.settings.php';
    include_once 'inc.validator.php';
    include_once 'inc.phpinfo.php';
    ?>
</form>

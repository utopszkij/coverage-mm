<?php
/**
* Plugin Name: Coverage monitoring & marketing
* Plugin URI: http://www.github.com/utopszkij/coverage-mm
* Description: Kiegészítés woocommerce -hez
* Version: 1.00
* Requires at least: 4.4 
* Requires PHP:      7.2
* Author: Fogler Tibor
* Author URI: http://www.github.com/utopszkij
* Text Domain:       cmm
* Domain Path:       /languages
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/
if (!session_id()) {
     session_start();
}

define('CMM','coverage_mm'); // must eq the plugin dir name !!!!

function isPluginActive( $plugin ) {
    return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
                    isPluginActiveNetwork($plugin); 
}
function isPluginActiveNetwork( $plugin ) {
    if ( ! is_multisite() ) {
        return false;
    }
    $plugins = get_site_option( 'active_sitewide_plugins' );
    if ( isset( $plugins[ $plugin ] ) ) {
        return true;
    }
    return false;
}

error_reporting(E_ERROR | E_PARSE);

add_action('admin_init','cmm_plugin_admin_init');
// add_action('init','cmm_plugin_init');
function cmm_plugin_admin_init() {
    // check required plugins
    if ((isPluginActive('woocommerce/woocommerce.php')) &
        (isPluginActive('advanced-custom-fields/acf.php')) &
        (isPluginActive('ultimate-maps-by-supsystic/ums.php'))
       ) {
    } else {
        echo '<script>
                alert("Covareg-mm error. \n woocommerce and/or advanced-custom-fields and/or ultimate-maps-by-supsystic plugin not active!");
              </script>';
        return;
    } 
    // load WPML translate file
    load_plugin_textdomain( CMM, false, basename( dirname( __FILE__ ) ) . '/languages' );
    // load plugin css
    wp_enqueue_style( 'style', get_site_url().'/wp-content/plugins/coverage_mm/css/coverage_mm.css');
    // load plugin javascript
    wp_enqueue_script( 'custom_js', plugins_url( '/js/coverage_mm.js', __FILE__ ));
    
    // extend product_cat taxonomy
    include_once  __DIR__.'/models/model.area.php';
    include_once  __DIR__.'/models/model.map.php';
    $areaModel = new AreaModel(false);
    $areaModel->checkDatabase();

    // create default map if not exists
    $mapModel = new MapModel(false);
    $mapRec = $mapModel->getMapByTitle('default');
    if (!$mapRec) {
        $mapModel->addDefaultMap('default', 45, 19, 5);
    }
    
    // product_cat add and edit admin form extend for leaflet API for openstreetmap server
    add_action('product_cat_add_form_fields', 'cmm_extend_category_form', 10, 0);
    add_action('product_cat_edit_form_fields', 'cmm_extend_category_form', 10, 1);
    function cmm_extend_category_form() {
        ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
            integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
                crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>
        <script type="text/javascript">
        	var defMap = {"lat":47.107, "lng":19.111, "zoom":7};
        	var cmmMapKey = "<?php echo get_option('cmmMapKey', null);?>";
        </script>    
        <?php
    }
    
    // product_cat save to database extend for openstreetmap
    // create default map if not exists and store map_id into ACF field
    add_action('edited_product_cat', 'cmm_extend_category_save', 10, 1);
    add_action('create_product_cat', 'cmm_extend_category_save', 10, 1);
    function cmm_extend_category_save($term_id) {
        $mapModel = new MapModel(false);
        if (!$mapModel->createOrUpdateMap($term_id)) {
            echo 'fatal error in create or update map '.$mapModel->getErrorMsg(); exit();
        }
    }
}

add_action('init','cmm_plugin_init');
function cmm_plugin_init() {
    // cmm_map shortcode
    add_shortcode('cmm_map', 'cmm_map_shortcode');
    function cmm_map_shortcode(array $atts):string {
        $atts2 = shortcode_atts(['map_id' => 0, 'markers' => ''],$atts, 'cmm_map');
        include_once __DIR__.'/controllers/class.map.php';
        $controller = new MapController();
        return $controller->getMapHtml($atts2['map_id'], $atts2['markers']);
    }
}

/**
 * WPML integration into ACF
 */
add_action('acf/prepare_field', 'my_acf_prepare_field');
function my_acf_prepare_field($field ) {
    $field['label'] = __($field['label'],CMM);
    if (is_array($field['choices'])) {
        foreach ($field['choices'] as $fn => $fv) {
            $field['choices'][$fn] = __($field['choices'][$fn],CMM);
        }
    }
    return $field;
}

/**
 * build WP admin menu
 */
add_action('admin_menu', 'cmm_plugin_create_menu');
function cmm_plugin_create_menu() {
	add_menu_page('covergae-mm' ,'Coverage Monitoring & Market','manage_options',
   	         'cmm-admin','cmm_adminMenu','', 11 );
	add_submenu_page('cmm-admin', 'Areas', 'Areas', 'manage_options',
	            'cmm-areas','cmm_areaAdminMenu', 1);
	add_submenu_page('cmm-admin', 'Sales', 'Sales', 'manage_options',
	            'cmm-sales','cmm_salesAdminMenu', 2);
	add_submenu_page('cmm-admin', 'Products', 'Products', 'manage_options',
	            'cmm-products','cmm_productsAdminMenu', 3);
	add_submenu_page('cmm-admin', 'Categories', 'Categories', 'manage_options',
	            'cmm-categories','cmm_categoriesAdminMenu', 4);
	add_submenu_page('cmm-admin', 'Distributors', 'Distributors', 'manage_options',
	            'cmm-distributors','cmm_distributorsAdminMenu', 5);
	add_submenu_page('cmm-admin', 'Agents', 'Agents', 'manage_options',
	            'cmm-agents','cmm_agentsAdminMenu', 6);
	add_submenu_page('cmm-admin', 'Ranks', 'Ranks', 'manage_options',
	            'cmm-ranks','cmm_ranksAdminMenu', 7);
	add_submenu_page('cmm-admin', 'MyAccount', 'MyAccount', 'manage_options',
	            'cmm-myaccount','cmm_myaccountAdminMenu', 8);
	add_submenu_page('cmm-admin', 'Settings', 'Settings', 'manage_options',
	            'cmm-settings','cmm_settingsAdminMenu', 9);
}
function cmm_adminMenu() {
    include_once __DIR__.'/controllers/class.admin.php';
    $controller = new AdminController();
    $controller->adminForm();
}
function cmm_areaAdminMenu() {
    include_once __DIR__.'/controllers/class.area.php';
    $controller = new AreaController();
    $controller->adminForm();
}
function cmm_productsAdminMenu() {
    include_once __DIR__.'/controllers/class.product.php';
    $controller = new ProductController();
    $controller->adminForm();
}
function cmm_categoriesAdminMenu() {
    include_once __DIR__.'/controllers/class.category.php';
    $controller = new CategoryController();
    $controller->adminForm();
}
function cmm_distributorsAdminMenu() {
    include_once __DIR__.'/controllers/class.distributor.php';
    $controller = new DistributorController();
    $controller->adminForm();
}
function cmm_agentsAdminMenu() {
    include_once __DIR__.'/controllers/class.agent.php';
    $controller = new AgentController();
    $controller->adminForm();
}
function cmm_ranksAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Ranks</h2>
	<p>A terjesztők, ügynökök, vásárlók aktivitásuk, teljesítményük alapján "rangot" kapnak. Ennek részletei még tisztázandók</p>	
	';
}
function cmm_myaccountAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>My Account</h2>
	<p>A képernyőn a user saját adatlapja jelenik meg</p>
	<ul>
		<li>Felhasználó személyes adatai (id, nick, név, email ....</li>
		<li>felhasználó elért rangja</li>
		<li>Ügynöki szerepei (azon ügynök objektumok listája ahol Ő az ügynök)</li>
		<li>Terjesztői szerepei (azon terjesztői objektumok listája ahol Ő a terjesztő)</li>
		<li>Vásárlásai (azon vásárlások listája ahol Ő a vásárló)</li>
	</ul>	
	<p>A ügynök, terjesztő, termék listák elemeire rá lehet kattintani, ekkor az adott objektum admin oldala jelenik meg
	filterezve az adott userhez tartozó adatokra.</p>
	';
}
function cmm_settingsAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Settings</h2>
	Mértékegységek és ezek átváltási szorzói
	';
}
function cmm_salesAdminMenu() {
    include_once __DIR__.'/controllers/class.sale.php';
    $controller = new SaleController();
    $controller->adminForm();
}

?>
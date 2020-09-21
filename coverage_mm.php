<?php
/**
* Plugin Name: Coverage monitoring & marketing
* Plugin URI: http://www.github.com/utopszkij/coverage-mm
* Description: Kiegészítés woocommerce -hez
* Version: 1.00.01
* Requires at least: 4.4 
* Requires PHP:      7.2
* Author: Fogler Tibor
* Author URI: http://www.github.com/utopszkij
* Text Domain:       cmm
* Domain Path:       /languages
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/


/**
 * cmm setup opciók:
 * - myCart URL   lehet hogy : $string = wc_get_cart_url(); megoldja?
 * - override "return_to_shop" buttons?
 * - hide shipping address fields?
 * 
 * shortcode -ok
 * [cmm_shop filters=]  -- saját shop képernyő (area select, product select, add to cart, checkout)
 * [cmm_areaselect filters] -- area selector box 
 * [cmm_productselect filters] -- product selector box
 * [cmm_map area_id=# | map_id=# markers=marker;marker]
 * [cmm_report .......]
 *     filters: "filter; filter ....."
 *     filter:  "fiterName:filterValue"
 *     filterValue: érték | érték,érték,...
 *     
 *     filterNmes: 
 *     agent, agent_type 
 *     distributor,  distributor_type
 *     maximum_ge, panned_ge, realised_ge,  
 *     maximum_le, panned_le, realised_le   
 *     
 *     marker: marker_id,lat,len
 *
 *    ha kell widget írás help:  
 *    https://www.wpbeginner.com/wp-tutorials/how-to-create-a-custom-wordpress-widget/
 *     
 */

global $cmm;
define('CMM','coverage_mm'); // must eq the plugin dir name !!!!

/*
 * cmm create, use cookie must before send html head
 */
add_action( 'send_headers', 'cmm_create' );
function cmm_create() {
    global $cmm;
    $cmm = new Cmm();
}

/**
 * cmm rooter object, controller creation and session processing
 */
class Cmm {
    protected $cmm_cookie;
    protected $sessionBuffer = false;
    
    function __construct() {
        global $wpdb;

        if (isset($_COOKIE['cmm_cookie'])) {
            $this->cmm_cookie = $_COOKIE['cmm_cookie'];
        } else {
            $this->cmm_cookie = date('YmdHisu').rand(123456,999999);
        }
        $_COOKIE['cmm_cookie'] = $this->cmm_cookie;
        
        // create session table if not exists
        if (!$wpdb->query('CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'cmm_sessions (
            session_id varchar(32),
            name varchar(32),
            value text,
            expire varchar(32),
            key session_id_ind (session_id)
            )
        ')) {
            $this->errorExit($wpdb->last_error);
        };
        
        // delete old sessionvars from database
        $wpdb->query('DELETE FROM '.$wpdb->prefix.'cmm_sessions  
        WHERE expire < "'.strtotime('-1 day').'"');
        if ($wpdb->last_error != '') {
            $this->errorExit($wpdb->last_error);
        }
        
        // get session vars from database into $this->sessionBuffer
        $res = $wpdb->get_row('SELECT * 
        FROM '.$wpdb->prefix.'cmm_sessions 
        WHERE session_id = "'.$this->cmm_cookie.'"');
        if ($res) {
            $this->sessionBuffer = JSON_decode($res->value);
        } else {
            $this->sessionBuffer = new stdClass();
        }
    }

    function __destruct() {
        global $wpdb;
        
        // save $this->sessionBuffer into database
        $rec = [];
        $rec["session_id"] = $this->cmm_cookie;
        $rec["name"] = "data";
        $rec["value"] = JSON_encode($this->sessionBuffer);
        $rec["expire"] = strtotime('+1 day');
        $res = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'cmm_sessions
                WHERE session_id = "'.$this->cmm_cookie.'" AND name = "data"');
        if ($wpdb->last_error != '') {
            $this->errorExit(' 2 '.$wpdb->last_error);
        }
        if ($res) {
            if (!$wpdb->update($wpdb->prefix.'cmm_sessions', $rec,
                ["session_id" => $this->cmm_cookie, "name" => data])) {
                    $this->errorExit($wpdb->last_error);
            }
        } else {
            if (!$wpdb->insert($wpdb->prefix.'cmm_sessions', $rec)) {
                    $this->errorExit($wpdb->last_error);
            }
        }
    }
    
    /**
     * echo error message and exit wp
     * @param string $msg
     */
    public function errorExit(string $msg) {
        echo '<p>cmm Fatal error '.$msg.'</p>'; exit();
    }
    
    /**
     * get value from session table
     * @param string $name
     * @param string $default
     * @return mixed
     */
    public function getFromSession(string $name, $default='') {
        if (isset($this->sessionBuffer->$name)) {
            $result = $this->sessionBuffer->$name;
        } else {
            $result = $default;
        }
        return $result;
    }
    
    /**
     * set value into session table
     * @param string $name
     * @param unknown $value
     * @return unknown
     */
    public function setToSession(string $name, $value) {
        $this->sessionBuffer->$name = $value;
        return;
    }
    
    /**
     * delete value from session table
     * @param string $name
     */
    public function deleteFromSession(string $name) {
        unset($this->sessinBuffer->$name);
    }
    
    /**
     * include and create controller
     * @param string $name
     * @return unknown|boolean
     */
    public function getController(string $name) {
        if (file_exists(__DIR__.'/controllers/class.'.$name.'.php')) {
            include_once __DIR__.'/controllers/class.'.$name.'.php';
            $className = ucFirst($name).'Controler';
            return new $className ();
        } else {
            return false;
        }
    }
}

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

    // create default map if not exists ======== nem biztos, hogy kell ==========
    $mapModel = new MapModel(false);
    $mapRec = $mapModel->getMapByTitle('default');
    if (!$mapRec) {
        $mapModel->addDefaultMap('default', 47.1673, 19.4348, 7);
    }
    
    // product_cat add and edit admin form extend for leaflet API for openstreetmap server
    // ===== azt hiszem ez nem fog kelleni, viszont az area adminhoz kell majd =====================
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
        	var defMap = {"lat":47.1673, "lng":19.4348, "zoom":7};
        	var cmmMapKey = "<?php echo get_option('cmmMapKey', null);?>";
        </script>    
        <?php
        
    }
    
    
    // product_cat save to database and delete from datavbase
    // extend for openstreetmap
    add_action('edited_product_cat', 'cmm_extend_category_save');
    add_action('create_product_cat', 'cmm_extend_category_save');
    // add_action('woocommerce_api_create_product_category','cmm_extend_category_save',10,2);
    function cmm_extend_category_save($term_id = 0, $tt_id = 0) {
        // create new map record  if not exists,
        //  - and update map_id in database' areaRecord 
        /* ez igy nem jó rekurzivan önmagát hívja vissza
        $mapModel = new MapModel(false);
        if (!$mapModel->createOrUpdateMap($term_id)) {
            echo 'fatal error in create or update map '.$mapModel->getErrorMsg(); exit();
        }
        */
    }
    
} // admin_init


add_action('init','cmm_plugin_init');
function cmm_plugin_init() {
    
    // global $cmm;
    // $cmm = new Cmm();
    
    // cmm_map shortcode
    add_shortcode('cmm_map', 'cmm_map_shortcode');
    function cmm_map_shortcode(array $atts):string {
        $atts2 = shortcode_atts(['map_id' => 0, 'markers' => ''],$atts, 'cmm_map');
        include_once __DIR__.'/controllers/class.map.php';
        $controller = new MapController();
        return $controller->getMapHtml($atts2['map_id'], $atts2['markers']);
    }
    
    // ===== ez csak egy test végül nem kell ====================
    add_shortcode('cmm_test', 'cmm_test_shortcode');
    function cmm_test_shortcode():string {
        WC()->cart->add_to_cart( 140, 13 ); //  EZ IS MÜKÖDIK !
        return 'cmm_tes end';
    }
    
    add_action('woocommerce_cart_is_empty','cmm_cart_is_empty');
    function cmm_cart_is_empty() {
        echo '<p>=========== cmm_cart_is_empty ================</p>';
        // itt kellene a "vissza a shop -ba " gombot szükség esetén átdefiniálni
        // a.wc-backward
    }
    
    add_action('woocommerce_before_checkout_form','cmm_before_checkout_form');
    function cmm_before_checkout_form() {
        echo '<p>=========== cmm_before checkout form ================</p>';
        // itt kellene szükség esetén a szállítási cím egyes mezőit elrejteni és
        // kezdőértékekel feltölteni
        /*    input             p0
        #shiiping_last_name    _field kötelező
        #shipping_first_name   -field kötelező
        #shipping_company      -field
        #shipping_country      -field kötelező
        #shipping_postcode     -field kötelező
        #shipping_city         -field kötelező
        #shipping_address_1    -field kötelező
        #shipping_address_2    -field
        #shipping_state        -field    megyék kétbetüs röviditése?
        */
        
    }
    
}


/**
 * WPML integration into ACF ========= azt hiszem nem fogom használni az ACF -et
 *
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
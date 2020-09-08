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


define('CMM','cmm');

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
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Areas</h2>
	
	<p>A képernyőn a területek listája jelenik meg, lapozható, rendezhető, filterezhető (név részlet,
	 disztributor, agent, product) </p>
	<p>A képernyőn megadható a területen történő elvárt értékesítés mértéke (EUR -ban vagy más mértékegységben)</p>
	<p>editálható, törölhető, exportálható, importálható</p> 
	<h2>Area object</h2>
	<h3>Properties</h3>
	<ul>
		<li>int id</li>
		<li>string $slug</li>
		<li>string $name</li>	
		<li>select $type (continent | country | region_1 ¶ region_2 | location | sublocation | pol_zone_1 | pol_zone_2)</li>	
		<li>array $poligon [[x,y], ....]</li>	
		<li>string $parent_id</li>
		<li>int population</li>
		<li>int place (km2)</li>	
		<li>int | string $planned (szám | summed | population | population/szám)</li>	
		<li>string $planned_unit</li>	
		<li>string $state (draft | active | closed)</li>
		<li>date $enable_start</li>
		<li>date $enable_end</li>
	</ul>
	<h3>special metods</h3>
	<ul>		
		<li>childAreas():array of Area</li>	
		<li>products():array of Product (a hozzá kapcsolt termékek)</li>	
		<li>distributors():array of Distributor (a hozzá kapcsolt terjesztők)</li>	
		<li>agents():array of Agent (a hozzá kapcsolt ügynökök)</li>
	</ul>
	<p>Ez az objektum a woocoommerce category objektum kiterjesztéseként
	 valósul meg. Adatainak egy része a woocoommerce -ben is látható, kezelhető.</p>
	';
}
function cmm_productsAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Products</h2>
	<p>A képernyőn a termékek listája jelenik meg, lapozható, rendezhető, filterezhető (név részlet,
	 disztributor, agent, product, category, area) </p>
	<p>A képernyőn megadható a termék területenkénti és/vagy kategoriánkénti elvárt értékesítés mértéke
	 (EUR -ban vagy más mértékegységben)</p>
	<p>editálható, törölhető, exportálható, importálható</p> 
	<h2>Product object</h2>
	<h3>Properties</h3>
	<ul>
		<li>int id</li>
		<li>string $slug</li>
		<li>string $name</li>
		<li>string $description</li>	
		<li>string $image</li>	
		<li>array of string $galery</li>	
		<li>string $unit</li>	
		<li>number $stock</li>
		<li>int | string $planned (number | summed | area_population | area_population/szám)</li>	
		<li>string $planned_unit</li>	
		<li>string $state (draft | active | closed)</li>
        <li>string $use_start (datum | salesdate)</li>
        <li>int $use_days (ennyi napig használható</li> 
		<li>date $enable_start</li>
		<li>date $enable_end</li>
	</ul>
	<h3>special metods</h3>
	<ul>		
		<li>areas():array of Area</li>	
		<li>distributors():array of Distributor (a hozzá kapcsolt terjesztők)</li>	
		<li>agents():array of Agent (a hozzá kapcsolt ügynökök)</li>
		<li>sales():array of Sale (a hozzá kapcsolt értékesítések)</li>
	</ul>
	<p>Ez az objektum a woocoommerce product objektum kiterjesztéseként
	 valósul meg. Adatainak egy része a woocoommerce -ben is látható, kezelhető.</p>
	
	';
}
function cmm_categoriesAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Categories</h2>
	
	<p>A képernyőn a kategóriák listája jelenik meg, lapozható, rendezhető, filterezhető (név részlet,
	 disztributor, agent, product) </p>
	<p>editálható, törölhető, exportálható, importálható</p> 
	<p>A képernyőn megadható a kategóriába történő elvárt értékesítés mértéke (EUR -ban vagy más mértékegységben)</p> 
	<h2>Category object</h2>
	<h3>Properties</h3>
	<ul>
		<li>int id</li>
		<li>string $slug</li>
		<li>string $name</li>	
		<li>select $type (area | other....)</li>	
		<li>string $parent_id</li>
		<li>int place (km2)</li>	
		<li>int|summed_up $planned</li>	
		<li>string $planned_unit</li>	
		<li>string $state (draft | active | closed)</li>
		<li>date $enable_start</li>
		<li>date $enable_end</li>
	</ul>
	<h3>special metods</h3>
	<ul>		
		<li>childCategories():array of Category</li>	
		<li>area(): Area</li>	
		<li>products():array of Product</li>	
		<li>distributors():array of Distributor</li>	
		<li>agents():array of Agent</li>
	</ul>
	<p>Ez az objektum a woocoommerce category objektum kiterjesztéseként
	 valósul meg. Adatainak egy része a woocoommerce -ben is látható, kezelhető.</p>
	';
}
function cmm_distributorsAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Distributors</h2>
	<p>A képernyőn a terjesztők listája jelenik meg, lapozható, rendezhető, filterezhető (név részlet,
	 category, area, product) </p>
	<p>editálható, törölhető, exportálható, importálható</p> 
	<p>A képernyőn megadható a tőle elvárt értékesítés mértéke (EUR -ban vagy más mértékegységben), és az, hogy
	meilyen kategóriákkal, területekkel, termékekkel foglalkozik</p> 
	<h2>Distributor object</h2>
	<h3>Properties</h3>
	<ul>
		<li>int id</li>
		<li>string $nick</li>
		<li>string $name</li>	
		<li>string $email</li>	
		<li>int|summed_up $planned</li>	
		<li>string $planned_unit</li>	
		<li>string $state (inactive | active | closed)</li>
		<li>date $enable_start</li>
		<li>date $enable_end</li>
	</ul>
	<h3>special metods</h3>
	<ul>		
		<li>categories():array of Category (kategóriák amivel foglalkozik)</li>	
		<li>areas(): array of Area (területek amivel foglalkozik)</li>	
		<li>products():array of Product (termékek amivel foglalkozik)</li>	
		<li>sales():array of Sale (a hozzá kapcsolt értékesítések)</li>
	</ul>
	<p>Ez az objektum a wp user objektum kiterjesztéseként
	 valósul meg. Adatainak egy része a wp user menüben is látható, kezelhető.</p>
	
	';
}
function cmm_agentsAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Agents</h2>
	<p>A képernyőn az ügynökök listája jelenik meg, lapozható, rendezhető, filterezhető(név részlet,
	 category, area, product) </p>
	<p>editálható, törölhető, exportálható, importálható</p> 
	<p>A képernyőn megadható a tőle elvárt értékesítés mértéke (EUR -ban vagy más mértékegységben), és az, hogy
	meilyen kategóriákkal, területekkel, termékekkel foglalkozik</p> 
	<h2>Agent object</h2>
	<h3>Properties</h3>
	<ul>
		<li>int id</li>
		<li>string $nick</li>
		<li>string $name</li>	
		<li>string $email</li>	
		<li>int|summed_up $planned</li>	
		<li>string $planned_unit</li>	
		<li>string $state (inactive | active | closed)</li>
		<li>date $enableStart</li>
		<li>date $enableEnd</li>
	</ul>
	<h3>special metods</h3>
	<ul>		
		<li>categories():array of Category (kategóriák amivel foglalkozik)</li>	
		<li>areas(): array of Area (területek amivel foglalkozik)</li>	
		<li>products():array of Product (termékek amivel foglalkozik)</li>	
		<li>sales():array of Sale (a hozzá kapcsolt értékesítések)</li>
	</ul>
	<p>Ez az objektum a wp user objektum kiterjesztéseként
	 valósul meg. Adatainak egy része a wp user menüben is látható, kezelhető.</p>
	
	';
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
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Sales</h2>
	
	<p>A képernyőn a realizált vagy folyamatban lévő értékesítések listája jelenik meg.</p>
	<p>Lapozható, rendezhető, korlátozottan editálható, filterezhető (dátum, státusz, termék, vevő, ügynök, terjesztő, kategória, terület)</p>
    <h2>Sale object</h2>
	<h3>Properties</h3>
	<ul>
		<li>int id</li>
		<li>int $customer_id</li>
		<li>int $product_id</li>	
		<li>number $quantity</li>	
		<li>string $unit</li>
		<li>number price</li>	
		<li>string $currency</li>	
		<li>date $date</li>
		<li>string $state(see woocoommerce order state: processed | pending | on-hold | completted | cancelled | refunded)</li>
		<li>int $distributor_id</li>
		<li>int $agent_id</li>
	</ul>
	<p>Ez az objektum részben a woocoommerce adatbázisában van tárolva, az adatok egy része ott is látható, kezelhető.</p>
	';
}






?>
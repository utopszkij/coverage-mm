<?php
/**
* Plugin Name: Covergae monitoring & marketing
* Plugin URI: http://www.github.com/utopszkij/coverage-mm
* Description: Kiegészítés woocommerce -hez
* Version: 1.00
* Requires at least: 4.4 
* Requires PHP:      7.2
* Author: Fogler Tibpre
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
	?>
	<h1>Coverage Monitoring & Marketing</h1>
	<h2>shortCodes:</h2>
	<h3>coverage</h3>
	<p>A lekérdezés paramétereibenmeghatározott terület/kategória/termék/ügynök/terjesztő/vevő lefedettségének kimutatása a lekérdezésben
	magadott pénznemben vagy mértékegységben.
	Az elvárt értékesítési mérték (a 100%) lehet a lekérdezésben adott, vagy az érintett 
	terület/kategória/termék/ügynök/terjesztő objektumban megadott, vagy az ahoz tartozó elemkből összegzett. 
	A setup képernyőn megadható mértékegység átváltó táblázat alapján a program mértékegységekközötti átváltásokat végez 
	(illetve a termék értékesitéseknél ha a kért result mértékegység pénznem akkor az egységárat használva számol).
	</p>
		
	<h4>input params</h4>
		<table border=1>
			<tr>
				<th>name</th><th>values</th><th>default</th>
			</tr>		
			<tr>
				<td>customer_id</td><td>user_id | all</td><td>all</td>			
			</tr>
			<tr>
				<td>product_id</td><td>product_id | all</td><td>all</td>			
			</tr>
			<tr>
				<td>area_id</td><td>area_id w all</td><td>all</td>			
			</tr>
			<tr>
				<td>category_id</td><td>category_id | all</td><td>all</td>			
			</tr>
			<tr>
				<td>distributor_id</td><td>distributor_id | all</td><td>all</td>			
			</tr>
			<tr>
				<td>agent_id</td><td>agent_id | all</td><td>all</td>			
			</tr>
			<tr>
				<td>date</td><td>example: 2020-05-05|today</td><td>today</td>			
			</tr>
			<tr>
				<td>planed</td><td>number | default</td><td>default (from area | product | category | customer | distributor | agent object)</td>			
			</tr>
			<tr>
				<td>planed_unit</td><td>Woocommerce pénznem | wooComment product unit</td><td>pc</td>			
			</tr>
			<tr>
				<td>sale_state</td><td>see sale states</td><td>completted</td>			
			</tr>
			<tr>
				<td>amount_display_mode</td><td>absolute|relative|none</td><td>relative</td>			
			</tr>
			<tr>
				<td>amount_unit</td><td>wooCommerce pénznem | woocommerce product unit</td><td><strong>pc</td>			
			</tr>
			<tr>
				<td>amountt_picture</td><td>height x width (pixel) | none</td><td>example: 300x300</td>			
			</tr>
			
		</table>	
	<h4>result</h4>
		<table border=1>
			<tr>
				<th>amount_display_mode</th><th>amount_picture</th><th>result example</th>
			</tr>		
			<tr>
				<td>absolute</td><td>none</td>
				<td><textarea cols="60" rows="14" style="readonly:readonly">
					<div class="coverage_result">
						<div class="customer_id">all</div>
						<div class="product_id">148</div>
						<div class="area_id">67</div>
						<div class="category_id">all</div>
						<div class="distributor_id">18</div>
						<div class="agent_id">all</div>
						<div class="planed">1400</div>
						<div class="real">750</div>
						<div class="unit">EUR</div>
					</div>
				</textarea></td>
			</tr>		
			<tr>
				<td>relative</td><td>none</td>
				<td><textarea cols="60" rows="12" style="readonly:readonly">
					<div class="coverage_result">
						<div class="customer_id">all</div>
						<div class="product_id">134</div>
						<div class="area_id">78</div>
						<div class="category_id">all</div>
						<div class="distributor_id">45</div>
						<div class="agent_id">all</div>
						<div class="planed_id">1400</div>
						<div class="unit">EUR</div>
						<div class="coverage">25%</div>
					</div>
				</textarea></td>
			</tr>		
			<tr>
				<td>none</td><td>300x300</td>
				<td><textarea cols="60" rows="6" style="readonly:readonly">
					<div class="coverage_result">
						<div class="picture">...</div>
					</div>
				</textarea></td>
			</tr>		
			<tr>
				<td>relative</td><td>300x300</td>
				<td><textarea cols="60" rows="18" style="readonly:readonly">
					<div class="coverage_result">
						<div class="customer_id">all</div>
						<div class="product_id">45</div>
						<div class="area_id">33</div>
						<div class="category_id">all</div>
						<div class="distributor_id">45</div>
						<div class="agent_id">all</div>
						<div class="planed_id">1400</div>
						<div class="unit">EUR</div>
						<div class="coverage">25%</div>
						<div class="picture">...</div>
					</div>
				</textarea></td>
			</tr>		
			<tr>
				<td>absolute</td><td>300x300</td>
				<td><textarea cols="60" rows="18" style="readonly:readonly">
					<div class="coverage_result">
						<div class="customer_id">all</div>
						<div class="product_id">all</div>
						<div class="area_id">345</div>
						<div class="category_id">all</div>
						<div class="distributor_id">all</div>
						<div class="agent_id">all</div>
						<div class="planed">1400</div>
						<div class="real">750</div>
						<div class="unit">EUR</div>
						<div class="picture">...</div>
					</div>
				</textarea></td>
			</tr>	
			<tr>
				<td colspan="2">if error</td>
				<td><textarea cols="60" rows="18" style="readonly:readonly">
					<div class="coverage_error_result">
						<div class="alert alert-danger">product not found</div>
					</div>
				</textarea>
				</td>			
			</tr>	
		</table>
		<p>Lehetséges hibák:</p>
		<ul>
			<li>Az input pareméterben megadott valamelyik szürő feltétel nem létezik (area, category, product, distributor, agent, customer)</li>
			<li>A rekurziv felösszegzés során az egyik feldolgozandó termék vagy értékesités elem olyan mértékegységben van megadva amit nem
			lehet átváltani a kért kimeneti mértékegységre.</li>		
		</ul>

	<p>az értékesitések és az elvárt teljesitmények adatait a resultba kért mértékegységre váltja át. Relative 
	eredmény kérésnél is kell kért mértékegysé, ebben történik a számítás. 
	</p>

	<h3>callable API functions</h3>

	<h4>cmm_coverage(array $params):string</h4>
	<p>Lefedettség lekérdezés</p>
	<p>params: associative array see:shortcode input</p>
	<p>result: html code, see shortcode result</p>
	
	<h4>cmm_add_sale(
	string $product_slug,
	string $distributor_slug,
	string $agent_slug,
	string $customer_nick,
	number $quantity,
	string $unit,
	string $state,
	date   $date 
	):int</h4>

	<p>Értékesítés státusz módosítása</p>
	<h4>cmm_edit_sale(
	int $sale_id,
	string $state,
	date   $date 
	):int</h4>
	<p>Új Értékesítés stárusz módosítása</p>
	
	
	
	
	<h3>Egyéb infók</h3>
	<p>A plugin init metodusa a következőket fogja tenni (ACF szabványos hivásokkal):</p>
	<p>lásd: https://www.advancedcustomfields.com/resources/register-fields-via-php/</p>
	<ul>
		<li>Bőviti a product_cat -ot új mezőkkel (type, poligon, population, place, planed, planed_unit, state, enble_start, enable_end)</li>
		<li>Bőviti a Product -ot új mezőkkel(planed, planed_unit, state, use_start, use_days, enble_start, enable_end)</li>
		<li>Bőviti a User -t új mezőkkel (planed, planed_unit, distributor, agent)</li>
		<li>Bőviti a woocoomerce order -t új mezőkkel (distributor_id, agent_id)</li>
		<li>add_action -al beékelődik a product_cat tárolásba, törlésbe</li>
		<li>add_action -al beékelődik a product tárolásba, törlésbe</li>
		<li>add_action -al beékelődik a user tárolásba, törlésbe</li>
		<li>add_action -al beékelődik a order tárolásba, törlésbe</li>
	</ul>
	<?php
}
function cmm_areaAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Areas</h2>
	
	<p>A képernyőn a területek listája jelenik meg, lapozható, rendezhető, filterezhető(név részlet,
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
		<li>int | string $planed (szám | summed | population | popupulation/szám)</li>	
		<li>string $planed_unit</li>	
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
	<p>Ez az objektum a woocommerce category objektum kiterjesztéseként
	 valósul meg. Adatainak egy része a woocommerce -ben is látható, kezelhető.</p>
	';
}
function cmm_productsAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Products</h2>
	<p>A képernyőn a termékek listája jelenik meg, lapozható, rendezhető, filterezhető(név részlet,
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
		<li>string $destcription</li>	
		<li>string $image</li>	
		<li>array of string $galery</li>	
		<li>string $unit</li>	
		<li>number $stock</li>
		<li>int | string $planed (number | summed | area_population | area_population/szám)</li>	
		<li>string $planed_unit</li>	
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
	<p>Ez az objektum a woocommerce product objektum kiterjesztéseként
	 valósul meg. Adatainak egy része a woocommerce -ben is látható, kezelhető.</p>
	
	';
}
function cmm_categoriesAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Categories</h2>
	
	<p>A képernyőn a kategóriák listája jelenik meg, lapozható, rendezhető, filterezhető(név részlet,
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
		<li>int|summed_up $planed</li>	
		<li>string $planed_unit</li>	
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
	<p>Ez az objektum a woocommerce category objektum kiterjesztéseként
	 valósul meg. Adatainak egy része a woocommerce -ben is látható, kezelhető.</p>
	';
}
function cmm_distributorsAdminMenu() {
	echo '<h1>Coverage Monitoring & Marketing</h1>';
	echo '<h2>Distributors</h2>
	<p>A képernyőn a terjesztők listája jelenik meg, lapozható, rendezhető, filterezhető(név részlet,
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
		<li>int|summed_up $planed</li>	
		<li>string $planed_unit</li>	
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
		<li>int|summed_up $planed</li>	
		<li>string $planed_unit</li>	
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
	<p>A terjesztők, ügynökök, vásárlók aktivitásuk, teljesitményük alapján "rangot" kapnak. Ennek részletei még tisztázandók</p>	
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
	
	<p>A képernyőn a relizált vagy folyamatban lévő értékesítések listája jelenik meg.</p>
	<p>Lapozható, rendezhető, korlátozottan editálható, filterezhető (dátum, státusz, termék, vevő, ügynök, terjesztő, kategória, terület)</p>
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
		<li>string $state(see woocommerce order state: processed | pending | on-hold | completted | cancelled | refunded)</li>
		<li>int $distributor_id</li>
		<li>int $agent_id</li>
	</ul>
	<p>Ez az objektum részben a woocommerce adatbázisában van tárolva, az adatok egy része ott is látható, kezelhető.</p>
	';
}






?>
	<div id="cmmAdminForm">
	<h1>Coverage Monitoring & Marketing</h1>

	<?php if (isset($this->msg)) : ?>
	<div class="<?php echo $this->msgClass; ?>"><?php echo $this->msg; ?></div>
	<?php endif;?>
	
	<h2>shortCodes:</h2>
	<h3>coverage</h3>
	
	
	<p>A lekérdezés paramétereiben meghatározott terület/kategória/termék/ügynök/terjesztő/vevő lefedettségének kimutatása a lekérdezésben
	magadott pénznemben vagy mértékegységben.
	Az elvárt értékesítési mérték (a 100%) lehet a lekérdezésben adott, vagy az érintett 
	terület/kategória/termék/ügynök/terjesztő objektumban megadott, vagy az ahoz tartozó elemekből összegzett. 
	A setup képernyőn megadható mértékegység átváltó táblázat alapján a program mértékegységek közötti átváltásokat végez 
	(illetve a termék értékesítéseknél ha a kért result mértékegység pénznem akkor az egységárat használva számol).
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
				<td>planned</td><td>number | default</td><td>default (from area | product | category | customer | distributor | agent object)</td>			
			</tr>
			<tr>
				<td>planned_unit</td><td>Woocommerce pénznem | wooComment product unit</td><td>pc</td>			
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
				<td>amount_picture</td><td>height x width (pixel) | none</td><td>example: 300x300</td>			
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
						<div class="planned">1400</div>
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
						<div class="planned_id">1400</div>
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
						<div class="planned_id">1400</div>
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
						<div class="planned">1400</div>
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
			<li>Az input pareméterben megadott valamelyik szűrő feltétel nem létezik (area, category, product, distributor, agent, customer)</li>
			<li>A rekurzív felösszegzés során az egyik feldolgozandó termék vagy értékesítés elem olyan mértékegységben van megadva amit nem
			lehet átváltani a kért kimeneti mértékegységre.</li>		
		</ul>

	<p>az értékesítések és az elvárt teljesítmények adatait a resultba kért mértékegységre váltja át. Relatíve 
	eredmény kérésnél is kell kért mértékegység, ebben történik a számítás. 
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
	<p>Értékesítés státusz módosítása</p>
	
	
	
	
	<h3>Egyéb infók</h3>
	<p>A plugin init metódusa a következőket fogja tenni (ACF szabványos hívásokkal):</p>
	<p>lásd: https://www.advancedcustomfields.com/resources/register-fields-via-php/</p>
	<ul>
		<li>Bővíti a product_cat -ot új mezőkkel (type, poligon, population, place, planed, planed_unit, state, enble_start, enable_end)</li>
		<li>Bővíti a Product -ot új mezőkkel(planed, planed_unit, state, use_start, use_days, enble_start, enable_end)</li>
		<li>Bővíti a User -t új mezőkkel (planed, planed_unit, distributor, agent)</li>
		<li>Bővíti a woocoomerce order -t új mezőkkel (distributor_id, agent_id)</li>
		<li>add_action -al beékelődik a product_cat tárolásba, törlésbe</li>
		<li>add_action -al beékelődik a product tárolásba, törlésbe</li>
		<li>add_action -al beékelődik a user tárolásba, törlésbe</li>
		<li>add_action -al beékelődik a order tárolásba, törlésbe</li>
	</ul>
	
</div>
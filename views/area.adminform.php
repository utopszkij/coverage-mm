	<?php 
	// use language tokens in ACF (requied for poedit)
	__('cmm product_cat extends',CMM);
	__('isarea',CMM);
	__('area_category',CMM);
	__('phase',CMM);
	__('continent',CMM);
	__('country',CMM);
	__('pol_zone_1',CMM);
	__('pol_zone_2',CMM);
	__('micro_village',CMM);
	__('small_village',CMM);
	__('village',CMM);
	__('large_village',CMM);
	__('small_city',CMM);
	__('district',CMM);
	__('large_city',CMM);
	__('subregion',CMM);
	__('region',CMM);
	__('other',CMM);
	__('data_entry',CMM);
	__('data_generation',CMM);
	__('data_mainance',CMM);
	__('data_archiving',CMM);
	__('not_phase',CMM);
	__('population',CMM);
	__('place',CMM);
	__('poligon',CMM);
	__('enable_start',CMM);
	__('enable_end',CMM);
	__('center_lat',CMM);
	__('center_lng',CMM);
	__('map_zoom',CMM);
	__('draft',CMM);
	__('active',CMM);
	__('closed',CMM);
	__('locality',CMM);
	__('city',CMM);
	__('status',CMM);
	
	
	?>
	<div id="areAdminForm">
	<h1>Coverage Monitoring & Marketing</h1>
	<h2>Areas</h2>
    
    <?php if (isset($this->msg)) : ?>
	<div class="<?php echo $this->msgClass; ?>"><?php echo $this->msg; ?></div>
	<?php endif;?>
    
    
    <table style="dispay:block; width:auto; border-style:none">
    		<tr>
    			<td>
            		<a href="<?php echo site_url(); ?>/wp-admin/edit-tags.php?taxonomy=product_cat&post_type=product" 
            				class="button button-primary">
            				<?php echo __('browse',CMM); ?>
            		</a>
    			</td>
    			<td>
            		<form method="post" action="<?php echo site_url(); ?>/wp-admin/admin.php?page=cmm-areas">
            			<input type="hidden" name="task" value="import_csv1" />
            			<button type="submit" class="button button-secondary">
            			<?php echo __('import',CMM); ?>
            			</button>		
            		</form>
    			</td>
    			<td>
            		<form method="post" action="<?php echo site_url(); ?>/wp-admin/admin.php?page=cmm-areas">
            			<input type="hidden" name="task" value="export_csv" />
            			<button type="submit" class="button button-secondary"
            			  onclick="jQuery('#progressIndicator').show(); true;">
            			<?php echo __('export',CMM); ?>
            			</button>		
            		</form>
    			<td>
    			</td>
    		</tr>
    </table>
    
	<p>Az admin képernyőn a területek listája jelenik meg, lapozható, rendezhető, filterezhető (név részlet,
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
		<li>string $phase (data_entry | data_generation | data_maintance | data_archiving | not_phase)</li>
		<li>date $enable_start</li>
		<li>date $enable_end</li>
	</ul>
	<h3>special metods</h3>
	<ul>		
		<li>childAreas():array of Area</li>	
		<li>products():array of Product (a hozzá kapcsolt termékek)</li>
		<li>AreaProductCats: array of AreaProductCat (hozzá kapcsolt termék kategóriák)</li>>
		<li>distributors():array of Distributor (a hozzá kapcsolt terjesztők)</li>	
		<li>agents():array of Agent (a hozzá kapcsolt ügynökök)</li>
	</ul>
	<p>Ez az objektum a woocoommerce category objektum kiterjesztéseként
		 valósul meg. Adatainak egy része a woocoommerce -ben is látható, kezelhető.
	</p>
	
	<h2>AreaProductCat object</h2>
	<h3>propertys</h3>
	<ul>
		<li>int id</li>
		<li>int area_id</li>
		<li>int productCat_id</li>
	    <li>int | string $planned (szám | summed | population | population/szám)</li>	
	    <li>string $planned_unit</li>	
		<li>string $phase
		<li>date $enable_start</li>
		<li>date $enable_end</li>
	</ul>
	
	
	<h2>shortcodes</h2>
	
	<h3>[cmm_areainfo id=szám]</h3>  
		result html kód a kért area tulajdonságai
	<h3>[cmm_areachilds id=szám]</h3>  
		result html kód a kért area gyermekeinek felsorolása (id és név)
	<h3>[cmm_areaparents id=szám]</h3>  
		result html kód a kért area tulajdonosainak felsorolása (id és név)
	</h3>[cmm areamap id=szám color=csscolor width=szám]</h3> 
		result html kód, a parent area térkép image -én a kért area poligonja, a kért szinnel, a kért méretben
	</h3>[cmm area_productcat_info area_id=szám productCat_id=szám </h3> 
		result html kód, az AreaProductCat objectum adatai
 	<h3>[cmm_map map_id=# markers=xxxxxxxx]</h3> 
    Ahol markers = "id:#,lat:#,lng:#;id:#,lat:#,lng:#; ....."<br /> 
    a markers -ben lévő "id" -knek álltalános célú markerekre kell mutatniuk.<br />
    A markers lehet üres string is, illetve el is hagyható.
    <h4>Müködése:</h4>
    Megjeleniti a "map_id" térképet, a rajta lévő "Egyedi markerekkel", valamint<br />
    megjelenítit a "markers" paraméterben felsorolt "álltalános célú" markereket,<br />
    a "markers" paraméterben magadott poziciókban.<br />
           		
	</div>	
	
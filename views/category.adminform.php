	<?php 
	// use language tokens in ACF (requied for poedit)
	// __('token',CMM);
	
	?>
    
	<h1>Coverage Monitoring & Marketing</h1>
	<h2>Categories</h2>
	
	<p>A képernyőn a kategóriák listája jelenik meg, lapozható, rendezhető, filterezhető (név részlet,
	 disztributor, agent, product) </p>
	<p>editálható, törölhető, exportálható, importálható</p> 
	<p>A képernyőn megadható a kategóriába történő elvárt értékesítés mértéke (EUR -ban vagy más mértékegységben)</p> 
	<h2>Category object</h2>
	<h3>Properties</h3>
	<ul>
		<li>int $id</li>
		<li>string $slug</li>
		<li>string $name</li>	
		<li>select $type (area | other....)</li>	
		<li>string $parent_id</li>
		<li>int $place (km2)</li>	
		<li>int|summed_up $planned</li>	
		<li>string $planned_unit</li>	
		<li>string $phase (data_entry | data_generation | data_maintance | data_archiving | not_phase)</li>
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


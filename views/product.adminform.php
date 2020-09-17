	<?php 
	// use language tokens in ACF (requied for poedit)
	// __('token',CMM);
	
	?>

	<h1>Coverage Monitoring & Marketing</h1>
	<h2>Products</h2>
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
		<li>string $phase (data_entry | data_generation | data_maintance | data_archiving | not_phase)</li>
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




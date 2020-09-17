	<?php 
	// use language tokens in ACF (requied for poedit)
	// __('token',CMM);
	
	?>
    
	<h1>Coverage Monitoring & Marketing</h1>
	<h2>Distributors</h2>
	<p>A képernyőn a terjesztők listája jelenik meg, lapozható, rendezhető, filterezhető (név részlet,
	 category, area, product) </p>
	<p>editálható, törölhető, exportálható, importálható</p> 
	<p>A képernyőn megadható a tőle elvárt értékesítés mértéke (EUR -ban vagy más mértékegységben), és az, hogy
	meilyen kategóriákkal, területekkel, termékekkel foglalkozik</p> 
	<h2>Distributor object</h2>
	<h3>Properties</h3>
	<ul>
		<li>int $id</li>
		<li>string $nick</li>
		<li>string $name</li>	
		<li>string $email</li>	
		<li>int|summed_up $planned</li>	
		<li>string $planned_unit</li>	
		<li>string $phase (data_entry | data_generation | data_maintance | data_archiving | not_phase)</li>
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

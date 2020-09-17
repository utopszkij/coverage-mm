	<?php 
	// use language tokens in ACF (requied for poedit)
	// __('token',CMM);
	
	?>
    
    <h1>Coverage Monitoring & Marketing</h1>
	<h2>Sales</h2>
	<p>A képernyőn a realizált vagy folyamatban lévő értékesítések listája jelenik meg.</p>
	<p>Lapozható, rendezhető, korlátozottan editálható, filterezhető (dátum, státusz, termék, vevő, ügynök, terjesztő, kategória, terület)</p>
    <h2>Sale object</h2>
	<h3>Properties</h3>
	<ul>
		<li>int $id</li>
		<li>int $customer_id</li>
		<li>int $product_id</li>	
		<li>number $quantity</li>	
		<li>string $unit</li>
		<li>number price</li>	
		<li>string $currency</li>	
		<li>date $date</li>
		<li>string $phase (data_entry | data_generation | data_maintance | data_archiving | not_phase)</li>
		<li>string $state(see woocoommerce order state: processed | pending | on-hold | completted | cancelled | refunded)</li>
		<li>int $distributor_id</li>
		<li>int $agent_id</li>
	</ul>
	<p>Ez az objektum részben a woocoommerce adatbázisában van tárolva, az adatok egy része ott is látható, kezelhető.</p>

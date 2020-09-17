<?php 
/**
 * csv export finished
 */
?>
<div id="exportCsv2">
	<h2><?php echo __('export_to_csv',CMM); ?></h2>
	<a class="button button-primary" 
	   href="<?php echo get_site_url().'/wp-content/plugins/coverage_mm/work/'.session_id().'.csv'; ?>">
	   <?php echo __('download',CMM); ?>
	</a>
</div> 
<div style="display:none">
	<iframe name="ifrmDelcsv"></iframe>
   	<form method="post" id="formDelcsv" 
   	    action="<?php echo site_url(); ?>/wp-admin/admin.php?page=cmm-areas" target="ifrmDelcsv">
		<input type="hidden" name="task" value="delcsv" />
	</form>
</div>
<script type="text/javascript">
    window.onbeforeunload = function() {
     	// if close this page then call delete csv file from work dir
		jQuery('#formDelcsv').submit();	
	}
</script>
<?php 
	/**
	 * ara import from CSV form
	 */
?>
<div id="areImportCsv1">
	<h1>Coverage Monitoring & Marketing</h1>
	<h2>Area import from CSV</h2>
	<form method="post" enctype="multipart/form-data"
	      action="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=cmm-areas">
	      <input type="hidden" name="task" value="import_csv2" />
    	<p><?php echo __('csv_import_help',CMM); ?></p>
    	<p>
    		<label><?php echo __('default_country',CMM); ?></label>
    		<input type="text" name="country" value="" />
    	</p>
    	<p>
    		<label><?php echo __('csv_field_separator',CMM); ?></label>
    		<select name="csvFieldSeparator">
    			<option value="," selected="selected"><?php echo __('comma',CMM);?></option>
    			<option value=";"><?php echo __('semicolon',CMM);?></option>
    			<option value="tab"><?php echo __('tabulator',CMM);?></option>
    		</select>
    	</p>
    	<p>
    		<label><?php echo __('csv_file',CMM); ?></label>
    		<input type="file" name="csvFile" />
    	</p>
    	<p>
    		<button type="submit" class="button button-primary" onclick="jQuery('#progressIndicator').show(); true;">
    		   <?php echo __('send',CMM); ?></button>
    	</p>
	</form>
</div>
	
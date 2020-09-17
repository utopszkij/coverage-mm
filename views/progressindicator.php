<?php 
if (!isset($this->prIndClass)) {
    $this->prIndClass = 'block';
}
if (!isset($this->prIndTotal)) {
    $this->prIndTotal = 0;
}
if (!isset($this->prIndSkip)) {
    $this->prIndSkip = 0;
}
if (($this->prIndTotal > 0) & ($this->prIndSkip > 0)) {
    $w = ($this->prIndSkip / $this->prIndTotal) * 100;
} else {
    $w = 1;
}
if ($w > 100) {
    $w = 100;
}

?>
<div id="progressIndicator" style="display:<?php echo $this->prIndClass; ?>;">
    <h2><?php echo __('processing_csv',CMM); ?></h2>
    <?php if ($this->prIndTotal > 0) : ?>
    	<?php echo __('working',CMM); ?>... <?php echo $this->prIndSkip; ?> / <?php echo $this->prIndTotal; ?>
    	<table width="80%" style="border-style:solid; border-width:1px;">
    		<tr>
    			<td style="background-color:blue; width:<?php echo $w; ?>%">&nbsp;</td>
    			<td style="background-color:white">&nbsp;</td>
    		</tr>
    	</table>
    <?php endif; ?>
	<div id="prIndImg" style="display:block; z-index:10; text-align:center; padding:50px">
	     <img src="<?php echo get_site_url(); ?>/wp-content/plugins/coverage_mm/images/progressing.gif" />
	</div>
</div>

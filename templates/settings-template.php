<div class="wrap">
	<h2>Simple Page Meta Settings</h2>
	<?php $settings->update_message(); ?>
	<form method="post" enctype="multipart/form-data" class="compiler-form">
		<div class="section">
			<h3>Open Graph Content Types</h3>
			<span class="description">This should be a descriptive word for the type of content.  Currently the three main accepted content types are: website, article, and profile.  Leaving blank will use the webiste type.</span>
			<?php $this->render_group("og-type-defaults"); ?>
		</div>
		<div class="form-bottom">		
			<?php wp_nonce_field(JPM::SLUG); ?>
			<input name="save" type="submit" id="submit" value="Save Changes" class="button button-primary">
		</div>
	</form>
</div>
<form action="" method="post">
    <?php wp_nonce_field( $namespace . "_options", $namespace . '_update_wpnonce' ); ?>
    <input type="hidden" name="form_action" value="update_options" />
    <div class="wrap">
        <h2><?php echo $page_title; ?></h2>
        <div class="tool-box">
            <h3 class="title">Your HelloBox Code</h3>
			<p>Fetch your HelloBox code from <a href="http://www.gethellobox.com">www.gethellobox.com</a> and paste it below.</p>
			<textarea rows="10" cols="70" name="hellobox_code"><?php echo $hellobox_code; ?></textarea>
            
            <div style="display:none;">
			<h4>Load the HelloBox code in your site's:</h4>
			<input id="<?php echo $namespace; ?>-load-code-in-header" type="radio" name="load_hellobox_in" value="header"<?php echo $load_hellobox_in == 'header' ? ' checked="checked"' : ''; ?> /><label for="<?php echo $namespace; ?>-load-code-in-header">Header (before &lt;/head&gt;)</label><br />
			<input id="<?php echo $namespace; ?>-load-code-in-footer"  type="radio" name="load_hellobox_in" value="footer"<?php echo $load_hellobox_in == 'footer' ? ' checked="checked"' : ''; ?> /><label for="<?php echo $namespace; ?>-load-code-in-footer">Footer (before &lt;/body&gt;)</label>
			</div>
			
        </div>
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>
    </div>
</form>
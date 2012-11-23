<?php

if( !empty( $_POST ) && isset( $_POST['fb_proxy_url'] ) )
    self::save_settings();
?>
<div id="icon-plugins" class="icon32"></div>

<div class="wrap">
    <h2><?php _e('Social Masta for Facebook', 'social-masta') ?></h2>
    
    <br />
    
    <form action="" method="post">
	<label for="fb_proxy_url"><?php _e('Facebook Proxy URL', 'social-master') ?></label>:
	<input name="fb_proxy_url" id="fb_proxy_url" type="text" maxlength="1024" value="<?php echo get_option('fb_proxy_url') ?>" class="large-text" />
	
	<br /><br /><br />
	
	<input type="submit" value="<?php _e('Save', 'social-masta') ?>" class="button-primary" />
    </form>
    
    <br />
        
    <?php if( self::is_logged_in() ): $profile = self::get_profile() ?>
    
    <p>You are currently logged in with Facebook as <b><a href="<?php echo $profile['link']?>" title="<?php _e('See your profile in facebook') ?>" target="_blank"><?php echo $profile['name'] ?></a></b>.</p>
    
    <?php else: ?>
    <p> <?php self::fb_print_data_debug(); ?> </p>
    <?php endif; ?>
</div>
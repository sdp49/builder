<div id="searchpage-inner" class="hide">
	<ul>
		<?php if ($page):?>
			<li>Check out your real estate search here: <a href="<?php echo get_permalink($page->ID)?>" target="_blank"><?php echo $page->post_title ?></a></li>
		<?php endif ?>
		<li>Right now we're showing example listings. To turn this off, go <a href="<?php echo admin_url('admin.php?page=placester_settings') ?>" target="_blank">here</a>.</li>
		<li>You can customize the way your real estate search looks (or create new real estate search pages) using shortcodes.</li>
		<li>Checkout shortcodes <a href="<?php echo admin_url('admin.php?page=placester_shortcodes_shortcode_edit') ?>" target="_blank">here</a> and checkout a guide to using shortcodes <a href="https://placester.com/developers/placester-shortcode-overview/" target="_blank">here</a>.</li>
	</ul>
</div>
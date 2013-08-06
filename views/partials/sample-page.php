<div id="searchpage-inner" class="hide">
	<ul>
		<?php if ($page):?>
			<p style="font-size: 23px; line-height: 1.2">We have created a real estate serach for you. Check it out <a href="<?php echo get_permalink($page->ID)?>" target="_blank">here</a></p>
		<?php endif ?>
		<p style="font-size: 14px;">The search is powered by Placester Shortcodes. Placester Shortcodes allow you to customize the way your real estate search looks (or create new search pages). Learn more about shortcodes <a href="https://placester.com/developers/placester-shortcode-overview/" target="_blank">here</a> or to view them, click <a href="<?php echo admin_url('admin.php?page=placester_shortcodes_shortcode_edit') ?>" target="_blank">here</a>.</p>
		<p style="font-size: 14px; color: #f70820">Right now we're showing example listings. To turn this off, go <a href="<?php echo admin_url('admin.php?page=placester_settings') ?>" target="_blank">here</a>.</p>
	</ul>
</div>
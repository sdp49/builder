<?php if ($page) :?>

	<?php if ($newpage) :?>

		<h2>Congratulations! We've added real estate search to your website</h2>
		<p>Check out your new real estate search here:</p>

	<?php else :?>

		<h2>It looks like you already have a listings page set up!</h2>
		<p>Check out your real estate search here:</p>

	<?php endif;?>

	<p><a href="<?php echo get_permalink($page->ID)?>" target="_blank"><?php echo $page->post_title ?></a></p>

	<?php if ($demodata) :?>
		<p>Right now we're showing example listings. To turn this off, go
		<a href="<?php echo admin_url('admin.php?page=placester_settings') ?>" target="_blank">here</a>.</p>
 	<?php endif; ?>

	<p>Finally, you can customize the way your real estate search
	<a href="<?php echo get_edit_post_link($page->ID)?>" target="_blank">looks</a>
	(or create new real estate search pages) using shortcodes.
	Checkout shortcodes <a href="<?php echo admin_url('admin.php?page=placester_shortcodes_shortcode_edit') ?>" target="_blank">here</a>
	and checkout a guide to using shortcodes here:</p>
	<p><a href="https://placester.com/developers/placester-shortcode-overview/" target="_blank">Guide to customizing property searches using shortcodes.</a></p>

<?php else :?>

	<p>Sorry, there was a problem creating a sample page.</p>

<?php endif;?>
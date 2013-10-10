<?php

$templates = PL_Shortcode_CPT::template_list('pl_idx', false, true);

?>
<div id="pl_tmplt_picker">
	<p>Select one of the following templates for your Real Estate Search Page:</p> 

	<div class="pl_tmplts">
		<?php foreach($templates as $id=>$template_info): ?>
			<?php if (empty($template_info['template'])): ?>
			<div class="pl_tmplt pl_custom">
				<h3 class="pl_tmplt_title"><?php echo $template_info['title'] ?></h3>
				<div class="screenshot"></div>
				<div class="pl_tmplt_description">Custom template.</div>
				<div class="pl_tmplt_actions"><a href="<?php echo $submit_link?>&action=idx_selected&tpl_id=<?php echo $id?>" class="pl_tmplt_select" data-tmplt_id="<?php echo $id ?>">Select Template</a></div>
			</div>
			<?php else: ?>
			<div class="pl_tmplt pl_default">
				<h3 class="pl_tmplt_title"><?php echo $template_info['title'] ?></h3>
				<div class="screenshot">
					<image src="<?php echo $template_info['template']['screenshot'] ?>" />
				</div>
				<div class="pl_tmplt_description"><?php echo $template_info['template']['description'] ?></div>
				<div class="pl_tmplt_actions"><a href="<?php echo $submit_link?>&action=idx_selected&tpl_id=<?php echo $id?>" class="pl_tmplt_select" data-tmplt_id="<?php echo $id ?>">Select Template</a></div>
			</div>
			<?php endif ?>
		<?php endforeach ?>
	</div>
</div>

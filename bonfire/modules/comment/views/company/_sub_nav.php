<ul class="nav nav-pills">
	<li <?php echo $this->uri->segment(4) == '' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/company/comment') ?>" id="list"><?php echo lang('comment_list'); ?></a>
	</li>
	<?php if ($this->auth->has_permission('Comment.Company.Create')) : ?>
	<li <?php echo $this->uri->segment(4) == 'create' ? 'class="active"' : '' ?> >
		<a href="<?php echo site_url(SITE_AREA .'/company/comment/create') ?>" id="create_new"><?php echo lang('comment_new'); ?></a>
	</li>
	<?php endif; ?>
</ul>
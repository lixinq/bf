<ul class="nav nav-pills">
	<li <?php echo $this->uri->segment(4) == '' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/settings/video') ?>" id="list"><?php echo lang('video_list'); ?></a>
	</li>
	<?php if ($this->auth->has_permission('Video.Settings.Create')) : ?>
	<li <?php echo $this->uri->segment(4) == 'create' ? 'class="active"' : '' ?> >
		<a href="<?php echo site_url(SITE_AREA .'/settings/video/create') ?>" id="create_new"><?php echo lang('video_new'); ?></a>
	</li>
	<?php endif; ?>
</ul>
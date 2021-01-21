<ul>
<?php foreach ($licenses as $license_id => $license_data): ?>
	<li><?= $license_data['key'] ?> - <?= $license_data['title'] ?></li>
<?php endforeach ?>
</ul>
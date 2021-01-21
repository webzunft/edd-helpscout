<ul>
<?php foreach ($orders as $order_id => $order_data): ?>
	<li><?= $order_data['date'] ?> #<?= $order_data['id'] ?></li>
<?php endforeach ?>
</ul>
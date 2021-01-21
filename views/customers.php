<ul>
<?php foreach ($customers as $customer_id => $customer): ?>
	<li>#<?= $customer_id ?> <?= $customer->name ?></li>
<?php endforeach ?>
</ul>
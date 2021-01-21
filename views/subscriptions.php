<ul>
<?php foreach ($subscriptions as $subscription_id => $subscription_data): ?>
	<li>#<?= $subscription_id ?> - <?= $subscription_data['title'] ?></li>
<?php endforeach ?>
</ul>
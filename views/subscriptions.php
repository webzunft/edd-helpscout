<div class="c-sb-section c-sb-section--toggle <?= $toggle ?> <?= $persist ?>">
	<div class="c-sb-section__title js-sb-toggle" style="font-size: 15px; padding: 6px 0 10px 0;">
		<i class="icon-star icon-sb" style="font-size: 19px; margin-right: 3px; top: 4px;"></i><?= __( 'Subscriptions', 'edd-helpscout' ); ?> <?= '(' . count( $subscriptions ) . ')'; ?><i class="caret sb-caret" style="margin-top: 4px;"></i>
	</div>
	<div class="c-sb-section__body">
		<ul class="c-sb-list c-sb-list--compact" style="padding-top: 16px;">
			<?php foreach ($subscriptions as $subscription_id => $subscription): ?>
				<li class="c-sb-list-item">
					<span class="c-sb-list-item__text t-tx-charcoal-500" style="font-size: 11px;">
					<span class="badge <?= $subscription['status_color'] ?>" style="font-size: 10px; padding: 3px 4px; margin: -1px 4px 0 0;"><?= $subscription['status_label'] ?></span> <a href="<?= $subscription['url'] ?>" title="<?= $subscription['status_label'] ?>">#<?= $subscription_id ?></a>
					</span>
				</li>
				<li class="c-sb-list-item" style="font-size: 14px; padding: 4px 0 2px 0;">
					<strong><?= $subscription['title'] ?></strong>
				</li>
				<li class="c-sb-list-item" style="padding-bottom: 8px; margin-bottom: 8px; border-bottom: 1px solid rgba(193,203,212,.2);">
					<span class="c-sb-list-item__label t-tx-charcoal-500" style="padding-bottom: 4px;">
						<span class="c-sb-list-item__text t-tx-charcoal-300" style="font-size: 11px;"><?= __( 'Created:', 'edd-helpscout' ); ?> <?= $subscription['created'] ?></span>
						<span class="c-sb-list-item__text t-tx-charcoal-300" style="font-size:11px;"><?= __( 'Expiration:', 'edd-helpscout' ); ?> <?= $subscription['expiration'] ?></span>
						<span class="c-sb-list-item__text t-tx-charcoal-300" style="font-size:11px;"><?= __( 'Billing Cycle:', 'edd-recurring' ); ?> <?= $subscription['billing_cycle'] ?></span>
					</span>
				</li>
			<?php endforeach ?>
		</ul>
	</div>
</div>

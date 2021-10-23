<div class="c-sb-section c-sb-section--toggle nested open">
	<div class="c-sb-section__title js-sb-toggle" style="font-size: 15px; padding: 6px 0 10px 0;">
		<i class="icon-folder icon-sb" style="font-size: 19px; margin-right: 3px; top: 4px;"></i><?= __( 'Licenses', 'edd-helpscout' ); ?><i class="caret sb-caret" style="margin-top: 4px;"></i>
	</div>
	<div class="c-sb-section__body" style="padding-top: 2px;">
		<?php foreach ($licenses as $license_id => $license): ?>
			<ul class="c-sb-list c-sb-list--compact" style="padding: 1em 0;">
				<?php if ( $license['show_activations'] ): ?>
					<li class="c-sb-list-item" style="font-style: italic;">
						<span class="c-sb-list-item__text t-tx-charcoal-500" style="font-size: 11px;">
						<span class="badge <?= $license['status_color'] ?>" style="font-size: 10px; padding: 3px 4px; margin: 0 4px 0 0;"><?= $license['status'] ?></span>
						<?php if ( ! empty( $license['is_lifetime'] ) ) : ?>
							<?= __( 'Lifetime', 'edd-helpscout' ); ?>
						<?php else : ?>
							<?= $license['is_expired'] ? __( 'Expired', 'edd-helpscout' ) : __( 'Expires', 'edd-helpscout' ); ?>: <?= $license['expires'] ?>
						<?php endif; ?>
						</span>
					</li>
				<?php endif ?>

				<li class="c-sb-list-item" style="font-size: 14px; padding: 6px 0 4px 0;"><strong><?= $license['title'] ?></strong></li>
				<li class="c-sb-list-item" style="font-size: 11px !important; display: block;">
					<?php if (!empty($license['price_option'])): ?>
						<?= $license['price_option'] ?>
						<a href="<?= $license['url']?>" target="_blank"><span class="icon-gear" style="font-size: 16px; margin-left: 4px;"></span></a>
						<br>
					<?php endif ?>
					<?= $license['key'] ?>
				</li>
			</ul>
			<?php if (!empty($license['children'])): ?>
				<div style="margin-left: 18px">
				<?php foreach ($license['children'] as $child_license_id => $child_license): ?>
					<ul class="c-sb-list c-sb-list--compact" style="padding: 8px 0 4px 0;">
						<?php if ( $child_license['show_activations'] ): ?>
							<li class="c-sb-list-item" style="font-style: italic;">
								<span class="c-sb-list-item__text t-tx-charcoal-500" style="font-size: 11px;">
								<span class="badge <?= $child_license['status_color'] ?>" style="font-size: 10px; padding: 3px 4px; margin: 0 4px 0 0;"><?= $child_license['status'] ?></span>
								<?php if ( ! empty( $child_license['is_lifetime'] ) ) : ?>
									<?= __( 'Lifetime', 'edd-helpscout' ); ?>
								<?php else : ?>
									<?= $child_license['is_expired'] ? __( 'Expired', 'edd-helpscout' ) : __( 'Expires', 'edd-helpscout' ); ?>: <?= $child_license['expires'] ?>
								<?php endif; ?>
							</li>
						<?php endif ?>
						<li class="c-sb-list-item" style="font-size: 12px; line-height: 16px; padding: 4px 10px 2px 0;"><strong><?= $child_license['title'] ?></strong></li>
						<li class="c-sb-list-item" style="font-size: 11px !important;">
							<?= $child_license['key'] ?>
							<a href="<?= $child_license['url']?>" target="_blank"><span class="icon-gear" style="top: 1px; margin-left: 4px; font-size: 16px;"></span></a>
						</li>
					</ul>
					<?php if ( $child_license['show_activations'] ): ?>
						<div class="c-sb-section c-sb-section--toggle nested" style="padding-bottom: 10px;">
							<div class="c-sb-section__title js-sb-toggle" style="font-size: 12px; border: none; padding: 0;">
								<?= __( 'Active sites', 'edd-helpscout' ); ?> (<?= $child_license['activation_count'] ?>/<?= $child_license['limit'] ?>) <i class="caret sb-caret"></i>
							</div>
							<div class="c-sb-section__body">
								<ul class="c-sb-list c-sb-list--compact">
									<?php foreach ($child_license['sites'] as $site): ?>
										<li class="c-sb-list-item--bullet" style="list-style-type: circle;">
											<a class="c-sb-list-item__link t-tx-blue-500" href="<?= esc_url( $site['url'] ) ?>"><?= $site['site'] ?></a>
											<a href="<?= esc_url( $site['deactivate_link'] ); ?>" target="_blank"> <small style="color: red;">(<?= __( 'deactivate', 'edd-helpscout' ) ?>)</small></a>
										</li>
									<?php endforeach ?>
								</ul>
							</div>
						</div>
					<?php endif ?>
				<?php endforeach ?>
				</div>
			<?php endif ?>
			<?php if (!empty($license['upgrades'])): ?>
				<div class="c-sb-section c-sb-section--toggle nested">
					<div class="c-sb-section__title js-sb-toggle" style="font-size: 12px; border: none; padding: 0 0 4px 0;">
						<?= __( 'Upgrades', 'edd-helpscout' ); ?> <i class="caret sb-caret"></i>
					</div>
					<div class="c-sb-section__body">
						<ul class="c-sb-timeline c-sb-timeline--list u-mrg-0">
							<?php foreach ($license['upgrades'] as $upgrade_id => $upgrade): ?>
								<li class="c-sb-timeline-item">
									<span class="c-sb-timeline-item__text">
										<a class="c-sb-timeline-item__link t-tx-blue-500" href="<?= $upgrade['url'] ?>" style="font-size: 12px;">
											<?= $upgrade['title'] ?> - <?= $upgrade['price_option'] ?>
										</a>
										<span class="c-sb-list-item__text__secondary t-tx-charcoal-400" style="display: block; margin-top: 4px; font-size: 11px; color: #999!important;">
											<?= $upgrade['price'] ?>
										</span>
									</span>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			<?php endif ?>
			<?php if ( $license['show_activations'] ): ?>
				<div class="c-sb-section c-sb-section--toggle nested">
					<div class="c-sb-section__title js-sb-toggle" style="font-size: 12px; border: none; padding: 0 0 4px 0;">
						<?= __( 'Active sites', 'edd-helpscout' ); ?> (<?= $license['activation_count'] ?>/<?= $license['limit'] ?>) <i class="caret sb-caret"></i>
					</div>
					<div class="c-sb-section__body">
						<ul class="c-sb-list c-sb-list--compact">
							<?php foreach ($license['sites'] as $site): ?>
								<li class="c-sb-list-item--bullet" style="list-style-type: circle;">
									<a class="c-sb-list-item__link t-tx-blue-500" href="<?= esc_url( $site['url'] ) ?>"><?= $site['site'] ?></a>
									<a href="<?= esc_url( $site['deactivate_link'] ); ?>" target="_blank"> <small style="color: red;">(<?= __( 'deactivate', 'edd-helpscout' ) ?>)</small></a>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			<?php endif ?>
			<?php if ( $license['is_expired'] ): ?>
				<div class="c-sb-section">
					<a href="<?= $license['renewal_url'] ?>"><?= __( 'Renew', 'edd-helpscout' ) ?></a>
				</div>
			<?php endif ?>
			<div class="divider" style="padding-top: 10px; border-bottom: 1px solid rgba(181,186,191,.2); margin-bottom: -1px;"></div>
		<?php endforeach ?>
	</div>
</div>


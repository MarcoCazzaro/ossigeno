<?php
/**
 * Contact block render template.
 *
 * @package Ossigeno
 * @var array  $block      Block settings and attributes.
 * @var bool   $is_preview Whether the block is being rendered in the editor preview.
 */

$anchor     = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
$heading    = get_field( 'ssnail_contact_heading' ) ?: 'Contattaci';
$form_intro = get_field( 'ssnail_contact_form_intro' ) ?: '';
$phone      = get_field( 'ssnail_opt_phone', 'option' );
$email      = get_field( 'ssnail_opt_email', 'option' );
$offices    = get_field( 'ssnail_opt_offices', 'option' );
?>
<section<?php echo $anchor; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="ssnail-block-contact py-32 bg-surface-container-low">
	<div class="px-6 md:px-24">

		<?php // Section header: heading + contact details. ?>
		<div class="flex flex-col md:flex-row md:items-end justify-between gap-8 pb-12 mb-12 border-b border-outline-variant/40">
			<h2 class="text-4xl md:text-5xl font-headline text-primary-container">
				<?php echo esc_html( $heading ); ?>
			</h2>
			<?php if ( $phone || $email ) : ?>
				<div class="flex flex-col gap-2 md:text-right">
					<?php if ( $phone ) : ?>
						<a href="<?php echo esc_url( 'tel:' . preg_replace( '/\s+/', '', $phone ) ); ?>" class="font-headline text-lg text-primary-container hover:text-secondary transition-colors flex items-center gap-2 md:justify-end">
							<span class="material-symbols-outlined text-secondary text-xl" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">call</span>
							<?php echo esc_html( $phone ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="font-headline text-lg text-primary-container hover:text-secondary transition-colors flex items-center gap-2 md:justify-end">
							<span class="material-symbols-outlined text-secondary text-xl" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">mail</span>
							<?php echo esc_html( $email ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-5 gap-16">

			<?php // Left column: form. ?>
			<div class="xl:col-span-2">
				<h3 class="text-2xl font-headline text-primary-container mb-6">
					<?php esc_html_e( 'Scrivici', 'ossigeno' ); ?>
				</h3>
				<?php if ( $form_intro ) : ?>
					<p class="text-on-surface-variant mb-8"><?php echo esc_html( $form_intro ); ?></p>
				<?php endif; ?>

				<?php if ( $is_preview ) : ?>
					<div class="border border-outline-variant/40 p-6 text-on-surface-variant/50 font-body text-sm">
						[ Modulo contatti — visibile sul front-end ]
					</div>
				<?php elseif ( function_exists( 'acf_form' ) ) : ?>
					<div class="ssnail-acf-contact-form">
						<?php
						acf_form(
							array(
								'post_id'             => 'new_post',
								'post_title'          => false,
								'post_content'        => false,
								'new_post'            => array(
									'post_type'   => 'ssnail_inquiry',
									'post_status' => 'publish',
								),
								'field_groups'        => array( 'group_ssnail_inquiry' ),
								'submit_value'        => __( 'Invia Richiesta', 'ossigeno' ),
								'updated_message'     => __( 'Messaggio inviato. La contatteremo al più presto.', 'ossigeno' ),
								'html_submit_button'  => '<input type="submit" class="btn btn-primary border-0" value="%s" />',
							)
						);
						?>
					</div>
				<?php endif; ?>
			</div>

			<?php // Right columns: offices. ?>
			<?php if ( $offices ) : ?>
				<div class="xl:col-span-3">
					<h3 class="text-2xl font-headline text-primary-container mb-8">
						<?php esc_html_e( 'Le nostre sedi', 'ossigeno' ); ?>
					</h3>
					<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
						<?php foreach ( $offices as $office_index => $office ) : ?>
							<div class="ssnail-office-<?php echo $office_index; ?><?php echo $office_index === 0 ? ' xl:col-span-2' : ''; ?>">
								<div class="flex items-center gap-3 mb-4">
									<span class="material-symbols-outlined text-secondary text-4xl flex-shrink-0" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">location_on</span>
									<div>
										<p class="text-primary-container font-headline text-4xl leading-none">
											<?php echo esc_html( $office['city'] ); ?>
										</p>
										<?php if ( $office['address'] ) : ?>
											<p class="text-on-surface-variant text-sm mt-1">
												<?php echo esc_html( $office['address'] ); ?>
											</p>
										<?php endif; ?>
									</div>
								</div>
								<?php if ( $office['maps_url'] ) : ?>
									<div class="aspect-[4/3] <?php echo $office_index === 0 ? ' xl:aspect-[8/3]' : ''; ?> overflow-hidden">
										<iframe
											src="<?php echo esc_url( $office['maps_url'] ); ?>"
											class="w-full h-full border-0 grayscale"
											loading="lazy"
											referrerpolicy="no-referrer-when-downgrade"
											title="<?php echo esc_attr( $office['city'] ); ?>"
										></iframe>
									</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>

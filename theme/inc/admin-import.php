<?php
/**
 * WP admin import page for Ossigeno demo data.
 *
 * Registers a submenu under Tools. Each import section is a separate AJAX
 * request (wp_ajax_ssnail_import_step), orchestrated sequentially by the
 * page JS. No single request does enough work to hit a server timeout.
 *
 * Intermediate image sizes are skipped during import — wp_generate_attachment_
 * metadata() only stores dimensions, no resizing. After all content steps,
 * the JS automatically runs one regenerate_thumbnail step per attachment so
 * thumbnail generation is spread across many short requests.
 *
 * Intermediate IDs (home page, news page, placeholder pages) are stored in
 * a transient between steps.
 *
 * Access is restricted to manage_options capability.
 *
 * @package Ossigeno
 */

add_action( 'admin_menu', 'ssnail_admin_import_register_page' );
add_action( 'wp_ajax_ssnail_import_step', 'ssnail_admin_import_step_ajax' );
add_action( 'wp_ajax_ssnail_check_import_path', 'ssnail_admin_import_check_path_ajax' );

/**
 * Registers the "Importa Ossigeno" submenu under Tools.
 */
function ssnail_admin_import_register_page(): void {
	add_submenu_page(
		'tools.php',
		__( 'Importa contenuti demo', 'ossigeno' ),
		__( 'Importa Ossigeno', 'ossigeno' ),
		'manage_options',
		'ssnail-import',
		'ssnail_admin_import_render_page'
	);
}

/**
 * AJAX handler: runs a single import step and returns its log entries.
 *
 * Accepted steps (run in this order by the page JS):
 *   cleanup (destructive only), site_identity, acf_options,
 *   home_page, news_page, placeholder_pages, menus,
 *   news_post (index 0–2), regenerate_thumbnail (one per attachment)
 *
 * All steps except regenerate_thumbnail filter out intermediate image sizes so
 * wp_generate_attachment_metadata() stores only dimensions without resizing.
 * The JS collects newly created attachment IDs from each step response and
 * queues a regenerate_thumbnail step for each one.
 */
function ssnail_admin_import_step_ajax(): void {
	check_ajax_referer( 'ssnail_import_action', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized', 403 );
		return;
	}

	$step        = sanitize_key( isset( $_POST['step'] ) ? $_POST['step'] : '' );
	$images_dir  = sanitize_text_field( wp_unslash( isset( $_POST['images_dir'] ) ? $_POST['images_dir'] : '' ) );
	$destructive = isset( $_POST['destructive'] ) && '1' === $_POST['destructive'];

	if ( 'site_identity' === $step && '' !== $images_dir ) {
		update_option( 'ssnail_import_images_dir', $images_dir );
	}

	require_once __DIR__ . '/import-functions.php';

	$log_entries = array();
	$log         = static function ( string $type, string $message ) use ( &$log_entries ): void {
		$log_entries[] = array(
			'type'    => $type,
			'message' => $message,
		);
	};

	global $wpdb;
	$wpdb->query( 'SET NAMES utf8mb4' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	set_time_limit( 120 );

	// Skip intermediate size generation for all steps except regenerate_thumbnail.
	// Each regenerate_thumbnail step handles exactly one attachment and runs after
	// all content has been imported, keeping every request well within timeout.
	$is_regen = 'regenerate_thumbnail' === $step;
	if ( ! $is_regen ) {
		add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array', PHP_INT_MAX );
	}

	// Snapshot existing attachment IDs so we can diff after the step.
	$track_att = ! in_array( $step, array( 'cleanup', 'regenerate_thumbnail' ), true );
	if ( $track_att ) {
		$att_ids_before = get_posts( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			array(
				'post_type'      => 'attachment',
				'posts_per_page' => -1,
				'post_status'    => 'inherit',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
	}

	// Read shared state (IDs produced by earlier steps).
	$state = get_transient( 'ssnail_import_state' );
	if ( ! is_array( $state ) ) {
		$state = array();
	}

	$extra = array();

	switch ( $step ) {
		case 'cleanup':
			if ( $destructive ) {
				ssnail_import_cleanup( $log );
			}
			$state = array();
			break;

		case 'site_identity':
			$state = array(); // Clear any leftover state from a previous run.
			ssnail_import_site_identity( $log, $destructive, $images_dir );
			break;

		case 'acf_options':
			ssnail_import_acf_options( $log, $destructive, $images_dir );
			break;

		case 'home_page':
			$home_page_id          = ssnail_import_home_page( $log, $destructive, $images_dir );
			$state['home_page_id'] = $home_page_id;
			$extra['home_page_id'] = $home_page_id;
			break;

		case 'news_page':
			$home_page_id          = isset( $state['home_page_id'] ) ? (int) $state['home_page_id'] : 0;
			$news_page_id          = ssnail_import_news_page( $log, $destructive, $home_page_id );
			$state['news_page_id'] = $news_page_id;
			$extra['news_page_id'] = $news_page_id;
			break;

		case 'placeholder_pages':
			$page_ids          = ssnail_import_placeholder_pages( $log, $destructive );
			$state['page_ids'] = $page_ids;
			break;

		case 'menus':
			$home_page_id = isset( $state['home_page_id'] ) ? (int) $state['home_page_id'] : 0;
			$news_page_id = isset( $state['news_page_id'] ) ? (int) $state['news_page_id'] : 0;
			$page_ids     = isset( $state['page_ids'] ) && is_array( $state['page_ids'] ) ? $state['page_ids'] : array();
			ssnail_import_menus( $log, $destructive, $home_page_id, $news_page_id, $page_ids );
			break;

		case 'news_post':
			$news_index = isset( $_POST['news_index'] ) ? (int) $_POST['news_index'] : null;
			ssnail_import_news_posts( $log, $destructive, $images_dir, $news_index );
			break;

		case 'regenerate_thumbnail':
			$att_id = isset( $_POST['att_id'] ) ? absint( $_POST['att_id'] ) : 0;
			if ( $att_id ) {
				$file = get_attached_file( $att_id );
				if ( $file && file_exists( $file ) ) {
					wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $file ) );
					$log( 'success', "  Thumbnail rigenerato (ID {$att_id})" );
				} else {
					$log( 'warning', "  File non trovato per attachment ID {$att_id}" );
				}
			}
			break;

		default:
			wp_send_json_error( array( 'message' => 'Unknown step: ' . $step ) );
			return;
	}

	// Diff to find attachment IDs created during this step.
	$step_att_ids = array();
	if ( $track_att ) {
		$att_ids_after = get_posts( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			array(
				'post_type'      => 'attachment',
				'posts_per_page' => -1,
				'post_status'    => 'inherit',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		$step_att_ids = array_values( array_diff( $att_ids_after, $att_ids_before ) );
	}

	set_transient( 'ssnail_import_state', $state, 30 * MINUTE_IN_SECONDS );

	wp_send_json_success(
		array_merge(
			array(
				'step'    => $step,
				'entries' => $log_entries,
				'att_ids' => $step_att_ids,
			),
			$extra
		)
	);
}

/**
 * AJAX handler: validates the images directory path.
 */
function ssnail_admin_import_check_path_ajax(): void {
	check_ajax_referer( 'ssnail_import_action', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized', 403 );
		return;
	}

	$path = sanitize_text_field( wp_unslash( isset( $_POST['path'] ) ? $_POST['path'] : '' ) );

	if ( '' === $path ) {
		wp_send_json_success( array( 'status' => 'empty' ) );
		return;
	}

	if ( ! is_dir( $path ) ) {
		wp_send_json_success(
			array(
				'status'  => 'invalid',
				'message' => __( 'Directory non trovata.', 'ossigeno' ),
			)
		);
		return;
	}

	if ( ! is_readable( $path ) ) {
		wp_send_json_success(
			array(
				'status'  => 'invalid',
				'message' => __( 'Directory non leggibile (permessi insufficienti).', 'ossigeno' ),
			)
		);
		return;
	}

	wp_send_json_success(
		array(
			'status'  => 'valid',
			'message' => __( 'Directory trovata e leggibile.', 'ossigeno' ),
		)
	);
}

/**
 * Renders the import admin page.
 */
function ssnail_admin_import_render_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Non hai i permessi necessari per accedere a questa pagina.', 'ossigeno' ) );
	}

	$images_dir   = (string) get_option( 'ssnail_import_images_dir', '' );
	$memory_limit = ini_get( 'memory_limit' );
	$run_nonce    = wp_create_nonce( 'ssnail_import_action' );

	// Validate stored path server-side for initial page load.
	$path_status = '';
	if ( '' !== $images_dir ) {
		if ( ! is_dir( $images_dir ) ) {
			$path_status = 'invalid:' . __( 'Directory non trovata.', 'ossigeno' );
		} elseif ( ! is_readable( $images_dir ) ) {
			$path_status = 'invalid:' . __( 'Directory non leggibile (permessi insufficienti).', 'ossigeno' );
		} else {
			$path_status = 'valid:' . __( 'Directory trovata e leggibile.', 'ossigeno' );
		}
	}

	list( $initial_path_type, $initial_path_msg ) = array_pad( explode( ':', $path_status, 2 ), 2, '' );

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Importa contenuti demo', 'ossigeno' ); ?></h1>
		<p><?php esc_html_e( 'Importa tutti i contenuti demo (news, home page, menu e opzioni) su una nuova installazione WordPress.', 'ossigeno' ); ?></p>

		<div id="ssnail-import-form-wrap">
			<form id="ssnail-import-form">
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="ssnail_images_dir"><?php esc_html_e( 'Directory immagini', 'ossigeno' ); ?></label>
						</th>
						<td>
							<input
								type="text"
								id="ssnail_images_dir"
								name="images_dir"
								value="<?php echo esc_attr( $images_dir ); ?>"
								class="regular-text code"
								placeholder="/home/forge/source/ossigeno/bin/images/"
							/>
							<p
								id="ssnail-path-status"
								style="margin-top:4px;<?php echo '' === $path_status ? 'display:none' : ''; ?>;color:<?php echo 'valid' === $initial_path_type ? '#46b450' : '#d63638'; ?>"
							>
								<?php
								if ( 'valid' === $initial_path_type ) {
									echo '&#x2713; ';
								} elseif ( 'invalid' === $initial_path_type ) {
									echo '&#x2717; ';
								}
								echo esc_html( $initial_path_msg );
								?>
							</p>
							<p class="description">
								<?php esc_html_e( 'Percorso assoluto alla directory bin/images/ del repository sorgente (es. /home/forge/source/ossigeno/bin/images/).', 'ossigeno' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Modalità', 'ossigeno' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><?php esc_html_e( 'Modalità importazione', 'ossigeno' ); ?></legend>
								<label>
									<input type="radio" name="mode" value="c" checked />
									<?php esc_html_e( 'Conservativa — salta i contenuti già esistenti', 'ossigeno' ); ?>
								</label>
								<br />
								<label>
									<input type="radio" name="mode" value="d" />
									<strong style="color:#d63638">
										<?php esc_html_e( 'Distruttiva — elimina prima tutti i contenuti creati dallo script, poi reimporta', 'ossigeno' ); ?>
									</strong>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'PHP memory_limit', 'ossigeno' ); ?></th>
						<td>
							<code><?php echo esc_html( $memory_limit ); ?></code>
							<p class="description">
								<?php esc_html_e( 'Impostato in php.ini / php-fpm pool. Il caricamento di molte immagini può richiedere 128M o più.', 'ossigeno' ); ?>
							</p>
						</td>
					</tr>
				</table>

				<?php
				submit_button(
					__( 'Avvia importazione', 'ossigeno' ),
					'primary',
					'ssnail_import_submit'
				);
				?>
			</form>
		</div>

		<div id="ssnail-import-progress" style="display:none">
			<div id="ssnail-import-notice" class="notice notice-info" style="margin-left:0">
				<p id="ssnail-import-status-msg"></p>
			</div>
			<pre id="ssnail-import-log" style="background:#1d2327;color:#c3c4c7;padding:16px;overflow:auto;max-height:60vh;font-size:12px;line-height:1.6;border-radius:4px;margin-top:0"></pre>
			<p id="ssnail-import-new" style="display:none">
				<button type="button" id="ssnail-import-reset" class="button">
					<?php esc_html_e( 'Nuova importazione', 'ossigeno' ); ?>
				</button>
			</p>
		</div>
	</div>

	<script>
	var ssnailImport = {
		nonce:   <?php echo wp_json_encode( $run_nonce ); ?>,
		ajaxUrl: <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>,
		steps: [
			{ id: 'site_identity' },
			{ id: 'acf_options' },
			{ id: 'home_page' },
			{ id: 'news_page' },
			{ id: 'placeholder_pages' },
			{ id: 'menus' },
			{ id: 'news_post', newsIndex: 0 },
			{ id: 'news_post', newsIndex: 1 },
			{ id: 'news_post', newsIndex: 2 }
		],
		stepLabels: {
			cleanup:           <?php echo wp_json_encode( __( 'Pulizia contenuti esistenti…', 'ossigeno' ) ); ?>,
			site_identity:     <?php echo wp_json_encode( __( 'Identità del sito…', 'ossigeno' ) ); ?>,
			acf_options:       <?php echo wp_json_encode( __( 'Opzioni del sito…', 'ossigeno' ) ); ?>,
			home_page:         <?php echo wp_json_encode( __( 'Home page…', 'ossigeno' ) ); ?>,
			news_page:         <?php echo wp_json_encode( __( 'Pagina blog…', 'ossigeno' ) ); ?>,
			placeholder_pages: <?php echo wp_json_encode( __( 'Pagine di servizio…', 'ossigeno' ) ); ?>,
			menus:             <?php echo wp_json_encode( __( 'Menu di navigazione…', 'ossigeno' ) ); ?>
		},
		i18n: {
			done:      <?php echo wp_json_encode( __( 'Importazione completata.', 'ossigeno' ) ); ?>,
			stepError: <?php echo wp_json_encode( __( 'Errore durante: ', 'ossigeno' ) ); ?>,
			pathCheck: <?php echo wp_json_encode( __( 'Verifica in corso…', 'ossigeno' ) ); ?>
		}
	};

	( function () {
		var formWrap  = document.getElementById( 'ssnail-import-form-wrap' );
		var progress  = document.getElementById( 'ssnail-import-progress' );
		var logPre    = document.getElementById( 'ssnail-import-log' );
		var notice    = document.getElementById( 'ssnail-import-notice' );
		var statusMsg = document.getElementById( 'ssnail-import-status-msg' );
		var newWrap   = document.getElementById( 'ssnail-import-new' );
		var resetBtn  = document.getElementById( 'ssnail-import-reset' );
		var form      = document.getElementById( 'ssnail-import-form' );
		var pathInput = document.getElementById( 'ssnail_images_dir' );
		var pathEl    = document.getElementById( 'ssnail-path-status' );

		var pathTimer = null;
		var allAttIds = [];

		var COLORS   = { success: '#68de7c', warning: '#f0c33c' };
		var PREFIXES = { success: '✓ ', warning: '⚠ ' };

		// --- Path validation ---

		function checkPath( path ) {
			if ( ! path ) {
				pathEl.style.display = 'none';
				return;
			}
			pathEl.style.display = 'block';
			pathEl.style.color   = 'inherit';
			pathEl.textContent   = ssnailImport.i18n.pathCheck;

			var data = new FormData();
			data.append( 'action', 'ssnail_check_import_path' );
			data.append( 'nonce', ssnailImport.nonce );
			data.append( 'path', path );

			fetch( ssnailImport.ajaxUrl, { method: 'POST', body: data } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( res ) {
					if ( ! res.success || res.data.status === 'empty' ) {
						pathEl.style.display = 'none';
						return;
					}
					pathEl.style.display = 'block';
					if ( res.data.status === 'valid' ) {
						pathEl.style.color = '#46b450';
						pathEl.textContent = '✓ ' + res.data.message;
					} else {
						pathEl.style.color = '#d63638';
						pathEl.textContent = '✗ ' + res.data.message;
					}
				} )
				.catch( function () { pathEl.style.display = 'none'; } );
		}

		pathInput.addEventListener( 'input', function () {
			clearTimeout( pathTimer );
			var val = this.value;
			pathTimer = setTimeout( function () { checkPath( val ); }, 500 );
		} );

		// --- Import steps ---

		function appendEntries( entries ) {
			entries.forEach( function ( entry ) {
				var span         = document.createElement( 'span' );
				span.style.color   = COLORS[ entry.type ] || 'inherit';
				span.style.display = 'block';
				span.textContent   = ( PREFIXES[ entry.type ] || '' ) + entry.message;
				logPre.appendChild( span );
			} );
			logPre.scrollTop = logPre.scrollHeight;
		}

		function stepLabel( stepObj, totalSteps ) {
			if ( stepObj.id === 'news_post' ) {
				return 'Articolo ' + ( stepObj.newsIndex + 1 ) + ' di 3…';
			}
			if ( stepObj.id === 'regenerate_thumbnail' ) {
				return 'Thumbnail ' + stepObj.regenIndex + ' di ' + totalSteps + '…';
			}
			return ssnailImport.stepLabels[ stepObj.id ] || stepObj.id;
		}

		function showComplete() {
			notice.className      = 'notice notice-success';
			statusMsg.textContent = ssnailImport.i18n.done;
			newWrap.style.display = 'block';
		}

		function showError( stepId ) {
			notice.className      = 'notice notice-error';
			statusMsg.textContent = ssnailImport.i18n.stepError + stepId;
			newWrap.style.display = 'block';
		}

		function runSteps( steps, index, imagesDir, destructive ) {
			if ( index >= steps.length ) {
				// All steps in this batch done — start thumbnail regeneration if needed.
				if ( allAttIds.length > 0 ) {
					var ids = allAttIds.slice();
					allAttIds = [];
					var regenSteps = ids.map( function ( id, i ) {
						return { id: 'regenerate_thumbnail', attId: id, regenIndex: i + 1 };
					} );
					runSteps( regenSteps, 0, imagesDir, destructive );
				} else {
					showComplete();
				}
				return;
			}

			var stepObj = steps[ index ];
			statusMsg.textContent = stepLabel( stepObj, steps.length );

			var data = new FormData();
			data.append( 'action', 'ssnail_import_step' );
			data.append( 'nonce', ssnailImport.nonce );
			data.append( 'step', stepObj.id );
			data.append( 'images_dir', imagesDir );
			data.append( 'destructive', destructive ? '1' : '0' );

			if ( stepObj.newsIndex !== undefined ) {
				data.append( 'news_index', stepObj.newsIndex );
			}
			if ( stepObj.attId !== undefined ) {
				data.append( 'att_id', stepObj.attId );
			}

			fetch( ssnailImport.ajaxUrl, { method: 'POST', body: data } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( res ) {
					if ( res.success ) {
						if ( Array.isArray( res.data.entries ) ) {
							appendEntries( res.data.entries );
						}
						if ( Array.isArray( res.data.att_ids ) && res.data.att_ids.length > 0 ) {
							allAttIds = allAttIds.concat( res.data.att_ids );
						}
					}
					if ( ! res.success ) {
						showError( stepObj.id );
						return;
					}
					runSteps( steps, index + 1, imagesDir, destructive );
				} )
				.catch( function () {
					showError( stepObj.id );
				} );
		}

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();

			var imagesDir   = form.querySelector( '[name="images_dir"]' ).value;
			var destructive = form.querySelector( '[name="mode"]:checked' ).value === 'd';
			var steps       = destructive
				? [ { id: 'cleanup' } ].concat( ssnailImport.steps )
				: ssnailImport.steps.slice();

			formWrap.style.display = 'none';
			progress.style.display = 'block';
			logPre.innerHTML       = '';
			notice.className       = 'notice notice-info';
			newWrap.style.display  = 'none';
			allAttIds              = [];

			runSteps( steps, 0, imagesDir, destructive );
		} );

		resetBtn.addEventListener( 'click', function () {
			progress.style.display = 'none';
			formWrap.style.display = 'block';
			logPre.innerHTML       = '';
		} );
	}() );
	</script>
	<?php
}

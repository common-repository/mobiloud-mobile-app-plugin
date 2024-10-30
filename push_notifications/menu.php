<?php
add_action( 'wp_ajax_ml_push_notification_manual_send', 'ml_push_notification_manual_send_callback' );
add_action( 'wp_ajax_ml_push_notification_history', 'ml_push_notification_history' );
add_action( 'wp_ajax_ml_push_notification_check_duplicate', 'ml_push_notification_check_duplicate' );
add_action( 'wp_ajax_ml_push_attachment_content', 'ml_push_attachment_content' );

function ml_push_notification_manual_send_callback() {
	if ( Mobiloud::is_action_allowed_ajax( 'tab_push', false ) ) {

		$result = 'There was an error sending this notification';
		if ( isset( $_POST['ml_push_notification_msg'] ) ) {
			$platform = array();
			switch ( $_POST['ml_push_notification_os'] ) {
				case 'all':
					$platform = array( 0, 1 );
					break;
				case 'android':
					$platform = array( 1 );
					break;
				case 'ios':
					$platform = array( 0 );
					break;
			}
			$tags     = array();
			$tagNames = array();
			$postId   = null;
			$url      = null;
			$no_tags  = ! empty( $_POST['ml_push_notification_no_tags'] );
			if ( strlen( $_POST['ml_push_notification_data_id'] ) > 0 ) {
				if ( strpos( $_POST['ml_push_notification_data_id'], 'custom' ) !== false ) {
					$postId = absint( $_POST['ml_push_notification_post_id'] );
				} elseif ( strpos( $_POST['ml_push_notification_data_id'], 'url' ) !== false ) {
					$url = esc_url_raw( $_POST['ml_push_notification_url'] );
				} else {
					$postId = absint( substr( $_POST['ml_push_notification_data_id'], 8 ) );
				}
			}
			if ( $postId != null ) {
				$tags     = ml_get_post_tag_ids( $postId );
				$tagNames = ml_get_post_tags( $postId );
			}
			$tags[]     = 'all';
			$tagNames[] = 'all';
			$payload    = array();
			if ( $postId !== null ) {
				$payload = array(
					'post_id' => $postId,
				);
				if ( Mobiloud::get_option( 'ml_push_include_image' ) ) {
					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $postId ), 'medium_large' );
					if ( is_array( $image ) ) {
						$payload['featured_image'] = $image[0];
					}
					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $postId ), 'thumbnail' );
					if ( is_array( $image ) ) {
						$payload['thumbnail'] = $image[0];
					}
				}
			} elseif ( $url !== null ) {
				$payload = array(
					'url' => $url,
				);
			}
			$data = array(
				'platform' => $platform,
				'msg'      => trim( wp_unslash( $_POST['ml_push_notification_msg'] ) ),
				'sound'    => 'default',
				'badge'    => '+1',
				'payload'  => $payload,
			);
			if ( ! $no_tags ) {
				$data['notags'] = true;
				$data['tags']   = $tags;
			} else {
				$tagNames = array();
			}
			require_once dirname( __FILE__ ) . '/class.mobiloud_notifications.php';
			$push_api = Mobiloud_Notifications::get();
			$result   = $push_api->send_notifications( $data, $tagNames );
		}

		header( 'Content-type: application/json' );
		echo wp_json_encode( $result );
		die();
	}
}

function ml_push_notification_history_ajax_load() {
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			loadNotificationHistory();

		});

		var loadNotificationHistory = function () {
			var data = {
				action: 'ml_push_notification_history',
				async: true,
				ml_nonce: jQuery( '#ml_nonce' ).val(),
			};
			jQuery("#ml_push_notification_history").css("display", "none");

			jQuery.post(ajaxurl, data, function (response) {
				//saving the result and reloading the div
				jQuery("#ml_push_notification_history").html(response).show();
			});
		};
	</script>
	<?php
}

function ml_push_notification_chart() {
	$notifications = ml_notifications( 100 );

	if ( is_array( $notifications ) && count( $notifications ) > 0 ) {
		?>

		<script type="text/javascript">
			google.load("visualization", "1", {packages: ["corechart"], callback: drawChart});

			function drawChart() {
				<?php ml_push_notification_chart_data(); ?>


				var options = {
					title: 'Latest Notifications',
					hAxis: {title: 'Notifications'}
				};

				var chart = new google.visualization.ColumnChart(document.getElementById('notifications_chart'));
				chart.draw(data, options);
			}
		</script>
		<div id="notifications_chart" style="width: 100%; height: 200px; margin: 0 auto; margin-bottom: 20px;"></div>
		<?Php
	}
}

function ml_push_notification_chart_data() {
	$data = 'var data = google.visualization.arrayToDataTable([
	[\'Date\', \'Count\'],';

	$notifications = ml_notifications( 100 );
	$dates         = array();
	if ( count( $notifications ) ) {
		foreach ( $notifications as $notification ) {
			if ( date( 'mY' ) === date( 'mY', $notification->time ) ) {
				// same month so group by day.
				$index = date( 'd M Y', $notification->time );
			} else {
				$index = date( 'M Y', $notification->time );
			}
			$dates[ $index ] = 1 + ( isset( $dates[ $index ] ) ? $dates[ $index ] : 0 );
		}
	}
	$dates = array_reverse( $dates );
	foreach ( $dates as $date => $count ) {
		$data .= '[\'' . $date . '\', ' . $count . '],';
	}
	$data  = rtrim( $data, ',' );
	$data .= ']);';
	echo $data; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
}

function ml_push_notification_history() {
	if ( Mobiloud::is_action_allowed_ajax( 'tab_push', false ) ) {
		ml_push_notification_chart();
		$date_time_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		?>
		<table class="wp-list-table widefat fixed posts">
			<thead>
				<tr>
					<th scope="col" id="time" class="manage-column column-time">Sent</th>
					<th scope="col" id="message" class="manage-column column-message">Message</th>
					<th scope="col" id="attachment" class="manage-column column-attachment">Attachment</th>
					<th scope="col" id="platform" class="manage-column column-platform" style="">Platform</th>
					<th scope="col" id="tags" class="manage-column column-tags" style="">Tags</th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th scope="col" id="time" class="manage-column column-time">Sent</th>
					<th scope="col" id="message" class="manage-column column-message">Message</th>
					<th scope="col" id="attachment" class="manage-column column-attachment">Attachment</th>
					<th scope="col" id="platform" class="manage-column column-platform" style="">Platform</th>
					<th scope="col" id="tags" class="manage-column column-tags" style="">Tags</th>
				</tr>
			</tfoot>

			<tbody id="the-list">
				<?php $notifications = ml_notifications( 100 ); ?>
				<?php if ( count( $notifications ) ) : ?>
					<?php foreach ( $notifications as $notification ) : ?>
						<?php
						$notificationPlatform = '';
						if ( $notification->android === 'Y' && $notification->ios === 'Y' ) {
							$notificationPlatform = 'All';
						} elseif ( $notification->android === 'Y' ) {
							$notificationPlatform = 'Android';
						} elseif ( $notification->ios === 'Y' ) {
							$notificationPlatform = 'iOS';
						}
						$attach = array();
						if ( $notification->post_id > 0 ) {
							$attach[] = 'post_id:' . $notification->post_id;
						}
						if ( ! empty( $notification->url ) ) {
							$attach[] = 'url:"' . $notification->url . '"';
						}
						?>
						<tr id="notification-<?php echo esc_attr( $notification->id ); ?>">
							<td class="column-time"><?php echo esc_html( date( $date_time_format, $notification->time ) ); ?></td>
							<td class="column-time"><?php echo esc_html( $notification->msg ); ?></td>
							<td class="column-time"><?php echo esc_html( ! empty( $attach ) ? '{' . implode( ', ', $attach ) . '}' : '' ); ?></td>
							<td class="column-time"><?php echo esc_html( $notificationPlatform ); ?></td>
							<td class="column-time"><?php ml_tags_to_labels( $notification->tags ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="5">No notifications found.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
		exit;
	}
}

function ml_push_notification_check_duplicate() {
	if ( Mobiloud::is_action_allowed_ajax( 'tab_push', false ) ) {
		$postId  = null;
		$url     = null;
		$android = null;
		$ios     = null;
		// phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification -- already checked using is_action_allowed_ajax()
		if ( strlen( $_POST['ml_push_notification_data_id'] ) > 0 ) {
			if ( strpos( $_POST['ml_push_notification_data_id'], 'custom' ) !== false ) {
				$postId = absint( $_POST['ml_push_notification_post_id'] );
			} elseif ( strpos( $_POST['ml_push_notification_data_id'], 'url' ) !== false ) {
				$url = esc_url_raw( $_POST['ml_push_notification_url'] );
			} else {
				$postId = absint( substr( $_POST['ml_push_notification_data_id'], 8 ) );
			}
		}

		switch ( $_POST['ml_push_notification_os'] ) {
			case 'all':
				$android = 'Y';
				$ios     = 'Y';
				break;
			case 'android':
				$android = 'Y';
				$ios     = 'N';
				break;
			case 'ios':
				$android = 'N';
				$ios     = 'Y';
				break;
		}
		$notifications = ml_get_notification_by(
			array(
				'msg'     => trim( $_POST['ml_push_notification_msg'] ),
				'post_id' => $postId,
				'url'     => $url,
				'android' => $android,
				'ios'     => $ios,
			)
		);
		// phpcs:enable
		echo count( $notifications ) > 0 ? 'true' : '';
		exit;
	}
}

function ml_push_notification_manual_send() {

	ml_push_notification_manual_send_div();

	?>


	<script type="text/javascript">
		jQuery(document).ready(function ($) {

			$("#ml_push_notification_msg").on("input", function () {
				limitChars(this, 107, 'ml_notification_chars');
			});

			jQuery("#ml_push_notification_data_id").change(function () {
				jQuery("#error-message").hide();
				jQuery("#ml_push_notification_post_id_row, #ml_push_notification_url_row").hide();
				if ($(this).val() === 'custom') {
					jQuery("#ml_push_notification_post_id_row").show();
				} else if ($(this).val() === 'url') {
					jQuery("#ml_push_notification_url_row").show();
				}
			});

			jQuery("#ml_push_notification_manual_send_submit").click(function () {

				if (validateNotification()) {
					var checkDuplicate = checkDuplicateNotification();

					var cont = true;
					if (checkDuplicate) {
						cont = confirm('It seems that you have sent this exact message already, are you sure you wish to send it again?');
					}

					if (cont) {
						jQuery("#ml_push_notification_manual_send_submit").val("<?php esc_attr_e( 'Sending...' ); ?>");
						jQuery("#ml_push_notification_manual_send_submit").attr("disabled", true);

						jQuery("#ml_push_notification_manual_send").css("opacity", "0.5");

						var data = {
							action: 'ml_push_notification_manual_send',
							ml_push_notification_msg: jQuery("#ml_push_notification_msg").val(),
							ml_push_notification_data_id: jQuery("#ml_push_notification_data_id").val(),
							ml_push_notification_post_id: jQuery("#ml_push_notification_post_id").val(),
							ml_push_notification_url: jQuery("#ml_push_notification_url").val(),
							ml_push_notification_os: jQuery("input[name='ml_push_notification_os']:checked").val(),
							ml_push_notification_no_tags: jQuery("input[name='ml_push_notification_no_tags']:checked").val() || '',
							ml_nonce: jQuery( '#ml_nonce' ).val(),
						};

						$.post(ajaxurl, data, function (response) {
							//saving the result and reloading the div
							jQuery("#ml_push_notification_manual_send_submit").val("<?php esc_attr_e( 'Send' ); ?>");
							jQuery("#ml_push_notification_manual_send_submit").attr("disabled", false);
							jQuery("#ml_push_notification_manual_send").css("opacity", "1.0");
							if (true === response) {
								loadNotificationHistory();
								jQuery("#success-message").show();
								setTimeout(function () {
									jQuery("#success-message").fadeOut();
									}, 2000);
							} else {
								if (false === response) {
									response = "There was an error sending this notification";
								} else {
									response = "There was an error sending this notification:<br>" + response;
								}
								jQuery('#error-message').html(response).show();
								setTimeout(function () {
									jQuery("#error-message").fadeOut();
									}, 20000);
							}
						});
					}
					return true;
				} else {
					return false;
				}
			});


		});

		var limitChars = function (txtMsg, CharLength, indicator) {
			chars = txtMsg.value.length;
			var chars_left  = CharLength - chars;
			document.getElementById(indicator).innerHTML = chars_left + " character" + (chars_left != 1 ? 's' : '') + " left.";
			if (chars > CharLength) {
				txtMsg.value = txtMsg.value.substring(0, CharLength);
				//Text in textbox was trimmed, re-set indicator value to 0
				document.getElementById(indicator).innerHTML = "0 characters left.";
			}
		}

		var checkDuplicateNotification = function () {
			var data = {
				action: 'ml_push_notification_check_duplicate',
				ml_push_notification_msg: jQuery("#ml_push_notification_msg").val(),
				ml_push_notification_data_id: jQuery("#ml_push_notification_data_id").val(),
				ml_push_notification_post_id: jQuery("#ml_push_notification_post_id").val(),
				ml_push_notification_url: jQuery("#ml_push_notification_url").val(),
				ml_push_notification_os: jQuery("input[name='ml_push_notification_os']:checked").val(),
				ml_nonce: jQuery( '#ml_nonce' ).val(),
			};
			var duplicate = false;
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				async: false,
				success: function (response) {
					if (jQuery.trim(response).length > 0) {
						duplicate = true;
					}
				}
			});
			return duplicate;
		};

		var isUrlValid = function (url) {
			return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
		}

		var validateNotification = function () {
			var errors = [];

			var message = jQuery.trim(jQuery("#ml_push_notification_msg").val());
			if (message.length === 0) {
				errors.push('Message cannot be blank');
			}

			var attach = jQuery("#ml_push_notification_data_id").val();
			if (attach === 'custom') {
				var customPostID = jQuery.trim(jQuery("#ml_push_notification_post_id").val());
				if (customPostID.length === 0) {
					errors.push('Custom ID cannot be blank');
				} else if (!jQuery.isNumeric(customPostID)) {
					errors.push('Custom ID must be a number');
				}
			}
			if (attach === 'url') {
				var customUrl = jQuery.trim(jQuery("#ml_push_notification_url").val());
				if (customUrl.length === 0) {
					errors.push('URL cannot be blank');
				} else if (!isUrlValid(customUrl)) {
					errors.push('You must enter a valid URL');
				}
			}

			if (errors.length > 0) {
				jQuery("#error-message").html(errors.join("<br/>")).show();
				return false;
			} else {
				jQuery("#error-message").hide();
				return true;
			}
		};
	</script>
	<?php
}

function ml_push_attachment_content() {
	if ( Mobiloud::is_action_allowed_ajax( 'tab_push', false ) ) {
		?>
		<option value=''>Select attachment...</option>
		<optgroup label="Custom">
			<option value="url">An external or internal URL</option>
			<option value="custom">Post/Page ID</option>
		</optgroup>
		<?php
		$posts = get_posts(
			array(
				'posts_per_page' => 10,
				'orderby'        => 'post_date',
				'order'          => 'DESC',
				'post_type'      => 'post',
			)
		);
		?>
		<?php
		$pages = get_pages(
			array(
				'sort_order'  => 'ASC',
				'sort_column' => 'post_title',
				'post_type'   => 'page',
				'post_status' => 'publish',
			)
		);
		?>
		<optgroup label="Posts">
			<?php foreach ( $posts as $post ) { ?>
				<option value="post_id-<?php echo esc_attr( $post->ID ); ?>">
					<?php echo esc_html( Mobiloud::trim_string( $post->post_title, 40 ) ); ?>
			</option><?php } ?>
		</optgroup>
		<optgroup label="Pages">
			<?php foreach ( $pages as $page ) { ?>
				<option value="post_id-<?php echo esc_attr( $page->ID ); ?>">
					<?php echo esc_html( Mobiloud::trim_string( $page->post_title, 40 ) ); ?>
			</option><?php } ?>
		</optgroup>
		<?php
	}
}

function ml_push_notification_manual_send_div() {
	?>
	<div class="ml_send_notification_box">
		<div id="error-message" class="error inline" style="display: none;"></div>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope='row'>
						<label for="ml_push_notification_msg">Message</label>
					</th>
					<td>
						<input id="ml_push_notification_msg" placeholder="Your message" name="ml_push_notification_msg"
							type="text" style="width: 100%" class='regular-text'/>
						<p id="ml_notification_chars" class="description">107 characters left.</p>
					</td>
				</tr>
				<tr>
					<th scope='row'>
						<label for='ml_push_notification_data_id'>Attach</label>
					</th>
					<td>
						<select id="ml_push_notification_data_id">
							<option value=''>Loading...</option>
						</select>
						<p class="description">You can attach a post or a page to your notification (optional).</p>
					</td>
				</tr>
				<tr id="ml_push_notification_post_id_row" style="display: none;">
					<th scope='row'>
						<label for="ml_push_notification_post_id">Custom Post/Page ID</label>
					</th>
					<td>
						<input id="ml_push_notification_post_id" placeholder="Custom ID" name="ml_push_notification_post_id"
							type="text" class='regular-text'/>
					</td>
				</tr>
				<tr id="ml_push_notification_url_row" style="display: none;">
					<th scope='row'>
						<label for="ml_push_notification_url">URL</label>
					</th>
					<td>
						<input id="ml_push_notification_url" placeholder="http://www.domain.com/url" name="ml_push_notification_url"
							type="url" class='regular-text' maxlength="255"/>
					</td>
				</tr>
				<tr>
					<th scope='row'>
						<label>Send to Platform</label>
					</th>
					<td>
						<p>
							<?php
							require_once dirname( __FILE__ ) . '/class.mobiloud_notifications.php';
							$push_api               = Mobiloud_Notifications::get();
							$registeredDevicesCount = $push_api->registered_devices_count();

							$total_count   = ( isset( $registeredDevicesCount['total'] ) ) ? $registeredDevicesCount['total'] : 0;
							$android_count = 0;
							$ios_count     = 0;
							if ( $registeredDevicesCount['android'] !== null ) {
								$total_count  += $registeredDevicesCount['android'];
								$android_count = $registeredDevicesCount['android'];
							}
							if ( $registeredDevicesCount['ios'] !== null ) {
								$total_count += $registeredDevicesCount['ios'];
								$ios_count    = $registeredDevicesCount['ios'];
							}
							?>
							<label>
								<input id="ml_push_notification_os_all" type="radio" name='ml_push_notification_os'
									value="all" checked/> All (<?php echo absint( $total_count ); ?> total devices)
							</label><br/>
							<label>
								<input id="ml_push_notification_android" type="radio" name='ml_push_notification_os'
									value="android"/> Android only
								<?php
								if ( ! is_null( $registeredDevicesCount['android'] ) ) {
									?>
								(<?php echo absint( $android_count ); ?> devices)<?php } ?>
							</label><br/>
							<label>
								<input id="ml_push_notification_ios" type="radio" name='ml_push_notification_os'
									value="ios"/> iOS only
								<?php
								if ( ! is_null( $registeredDevicesCount['ios'] ) ) {
									?>
								(<?php echo absint( $ios_count ); ?> devices)<?php } ?>
							</label>
						</p>
						<?php
						$app_id  = Mobiloud::get_option( 'ml_onesignal_app_id' );
						if ( ! empty( $app_id ) ) {
							?>
							<p class="description">For push notifications statistics, access the <a href="https://onesignal.com/apps/<?php echo esc_attr( $app_id ); ?>" target="_blank">Onesignal dashboard</a></p>
							<?php
						}
						?>

					</td>
				</tr>
				<tr>
					<th scope='row'>
						<label>Notification tags</label>
					</th>
					<td>
						<label>
							<input id="ml_push_notification_no_tags" type="checkbox" name='ml_push_notification_no_tags'
								value="1" /> Send this notification with no tags
						</label><br/>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input <?php echo ! Mobiloud_Admin::no_push_keys() ? '' : 'disabled' ?> type="submit" onclick="return false;" class='button button-primary button-large'
				id="ml_push_notification_manual_send_submit" value="<?php esc_attr_e( 'Send' ); ?>"/>
		</p>
	</div><!--.ml_send_notification_box-->
	<?php
}

function ml_tags_to_labels( $tags ) {
	if ( strlen( $tags ) > 0 ) {
		$tags = explode( ',', $tags );
		foreach ( $tags as $tag ) {
			echo '<div class="tag-label info">' . esc_html( $tag ) . '</div>';
		}
	}
}

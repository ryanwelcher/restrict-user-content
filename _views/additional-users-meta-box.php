<table class="form-table">
	<tr>
		<th scope="row"><?php esc_html_e( "Add media from user ID's", 'ruc' );?></th>
		<td>
			<input type="text" name="<?php echo esc_attr( $this->_settings_name ); ?>[additional_user_ids]" value="<?php esc_html_e( isset( $settings['additional_user_ids'] ) ? $settings['additional_user_ids'] : '' );?>"><br/>
			<small><?php esc_html_e( 'Enter a comma separated list of any additional users who\'s media you want available to logged in users in addition to their own.' );?></small>
		</td>
	</tr>
</table>
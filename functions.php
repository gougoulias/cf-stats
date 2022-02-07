<?php
// process to create the database table that will store the cached data of the results STARTS
function cfstat_install_db(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "
		CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			last_update datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			form_name tinytext NOT NULL,
			allstats longtext NOT NULL,
			dataPoints longtext NOT NULL,
			post_id mediumint NOT NULL,
			PRIMARY KEY  (id)
		);
		";

	$wpdb->get_results($sql);
}

// process to create the database table that will store the cached data of the results ENDS

//function to drop database cf_stat
function cfstat_drop_db(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	$sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}

//CF STATS User's Settings STARTS
function cf_stats_add_settings_page() {
    add_options_page( 'CF Stats page', 'CF Stats Settings', 'manage_options', 'cf_stats_plugin', 'cf_stat_render_plugin_settings_page' );
}
add_action( 'admin_menu', 'cf_stats_add_settings_page' );

function cf_stats_settings_init() {
  register_setting('cf_stats', 'cf_stats_settings');
  add_settings_section( 'cf_stats_settings_section', '', '', 'cf_stats' );
  add_settings_field( 'cf_stats_cached', 'Use of Cached Data', 'cf_stats_cached_render', 'cf_stats', 'cf_stats_settings_section' );

}
add_action('admin_init', 'cf_stats_settings_init');

function cf_stats_cached_render(){
	$options = get_option( 'cf_stats_settings' );
	?>
	<input id="off" type='radio' name='cf_stats_settings[cf_stats_cached]' <?php checked( $options['cf_stats_cached'], 'off' ); ?> value='off'>
	<label for="off">No, Disable cache</label>
	<br>
	<input id="on" type='radio' name='cf_stats_settings[cf_stats_cached]' <?php checked( $options['cf_stats_cached'], 'on' ); ?> value='on'>
	<label for="on">Yes, Enable cache</label>
	<?php
}

function cf_stat_render_plugin_settings_page(){
	?>
    <h2>Cf Stats Settings</h2>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'cf_stats' );
        do_settings_sections( 'cf_stats' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

//CF STATS User's Settings ENDS

//function that returns user's options about cache
function cached_option(){
	$options=get_option('cf_stats_settings');

	if (!$options['cf_stats_cached']){
		$cached_setting='off';
	}else{
		$cached_setting=$options['cf_stats_cached'];
	}
	return $cached_setting;
}


// functions that finds the post id that used the shortcode
function get_the_post_id_that_used_the_shotcode(){
	global $post;
	$post_id = get_the_ID();
	return $post_id;
}

//inserts the dataPoints to our database table
function cf_stat_all_stats_import($form_name,$allstats,$post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	$wpdb->insert( 
		$table_name, 
		array( 
			'last_update' => current_time( 'mysql' ), 
			'form_name' => $form_name, 
			'allstats' => $allstats,
			'post_id'=>$post_id, 
		) 
	);
}

// updates the dataPoints to our database table
function cf_stat_allstats_update($allstats,$post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	$wpdb->update( 
		$table_name, 
		array( 
			//'last_update' => current_time( 'mysql' ), 
			'allstats' => $allstats,
		),
		array(
			'post_id'=>$post_id, 
		) 
	);
}

//inserts the dataPoints to our database table
function cf_stat_data_import($dataPoints,$post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	$wpdb->update( 
		$table_name, 
		array( 
			'last_update' => current_time( 'mysql' ),  
			'dataPoints' => $dataPoints,
		),
		array(
			'post_id'=>$post_id, 
		) 
	);
}

// updates the dataPoints to our database table
function cf_stat_data_update($dataPoints,$post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	$wpdb->update( 
		$table_name, 
		array( 
			'last_update' => current_time( 'mysql' ), 
			'dataPoints' => $dataPoints,
		),
		array(
			'post_id'=>$post_id, 
		) 
	);
}

//checks if there are any data stored in the database table for the post_id that used the shortcode
function check_if_data_stored($post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	$result=$wpdb->get_var("SELECT dataPoints FROM $table_name WHERE post_id='$post_id'");
	if ($result!=0 || $result != null){
		return true;
	}else{
		return false;
	}
}

// returns allstats stored in the database table
function cf_stat_get_allstats($post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	$result=$wpdb->get_var("SELECT allstats FROM $table_name WHERE post_id='$post_id'");
	return $result;
}

// returns the dataPoints stored in the database table
function cf_stat_get_data($post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	$result=$wpdb->get_var("SELECT dataPoints FROM $table_name WHERE post_id='$post_id'");
	return $result;
}

// returns the time when data stored in the database table
function cf_stat_get_last_update($post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	$result=$wpdb->get_var("SELECT last_update FROM $table_name WHERE post_id='$post_id'");
	return $result;
}

//deletes (clear cached values) form database table
function cf_stat_clear_cached_data($post_id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_stats';
	$wpdb->delete( 
		$table_name, 
		array( 
			'post_id' => $post_id, 
		),
		['%d']
	);
}
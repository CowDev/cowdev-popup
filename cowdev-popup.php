<?php
/*
	Plugin Name: Cowdev Popup
	Plugin URI: https://www.cowdev.com
	Description: A popup plugin
	Version: 0.9.0
	Author: Cowdev
	Author URI: https://www.cowdev.com
	*/

    /**
     * Plugin setup
     * Register popup post type
     */
    function popup_setup() {
    	$labels = array(
    		'name' => __( 'Popup', 'cowdev_popups' ),
    		'singular_name' => __( 'Popup', 'cowdev_popups' ),
    		'add_new_item' => __( 'Add New Popup', 'cowdev_popups' ),
    		'edit_item' => __( 'Edit Popup', 'cowdev_popups' ),
    		'new_item' => __( 'New Popup', 'cowdev_popups' ),
    		'not_found' => __( 'No Popups found', 'cowdev_popups' ),
    		'all_items' => __( 'All Popups', 'cowdev_popups' )
    	);
    	$args = array(
    		'labels' => $labels,
    		'public' => true,
    		'show_ui' => true,
    		'show_in_menu' => true,
    		'has_archive' => true,
    		'map_meta_cap' => true,
    		'menu_icon' => 'dashicons-desktop',
    		'supports' => array( 'title', 'editor', 'author' )
    	);
    	register_post_type( 'popup', $args );
    }
    add_action( 'init', 'popup_setup' );

    /**
     * Add meta box
     *
     * @param post $post The post object
     * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
     */
    function popup_add_meta_boxes( $post ){
    	add_meta_box( 'popup_meta_box', __( 'Popup settings', 'cowdev_popups' ), 'popup_build_meta_box', 'popup', 'side', 'low' );
    }
    add_action( 'add_meta_boxes_popup', 'popup_add_meta_boxes' );
    /**
     * Build custom field meta box
     *
     * @param post $post The post object
     */
    function popup_build_meta_box( $post ){
    	// make sure the form request comes from WordPress
    	wp_nonce_field( basename( __FILE__ ), 'popup_meta_box_nonce' );
    	// retrieve the _popup_background current value
    	$current_background = get_post_meta( $post->ID, '_popup_background', true );
    	// retrieve the _popup_expires current value
    	$current_expires = get_post_meta( $post->ID, '_popup_expires', true );
    	// retrieve the _popup_popup_background current value
    	$current_popup_background = get_post_meta( $post->ID, '_popup_popup_background', true );
    	$positions = array( 'Top left', 'Top center', 'Top right', 'Middle left', 'Middle center', 'Middle right', 'Bottom left', 'Bottom center', 'Bottom right' );

    	// stores _popup_position array
    	$current_position = ( get_post_meta( $post->ID, '_popup_position', true ) ) ? get_post_meta( $post->ID, '_popup_position', true ) : array();
    	?>
    	<div class='inside'>

    		<h3><?php _e( 'Background', 'cowdev_popups' ); ?></h3>
    		<p>
    			<input type="radio" name="background" value="transparent" <?php checked( $current_background, 'transparent' ); ?> /> Transparent<br />
    			<input type="radio" name="background" value="white" <?php checked( $current_background, 'white' ); ?> /> White<br />
                <input type="radio" name="background" value="black" <?php checked( $current_background, 'black' ); ?> /> Black
    		</p>

    		<h3><?php _e( 'Popup background', 'cowdev_popups' ); ?></h3>
    		<p>
    			<input type="radio" name="popup_background" value="transparent" <?php checked( $current_popup_background, 'transparent' ); ?> /> Transparent<br />
    			<input type="radio" name="popup_background" value="white" <?php checked( $current_popup_background, 'white' ); ?> /> White<br />
                <input type="radio" name="popup_background" value="black" <?php checked( $current_popup_background, 'black' ); ?> /> Black
    		</p>

    		<h3><?php _e( 'Popup expire', 'cowdev_popups' ); ?></h3>
            <em><?php _e( 'Set the time in minutes after which the popup will show again. 0 for only once.', 'cowdev_popups' ); ?></em>
    		<p>
    			<input type="text" name="expires" value="<?php echo ($current_expires?$current_expires:"0"); ?>" /><br />
    		</p>

    		<h3><?php _e( 'Popup position', 'cowdev_popups' ); ?></h3>
    		<p>
    		<?php
    		foreach ( $positions as $position ) {
    			?>
    			<input type="radio" name="position" value="<?php echo $position; ?>" <?php checked( ( in_array( $position, $current_position ) ) ? $position : '', $position ); ?> /><?php echo $position; ?> <br />
    			<?php
    		}
    		?>
    		</p>
    	</div>
    	<?php
    }
    /**
     * Store custom field meta box data
     *
     * @param int $post_id The post ID.
     * @link https://codex.wordpress.org/Plugin_API/Action_Reference/save_post
     */
    function popup_save_meta_box_data( $post_id ){
    	// verify meta box nonce
    	if ( !isset( $_POST['popup_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['popup_meta_box_nonce'], basename( __FILE__ ) ) ){
    		return;
    	}
    	// return if autosave
    	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
    		return;
    	}
      // Check the user's permissions.
    	if ( ! current_user_can( 'edit_post', $post_id ) ){
    		return;
    	}
    	// store custom fields values
    	// background string
    	if ( isset( $_REQUEST['background'] ) ) {
    		update_post_meta( $post_id, '_popup_background', sanitize_text_field( $_POST['background'] ) );
    	}
    	// store custom fields values
    	// expires string
    	if ( isset( $_REQUEST['expires'] ) ) {
    		update_post_meta( $post_id, '_popup_expires', sanitize_text_field( $_POST['expires'] ) );
    	}
    	// store custom fields values
    	// popup_background string
    	if ( isset( $_REQUEST['popup_background'] ) ) {
    		update_post_meta( $post_id, '_popup_popup_background', sanitize_text_field( $_POST['popup_background'] ) );
    	}
    	// store custom fields values
    	// position array
    	if( isset( $_POST['position'] ) ){
    		$position = (array) $_POST['position'];
    		// sinitize array
    		$position = array_map( 'sanitize_text_field', $position );
    		// save data
    		update_post_meta( $post_id, '_popup_position', $position );
    	}else{
    		// delete data
    		delete_post_meta( $post_id, '_popup_position' );
    	}
    }
    add_action( 'save_post_popup', 'popup_save_meta_box_data' );

    /* Describe what the code snippet does so you can remember later on */
    add_action('wp_footer', 'cowdev_popup_code');
    function cowdev_popup_code(){
        $popups = get_posts( array(
                'post_type'         => 'popup',
                'numberposts'       => 1
            )
        );

        if( $popups ):
            foreach( $popups as $popup):
                $pos        = get_post_meta( $popup->ID, '_popup_position', true );
                $background = get_post_meta( $popup->ID, '_popup_background', true );
                $expires    = get_post_meta( $popup->ID, '_popup_expires', true );
            ?>
            <?php if ( $background && $background != 'transparent' ): ?>
                <div class="cowdev-overlay background-<?php echo $background; ?>"></div>
            <?php endif; ?>
            <div class="cowdev-popup <?php echo strtolower( $pos[0] ) . ' ' . get_post_meta( $popup->ID, '_popup_popup_background', true ); ?>" data-popup="<?php echo $popup->post_name; ?>" data-expires="<?php echo $expires; ?>">
                <div class="cowdev-popup-close"></div>
                <?php echo $popup->post_content; ?>
            </div>
            <?php
            endforeach;
        endif;
    }

    add_action("wp_enqueue_scripts", "cowdev_popup_scripts");
    function cowdev_popup_scripts() {
        wp_register_script('cowdev_popup_script',
                            plugin_dir_url( __FILE__ ) .'script.js',
                            array ('jquery'),
                            false, true);
        wp_enqueue_script('cowdev_popup_script');

        wp_register_style( 'cowdev_popup_style',
                            plugin_dir_url( __FILE__ ) . 'style.css' );
        wp_enqueue_style( 'cowdev_popup_style' );

    }

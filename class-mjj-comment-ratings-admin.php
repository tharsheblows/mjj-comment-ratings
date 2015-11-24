<?php

class MJJ_Comment_Ratings_Admin{

	protected static $instance = null;

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		} // end if

		return self::$instance;

	} // end get_instance

	private function __construct(){
		add_action( 'admin_init', array( 'MJJ_Comment_Ratings_Admin', 'add_ratings_to_post_type' ) );
	}

	// this adds a section to the discussion page which enables you to add ratings to particular post types
	public static function add_ratings_to_post_type() {

	 	add_settings_section(
			'mjj_comment_ratings',
			'Comment ratings',
			array( 'MJJ_Comment_Ratings_Admin', 'mjj_comment_ratings_callback_function'),
			'discussion'
		);

	 	add_settings_field(
			'mjj_choose_post_types_for_ratings',
			'Enbable ratings',
			array( 'MJJ_Comment_Ratings_Admin', 'mjj_choose_post_types_for_ratings_callback'),
			'discussion',
			'mjj_comment_ratings'
		);
	 	

	 	register_setting( 'discussion', 'mjj_choose_post_types_for_ratings' );
	 } 
	 
	 public static function mjj_comment_ratings_callback_function() {
	 		echo '<p>Choose which post types should have ratings enabled on their comments. Only those post types with comments enabled are shown.</p>';
	 }
	 

	 public static function mjj_choose_post_types_for_ratings_callback() {
	 		$post_types = get_post_types();

	 		$post_types_currently_chosen = get_option( 'mjj_choose_post_types_for_ratings', false );

	 		foreach( $post_types as $post_type ){

	 			if( post_type_supports( $post_type, 'comments' ) ){
	 				$checked = ( in_array( $post_type, (array)$post_types_currently_chosen ) ) ? 'checked' : '' ;
	 				echo "<input name='mjj_choose_post_types_for_ratings[]' type='checkbox' value='{$post_type}' class='code' " . $checked . " /> {$post_type}<br />";
	 			}
	 		}
	 }
}
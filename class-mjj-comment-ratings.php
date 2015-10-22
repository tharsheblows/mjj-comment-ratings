<?php

class MJJ_Comment_Ratings{

	protected static $instance = null;

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		} // end if

		return self::$instance;

	} // end get_instance

	private function __construct(){
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );

		add_action( 'comment_form_logged_in_after', array( $this, 'add_rating_field' ) );
		add_action( 'comment_post', array( $this, 'save_rating_field' ) );
		add_action( 'comment_post', array( $this, 'save_average_rating' ) );
	}

	public function add_styles() {
	 	// add in if clause here to target pages. Right now it goes on all.
		wp_register_style( 'mjj-comment-ratings', plugins_url( 'css/mjj-comment-ratings.css', __FILE__ ) );
		wp_enqueue_style( 'mjj-comment-ratings' );
	}


	public function add_scripts() {

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : ''; //.min

		wp_register_script( 'mjj-comment-ratings', plugins_url( "js/mjj-comment-ratings$suffix.js", __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'mjj-comment-ratings' );

	} // end add_scripts

	public function add_rating_field(){

    	echo '<ul class="choose-rating">';
    	for( $i = 1; $i <= 5; $i++ ){
    		echo '<li class="star-div grey-star choose" data-rating="'. $i .'"> &#x2605;</li>';
    	}
    	echo '</ul>';
    	echo '<div class="clear-rating"><span class="clear-icon">&#x02297;</span> clear rating</div>';
    	echo '<input type="hidden" name="review-rating" id="review-rating" value="0" />';

	}

	static function show_star_rating( $rating = 0, $width = 30 ){

		$star_fraction_width = ceil( $width * fmod( (float)$rating, 1 ) );

		$colour_stars = floor( $rating );

		$full_stars = ( $star_fraction_width != 0 ) ? 4 : 5; // if there is a partial star, we need to subtract one from the grey stars
		$grey_stars = $full_stars - $colour_stars; // this is 4 because one star will be mixed

		for( $i = 1; $i <= $colour_stars; $i++ ){
			echo '<div class="star-div colour-star" style="width: ' . $width . 'px; font-size: ' . $width . 'px;">';
      		echo '&#x2605;';
      		echo '</div>';
		}

		if( $star_fraction_width != 0 ){
			echo '<div class="star-div grey-star" style="width: ' . $width . 'px; font-size: ' . $width . 'px;">';
      			echo '&#x2605;';
      			echo '<div class="partial-star-div" style="width: ' . $star_fraction_width . 'px; font-size: ' . $width . 'px;">&#x2605;</div>';
      		echo '</div>';
      	}

      	if( $grey_stars > 0 ){
      		for( $i = 1; $i <= $grey_stars; $i++ ){
				echo '<div class="star-div grey-star" style="width: ' . $width . 'px; font-size: ' . $width . 'px;">';
      			echo '&#x2605;';
      			echo '</div>';
			}
		}
	}

	public function save_rating_field( $comment_id ){
		if ( ( isset( $_POST['review-rating'] ) ) && ( is_numeric( $_POST['review-rating'] ) ) ){
			$rating = (int)$_POST['review-rating'];
			add_comment_meta( $comment_id, '_mjj_comment_rating', $rating );
		}
	}

	public function save_average_rating( $comment_id ){
		$post_id = get_comment( $comment_id )->comment_post_ID;
		$average_rating = $this->calculate_average_rating( $post_id );

		update_post_meta( $post_id, '_mjj_post_rating', $average_rating );
	}

	public function calculate_average_rating( $post_id ){
		global $wpdb;

		$prepared_query = $wpdb->prepare(
				"
				SELECT AVG(m.meta_value) FROM $wpdb->commentmeta AS m
				INNER JOIN
					( SELECT comment_ID
						FROM $wpdb->comments
						WHERE comment_post_ID = %d
					) c
				ON c.comment_ID = m.comment_id WHERE m.meta_key = '_mjj_comment_rating'
				",
				$post_id
			);

		$average_rating = $wpdb->get_var( $prepared_query );

		return $average_rating;
	}


}

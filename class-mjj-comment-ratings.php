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

		// ratings are added for logged in users
		add_action( 'comment_form_logged_in_after', array( $this, 'add_rating_field' ) );

		// when the comment saves, it saves the individual comment rating, then re-calculates the rating of the post and saves that
		add_action( 'comment_post', array( $this, 'save_rating_field' ) );
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

	// this adds the ratings field to the comment form
	public function add_rating_field(){

		// does this post type have ratings enabled?
		$post_types = get_option( 'mjj_choose_post_types_for_ratings', false );
		if( ! in_array( get_post_type(), (array)$post_types ) ){
			return;
		}
		echo '<div id="initial-rating">';
		echo '<h4>Rate this recipe</h4>';
		echo '<p class="meta-info">Leave a rating between one star and five stars (five is the best!).
				If you&rsquo;d rather leave a review without a rating, we won&rsquo;t count yours if you leave this blank
				so don&rsquo;t worry about bringing the average down if you just have a question.</p>';
    	echo '<ul class="choose-rating">';
    	for( $i = 1; $i <= 5; $i++ ){
    		echo '<li class="star-div grey-star choose" data-rating="'. $i .'"> &#x2605;</li>';
    	}
    	echo '<li class="clear-rating"><span class="clear-icon">&#x02297;</span> delete rating</li>';
    	echo '</ul>';

    	echo '<input type="hidden" name="review-rating" id="review-rating" value="0" />';
    	echo '<input type="hidden" name="review-rating-nonce" id="review-rating-nonce" value="' . wp_create_nonce( 'review-ratings-nonce' ) . '" />';
    	echo '</div>';

	}

	// use this to show the star rating anywhere. Set $edit to true to get an editable field which you can use in a save function
	static function show_star_rating( $rating = 0, $width = 30, $edit = false ){

		$star_fraction_width = ceil( $width * fmod( (float)$rating, 1 ) );

		$colour_stars = floor( (float) $rating );

		$full_stars = ( $star_fraction_width != 0 ) ? 4 : 5; // if there is a partial star, we need to subtract one from the grey stars
		$grey_stars = $full_stars - $colour_stars; // this is 4 because one star will be mixed

		$ul_class = $li_class = '';

		if( $edit ){
			$ul_class = ' choose-rating';
			$li_class = ' set'; //disable the hover
		}

		$rating_list = '<ul class="star-rating' . $ul_class .  '">';

		for( $i = 1; $i <= $colour_stars; $i++ ){

			$chosen = ( $edit && ( $i == $colour_stars ) ) ? ' chosen-rating' : '';
			$rating_list .= '<li data-rating="' . $i . '" class="star-div colour-star' . $li_class . $chosen . '" style="width: ' . $width . 'px; font-size: ' . $width . 'px;">';
      		$rating_list .= '&#x2605;';
      		$rating_list .= '</li>';
		}

		if( $star_fraction_width != 0 ){
			$rating_list .= '<li data-rating="' . $i . '" class="star-div grey-star' . $li_class . '" style="width: ' . $width . 'px; font-size: ' . $width . 'px;">';
      			$rating_list .= '&#x2605;';
      			$rating_list .= '<div class="partial-star-div" style="width: ' . $star_fraction_width . 'px; font-size: ' . $width . 'px;">&#x2605;</div>';
      		$rating_list .= '</li>';
      		$i++;
      	}

      	if( $grey_stars > 0 ){
      		for( $j = 1; $j <= $grey_stars; $j++ ){
				$rating_list .= '<li data-rating="' . $i . '" class="star-div grey-star' . $li_class . '" style="width: ' . $width . 'px; font-size: ' . $width . 'px;">';
      			$rating_list .= '&#x2605;';
      			$rating_list .= '</li>';
      			$i++;
			}
		}

		$rating_list .= '</ul>';

		return $rating_list;
	}

	// this saves the initial comment rating to the commentmeta table with the metakey _mjj_comment_rating - it fires on comment_post, just after a comment is saved
	public function save_rating_field( $comment_id ){

		if( ( !isset( $_POST[ 'review-rating-nonce' ] ) ) || ! wp_verify_nonce( $_POST[ 'review-rating-nonce' ], 'review-ratings-nonce' ) ) {
			error_log( 'save_rating_field nonce failed', 0 );
			return;
		}

		$current_user = wp_get_current_user();
		if( ! wp_get_current_user() ){
			error_log( 'must be logged in to save rating', 0 );
			return;
		}

		$comment = get_comment( $comment_id );
		$comment_post_ID = $comment->comment_post_ID;
		$comment_author_id = $comment->user_id; // the comment author's user id

		// does this post type have ratings enabled?
		$post_types = get_option( 'mjj_choose_post_types_for_ratings', false );
		$post_type = get_post_type( $comment_post_ID );
		if( ! in_array( $post_type, (array)$post_types ) ){
			return;
		}

		// the default is that you can add the rating if you own the comment
		$user_is_author = ( (int)$comment_author_id === (int)$current_user->ID ) ? 'yes' : 'no';

		// this filter allows you to add moderators etc -- currently no one but the author can save a rating with this function
		$user_can_rate = apply_filters( 'mjj-can-save-rating', $user_is_author, $current_user );

		if( $user_can_rate === 'yes' ){

			if ( ( isset( $_POST['review-rating'] ) ) && ( is_numeric( $_POST['review-rating'] ) ) ){
				$rating = (int)$_POST['review-rating'];
				if( is_numeric( $rating ) && $rating !== 0 ){
					add_comment_meta( $comment_id, '_mjj_comment_rating', $rating );
				}
				else{
					delete_comment_meta( $comment_id, '_mjj_comment_rating' );
				}

				self::save_average_rating( $comment_id, $comment_post_ID );
			}
		}
	}

	// this saves the average rating to the postmeta table with the metakey _mjj_{$post_type}_rating - it fires on comment_post, just after a comment is saved
	public static function save_average_rating( $comment_id = 0, $post_id = 0 ){

		if( $comment_id === 0 && $post_id === 0 ){
			return;
		}

		$post_id = ( $post_id === 0 ) ? get_comment( $comment_id )->comment_post_ID : $post_id;

		if( $post_id === 0 ){
			return;
		}

		$post_type = get_post_type( $post_id );

		// does this post type have ratings enabled?
		$post_types = get_option( 'mjj_choose_post_types_for_ratings', false );
		if( ! in_array( $post_type, (array)$post_types ) ){
			return;
		}

		$average_rating = self::calculate_average_rating( $post_id );

		$update_rating = update_post_meta( (int)$post_id, '_mjj_' . esc_attr( $post_type ) .'_rating', number_format( (float)$average_rating, 4 ) );

		$saved_rating = ( $update_rating ) ? $average_rating : false;

		return $saved_rating;
	}

	// this calculates the average rating for a post
	private static function calculate_average_rating( $post_id ){
		global $wpdb;

		$prepared_query = $wpdb->prepare(
				"
				SELECT AVG(m.meta_value) FROM $wpdb->commentmeta AS m
				INNER JOIN
					( SELECT comment_ID
						FROM $wpdb->comments
						WHERE comment_post_ID = %d
						AND comment_approved = 1
					) c
				ON c.comment_ID = m.comment_id WHERE m.meta_key = '_mjj_comment_rating'
				",
				$post_id
			);

		$average_rating = $wpdb->get_var( $prepared_query );

		return $average_rating;
	}

}

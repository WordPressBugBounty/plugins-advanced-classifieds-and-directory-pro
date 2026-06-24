<?php

/**
 * Rank Math SEO.
 *
 * @link    https://plugins360.com
 * @since   3.4.2
 *
 * @package Advanced_Classifieds_And_Directory_Pro
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Public_Rank_Math class.
 *
 * @since 3.4.2
 */
class ACADP_Public_Rank_Math {

	/**
	 * Construct Rank Math title for our category, location & user_listings pages.
	 *
	 * @since  3.4.2
	 * @param  string $title The Rank Math title.
	 * @return string        Modified title.
	 */
	public function meta_title( $title ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $title;
		}

		$page_settings = get_option( 'acadp_page_settings' );

		if ( $post->ID != $page_settings['category'] && $post->ID != $page_settings['location'] && $post->ID != $page_settings['user_listings'] ) {
			return $title;
		}

		$title_template = '';

		// Category page
		if ( $post->ID == $page_settings['category'] ) {
			if ( $slug = get_query_var( 'acadp_category' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'acadp_categories' ) ) {
					// Get the global title template for the category pages
					$title_template = RankMath\Helper::get_settings( 'titles.tax_acadp_categories_title' );

					// Get the title template for the current category page (if available)
					$current_term_title_template = get_term_meta( $term->term_id, 'rank_math_title', true );
					if ( ! empty( $current_term_title_template ) ) {
						$title_template = $current_term_title_template;
					}

					if ( ! empty( $title_template ) ) {
						$title = RankMath\Helper::replace_vars( $title_template, $term );
					}
				}
			}
		}

		// Location page
		if ( $post->ID == $page_settings['location'] ) {
			if ( $slug = get_query_var( 'acadp_location' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'acadp_locations' ) ) {
					// Get the global title template for the location pages
					$title_template = RankMath\Helper::get_settings( 'titles.tax_acadp_locations_title' );

					// Get the title template for the current location page (if available)
					$current_term_title_template = get_term_meta( $term->term_id, 'rank_math_title', true );
					if ( ! empty( $current_term_title_template ) ) {
						$title_template = $current_term_title_template;
					}

					if ( ! empty( $title_template ) ) {
						$title = RankMath\Helper::replace_vars( $title_template, $term );
					}
				}
			}
		}

		// User listings page
		if ( $post->ID == $page_settings['user_listings'] ) {
			if ( $slug = acadp_get_user_slug() ) {
				if ( $user = get_user_by( 'slug', $slug ) ) {
					// Get the global title template for the pages
					$title_template = RankMath\Helper::get_settings( 'titles.pt_page_title' );

					// Get the title template for the current page (if available)
					$current_page_title_template = get_post_meta( $post->ID, 'rank_math_title', true );
					if ( ! empty( $current_page_title_template ) ) {
						$title_template = $current_page_title_template;
					}

					if ( ! empty( $title_template ) ) {
						$title_template = str_replace( '%title%', $user->display_name, $title_template );
						$title = RankMath\Helper::replace_vars( $title_template, $post );
					}
				}
			}
		}

		return $title;
	}

	/**
	 * Construct Rank Math description for our category, location & user_listings pages.
	 *
	 * @since  3.4.2
	 * @param  string $description The Rank Math description.
	 * @return string              Modified description.
	 */
	public function meta_description( $description ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $description;
		}

		$page_settings = get_option( 'acadp_page_settings' );

		if ( $post->ID != $page_settings['category'] && $post->ID != $page_settings['location'] && $post->ID != $page_settings['user_listings'] ) {
			return $description;
		}

		$description_template = '';

		// Category page
		if ( $post->ID == $page_settings['category'] ) {
			if ( $slug = get_query_var( 'acadp_category' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'acadp_categories' ) ) {
					// Get the global description template for the category pages
					$description_template = RankMath\Helper::get_settings( 'titles.tax_acadp_categories_description' );

					// Get the description template for the current category page (if available)
					$current_term_description_template = get_term_meta( $term->term_id, 'rank_math_description', true );
					if ( ! empty( $current_term_description_template ) ) {
						$description_template = $current_term_description_template;
					}

					if ( ! empty( $description_template ) ) {
						$description = RankMath\Helper::replace_vars( $description_template, $term );
					}
				}
			}
		}

		// Location page
		if ( $post->ID == $page_settings['location'] ) {
			if ( $slug = get_query_var( 'acadp_location' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'acadp_locations' ) ) {
					// Get the global description template for the location pages
					$description_template = RankMath\Helper::get_settings( 'titles.tax_acadp_locations_description' );

					// Get the description template for the current location page (if available)
					$current_term_description_template = get_term_meta( $term->term_id, 'rank_math_description', true );
					if ( ! empty( $current_term_description_template ) ) {
						$description_template = $current_term_description_template;
					}

					if ( ! empty( $description_template ) ) {
						$description = RankMath\Helper::replace_vars( $description_template, $term );
					}
				}
			}
		}

		// User listings page
		if ( $post->ID == $page_settings['user_listings'] ) {
			if ( $slug = acadp_get_user_slug() ) {
				if ( $user = get_user_by( 'slug', $slug ) ) {
					// Get the global description template for the pages
					$description_template = RankMath\Helper::get_settings( 'titles.pt_page_description' );

					// Get the description template for the current page (if available)
					$current_page_description_template = get_post_meta( $post->ID, 'rank_math_description', true );
					if ( ! empty( $current_page_description_template ) ) {
						$description_template = $current_page_description_template;
					}

					if ( ! empty( $description_template ) ) {
						$description_template = str_replace( '%title%', $user->display_name, $description_template );
						$description = RankMath\Helper::replace_vars( $description_template, $post );
					}
				}
			}
		}

		return $description;
	}

	/**
	 * Override the Rank Math canonical URL on our category, location & user_listings pages.
	 *
	 * @since  3.4.2
	 * @param  string $canonical_url The Rank Math canonical URL.
	 * @return string                Modified canonical URL.
	 */
	public function canonical_url( $canonical_url ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $canonical_url;
		}

		$page_settings = get_option( 'acadp_page_settings' );

		// Category page
		if ( $post->ID == $page_settings['category'] ) {
			if ( $slug = get_query_var( 'acadp_category' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'acadp_categories' ) ) {
					$canonical_url = acadp_get_category_page_link( $term );
				}
			}
		}

		// Location page
		if ( $post->ID == $page_settings['location'] ) {
			if ( $slug = get_query_var( 'acadp_location' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'acadp_locations' ) ) {
					$canonical_url = acadp_get_location_page_link( $term );
				}
			}
		}

		// User listings page
		if ( $post->ID == $page_settings['user_listings'] ) {
			if ( $slug = acadp_get_user_slug() ) {
				if ( $user = get_user_by( 'slug', $slug ) ) {
					$canonical_url = acadp_get_user_page_link( $user->ID );
				}
			}
		}

		return $canonical_url;
	}

}

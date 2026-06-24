<?php

/**
 * Yoast SEO.
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
 * ACADP_Public_Yoast_Seo class.
 *
 * @since 3.4.2
 */
class ACADP_Public_Yoast_Seo {

	/**
	 * Construct Yoast SEO title for our category, location & user_listings pages.
	 *
	 * @since  3.4.2
	 * @param  string $title The Yoast title.
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

		$wpseo_titles = get_option( 'wpseo_titles' );

		$sep_options = WPSEO_Option_Titles::get_instance()->get_separator_options();

		if ( isset( $wpseo_titles['separator'] ) && isset( $sep_options[ $wpseo_titles['separator'] ] ) ) {
			$sep = $sep_options[ $wpseo_titles['separator'] ];
		} else {
			$sep = '-'; // Setting default separator if Admin didn't set it from backed
		}

		$replacements = array(
			'%%sep%%'              => $sep,
			'%%page%%'             => '',
			'%%primary_category%%' => '',
			'%%sitename%%'         => get_bloginfo( 'name' )
		);

		$title_template = '';

		// Category page
		if ( $post->ID == $page_settings['category'] ) {
			if ( $slug = get_query_var( 'acadp_category' ) ) {
				// Get Archive SEO title
				if ( array_key_exists( 'title-tax-acadp_categories', $wpseo_titles ) ) {
					$title_template = $wpseo_titles['title-tax-acadp_categories'];
				}

				// Get Term SEO title
				if ( $term = get_term_by( 'slug', $slug, 'acadp_categories' ) ) {
					$replacements['%%term_title%%'] = $term->name;
					$replacements['%%term_description%%'] = $term->description;

					$meta = get_option( 'wpseo_taxonomy_meta' );

					if ( array_key_exists( 'acadp_categories', $meta ) ) {
						if ( array_key_exists( $term->term_id, $meta['acadp_categories'] ) ) {
							if ( array_key_exists( 'wpseo_title', $meta['acadp_categories'][ $term->term_id ] ) ) {
								$title_template = $meta['acadp_categories'][ $term->term_id ]['wpseo_title'];
							}
						}
					}
				}
			}
		}

		// Location page
		if ( $post->ID == $page_settings['location'] ) {
			if ( $slug = get_query_var( 'acadp_location' ) ) {
				// Get Archive SEO title
				if ( array_key_exists( 'title-tax-acadp_locations', $wpseo_titles ) ) {
					$title_template = $wpseo_titles['title-tax-acadp_locations'];
				}

				// Get Term SEO title
				if ( $term = get_term_by( 'slug', $slug, 'acadp_locations' ) ) {
					$replacements['%%term_title%%'] = $term->name;
					$replacements['%%term_description%%'] = $term->description;

					$meta = get_option( 'wpseo_taxonomy_meta' );

					if ( array_key_exists( 'acadp_locations', $meta ) ) {
						if ( array_key_exists( $term->term_id, $meta['acadp_locations'] ) ) {
							if ( array_key_exists( 'wpseo_title', $meta['acadp_locations'][ $term->term_id ] ) ) {
								$title_template = $meta['acadp_locations'][ $term->term_id ]['wpseo_title'];
							}
						}
					}
				}
			}
		}

		// User listings page
		if ( $post->ID == $page_settings['user_listings'] ) {
			if ( $slug = acadp_get_user_slug() ) {
				$user = get_user_by( 'slug', $slug );
				if ( ! empty( $user ) ) {
					$replacements['%%title%%'] = $user->display_name;
				}

				// Get Archive SEO title
				if ( array_key_exists( 'title-page', $wpseo_titles ) ) {
					$title_template = $wpseo_titles['title-page'];
				}

				// Get page meta title
				$meta = get_post_meta( $post->ID, '_yoast_wpseo_title', true );

				if ( ! empty( $meta ) ) {
					$title_template = $meta;
				}
			}
		}

		// Return
		if ( ! empty( $title_template ) ) {
			$title = strtr( $title_template, $replacements );
		}

		return $title;
	}

	/**
	 * Construct Yoast SEO description for our category, location & user_listings pages.
	 *
	 * @since  3.4.2
	 * @param  string $desc The Yoast description.
	 * @return string       Modified description.
	 */
	public function meta_description( $desc ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $desc;
		}

		$page_settings = get_option( 'acadp_page_settings' );

		if ( $post->ID != $page_settings['category'] && $post->ID != $page_settings['location'] && $post->ID != $page_settings['user_listings'] ) {
			return $desc;
		}

		$wpseo_titles = get_option( 'wpseo_titles' );

		$sep_options = WPSEO_Option_Titles::get_instance()->get_separator_options();

		if ( isset( $wpseo_titles['separator'] ) && isset( $sep_options[ $wpseo_titles['separator'] ] ) ) {
			$sep = $sep_options[ $wpseo_titles['separator'] ];
		} else {
			$sep = '-'; // Setting default separator if Admin didn't set it from backed
		}

		$replacements = array(
			'%%sep%%'              => $sep,
			'%%page%%'             => '',
			'%%primary_category%%' => '',
			'%%sitename%%'         => get_bloginfo( 'name' )
		);

		$desc_template = '';

		// Category page
		if ( $post->ID == $page_settings['category'] ) {
			if ( $slug = get_query_var( 'acadp_category' ) ) {
				// Get Archive SEO desc
				if ( array_key_exists( 'metadesc-tax-acadp_categories', $wpseo_titles ) ) {
					$desc_template = $wpseo_titles['metadesc-tax-acadp_categories'];
				}

				// Get Term SEO desc
				if ( $term = get_term_by( 'slug', $slug, 'acadp_categories' ) ) {
					$replacements['%%term_title%%'] = $term->name;
					$replacements['%%term_description%%'] = $term->description;

					$meta = get_option( 'wpseo_taxonomy_meta' );

					if ( array_key_exists( 'acadp_categories', $meta ) ) {
						if ( array_key_exists( $term->term_id, $meta['acadp_categories'] ) ) {
							if ( array_key_exists( 'wpseo_desc', $meta['acadp_categories'][ $term->term_id ] ) ) {
								$desc_template = $meta['acadp_categories'][ $term->term_id ]['wpseo_desc'];
							}
						}
					}
				}
			}
		}

		// Location page
		if ( $post->ID == $page_settings['location'] ) {
			if ( $slug = get_query_var( 'acadp_location' ) ) {
				// Get Archive SEO desc
				if ( array_key_exists( 'metadesc-tax-acadp_locations', $wpseo_titles ) ) {
					$desc_template = $wpseo_titles['metadesc-tax-acadp_locations'];
				}

				// Get Term SEO desc
				if ( $term = get_term_by( 'slug', $slug, 'acadp_locations' ) ) {
					$replacements['%%term_title%%'] = $term->name;
					$replacements['%%term_description%%'] = $term->description;

					$meta = get_option( 'wpseo_taxonomy_meta' );

					if ( array_key_exists( 'acadp_locations', $meta ) ) {
						if ( array_key_exists( $term->term_id, $meta['acadp_locations'] ) ) {
							if ( array_key_exists( 'wpseo_desc', $meta['acadp_locations'][ $term->term_id ] ) ) {
								$desc_template = $meta['acadp_locations'][ $term->term_id ]['wpseo_desc'];
							}
						}
					}
				}
			}
		}

		// User listings page
		if ( $post->ID == $page_settings['user_listings'] ) {
			if ( $slug = acadp_get_user_slug() ) {
				$user = get_user_by( 'slug', $slug );
				if ( ! empty( $user ) ) {
					$replacements['%%title%%'] = $user->display_name;
				}

				// Get Archive SEO desc
				if ( array_key_exists( 'metadesc-page', $wpseo_titles ) ) {
					$desc_template = $wpseo_titles['metadesc-page'];
				}

				// Get page meta desc
				$meta = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );

				if ( ! empty( $meta ) ) {
					$desc_template = $meta;
				}
			}
		}

		// Return
		if ( ! empty( $desc_template ) ) {
			$desc = strtr( $desc_template, $replacements );
		}

		return $desc;
	}

	/**
	 * Override the Yoast SEO canonical URL on our category, location & user_listings pages.
	 *
	 * @since  3.4.2
	 * @param  string $url The Yoast canonical URL.
	 * @return string      Modified canonical URL.
	 */
	public function canonical_url( $url ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $url;
		}

		$page_settings = get_option( 'acadp_page_settings' );

		// Location page
		if ( $post->ID == $page_settings['location'] ) {
			if ( $slug = get_query_var( 'acadp_location' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'acadp_locations' ) ) {
					$url = acadp_get_location_page_link( $term );
				}
			}
		}

		// Category page
		if ( $post->ID == $page_settings['category'] ) {
			if ( $slug = get_query_var( 'acadp_category' ) ) {
				if ( $term = get_term_by( 'slug', $slug, 'acadp_categories' ) ) {
					$url = acadp_get_category_page_link( $term );
				}
			}
		}

		// User listings page
		if ( $post->ID == $page_settings['user_listings'] ) {
			if ( $slug = acadp_get_user_slug() ) {
				$user = get_user_by( 'slug', $slug );
				if ( ! empty( $user ) ) {
					$url = acadp_get_user_page_link( $user->ID );
				}
			}
		}

		return $url;
	}

}

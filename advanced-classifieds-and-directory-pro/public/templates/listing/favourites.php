<?php

/**
 * Favourites.
 *
 * @link    https://pluginsware.com
 * @since   3.0.0
 *
 * @package Advanced_Classifieds_And_Directory_Pro
 */

if ( is_user_logged_in() ) : 
    $favourites = (array) get_user_meta( get_current_user_id(), 'acadp_favourites', true );
    ?>	
    <button type="button" class="acadp-button acadp-button-secondary acadp-button-add-to-favourites acadp-py-2<?php if ( in_array( $post->ID, $favourites ) ) echo ' acadp-hidden'; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="20px" height="20px" stroke-width="1.5" stroke="currentColor" class="acadp-flex-shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
        </svg>
        <?php esc_html_e( 'Favourite', 'advanced-classifieds-and-directory-pro' ); ?>
    </button>

    <button type="button" class="acadp-button acadp-button-secondary acadp-button-remove-from-favourites acadp-py-2<?php if ( ! in_array( $post->ID, $favourites ) ) echo ' acadp-hidden'; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="20px" height="20px" stroke-width="1.5" stroke="currentColor" class="acadp-flex-shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
        </svg>
        <?php esc_html_e( 'Unfavourite', 'advanced-classifieds-and-directory-pro' ); ?>
    </button>
<?php else : ?>
    <button type="button" class="acadp-button acadp-button-secondary acadp-button-require-login acadp-py-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="20px" height="20px" stroke-width="1.5" stroke="currentColor" class="acadp-flex-shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
        </svg>
        <?php esc_html_e( 'Favourite', 'advanced-classifieds-and-directory-pro' ); ?>
    </button>
<?php endif;

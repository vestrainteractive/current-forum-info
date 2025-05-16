<?php
/**
 * Plugin Name: BBPress Current Forum Info Shortcode
 * Description: Displays current bbPress forum description via shortcode [current_forum_info], detecting forum ID from URL slug including nested forums.
 * Version: 1.2
 * Author: Vestra Interactive
 * Author URI: https://vestrainteractive.com
 */

function bbp_current_forum_info_shortcode($atts) {
    $forum_id = 0;

    if (function_exists('bbp_is_forum') && bbp_is_forum()) {
        $forum_id = bbp_get_forum_id();
    }

    if (!$forum_id) {
        global $wp;

        $current_path = isset($wp->request) ? trim($wp->request, '/') : '';

        $forum_base_slug = 'forum';

        // Remove the forum base slug from path
        if (strpos($current_path, $forum_base_slug) === 0) {
            $forum_slug = substr($current_path, strlen($forum_base_slug));
            $forum_slug = trim($forum_slug, '/'); // e.g. 'from-the-road/views'
        } else {
            $forum_slug = $current_path;
        }

        if ($forum_slug) {
            $forum = get_page_by_path($forum_slug, OBJECT, 'forum');
            if ($forum) {
                $forum_id = $forum->ID;
            }
        }
    }

    if (!$forum_id) {
        return '';
    }

    $forum_description = get_the_excerpt($forum_id);
    if (!$forum_description) {
        $forum_description = apply_filters('the_content', get_post_field('post_content', $forum_id));
    }

    ob_start();
    ?>
    <div class="bbp-current-forum-info">
        <div class="forum-description"><?php echo $forum_description; ?></div>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('current_forum_info', 'bbp_current_forum_info_shortcode');

<?php
/*
Plugin Name: Post to Facebook
Description: Automatically post WordPress posts to a Facebook page.
Version: 1.0
Author: Durgesh Chander
*/

// Add a menu item in the admin dashboard
add_action('admin_menu', 'post_to_facebook_menu');

function post_to_facebook_menu() {
    add_options_page(
        'Post to Facebook Settings',
        'Post to Facebook',
        'manage_options',
        'post-to-facebook',
        'post_to_facebook_settings_page'
    );
}

// Render the settings page
function post_to_facebook_settings_page() {
    // Check if the user is allowed to make changes
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save the settings when the form is submitted in administration
    if (isset($_POST['submit'])) {
        check_admin_referer('post_to_facebook_save_settings');

        update_option('facebook_page_access_token', sanitize_text_field($_POST['facebook_page_access_token']));
        update_option('facebook_page_id', sanitize_text_field($_POST['facebook_page_id']));

        echo '<div class="updated"><p>Settings saved successfully!</p></div>';
    }

    // Retrieve stored options
    $access_token = get_option('facebook_page_access_token', '');
    $page_id = get_option('facebook_page_id', '');

    ?>
    <div class="wrap">
        <h1>Post to Facebook Settings</h1>
        <form method="post">
            <?php wp_nonce_field('post_to_facebook_save_settings'); ?>
            <table class="form-table table-responsive">
                <tr>
                    <th scope="row"><label for="facebook_page_access_token">Facebook Page Access Token</label></th>
                    <td><input type="text" id="facebook_page_access_token" name="facebook_page_access_token" value="<?php echo esc_attr($access_token); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="facebook_page_id">Facebook Page ID</label></th>
                    <td><input type="text" id="facebook_page_id" name="facebook_page_id" value="<?php echo esc_attr($page_id); ?>" class="regular-text"></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <?php
}

// Function to post a article in to Facebook Page
function post_to_facebook($post_id) {
    // Avoid infinite loops
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // check post are puplished in case of update post not work
    if (get_post_status($post_id) !== 'publish') {
        return;
    }

    // Retrieve Facebook API settings
    $pageAccessToken = get_option('facebook_page_access_token');
    $pageId = get_option('facebook_page_id');

    if (empty($pageAccessToken) || empty($pageId)) {
        return;
    }

    $title = get_the_title($post_id);
    $excerpt = get_the_excerpt($post_id);
    $url = get_permalink($post_id);

    if (!empty($excerpt)) {
        $message = $excerpt;
    } else {
        $content = get_post_field('post_content', $post_id);
        $message = wp_strip_all_tags($content);
    }

    $postUrl = "https://graph.facebook.com/{$pageId}/feed";

    $postData = [
        'message' => $message,
        'link' => $url,
        'access_token' => $pageAccessToken,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $postUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
    } else {
        $responseData = json_decode($response, true);

        if (!isset($responseData['id'])) {
            error_log('Error post posting to Facebook: ' . $responseData['error']['message']);
        }
    }

    curl_close($ch);
}
add_action('post_to_facebook', 'post_to_facebook');

function my_post_facebook_save($post_id, $post, $update) {
    // Ensure it only runs when the post is being published
    if ($post->post_status != 'publish' || wp_is_post_revision($post_id)) {
        return;
    }
   
    if (!wp_next_scheduled('post_to_facebook', array($post_id))) {
        wp_schedule_single_event(time() + 3, 'post_to_facebook', array($post_id));
    }
   

}

add_action('save_post', 'my_post_facebook_save', 10, 3);

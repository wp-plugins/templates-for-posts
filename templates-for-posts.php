<?php
/*
Plugin Name: Templates for Posts
Plugin URI: http://wordpress.org/extend/plugins/templates-for-posts/
Description: The same as page templates, but for posts.
Author: Nikolay Bachiyski
Version: 1.1-budev
Author URI: http://nikolay.bg/
Text Domain: templates-for-posts
*/

class PostTemplates {
	
	function init() {
		load_plugin_textdomain('templates-for-posts', false, 'templates-for-posts');
	}
	
	function template_redirect() {
		if (is_single()) {
			$page_template = get_page_template();
			if ($page_template && !preg_match('/page\.php$/', $page_template)) {
				include($page_template);
				die();
			}
		}
	}

	function save_post($post_id, $post) {
		$page_template = isset($_POST['post_template'])? $_POST['post_template'] : '';
		if ( !empty($page_template)  ) {
			$page_templates = get_page_templates();
			$post->page_template = $page_template;
			if ( 'default' != $page_template && !in_array($page_template, $page_templates) ) {
				return;
			}
			update_post_meta($post_id, '_wp_page_template',  $page_template);
		}
	}

	function meta_box_contents($post) {
		$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
?>
	<label class="hidden" for="post_template"><?php _e('Post Template', 'templates-for-posts') ?></label>
	<select name="post_template" id="post_template">
		<option value='default'><?php _e('Default Template', 'templates-for-posts'); ?></option>
		<?php page_template_dropdown($page_template); ?>
	</select>
<?php
	}

	function add_dropdown() {
		if ( 0 != count( get_page_templates() ) ) {
		    add_meta_box('posttemplatediv', __('Post Template', 'templates-for-posts'), array(&$this, 'meta_box_contents'), 'post');
		}
	}
};

$__post_templates = & new PostTemplates;
add_action('init', array(&$__post_templates, 'init'));
add_action('template_redirect', array(&$__post_templates, 'template_redirect'));
add_action('save_post', array(&$__post_templates, 'save_post'), 10, 2);
if (is_admin()) {
	add_action('admin_menu', array(&$__post_templates, 'add_dropdown'));
}

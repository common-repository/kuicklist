<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       kuick.co
 * @since      1.0.0
 *
 * @package    KuickList
 * @subpackage KuickList/public/partials
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <?php if (isset($kuicklist_checklist) && !empty($kuicklist_checklist->data->name )) : ?>
        <title><?php echo $kuicklist_checklist->data->name; ?></title>
    <?php else: ?>
        <title><?php echo wp_get_document_title(); ?></title>
    <?php endif;?>
    <?php wp_head(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
</head>
<body id="kuicklist_pub_page_id">
    <div id="kuicklist-iframe-container">
        <iframe src="<?php _e(esc_url($kuicklist_iframe_url)); ?>" allowfullscreen mozallowfullscreen webkitallowfullscreen>Your browser doesn't support iFrames.</iframe>
    </div>
</body>
</html>
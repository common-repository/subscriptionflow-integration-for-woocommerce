<?php
/**
 * SubscriptionFlow Integration for WooCommerce Uninstall
 *
 * Uninstalling deletes ptions.
 *
 * @version 1.2.3
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;


// Clear any cached data that has been removed.
wp_cache_flush();


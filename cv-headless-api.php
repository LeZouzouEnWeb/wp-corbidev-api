<?php
/**
 * Plugin Name: CV Headless API
 * Description: Gestion de CV modulaire avec API REST JSON
 * Version: 1.0.1
 * Author: Éric Corbisier
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/includes/Core/Plugin.php';

\Core\Plugin::init();

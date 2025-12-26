<?php

/**
 * Plugin Name: Headless API
 * Description: Gestion d'APIs modulaires avec API REST JSON
 * Version: 1.0.1
 * Author: Éric Corbisier
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/includes/Core/Plugin.php';

\Core\Plugin::init();
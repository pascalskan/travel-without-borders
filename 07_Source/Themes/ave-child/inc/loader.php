<?php
/**
 * Travel Without Borders Child Theme Loader
 *
 * Central entry point for loading all child theme components.
 *
 * Components must be loaded explicitly in dependency order.
 * Do not use directory globbing or automatic discovery.
 *
 * This architecture is defined in:
 * ADR-001-Testimonials-Architecture.md
 *
 * `functions.php` requires this file once; this file then lists explicit
 * `require_once` statements in dependency order.
 *
 * Rationale (see ADR-001 D9):
 *  - Deterministic loading — order is guaranteed across OS/filesystems.
 *  - Easier debugging — the boot sequence is one readable file; fatals are
 *    traceable to a line.
 *  - Predictable dependencies — dependency order is encoded and visible.
 *  - Reduced merge conflicts — adding a component is one placed line here;
 *    `functions.php` is not re-touched.
 *
 * A glob/loop over `inc/` is deliberately NOT used: it gives non-deterministic
 * order, hides dependencies, and risks including stray or half-written files.
 *
 * To add a component: add one `require_once` line below, in dependency order
 * (dependencies first).
 *
 * @package Ave Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$twb_inc = get_stylesheet_directory() . '/inc/';

// Existing component — unchanged.
require_once $twb_inc . 'hero-carousel.php';

// Testimonials — reusable WPBakery carousel element (own CSS/JS, on-demand).
require_once $twb_inc . 'testimonials.php';

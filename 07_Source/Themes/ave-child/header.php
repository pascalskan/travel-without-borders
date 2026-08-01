<?php
/**
 * The template for displaying the header
 *
 * @package Ave theme
 */

?><!DOCTYPE html>
<html <?php language_attributes( 'html' ); ?>>
<head <?php liquid_helper()->attr( 'head' ); ?>>
	<?php // Google Tag Manager: printed by twb_cookie_consent_head() (inc/cookie-consent.php) via wp_head, as a dormant function that only loads once the visitor accepts the cookie banner — not hardcoded here so it can't fire before consent. ?>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ) ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?> <?php liquid_helper()->attr( 'body' ); ?>>
	<?php // Google Tag Manager noscript fallback: inserted dynamically by twbLoadGTM() only after consent — see inc/cookie-consent.php. A static <noscript> here would ping GTM unconditionally, bypassing consent entirely for JS-disabled visitors. ?>
	<?php liquid_action( 'before' ) ?>

	<div id="wrap">

		<?php
			liquid_action( 'before_header' );
			liquid_action( 'header' );
			liquid_action( 'after_header' );
		?>

		<main <?php liquid_helper()->attr( 'content' ); ?>>
			<?php liquid_action( 'before_content' ); ?>

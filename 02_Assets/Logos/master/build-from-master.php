<?php
/**
 * Build header logo assets from the master artwork Savita supplied (2026-09-04).
 *
 * Run from anywhere:  php 02_Assets/Logos/master/build-from-master.php
 *
 * The master is a JPEG on white with no alpha, and the header needs
 * transparency, so it cannot be dropped in as-is. Two ordering decisions
 * matter:
 *
 *  - Resample BEFORE keying. "Composited on white" is already premultiplied,
 *    so resampling in that space gives clean edges; keying to alpha first and
 *    resampling after fringes every letter.
 *  - Fit to the ARTWORK box, not to the canvas. The master's own margins差
 *    from the in-use file's, so each output places the artwork at a stated
 *    box rather than filling the canvas. Every size below is an exact integer
 *    multiple of the one above it, so a 2x file is double its base in every
 *    number.
 */

$OUT    = __DIR__ . '/../';
$master = imagecreatefromjpeg( __DIR__ . '/twb-logo-master-1747x356.jpg' );

// Measured: the master's artwork occupies x 3..1746, y 1..351.
$SRC = array( 'x' => 3, 'y' => 1, 'w' => 1744, 'h' => 351 );

// canvas w, canvas h, art x, art y, art w, art h
// 337/674 reproduce the logo already in the header exactly (art 334x67 at 0,2).
// 400/800 are the enlarged pair; margins stay 3 right and 1 bottom as before.
$SIZES = array(
	// 337 is a control build only - it reproduces the logo already on the site,
	// so it is written to control/ and never over the reference copies.
	'337' => array( 337,  70, 0, 2, 334,  67 ),
	'674' => array( 674, 140, 0, 4, 668, 134 ),
	'400' => array( 400,  83, 0, 2, 397,  80 ),
	'800' => array( 800, 166, 0, 4, 794, 160 ),
);

/**
 * Key the white background back out to alpha.
 *
 * Every colour in this logo - black, flag red, flag gold - has at least one
 * channel at zero, so alpha = 255 - min(r,g,b) recovers it exactly and the
 * colour divides back out. That also swallows the JPEG ringing around the
 * white, which would otherwise survive as a grey halo.
 */
function twb_white_to_alpha( $src, $w, $h ) {
	$out = imagecreatetruecolor( $w, $h );
	imagealphablending( $out, false );
	imagesavealpha( $out, true );
	for ( $y = 0; $y < $h; $y++ ) {
		for ( $x = 0; $x < $w; $x++ ) {
			$c = imagecolorat( $src, $x, $y );
			$r = ( $c >> 16 ) & 255; $g = ( $c >> 8 ) & 255; $b = $c & 255;
			$a = 255 - min( $r, $g, $b );
			if ( 0 === $a ) {
				imagesetpixel( $out, $x, $y, 0x7FFFFFFF );
				continue;
			}
			$white = 255 - $a;
			$r = max( 0, min( 255, (int) round( ( $r - $white ) * 255 / $a ) ) );
			$g = max( 0, min( 255, (int) round( ( $g - $white ) * 255 / $a ) ) );
			$b = max( 0, min( 255, (int) round( ( $b - $white ) * 255 / $a ) ) );
			$pa = 127 - (int) round( $a * 127 / 255 );   // GD: 0 opaque, 127 clear
			imagesetpixel( $out, $x, $y, ( $pa << 24 ) | ( $r << 16 ) | ( $g << 8 ) | $b );
		}
	}
	return $out;
}

/**
 * Locate the German flag block by colour rather than by a hard-coded column.
 *
 * Nothing else in the logo is red or gold, and the flag is a solid stack, so
 * every column inside it carries both. Detecting it keeps the white variant
 * correct at any output size.
 */
function twb_flag_columns( $im, $w, $h ) {
	$lo = $w; $hi = -1;
	for ( $x = 0; $x < $w; $x++ ) {
		$red = false; $gold = false;
		for ( $y = 0; $y < $h; $y++ ) {
			$c = imagecolorat( $im, $x, $y );
			if ( 0x7F === ( ( $c >> 24 ) & 0x7F ) ) {
				continue;   // fully clear, nothing to judge
			}
			$r = ( $c >> 16 ) & 255; $g = ( $c >> 8 ) & 255; $b = $c & 255;
			if ( $r > 150 && $g < 90 && $b < 90 ) { $red = true; }
			if ( $r > 200 && $g > 150 && $b < 90 ) { $gold = true; }
		}
		// "or", not "and": the flag's outermost columns are antialiased, so one
		// band can register there while the other has not started yet. Missing
		// them would whiten a hairline of the flag's own black band.
		if ( $red || $gold ) {
			if ( $x < $lo ) { $lo = $x; }
			if ( $x > $hi ) { $hi = $x; }
		}
	}
	return array( $lo, $hi );
}

/**
 * The white variant differs from the black one only by turning pure black to
 * pure white - and only outside the flag, whose own top band stays black.
 * Confirmed against the existing 337px pair: the sole difference between them
 * is 000000 -> FFFFFF with the alpha untouched.
 */
function twb_to_white( $src, $w, $h, $flag_lo, $flag_hi ) {
	$out = imagecreatetruecolor( $w, $h );
	imagealphablending( $out, false );
	imagesavealpha( $out, true );
	for ( $y = 0; $y < $h; $y++ ) {
		for ( $x = 0; $x < $w; $x++ ) {
			$c  = imagecolorat( $src, $x, $y );
			$pa = ( $c >> 24 ) & 0x7F;
			$r  = ( $c >> 16 ) & 255; $g = ( $c >> 8 ) & 255; $b = $c & 255;
			if ( ( $x < $flag_lo || $x > $flag_hi ) && $r < 40 && $g < 40 && $b < 40 ) {
				$r = $g = $b = 255;
			}
			imagesetpixel( $out, $x, $y, ( $pa << 24 ) | ( $r << 16 ) | ( $g << 8 ) | $b );
		}
	}
	return $out;
}

foreach ( $SIZES as $name => $box ) {
	list( $cw, $ch, $dx, $dy, $dw, $dh ) = $box;

	$plate = imagecreatetruecolor( $cw, $ch );
	imagefill( $plate, 0, 0, imagecolorallocate( $plate, 255, 255, 255 ) );
	imagecopyresampled( $plate, $master, $dx, $dy, $SRC['x'], $SRC['y'], $dw, $dh, $SRC['w'], $SRC['h'] );

	$black = twb_white_to_alpha( $plate, $cw, $ch );
	list( $lo, $hi ) = twb_flag_columns( $black, $cw, $ch );
	$white = twb_to_white( $black, $cw, $ch, $lo, $hi );

	// PHP turns numeric array keys into ints, so compare as a string.
	$dir = ( '337' === (string) $name ) ? __DIR__ . '/control/' : $OUT;
	if ( ! is_dir( $dir ) ) {
		mkdir( $dir, 0777, true );
	}
	imagepng( $black, $dir . "logo-black-{$name}px.png" );
	imagepng( $white, $dir . "logo-white-{$name}px.png" );

	printf( "%-4s  %dx%d  art %dx%d at %d,%d  flag cols %d..%d  %d / %d bytes\n",
		$name, $cw, $ch, $dw, $dh, $dx, $dy, $lo, $hi,
		filesize( $dir . "logo-black-{$name}px.png" ),
		filesize( $dir . "logo-white-{$name}px.png" ) );
}

<?php
/**
 * Build true 2x header logos from the master artwork Savita supplied.
 *
 * The master is a JPEG on white, so the white is keyed back out to alpha after
 * resampling - resampling while the artwork is still composited on white is the
 * correct order, because "composited on white" is already premultiplied and
 * gives clean edges. The artwork is fitted to exactly twice the bounding box of
 * the logo already in use, at exactly twice its offset, so the header framing
 * does not shift by a pixel: only the resolution changes.
 */
// Run from anywhere: php 02_Assets/Logos/master/build-2x-from-master.php
$SP = __DIR__ . '/../';
$master = imagecreatefromjpeg(__DIR__ . '/twb-logo-master-1747x356.jpg');

// Measured: master artwork occupies x 3..1746, y 1..351.
$sx = 3; $sy = 1; $sw = 1744; $sh = 351;
// Measured: existing 337x70 logo places its artwork at x 0, y 2, size 334x67.
$CW = 674; $CH = 140; $dx = 0; $dy = 4; $dw = 668; $dh = 134;

$canvas = imagecreatetruecolor($CW, $CH);
imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
imagecopyresampled($canvas, $master, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh);

// White -> alpha. For every colour in this logo (black, flag red, flag gold)
// at least one channel is 0, so the alpha is recoverable exactly and the
// colour can be unpremultiplied off the white.
$out = imagecreatetruecolor($CW, $CH);
imagealphablending($out, false);
imagesavealpha($out, true);
for ($y = 0; $y < $CH; $y++) {
	for ($x = 0; $x < $CW; $x++) {
		$c = imagecolorat($canvas, $x, $y);
		$r = ($c >> 16) & 255; $g = ($c >> 8) & 255; $b = $c & 255;
		$a = 255 - min($r, $g, $b);
		if ($a === 0) { imagesetpixel($out, $x, $y, 0x7FFFFFFF); continue; }
		$w = 255 - $a;
		$r = max(0, min(255, (int) round(($r - $w) * 255 / $a)));
		$g = max(0, min(255, (int) round(($g - $w) * 255 / $a)));
		$b = max(0, min(255, (int) round(($b - $w) * 255 / $a)));
		$pa = 127 - (int) round($a * 127 / 255);   // GD alpha: 0 opaque, 127 clear
		imagesetpixel($out, $x, $y, ($pa << 24) | ($r << 16) | ($g << 8) | $b);
	}
}
imagepng($out, $SP.'logo-black-674px.png');

// The white variant differs from the black one only by turning pure black to
// pure white in the wordmark and the vertical "germany" - the flag block keeps
// its own black band. Measured on the existing pair, the flag occupies columns
// 281..318 of 337, so at 2x it is columns 562..637.
$white = imagecreatetruecolor($CW, $CH);
imagealphablending($white, false);
imagesavealpha($white, true);
for ($y = 0; $y < $CH; $y++) {
	for ($x = 0; $x < $CW; $x++) {
		$c = imagecolorat($out, $x, $y);
		$pa = ($c >> 24) & 0x7F; $r = ($c >> 16) & 255; $g = ($c >> 8) & 255; $b = $c & 255;
		$inflag = ($x >= 562 && $x <= 637);
		if (!$inflag && $r < 40 && $g < 40 && $b < 40) { $r = $g = $b = 255; }
		imagesetpixel($white, $x, $y, ($pa << 24) | ($r << 16) | ($g << 8) | $b);
	}
}
imagepng($white, $SP.'logo-white-674px.png');

foreach (['logo-black-674px.png', 'logo-white-674px.png'] as $f) {
	printf("%-24s %d bytes  %s\n", $f, filesize($SP.$f), implode('x', array_slice(getimagesize($SP.$f), 0, 2)));
}

<?php
require('config.php');
private function download_file($path, $url) {
	$newfname = $path;
	$file = fopen ($url, "rb");
	if ($file) {
		$newf = fopen ($newfname, "wb");

		if ($newf)
			while(!feof($file)) {
				fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
			}
	}

	if ($file) {
		fclose($file);
	}

	if ($newf) {
		fclose($newf);
	}
}

function download_aga_file($aga_ratings_txt)
{
	// download_file($path, "https://usgo.org/mm/tdlista.txt");
	$newf = fopen ($newfname, "wb");
	if ($newf) {
		fwrite($newf, AGA_RATING_NAMES);
		fclose($newf);
	}
}

function main()
{
	$aga_ratings_txt = "/tmp/gohappycup_tdlista.txt";
	download_aga_file($aga_ratings_txt);
	import_aga_ratings($aga_ratings_txt);
}

main();
?>

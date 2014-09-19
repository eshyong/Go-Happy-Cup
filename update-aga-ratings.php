<?php
// read AGA ratings and update club ratings with AGA ratings
// if the AGA ratings have been updated in the past 2 months

private function downloadFile ($url, $path) {
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

function main() {
	// download AGA file
	// load into AGA table
	// update players ratings
}

?>

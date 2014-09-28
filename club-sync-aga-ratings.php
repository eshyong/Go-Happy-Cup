<?php
require_once('config.php');
// TODO: Find out how to download files using HTTP for better error handling.
function download_file($path, $url) {
    // Ask the website for a file.
	$download = fopen ($url, "rb");
	if (!$download) {
        // HTTP request failed somehow.
        echo "Request failed";
        return false;
    }

    $localfile = fopen ($path, "wb");
    if (!$localfile) {
        // Some system error.
        echo "Error creating file";
        return false;
    }
    while(!feof($download)) {
        // Read in chunks.
        fwrite($localfile, fread($download, 1024 * 8 ), 1024 * 8 );
    }

    // Close our file descriptors.
    fclose($download);
    fclose($localfile);
    return true;
}

function update_aga_ranks($path) {
    // Get today's date as a timestamp.
    $today = strtotime('today');

    // DB configurations.
    $conn = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
    mysql_select_db(MYSQL_DB, $conn);
    $update_query = 'UPDATE members SET rank=$rating WHERE aga_id=$id';

    $contents = file_get_contents($path);
    if ($contents) {
        // Split contents by line, and leave off last empty item.
        $lines = explode("\n", $contents);
        $lines = array_slice($lines, 0, count($lines) - 1);

        // Get each line as an $entry array.
        foreach($lines as $line) {
            // Each CSV line is delimited by tabs, so we use preg_split to turn it into an array.
            $entry = preg_split("/\t/", $line);
            $name = $entry[0];
            $id = $entry[1];
            $rating = $entry[3];
            $expiration = $entry[4];

            // Validate $id and $rating as numbers, and update player rank if aga_id matches.
            if (is_numeric($id) && is_numeric($rating) && strtotime($expiration) > $today) {
                $query = str_replace(['$rating', '$id'], [$rating, $id], $update_query);
                $result = mysql_query($query, $conn);
                echo "name: $name; id: $id; rating: $rating; expiration date: $expiration\n";
            }
        }
    }
}

function main()
{
    date_default_timezone_set('America/Los_Angeles');
    $url = 'https://usgo.org/mm/tdlista.txt';
    $path = 'tmp_ratings.txt';
    if (!download_file($path, $url)) {
        echo "Unable to download file.";
        return;
    }
    update_aga_ranks($path);
}

main();
?>

<?php
$filename = "current_projects.txt";

if (file_exists($filename)) {
    echo nl2br(file_get_contents($filename));
} else {
    echo "No history found.";
}
?>

<?php
// Script to create storage link on shared hosting without SSH access

$targetFolder = '/home/u466475909/servicehub/storage/app/public';
$linkFolder = '/home/u466475909/public_html/admin/storage';

if (file_exists($linkFolder)) {
    echo "Storage link folder already exists. Deleting it first...<br>";
    if (is_link($linkFolder)) {
        unlink($linkFolder);
    } else {
        // If it's a directory, rename or delete
        rename($linkFolder, $linkFolder . '_backup_' . time());
    }
}

if (symlink($targetFolder, $linkFolder)) {
    echo "<h1>Success!</h1>";
    echo "Symlink created successfully.<br>";
    echo "Target: <code>" . $targetFolder . "</code><br>";
    echo "Link: <code>" . $linkFolder . "</code><br>";
} else {
    echo "<h1>Error!</h1>";
    echo "Failed to create symlink. Please check permissions or folder paths.";
}

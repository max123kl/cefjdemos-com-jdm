#!/usr/bin/env php
<?php

// Set paths
$jsDir  = __DIR__ . '/com_jdocmanual/media/js';
$cssDir = __DIR__ . '/com_jdocmanual/media/css';

function runCommand(string $cmd): void {
    echo "[cmd] $cmd\n";
    $exitCode = 0;
    passthru($cmd, $exitCode);
    if ($exitCode !== 0) {
        echo "Error: command failed with code $exitCode\n";
        exit($exitCode);
    }
}

function shouldProcess(string $source, string $target): bool {
    if (!file_exists($target)) return true;
    return filemtime($source) > filemtime($target);
}

function minifyAndGzip(string $file, string $type, $dir): void {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $base = pathinfo($file, PATHINFO_FILENAME);
    $minFile = "$base.min.$ext";
    $gzFile  = "$minFile.gz";

    if (shouldProcess($file, $minFile)) {
        echo "Minifying: $file → $minFile\n";
        if ($type === 'js') {
            runCommand("uglifyjs $file -o $dir/$minFile");
        } elseif ($type === 'css') {
            runCommand("cleancss -o $dir/$minFile $file");
        }
    }

    if (shouldProcess($minFile, $gzFile)) {
        echo "Gzipping: $minFile → $gzFile\n";
        runCommand("gzip -k -9 -f $dir/$minFile");
    }
}

function processDirectory(string $dir, string $type): void {
    $files = glob("$dir/*.$type");
    foreach ($files as $file) {
        minifyAndGzip($file, $type, $dir);
    }
}

processDirectory($jsDir, 'js');
processDirectory($cssDir, 'css');

echo "✔ Done\n";

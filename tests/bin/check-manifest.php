<?php

/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2026 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of AllediaFramework.
 *
 * AllediaFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * AllediaFramework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AllediaFramework.  If not, see <https://www.gnu.org/licenses/>.
 */

/*
 * Compare the manifest's <files> and <media> whitelists with what is actually under src/.
 * Files come from git (tracked, plus untracked files that aren't ignored), so empty or
 * ignored local folders don't count.
 */

$root     = dirname(__DIR__, 2);
$manifest = simplexml_load_file($root . '/src/allediaframework.xml');

if ($manifest === false) {
    fwrite(STDERR, "Unable to read src/allediaframework.xml\n");
    exit(1);
}

exec(
    'git -C ' . escapeshellarg($root) . ' ls-files --cached --others --exclude-standard -- src',
    $paths,
    $status
);
if ($status !== 0) {
    fwrite(STDERR, "Unable to list files with git\n");
    exit(1);
}

$paths = array_filter($paths, fn($path) => is_file($root . '/' . $path));

// Top level folders and files found under $base
$findEntries = function (string $base) use ($paths): array {
    $entries = ['folder' => [], 'filename' => []];
    foreach ($paths as $path) {
        if (strpos($path, $base) === 0) {
            $parts = explode('/', substr($path, strlen($base)), 2);
            $type  = isset($parts[1]) ? 'folder' : 'filename';

            $entries[$type][$parts[0]] = true;
        }
    }

    return array_map('array_keys', $entries);
};

// Folders and files listed in a manifest element
$listedEntries = function (?SimpleXMLElement $element): array {
    $entries = ['folder' => [], 'filename' => []];
    foreach (array_keys($entries) as $type) {
        foreach ($element ? $element->{$type} : [] as $entry) {
            $entries[$type][] = trim((string)$entry);
        }
    }

    return $entries;
};

$mediaFolder = (string)($manifest->media['folder'] ?? 'media');

$checks = [
    'src/' => [
        'listed' => $listedEntries($manifest->files),
        // The manifest and media folder are installed by other means
        'skip'   => ['allediaframework.xml', $mediaFolder],
    ],
    'src/' . $mediaFolder . '/' => [
        'listed' => $listedEntries($manifest->media),
        'skip'   => [],
    ],
];

$problems = [];
foreach ($checks as $base => $check) {
    $found = $findEntries($base);

    foreach ($found as $type => $names) {
        $missing = array_diff($names, $check['listed'][$type], $check['skip']);
        $stale   = array_diff($check['listed'][$type], $names);

        foreach ($missing as $name) {
            $problems[] = sprintf('Not in manifest: %s%s (<%s>)', $base, $name, $type);
        }
        foreach ($stale as $name) {
            $problems[] = sprintf('In manifest but not found: %s%s (<%s>)', $base, $name, $type);
        }
    }
}

if ($problems) {
    fwrite(STDERR, implode("\n", $problems) . "\n");
    exit(1);
}

echo "Manifest matches src/\n";

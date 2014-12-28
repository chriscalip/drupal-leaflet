<?php
/**
 * @file
 * drush integration for Leaflet.
 */

/**
 * The Leaflet library URI.
 */
define('LEAFLET_DOWNLOAD_URI', 'http://leaflet-cdn.s3.amazonaws.com/build/leaflet-0.7.3.zip');

/**
 * Implements hook_drush_command().
 */
function leaflet_drush_command() {
  $items = array();

  // The key in the $items array is the name of the command.
  $items['dl-leaflet'] = array(
    'callback' => 'drush_leaflet_plugin',
    'description' => dt('Download and install the Leaflet library.'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH, // No bootstrap.
    'arguments' => array(
      'path' => dt('Optional. A path where to install the Leaflet library. If omitted Drush will use the default location.'),
    ),
  );

  return $items;
}

/**
 * Implements hook_drush_help().
 */
function leaflet_drush_help($section) {
  switch ($section) {
    case 'drush:dl-leaflet':
      return dt('Download and install the Leaflet library from http://leaflet.org, default location is sites/all/libraries.');
  }
}

/**
 * Command to download the Leaflet library.
 */
function drush_leaflet_plugin() {
  $args = func_get_args();
  if (!empty($args[0])) {
    $path = $args[0];
  }
  else {
    $path = 'sites/all/libraries';
  }

  // Create the path if it does not exist.
  if (!is_dir($path)) {
    drush_op('mkdir', $path);
    drush_log(dt('Directory @path was created', array('@path' => $path)), 'notice');
  }

  // Set the directory to the download location.
  $olddir = getcwd();
  chdir($path);

  // Download the archive.
  if ($filepath = drush_download_file(LEAFLET_DOWNLOAD_URI)) {
    $filename = basename($filepath);
    $dirname = basename($filepath, '.zip');

    // Remove any existing Leaflet library directory.
    if (is_dir($dirname) || is_dir('leaflet')) {
      drush_delete_dir($dirname, TRUE);
      drush_delete_dir('leaflet', TRUE);
      drush_log(dt('A existing Leaflet library was deleted from @path', array('@path' => $path)), 'notice');
    }

    // Decompress the archive.
    drush_tarball_extract($filename);
    drush_move_dir($dirname, 'leaflet', TRUE);
    $dirname = 'leaflet';
  }

  if (is_dir($dirname)) {
    drush_log(dt('Leaflet library has been installed in @path', array('@path' => $path)), 'success');
  }
  else {
    drush_log(dt('Drush was unable to install the Leaflet library to @path', array('@path' => $path)), 'error');
  }

  // Set working directory back to the previous working directory.
  chdir($olddir);
}

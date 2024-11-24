<?php

/**
 * @package     jdocmanual.Administrator
 * @subpackage  com_jdocmanual
 *
 * @copyright   (C) 2023 Clifford E Ford. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die('Restricted access');

use Joomla\Filesystem\Folder;

class com_jdocmanualInstallerScript
{
    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        $this->deleteUnexistingFiles();

        return true;
    }

    private function deleteUnexistingFiles()
    {
      $folders = [
        '/components/com_jdocmanual/src/View/Jdmpage',
        '/components/com_jdocmanual/tmpl/jdmpage',
      ];  // overwrite this line with your files to delete

      if (empty($folders)) {
        return;
      }

      foreach ($folders as $folder) {
        if (is_dir(JPATH_ROOT . $folder)) {
            // Deletes an entire directory if exists. If the directory
            // is not empty, it deletes its contents first.
            Folder::delete(JPATH_ROOT . $folder);
        }
      }
    }
}

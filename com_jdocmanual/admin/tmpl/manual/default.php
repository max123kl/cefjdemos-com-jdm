<?php

/**
 * @package     Jdocmanual
 * @subpackage  Administrator
 *
 * @copyright   (C) 2023 Clifford E Ford. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_jdocmanual.jdocmanual')
->useStyle('com_jdocmanual.offcanvas')
->useScript('com_jdocmanual.jdocmanual');

// make the toolbar not sticky
$wa->addInlineStyle('.subhead { position: inherit; ');

$url = '';

// if using proxy do not show link to original
$proxy = false;
if (strpos($url, '/proxy/') !== false) {
    $proxy = true;
}
$gfmfiles_path = ComponentHelper::getComponent('com_jdocmanual')->getParams()->get('gfmfiles_path');

?>
<?php if (empty($this->dbisgood) || !str_ends_with($gfmfiles_path, '/manuals/')) : ?>
    <?php include __DIR__ . '/notes.php'; ?>
<?php else : ?>
    <?php if (empty($this->menu)) : ?>
        <p class="alert alert-warning">
            <?php echo Text::_('COM_JDOCMANUAL_MANUAL_MANUAL_SELECT_MISSING'); ?>
        </p>
    <?php else : ?>
        <?php include JPATH_SITE . '/components/com_jdocmanual/tmpl/manual/site-layout.php'; ?>
    <?php endif; ?>
<?php endif; ?>

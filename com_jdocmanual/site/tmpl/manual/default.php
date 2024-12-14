<?php

/**
 * @package     Jdocmanual
 * @subpackage  Site
 *
 * @copyright   (C) 2023 Clifford E Ford. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_jdocmanual.jdocmanual')
->useScript('com_jdocmanual.jdocmanual');
//->addInlineScript($scripts);

//$wa->registerAndUseStyle('jdocmanual-site', 'com_jdocmanual/jdocmanual-site.css', [], [], []);
// Register and attach a custom item in one run
$wa->registerAndUseStyle('highlight', 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/default.min.css', [], [], []);

// Register and attach a custom item in one run
$wa->registerAndUseScript('highlight','https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js', [], [], ['core']);

// Add an inline content as usual, will be rendered in flow after all assets
$wa->addInlineScript('hljs.highlightAll();');

$proxy = false;

//HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('bootstrap.dropdown', 'select', []);
HTMLHelper::_('bootstrap.collapse');

$app = Factory::getApplication();
$sitemenu = $app->getMenu();
$activeMenuitem = $sitemenu->getActive();

?>

<?php $this->addToolbar(); ?>

<?php if (empty($this->menu)) : ?>
    <p class="alert alert-warning">
        <?php echo Text::_('COM_JDOCMANUAL_MANUAL_MANUAL_SELECT_MISSING'); ?>
    </p>
<?php else : ?>
    <h1><?php echo $this->source->title; ?></h1>

    <?php include 'site-layout.php'; ?>
<?php endif; ?>

<?php include_once JPATH_COMPONENT . '/layouts/modalbox.php'; ?>
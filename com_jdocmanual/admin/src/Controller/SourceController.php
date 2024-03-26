<?php

/**
 * @package     Jdocmanual
 * @subpackage  Administrator
 *
 * @copyright   (C) 2023 Clifford E Ford. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cefjdemos\Component\Jdocmanual\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Cefjdemos\Component\Jdocmanual\Administrator\Cli\Buildarticles;
use Cefjdemos\Component\Jdocmanual\Administrator\Cli\Buildmenus;
use Cefjdemos\Component\Jdocmanual\Administrator\Cli\Buildproxy;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller for a single source
 *
 * @since  1.6
 */
class SourceController extends FormController
{
    protected $text_prefix = 'COM_JDOCMANUAL_SOURCE';

    /**
     * Update the article html for the selected manual and language.
     * This function updates all of the articles (ToDo: selected articlle).
     *
     * @return  $void
     *
     * @since   1.0.0
     */
    public function buildhtml()
    {
        $app = Factory::getApplication();
        $jform = $app->input->get('jform', array(), 'array');

        $manual = $jform['manual'];
        if (!empty($manual)) {
            $ba = new Buildarticles();
            $summary = $ba->go($manual, 'all');
            $this->app->enqueueMessage(nl2br($summary, true));
        }
        return parent::display();
    }

    /**
     * Update the menus html for the selected manual.
     *
     * @return  $void
     *
     * @since   1.0.0
     */
    public function buildmenus()
    {
        $app = Factory::getApplication();
        $jform = $app->input->get('jform', array(), 'array');

        $manual = $jform['manual'];
        if (!empty($manual)) {
            $bm = new Buildmenus();
            $summary = $bm->go($manual, 'all');
            $this->app->enqueueMessage(nl2br($summary, true));
        }
        return parent::display();
    }

    /**
     * Update the proxy html for the help manual.
     *
     * @return  $void
     *
     * @since   1.0.0
     */
    public function buildproxy()
    {
        $app = Factory::getApplication();
        $jform = $app->input->get('jform', array(), 'array');

        $manual = $jform['manual'];
        if (!empty($manual) && $manual == 'help') {
            $bp = new Buildproxy();
            $summary = $bp->go($manual, 'all');
        } else {
            $summary = 'Select the <strong>help</strong> manual to buld the proxy server';
        }
        $this->app->enqueueMessage(nl2br($summary, true));
        return parent::display();
    }
}

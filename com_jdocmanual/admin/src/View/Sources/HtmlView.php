<?php

/**
 * @package     Jdocmanual
 * @subpackage  Administrator
 *
 * @copyright   (C) 2023 Clifford E Ford. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cefjdemos\Component\Jdocmanual\Administrator\View\Sources;

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;
use Cefjdemos\Component\Jdocmanual\Administrator\Helper\CheckdbHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class for a list of jdocmanual locations.
 *
 * @since  4.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The search tools form
     *
     * @var    Form
     * @since  1.6
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var    array
     * @since  1.6
     */
    public $activeFilters = [];

    /**
     * Category data
     *
     * @var    array
     * @since  1.6
     */
    protected $categories = [];

    /**
     * An array of items
     *
     * @var    array
     * @since  1.6
     */
    protected $items = [];

    /**
     * The pagination object
     *
     * @var    Pagination
     * @since  1.6
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var    Registry
     * @since  1.6
     */
    protected $state;

    /**
     * The media tree
     *
     * @var    Array
     * @since  4.0
     */
    protected $tree;

    protected $plugin_status;

    protected $dbisgood;

    /**
     * Method to display the view.
     *
     * @param   string  $tpl  A template file to load. [optional]
     *
     * @return  void
     *
     * @since   1.6
     * @throws  Exception
     */
    public function display($tpl = null): void
    {
        /** @var JdocmanualModel $model */
        $model               = $this->getModel();
        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->state         = $model->getState();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        $this->plugin_status = $model->checkplugin();
        $this->activeLanguages = $model->getActiveLanguages();

        $this->dbisgood = CheckdbHelper::isGood();

        //$this->categorynames = $model->getCategorynames();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolbar(): void
    {
        $tmpl = Factory::getApplication()->input->getCmd('tmpl');

        $user  = $this->getCurrentUser();

        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance('toolbar');

        ToolbarHelper::title(Text::_('COM_JDOCMANUAL_SOURCES'), 'sources jdocmanual');

        $toolbar->addNew('source.add');

        if ($user->authorise('core.admin', 'com_jdocmanual') || $user->authorise('core.options', 'com_jdocmanual')) {
            $toolbar->preferences('com_jdocmanual');
        }

        if ($tmpl !== 'component') {
            ToolbarHelper::help('sources', true);
        }
    }

    protected function getLanguageFormHTML($manual) {
        // For the given manual find installed languages.
        $params = ComponentHelper::getParams('com_jdocmanual');

        // Get the the 'manuals' path from the component parameters.
        $manual_path = $params->get('gfmfiles_path') . '/' . $manual;

        // Scan for language folders.
        $items = scandir($manual_path);
        $dirs = [];
        foreach($items as $item) {
            if (is_dir($manual_path . '/' . $item)) {
                $dirs[] = $item;
            }
        }

        // If the English data is not installed return an error.
        if (!in_array('en', $dirs)) {
            return '<div class="alert alert-danger">English data not installed!</div>';
        }

        // Compose the Select element.
        $html = '<select id="' . $manual . '" name="' . $manual . '" class="form-select buildhtml">' . "\n";
        $html .= '<option value="">- Select -</option>' . "\n";
        foreach ($this->activeLanguages as $activeLanguage) {
            if (in_array($activeLanguage, $dirs)) {
                $html .= '<option value="' . $activeLanguage . '">' . $activeLanguage . '</option>' . "\n";
            }
        }
        $html .= '</select>' . "\n";
        return $html;
    }
}

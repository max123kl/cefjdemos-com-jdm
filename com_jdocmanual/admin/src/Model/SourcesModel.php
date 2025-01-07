<?php

/**
 * @package     Jdocmanual
 * @subpackage  Administrator
 *
 * @copyright   (C) 2023 Clifford E Ford. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cefjdemos\Component\Jdocmanual\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Cefjdemos\Component\Jdocmanual\Administrator\Helper\BuildHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Methods for a list of manual sources.
 *
 * @since  1.0
 */
class SourcesModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @since   1.6
     * @see     \Joomla\CMS\MVC\Controller\BaseController
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                    'id', 'a.id',
                    'manual', 'a.manual',
                    'title', 'a.title',
                    'state', 'a.state',
                    'ordering', 'a.ordering',
                );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = 'a.ordering', $direction = 'asc')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string  $id  A prefix for the store id.
     *
     * @return  string  A store id.
     *
     * @since   1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  \Joomla\Database\DatabaseQuery
     *
     * @since   1.6
     */
    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.*'
            )
        );
        $query->from($db->quoteName('#__jdm_manuals') . ' AS a');

        // Filter by published state
        $published = (string) $this->getState('filter.published');

        if (is_numeric($published)) {
            $query->where($db->quoteName('a.state') . ' = :published');
            $query->bind(':published', $published, ParameterType::INTEGER);
        } elseif ($published === '') {
            $query->whereIn($db->quoteName('a.state'), array(0, 1));
        }

        // Filter by search in title.
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            $search = '%' . str_replace(' ', '%', trim($search) . '%');
            $query->where($db->quoteName('a.title') . ' LIKE :search')
            ->bind(':search', $search, ParameterType::STRING);
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.ordering');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
    /**
     * Check that the jdocmanual plugin has been enabled.
     *
     * @return int The enabled value, 0 or 1.
     */
    public function checkplugin()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('extension_id', 'enabled')))
        ->from($db->quoteName('#__extensions'))
        ->where($db->quoteName('name') . ' = ' . $db->quote('plg_system_jdocmanualcli'));
        $db->setQuery($query);
        $row = $db->loadObject();
        if (empty($row)) {
            return 0;
        }
        if (empty($row->enabled)) {
            return 1;
        }
        return 2;
    }

    public function getActiveLanguages()
    {
        $db    = $this->getDatabase();
        return BuildHelper::getActivelanguages($db);
    }
}

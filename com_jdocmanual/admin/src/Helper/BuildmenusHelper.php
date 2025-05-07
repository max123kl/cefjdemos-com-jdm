<?php

/**
 * @package     Jdocmanual
 * @subpackage  Administrator
 *
 * @copyright   (C) 2023 Clifford E Ford. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cefjdemos\Component\Jdocmanual\Administrator\Helper;

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper to create a menu preview for the Menu Stash: Edit form.
 * This is an abbreiviated version of the code used in Cli/Buildmenus.php.
 *
 * @since  1.0
 */
class BuildmenusHelper
{
    /**
     * Build a menu table for a menu stash preview.
     *
     * @param   string  $manual     The path fragment for the manual.
     * @param   string  $menu       A string containing the menu-index.txt.
     * @param   string  $language   The language code for this menu.
     *
     * @return  string  The menu in html.
     *
     * @since  1.0.0
     */
    public function buildmenus($manual, $menu, $language = 'en')
    {
        // if any parameter is missing return false
        if (empty($manual) || empty($menu)) {
            return false;
        }

        // Fetch a list of article headings in English.
        $headings_english = $this->setHeadings($manual);

        // Fetch a list of headings in the specified language.
        if ($language !== 'en') {
            $headings_language = $this->setHeadings($manual, $language);
        }

        // Split the menu into lines.
        $lines = preg_split("/((\r?\n)|(\r\n?))/", $menu);
        $count = count($lines);
        $count_headings = 0;
        $count_articles = 0;
        $html = '';

        foreach ($lines as $line) {
            $line = trim($line);
            // Skip any line starting with a semi-colon
            if (empty($line) || strpos($line, ';') === 0) {
                continue;
            }
            $parts = explode('=', $line);
            // If the line starts with 'heading=' start a new accordion.
            if ($parts[0] === 'heading') {
                if ($count_headings > 0) {
                    $html .= $this->accordionend();
                }
                // Example: heading=getting-started=Getting Started
                $count_headings += 1;
                $html .= $this->accordionstart($parts[2]);
            } else {
                $count_articles += 1;

                // Example: developer=getting-started=developer-required-software.md
                $title = str_replace('.md', '', $parts[2]);
                $title = ucwords(str_replace('-', ' ', $title));
                $html .= $this->accordionitem($count_articles, $title);
            }
        }
        $html .= $this->accordionend();
        return $html;
    }

    /**
     * Create an accordian start code.
     *
     * @param   integer $id     The sequence number of the accordion.
     * @param   string  $label  A summary label.
     *
     * @return  $html   The required html code.
     */
    protected function accordionStart($label)
    {
        $html = "<li>\n";
        $html .= '<a class="has-arrow" href="#" aria-expanded="false">';
        $html .= "{$label}</a>\n<ul class=\"jdmindent-1\">\n";
        return $html;
    }

    /**
     * Create an accordian end code.
     *
     * @return  string  The required html code.
     */
    protected function accordionEnd()
    {
        return "\n</ul>\n</li>\n";
    }

    /**
     * Create an accordian item code.
     *
     * @param   integer     $id             The sequence number of the accordion.
     * @param   string      $display_title  The display title.
     * @param   string      $path           The link path
     *
     * @return  string      The required html code.
     */
    protected function accordionItem($id, $display_title)
    {
        // Escape any " character in the link.
        //'<li><span class="icon-file-alt icon-fw icon-jdocmanual" aria-hidden="true"></span>';
        $html = '<li id="article-' . $id . '">';
        $html .= '<a href="#">' . $display_title . '</a></li>' . "\n";
        return $html;
    }

    /**
     * Get article data for the menu.
     *
     * @param   string  $manual     The article path fragment.
     * @param   string  $language   The article language.
     * @param   string  $heading    The article filename.
     *
     * @return  string  The required html code.
     *
     * @since   1.0.0
     */
    protected function getArticleData($manual, $language, $heading)
    {
        // Try to get the article record from the database.
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id','display_title', 'source_url')))
        ->from($db->quoteName('#__jdm_articles'))
        ->where($db->quoteName('manual') . ' = :manual')
        ->where($db->quoteName('language') . ' = :language')
        ->where($db->quoteName('heading') . ' = :heading')
        ->bind(':manual', $manual, ParameterType::STRING)
        ->bind(':language', $language, ParameterType::STRING)
        ->bind(':heading', $heading, ParameterType::STRING);
        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Get the accordion headings for the menu.
     *
     * @param   manual     $manual      The article path fragment.
     * @param   language   $language    The article language.
     *
     * @return  array      An associative array of headings.
     *
     * @since   1.0.0
     */
    protected function setHeadings($manual, $language = 'en')
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('heading','display_title')))
        ->from($db->quoteName('#__jdm_menu_headings'))
        ->where($db->quoteName('manual') . ' = :manual')
        ->where($db->quoteName('language') . ' = :language')
        ->bind(':manual', $manual, ParameterType::STRING)
        ->bind(':language', $language, ParameterType::STRING);
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $headings = [];
        foreach ($rows as $row) {
            $headings[$row->heading] = $row->display_title;
        }
        return $headings;
    }
}

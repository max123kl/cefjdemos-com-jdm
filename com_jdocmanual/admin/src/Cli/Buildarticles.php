<?php

/**
 * @package     Jdocmanual
 * @subpackage  Cli
 *
 * @copyright   Copyright (C) 2023 Clifford E Ford. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cefjdemos\Component\Jdocmanual\Administrator\Cli;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\ParameterType;
use Joomla\CMS\Plugin\PluginHelper;
use Cefjdemos\Component\Jdocmanual\Administrator\Helper\Markdown2html;
use Cefjdemos\Component\Jdocmanual\Administrator\Helper\Responsive;

// phpcs:disable PSR1.Files.SideEffects
\defined('JPATH_PLATFORM') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Build the jdm_articles table.
 *
 * @since  1.0.0
 */
class Buildarticles
{
    /**
     * Path to local source of markdown files.
     *
     * @var     string
     * @since   1.0.0
     */
    protected $gfmfiles_path;

    /**
     * The pattern used to look for img links in markdown.md files.
     *
     * @var     string
     * @since  1.0.0
     *
     * Example link: ![action logs module form](../../../images/en/admin-modules/modules-actionlogs-latest-screenshot.png) "Action Logs Module Form"
     * First bracket is alt text.
     * Second bracket is img url to be added to root/manual/
     * Third bracket is filename
     * Fourth bracket is title.
     */
    protected $pattern = '/\!\[(.*?)\]\(..\/..\/..(\/.*)?\/(.*?)\s"(.*?)".*\)/m';

    /**
     * Regex pattern to select Display title from GFM comment string.
     *
     * @var     string
     * @since  1.0.0
     */
    protected $pattern2 = '/<!--.*Display title:(.*)?-->/m';

    /**
     * Count of number of local images processed for a given manual.
     *
     * @var     string
     * @since  1.0.0
     */
    protected $local_image_count = 0;

    /**
     * Path fragment of manual to process.
     *
     * @var     string
     * @since  1.0.0
     */
    protected $manualtodo;

    /**
     * Path fragment of language to process.
     *
     * @var     string
     * @since  1.0.0
     */
    protected $languagetodo;

    /**
     * Content of menu index file.
     *
     * @var     string
     * @since  1.0.0
     */
    protected $tmp;

    protected $responsive;

    /**
     * Constructor
     *
     * @param array<string, mixed> $data
     */
    public function __construct()
    {
        $this->responsive = new Responsive();
    }

    /**
     * Entry point to convert md to html and save.
     *
     * @param   $manual     The path fragment of the manual to process.
     * @param   $language   The path fragment of the language to process.
     *
     * @return  $string     A message reporting the outcome.
     *
     * @since   1.0.0
     */
    public function go($manual, $language)
    {
        $time_start = microtime(true);

        // The echo items appear in the CLI but not in Joomla.
        //echo "\n\nBegin Build Articles in Database\n";

        $this->manualtodo = $manual;
        $this->languagetodo = $language;

        // The memory limit needs to be quite large to build all of the articles.
        $memlimit = ini_get('memory_limit');
        ini_set("memory_limit", "2048M");

        $summary = $this->build();

        // echo "\nEnd Build Articles\n\n";

        $time_end = microtime(true);
        $execution_time = $time_end - $time_start;

        $summary .= 'Total Execution Time: ' . number_format($execution_time, 2) . ' Seconds' . "\n\n";

        return $summary;
    }

    /**
     * Cycle over manuals and languages to convert md to html and save.
     *
     * @return  $string     A message reporting the outcome.
     *
     * @since   1.0.0
     */
    protected function build()
    {
        // Get the data source path from the component parameters.
        $this->gfmfiles_path = ComponentHelper::getComponent('com_jdocmanual')->getParams()->get('gfmfiles_path', ',');
        if (empty($this->gfmfiles_path)) {
            return "\nThe Markdown source could not be found: {$this->gfmfiles_path}. Set in Jdocmanual configuration.\n";
        }

        // Get a list of manual folders in /Users/ceford/data/manuals/
        $manuals = array_diff(scandir($this->gfmfiles_path), array('*.txt', '..', '.', '.DS_Store'));
        $summary = '';

        foreach ($manuals as $manual) {
            // Skip of not all manuals are being updated
            if (!($this->manualtodo === 'all' || $this->manualtodo === $manual)) {
                continue;
            }

            // Read in articles-index.txt - changed to new format ini
            $articles_index = $this->gfmfiles_path . $manual . '/articles/articles-index.txt';
            if (!file_exists($articles_index)) {
                $summary .= "Skipping {$manual} - file does not exist: {$articles_index}\n";
                continue;
            }
            // Read in the articles_index
            $this->tmp = file_get_contents($articles_index);

            // Get a list of the language folders in a manual
            $languages = array_diff(scandir($this->gfmfiles_path . $manual . '/articles'), array('..', '.', '.DS_Store'));
            foreach ($languages as $language) {
                // Skip of not all languages are being updated
                if (!($this->languagetodo === 'all' || $this->languagetodo === $language)) {
                    continue;
                }
                if (is_dir($this->gfmfiles_path . $manual . '/articles/' . $language)) {
                    $headings = $this->setMenuHeadings($manual, $language);
                    list ($count, $problems) = $this->html4lingo($manual, $language);
                    $summary .= "Summary: {$manual}/{$language} Number of articles: {$count}";
                    $summary .= ", Number of local images: {$this->local_image_count}, {$headings}";
                    $summary .= $problems;
                }
            }
        }
        return $summary;
    }

    protected function setMenuHeadings($manual, $language)
    {
        // Get the menu headings file
        $menu_headings_file = $this->gfmfiles_path . $manual . '/articles/' . $language . '/menu-headings.ini';
        if (!file_exists($menu_headings_file)) {
            return 'Menu headings missing: ' . $menu_headings_file . "\n";
        }

        $contents = file_get_contents($menu_headings_file);
        $db = Factory::getContainer()->get('DatabaseDriver');
        $count = 0;

        foreach (preg_split("/((\r?\n)|(\r\n?))/", $contents) as $line) {
            if (empty($line)) {
                continue;
            }
            list ($heading, $translation) = explode('=', $line);
            // Check if there is an existing entry.
            $query = $db->getQuery(true);
            $query->select($db->quoteName('id'))
            ->from($db->quoteName('#__jdm_menu_headings'))
            ->where($db->quoteName('manual') . ' = :manual')
            ->where($db->quoteName('language') . ' = :language')
            ->where($db->quoteName('heading') . ' = :heading')
            ->bind(':manual', $manual, ParameterType::STRING)
            ->bind(':language', $language, ParameterType::STRING)
            ->bind(':heading', $heading, ParameterType::STRING);
            $db->setQuery($query);
            $id = $db->loadResult();

            $query = $db->getQuery(true);
            if (empty($id)) {
                $query->insert($db->quoteName('#__jdm_menu_headings'))
                ->set($db->quoteName('manual') . ' = :manual')
                ->set($db->quoteName('language') . ' = :language')
                ->set($db->quoteName('heading') . ' = :heading')
                ->bind(':manual', $manual, ParameterType::STRING)
                ->bind(':language', $language, ParameterType::STRING)
                ->bind(':heading', $heading, ParameterType::STRING);
            } else {
                $query->update($db->quoteName('#__jdm_menu_headings'))
                ->where($db->quoteName('id') . ' = ' . $id);
            }
            $query->set($db->quoteName('display_title') . ' = :translation')
            ->bind(':translation', $translation, ParameterType::STRING);
            $db->setQuery($query);
            $db->execute();
            $count += 1;
        }
        return "Headings translated: {$count}\n";
    }

    /**
     * Convert a single md file to html and save.
     *
     * @param   $manual     The path fragment of the manual to process.
     * @param   $language   The path fragment of the language to process.
     *
     * @return  $int        Count of the number of files.
     *
     * @since   1.0.0
     */
    protected function html4lingo($manual, $language)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $count = 0;
        $summary = '';

        // Set the time limit for every manual and language
        set_time_limit(60);

        foreach (preg_split("/((\r?\n)|(\r\n?))/", $this->tmp) as $line) {
            if (empty($line)) {
                continue;
            }
            list ($heading, $filename, $source_url) = explode('=', $line);

            $gfm_file = $this->gfmfiles_path . $manual . '/articles/' . $language . '/' . $heading . '/' . $filename;
            if (!file_exists($gfm_file)) {
                continue;
            }

            $contents = file_get_contents($gfm_file);

            // Get the title from the contents.
            // Look for Display Title.
            // <!-- Filename: J4.x:Http_Header_Management / Display title: HTTP Header Verwaltung -->
            $test = preg_match($this->pattern2, $contents, $matches);
            if (empty($test)) {
                $summary .= "Warning {$manual}/{$language}/{$heading}/{$filename} does not contain h1\n";
                $fn = substr($filename, 0, strpos($filename, '.md'));
                $display_title = ucwords(str_replace('_', ' ', $fn));
            } else {
                $display_title = trim($matches[1]);
            }

            // Todo: new function here to process images from repo
            $contents = $this->fiximages($manual, $contents);

            $html = Markdown2html::go($contents);

            // Check if there is an entry for this article.
            $query = $db->getQuery(true);
            $query->select($db->quotename('id'))
            ->from($db->quotename('#__jdm_articles'))
            ->where($db->quotename('manual') . ' = :manual')
            ->where($db->quotename('language') . ' = :language')
            ->where($db->quotename('heading') . ' = :heading')
            ->where($db->quotename('filename') . ' = :filename')
            ->bind(':manual', $manual, ParameterType::STRING)
            ->bind(':language', $language, ParameterType::STRING)
            ->bind(':heading', $heading, ParameterType::STRING)
            ->bind(':filename', $filename, ParameterType::STRING);
            $db->setQuery($query);
            $id = $db->loadResult();

            $query = $db->getQuery(true);
            if (empty($id)) {
                // If id was empty do an insert.
                $query->insert($db->quotename('#__jdm_articles'));
            } else {
                // Otherwise do an update.
                $query->update($db->quotename('#__jdm_articles'));
                $query->where($db->quotename('id') . ' = ' . $id);
            }

            $query->set($db->quotename('source_url') . ' = :source_url')
            ->set($db->quotename('manual') . ' = :manual')
            ->set($db->quotename('language') . ' = :language')
            ->set($db->quotename('heading') . ' = :heading')
            ->set($db->quotename('filename') . ' = :filename')
            ->set($db->quotename('display_title') . ' = :display_title')
            ->set($db->quotename('html') . ' = :html')
            ->bind(':source_url', $source_url, ParameterType::STRING)
            ->bind(':manual', $manual, ParameterType::STRING)
            ->bind(':language', $language, ParameterType::STRING)
            ->bind(':heading', $heading, ParameterType::STRING)
            ->bind(':filename', $filename, ParameterType::STRING)
            ->bind(':display_title', $display_title, ParameterType::STRING)
            ->bind(':html', $html, ParameterType::STRING);
            $db->setQuery($query);
            $db->execute();

            $count += 1;
            // For testing - do one file from each manual and language.
            // Otherwise comment out these two lines
            //echo "Test = {$manual}/{$language}/{$heading}/{$filename}\n";
            //return $count;
        }
        return [$count, $summary];
    }

    /**
     * Look for repo images to make picture srcset.
     *
     * @param   $manual   The manual to process.
     * @param   $contents A single page content in Markdown format.
     *
     * @return  $html     Markdown img links converted for this site.
     *
     * @since   1.0.0
     */
    private function fiximages($manual, $contents)
    {
        // links are like this and must be on one line
        // ![action logs module form](../../../images/help/en/admin-modules/modules-actionlogs-latest-screenshot.png) "Action Logs Module Form"
        $test = preg_match_all($this->pattern, $contents, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            // $match[0] is the whole line to be replaced with a picture tag.
            // $match[1] is the alt tag.
            // $match[2] is the path to the image in the repo source
            // $match[3] is the image filename
            // $match[4] is the title.

            // Copy the image to the images folder.
            $destination_dir = JPATH_ROOT . '/jdmimages/manuals/' . $manual . $match[2];
            $destination =  $destination_dir . '/' . $match[3];

            // Does the destination folder exist?
            if (!is_dir($destination_dir)) {
                mkdir($destination_dir, 0755, true);
            }
            $origin = $this->gfmfiles_path . $manual . $match[2] . '/' . $match[3];
            file_put_contents($destination, file_get_contents($origin));

            // Create an img src set and set of images from an img tag.
            $img = '<img src="/jdmimages/manuals/' . $manual . $match[2] . '/' . $match[3] . '" alt="' . $match[1] . '" title="' . $match[4] . '">';
            $processed = $this->responsive->transformImage($img);
            if (!empty($processed)) {
                $contents = str_replace($match[0], $processed, $contents);
            }
            $this->local_image_count += 1;
        }
        return $contents;
    }
}

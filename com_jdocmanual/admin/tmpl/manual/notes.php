<?php

/**
 * @package     Jdocmanual
 * @subpackage  Administrator
 *
 * @copyright   (C) 2023 Clifford E Ford. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Component\ComponentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$gfmfiles_path = ComponentHelper::getComponent('com_jdocmanual')->getParams()->get('gfmfiles_path');
if (empty($gfmfiles_path)) {
    $data_path = '<span class="badge bg-warning">is empty</span>';
} else {
    // Does it end with /manuals/
    if (str_ends_with($gfmfiles_path, '/manuals/')) {
        $data_path = '<span class="badge bg-success">is set</span>';
    } else {
        $data_path = '<span class="badge bg-warning">is set but does not end with /manuals/</span>';
    }
}

$plugin_status = array(
    '<span class="badge bg-warning">is absent</span>',
    '<span class="badge bg-warning">is is not enabled</span>',
    '<span class="badge bg-success">is present and enabled</span>'
);

?>
<h1>Installation Notes</h1>

<p>Jdocmanual is designed to deliver Joomla documentation in an easy reference
form. It can also be used for other custom documentation. Most of the original
content came from the docs.joomla.org MediaWiki installation and many of the
images used here are delivered from that source. </p>

<p>The data for Jdocmanual are located on GitHub in GitHub Flavoured Markdown
format. Four manuals are available in separate GitHub repositories. You can
choose which to install.</p>

<p>Version 3 of Jdocmanual is not compatible with previous versions. Any previous
version should be uninstalled before installing this new version.</p>

<h2>In brief</h2>

<ul>
    <li>
    Install one or more data sets
    <ul>
        <li><strong>developer:</strong> Information for Joomla developers.<br>
        source: https://github.com/ceford/cefjdemos-data-jdm-developer
        <li><strong>docs:</strong> Information for those contributing to Joomla Documentation.<br>
        source: https://github.com/ceford/cefjdemos-data-jdm-docs
        </li>
        <li><strong>help:</strong> The Help screens used for the Administrator pages.<br>
        source: https://github.com/ceford/cefjdemos-data-jdm-help
        </li>
        <li><strong>user:</strong> Information for Joomla users with limited familiarity with
        HTML, CSS and JavaScript.<br>
        source: https://github.com/ceford/cefjdemos-data-jdm-user
        </li>
    </ul>
    Install the data in a folder ending in <code>/manuals/</code> outside your
    web tree. <br>
    Example: <code>/home/username/data/manuals/</code><br>
    An unzipped folder should be given the short name of the manual: one of
    developer, docs, help or user.
    </li>

    <li>Set the dataset path and installation subfolder (if required) in the
    Jdocmanual Options page.<br>
    Your path: <?php echo $data_path; ?>.
    </li>

    <li>Build your database articles and menus: select the
    <code>Jdocmanual/Sources</code> menu item, then the manual you have
    installed. Use the <code>Actions</code> button to
    <code>Update HTML</code>, then <code>Update Menus</code>. The Help manual
    also has an option to <code>Update Proxy</code> to build a proxy server.
    </li>

    <li>If you wish to use the command line for data maintenance you need to
    install the Jdocmanualcli plugin.<br>
    Source: https://github.com/ceford/j4xdemos-plg-jdocmanualcli<br>
    <?php if (isset($this->plugin_status)) : ?>
    Your plugin: <?php echo $plugin_status[$this->plugin_status]; ?>.
    <?php endif; ?>
    </li>
</ul>

<h2>In detail</h2>

<h2>Obtain a Source Dataset</h2>

<p>You can use one of several different method to obtain a dataset. Go to
the GitHub site and select the green Code button. It reveals a dropdown list
with several options:</p>
<ul>
<li>Download ZIP file for a one off installation.</li>
<li>Copy the Clone URL to be able to use Git to update your copy of the source.</li>
</ul>
<p>If you would like to contribute to documentation you could create a GitHub
fork and clone that. </p>

<p>Use of the git clone command requires installation of Git on your computer.
This is usually quick and easy! </p>

<p>The source data sets should be located outside your web tree. For example:</p>
<pre>
/home/username/data/manuals/help/...
/home/username/data/manuals/user/...
/home/username/public_html/...
</pre>

<p>Jdocmanual requires the last item in your path for all manuals to be
<strong>manuals</strong>. Each separate manual will be installed in this
folder.
</p>

<p><strong>Either:</strong> unzip the downloaded ZIP file in your manuals
folder. It will create a folder named cefjdemos-data-jdm-xxxxx-main
where xxxx is the short name of the manual.</p>

<p><strong>Or:</strong> using a terminal window, cd to your manuals folder and
use the git clone command:<br>
<code>git clone https://github.com/ceford/cefjdemos-data-jdm-user.git</code><br>
Rename the installation folder, <code>cefjdemos-data-jdm-user</code>, to
<code>user</code>.</p>

<p>Check that the new folder contains articles and images
folders. Remember or write down the path to your <strong>manuals</strong>
folder. Repeat for each manual you wish to install.</p>

<p>With one or more datasets in place you are ready to build your Jdocmanual
site. </p>

<h2>Set the Markdown data source location</h2>

<p>After downloading the data files select the <strong>Options</strong> button
in the Toolbar to set the data path.</p>

<p>Your path: <?php echo $data_path; ?>.</p>

<h2>Install and Enable the Plugin</h2>

<h3>Jdocmanual CLI Plugin</h3>

<p>To populate the database you need to install the plg_jdocmanual plugin.
This may have been installed with the Jdocmanual package or separately. It
is not enabled by default as it is only used to populate the database
from the command line. </p>

<p>Source: https://github.com/ceford/j4xdemos-plg-jdocmanualcli</p>

<p>Go to the list of system plugins, find the Jdocmanul plugin and enable it</p>

<?php if (isset($this->plugin_status)) : ?>
<p>Your plugin: <?php echo $plugin_status[$this->plugin_status]; ?>.</p>
<?php endif; ?>

<h2>Database Population - Command Line Method</h2>

<p>The database jdm_articles and jdm_menus tables are populated by reading
the data files. This can take a long time - at least a couple of minutes
and perhaps much longer on slow hosts. Proceed as follows:</p>

<ol>
<li>Open a terminal window and cd into the cli folder within
your Joomla installation root. Here is an example:<br>
<code>cd /home/username/public_html/optional-subfolder/cli</code><br></li>

<li>Issue the command to convert data from the markdown source files to html
in the #__jdm_articles table of the database:<br>
<code>php joomla.php jdocmanual:action buildarticles user all</code></li>

<li>Issue the command to create the menus:<br>
<code>php joomla.php jdocmanual:action buildmenus user all</code></li>
</ol>

<p>In these commands, user is the name of the dataset to be processed. The
Markdown format is converted to HTML and stored in the database. Also, if
there are any images in the data source a set of 6 responsive images is created.
</p>

<p>If you encounter <strong>out of memory</strong> problems or
<strong>out of time</strong> problems you can replace the
<code>all</code> parameter with a single language code,
one of <code>de en es fr nl pt pt-br ru</code>. Using that method
you can build the database manual by manual and/or language by
language.</p>

<h2>Database Population - Cron Method</h2>

<p>If you do not have access to the command line, common with shared
hosting, you should be able to call these commands from a cron job.
Make sure you give the full path to the php executable, example: <br>
<code>/usr/local/opt/php@8.1/bin/php</code><br>
You need to find out where the php executable is located on your
host operating system. </p>

<h2>Enable the Installed Manuals</h2>

<p>Select the <strong>Components/JdocManual/Sources</strong> menu item to
see a list of potential manuals. All are disabled on installation. Select
the title of any to be enabled and set Enabled in the data edit form. ToDo:
provide a toggle for this function.</p>

<h2>Test</h2>

<p>That is it! Select the Joomla Administrator Components/Jdocmanual/Manual
menu item and expect to see the default Manual selected in English.</p>

<h2>Help Proxy Server</h2>

<p>If you would like to serve your own Help files you can use the following
command in your terminal window: </p>

<code>php joomla.php jdocmanual:action buildproxy help all</code></li>

<p>And then edit your configuration file to remove the domain of the existing
Help server. It might look like this: </p>

<code>
    public $helpurl = '/xxx?/proxy?keyref=Help{major}{minor}:{keyref}&lang={langcode}';
</code>
 <p>The /xxx? part would be the name of any subfolder where your installation
is located in public_html, or absent. The build process creates subfolders in
a /proxy folder in the root of your Joomla installatio. They contain HTML files
generated from the help HTML files stored in the database. </p>

<h2>Site Menu</h2>

<p>If you want to show the Manuals on the site just create a JDOC Manual
menu item. Note the single page is for search results but it has not
been implemented.</p>

<p><strong>Important:</strong> The menu alias must be <strong>jdocmanual</strong>
or internal links will be broken and lead to frontend problems.</p>

<p>You may wish to place the menu on a page without side modules so that
the full width of the page may be used for content.</p>

<h2>Multilingual Sites</h2>

<p>The <strong>System - Language Filter</strong> plugin must have the
<strong>Remove URL Language Code</strong> set to <strong>Yes</strong> or
frontend local images will not display and internal links will be broken.</p>

<h2>User Groups</h2>

If you wish to allow others to help maintain content you need to
create two User Groups:</p>

<p>- **JDM Author**: allowed to edit content in English and other languages.
- **JDM Publisher**: allowed to commit and publish updated content.</p>

<p>The **JDM Author** group should have Public as its parent. The **JDM Publisher**
group should have **JDM Author** as its parent.</p>

<h3>Users: Viewing Access Levels</h3>

<p>**JDM Author** should be set to the Special Viewing Access level.
From the Users / Access Levels page select `Special` and add `JDM Author` to
the User Groups with Viewing Access.</p>

<h3>Global Options</h3>

<p>In the Global Options form select the Permissions tab and then the
**JDM Author** item. Set Administrator login to Allowed.</p>

<h3>Jdocmanual Options</h3>

<p>From the JDOC Manual, Manual page select the Options button. In the
Permissions list select **JDM Author** and set the following to Allowed:
- Access Administration Interface
- Create
- Delete
- Edit</p>

<p>Select **JDM Publisher** and set the following to Allowed:
- Publish</p>

<p>Save and Close</p>

<p>If you now login as a user in the **JDM Author** group you will see the
Home Dashboard with some modules not relevant for Jdocmanual.</p>

<h3>Turn off the Help menu item</h3>

<p>Go to the list of Administrator modules and find the Administrator
Menu module. In the Module tab set the Help Menu item to Hide.</p>

<h3>Unpublish or restrict Access to modules</h3>

<p>In the Administrator modules list find the Logged-in Users item used
in the cpanel position (not the cpanel-users position). Either
Unpublish it or set Access to Super Users.</p>

<p>Find and unpublish the Popular Articles and Recently Added Articles
items for the cpanel position (not the cpanel-content position).</p>

<p>There may be other modules needing similar treatment.</p>

<p>The Home Dashboard should now be empty for a **JDM Author**.</p>

<h2> Who can do what?</h2>

<p>**JDM Author** and **JDM Publisher** can login but should only have
access to the the Home Dashboard and the Jdocmanual component.</p>

<p>**JDM Author** does not have access to the Commit button in the
Article Edit page toolbar so cannot update the git repository or
displayed page. Otherwise each can use all other features.</p>

<p>**Manager** and **Administrator** can login but will not see the
Jdocmanual component.</p>

<p>**Author**, **Editor** and **Publisher** do not have access to
the Administrator interface.</p>

<p>**Super Users** have complete access.
</p>
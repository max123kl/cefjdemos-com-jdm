# Installation Notes

Jdocmanual is designed to deliver Joomla documentation in an easy
reference form. It can also be used for other custom documentation. Most
of the original content came from the docs.joomla.org MediaWiki
installation and many of the images used here are delivered from that
source.

The data for Jdocmanual are located on GitHub in GitHub Flavoured
Markdown format. Four manuals are available in separate GitHub
repositories. You can choose which to install.

Version 3 of Jdocmanual is not compatible with previous versions. Any previous
version should be uninstalled before installing this new version.

## In brief

- Install one or more data sets
  - **developer:** Information for Joomla developers.
    source: https://github.com/ceford/cefjdemos-data-jdm-developer
  - **docs:** Information for those contributing to Joomla
    Documentation.
    source: https://github.com/ceford/cefjdemos-data-jdm-docs
  - **help:** The Help screens used for the Administrator pages.
    source: https://github.com/ceford/cefjdemos-data-jdm-help
  - **user:** Information for Joomla users with limited familiarity with
    HTML, CSS and JavaScript.
    source: https://github.com/ceford/cefjdemos-data-jdm-user

  Install the data in a folder ending in `/manuals/` outside your web
  tree.
  Example: `/home/username/data/manuals/`
  An unzipped folder should be given the short name of the manual: one
  of developer, docs, help or user.
- Set the dataset path and installation subfolder (if required) in the
  Jdocmanual Options page.
- Build your database articles and menus: select the
  `Jdocmanual/Sources` menu item, then the manual you have installed.
  Use the `Actions` button to `Update Articles`, then `Update Menus`. The
  Help manual also has an option to `Update Proxy` to build a proxy
  server.
- If you wish to use the command line for data maintenance you need to
  install the Jdocmanualcli plugin.
  Source: https://github.com/ceford/cefjdemos-plg-jdocmanualcli

## In detail

## Obtain a Source Dataset

You can use one of several different method to obtain a dataset. Go to
the GitHub site and select the green Code button. It reveals a dropdown
list with several options:

- Download ZIP file for a one off installation.
- Copy the Clone URL to be able to use Git to update your copy of the
  source.

If you would like to contribute to documentation you could create a
GitHub fork and clone that.

Use of the git clone command requires installation of Git on your
computer. This is usually quick and easy!

The source data sets should be located outside your web tree. For
example:

    /home/username/data/manuals/help/...
    /home/username/data/manuals/user/...
    /home/username/public_html/...

Jdocmanual requires the last item in your path for all manuals to be
**manuals**. Each separate manual will be installed in this folder.

**Either:** unzip the downloaded ZIP file in your manuals folder. It
will create a folder named cefjdemos-data-jdm-xxxxx-main where xxxx is
the short name of the manual.

**Or:** using a terminal window, cd to your manuals folder and use the
git clone command:
`git clone https://github.com/ceford/cefjdemos-data-jdm-user.git`
Rename the installation folder, `cefjdemos-data-jdm-user`, to `user`.

Check that the new folder contains articles and images folders. Remember
or write down the path to your **manuals** folder. Repeat for each
manual you wish to install.

With one or more datasets in place you are ready to build your
Jdocmanual site.

## Set the Markdown data source location

After downloading the data files select the **Options** button in the
Toolbar to set the data path.

Your path: .

## Install and Enable the Plugin

### Jdocmanual CLI Plugin

To populate the database you need to install the plg_jdocmanual plugin.
This may have been installed with the Jdocmanual package or separately.
It is not enabled by default as it is only used to populate the database
from the command line.

Source: https://github.com/ceford/j4xdemos-plg-jdocmanualcli

Go to the list of system plugins, find the Jdocmanul plugin and enable
it.

## Database Population - Command Line Method

The database jdm_articles and jdm_menus tables are populated by reading
the data files. This can take a long time - at least a couple of minutes
and perhaps much longer on slow hosts. Proceed as follows:

1.  Open a terminal window and cd into the cli folder within your Joomla
    installation root. Here is an example:
    `cd /home/username/public_html/optional-subfolder/cli`
2.  Issue the command to convert data from the markdown source files to
    html in the \#\_\_jdm_articles table of the database:
    `php joomla.php jdocmanual:action buildarticles user all`
3.  Issue the command to create the menus:
    `php joomla.php jdocmanual:action buildmenus user all`

In these commands, user is the name of the dataset to be processed. The
Markdown format is converted to HTML and stored in the database. Also,
if there are any images in the data source a set of 6 responsive images
is created.

If you encounter **out of memory** problems or **out of time** problems
you can replace the `all` parameter with a single language code, one of
`de en es fr nl pt pt-br ru`. Using that method you can build the
database manual by manual and/or language by language.

## Database Population - Cron Method

If you do not have access to the command line, common with shared
hosting, you should be able to call these commands from a cron job. Make
sure you give the full path to the php executable, example:
`/usr/local/opt/php@8.1/bin/php`
You need to find out where the php executable is located on your host
operating system.

## Enable the Installed Manuals

Select the **Components/JdocManual/Sources** menu item to see a list of
potential manuals. All are disabled on installation. Select the title of
any to be enabled and set Enabled in the data edit form. ToDo: provide a
toggle for this function.

## Test

That is it! Select the Joomla Administrator Components/Jdocmanual/Manual
menu item and expect to see the default Manual selected in English.

## Help Proxy Server

If you would like to serve your own Help files you can use the following
command in your terminal window:

`php joomla.php jdocmanual:action buildproxy help all`

And then edit your configuration file to remove the domain of the
existing Help server. It might look like this:

` public $helpurl = '/xxx?/proxy?keyref=Help{major}{minor}:{keyref}&lang={langcode}'; `

The /xxx? part would be the name of any subfolder where your
installation is located in public_html, or absent. The build process
creates subfolders in a /proxy folder in the root of your Joomla
installatio. They contain HTML files generated from the help HTML files
stored in the database.

## Site Menu

If you want to show the Manuals on the site just create a JDOC Manual
menu item. Note the single page is for search results but it has not
been implemented.

**Important:** The menu alias must be **jdocmanual** or internal links
will be broken and lead to frontend problems.

You may wish to place the menu on a page without side modules so that
the full width of the page may be used for content.

## Multilingual Sites

The **System - Language Filter** plugin must have the **Remove URL
Language Code** set to **Yes** or frontend local images will not display
and internal links will be broken.

## User Groups

If you wish to allow others to help maintain content you need to create
two User Groups:

- **JDM Author**: allowed to edit content in English and other
languages.
- **JDM Publisher**: allowed to commit and publish
updated content.

The **JDM Author** group should have Public as its parent. The
**JDM Publisher** group should have **JDM Author** as its
parent.

### Users: Viewing Access Levels

**JDM Author** should be set to the Special Viewing Access level.
From the Users / Access Levels page select `Special` and add `JDM
Author` to the User Groups with Viewing Access.

### Global Options

In the Global Options form select the Permissions tab and then the
**JDM Author** item. Set Administrator login to Allowed.

### Jdocmanual Options

From the JDOC Manual, Manual page select the Options button. In the
Permissions list select **JDM Author** and set the following to
Allowed: - Access Administration Interface - Create - Delete - Edit

Select **JDM Publisher** and set the following to Allowed: - Publish

Save and Close

If you now login as a user in the **JDM Author** group you will see
the Home Dashboard with some modules not relevant for Jdocmanual.

### Turn off the Help menu item

Go to the list of Administrator modules and find the Administrator Menu
module. In the Module tab set the Help Menu item to Hide.

### Unpublish or restrict Access to modules

In the Administrator modules list find the Logged-in Users item used in
the cpanel position (not the cpanel-users position). Either Unpublish it
or set Access to Super Users.

Find and unpublish the Popular Articles and Recently Added Articles
items for the cpanel position (not the cpanel-content position).

There may be other modules needing similar treatment.

The Home Dashboard should now be empty for a **JDM Author**.

## Who can do what?

**JDM Author** and **JDM Publisher** can login but should only
have access to the the Home Dashboard and the Jdocmanual component.

**JDM Author** does not have access to the Commit button in the
Article Edit page toolbar so cannot update the git repository or
displayed page. Otherwise each can use all other features.

**Manager** and **Administrator** can login but will not see the
Jdocmanual component.

**Author**, **Editor** and **Publisher** do not have access
to the Administrator interface.

**Super Users** have complete access.

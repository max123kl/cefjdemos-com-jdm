## Installation Notes

Jdocmanual is designed to deliver Joomla documentation in an easy to use
reference form. It can also be used for other custom documentation. 

Jdocmanual consists of two parts:

* The Jdocmanual component code and an optional plugin to provide CLI access.
* Data files for each available Manual and Language. There are four manuals and
  eight languages available. You only need one to get started but the process
  is so easy you may as well get the English data for all manuals.

Version 4 of Jdocmanual is not compatible with previous versions. Any previous 
version should have the #\_\_jdm_articles and #\_\_jdm_menus tables emptied.

## Jdocmanual Installation

Install and enable Jdocmanual as you would any other Joomla extension. It can
be obtained from:

https://github.com/ceford/cefjdemos-com-jdm

Select the green **Code** button and then the **Download ZIP** item. Save the
ZIP file in your Downloads directory. In your Joomla installation select 
System > Install > Extensions and then select the **Upload Package File** tab.

You will also need the Jdocmanualcli plugin. It used to run data installation
commands from the command line with a little more flexibility than the 
Jdocmanual component within Joomla. It can be obtained from:

https://github.com/ceford/cefjdemos-plg-jdocmanualcli

Install and enable the plugin.

## Data Files 

The data files should be located in your filespace but outside your 
website tree. The parent directory must be named **Manuals**. This is
a suggested Linux structure:
```
/home/username/manuals/developer/
/home/username/manuals/docs/
/home/username/manuals/help/
/home/username/manuals/user/
/home/username/public_html (where your Joomla website is located)
```
Obtain the English data files for each manual from the GitHub. You can 
download ZIP files or use git clone:

* https://github.com/ceford/cefjdemos-data-jdm-developer-en
* https://github.com/ceford/cefjdemos-data-jdm-docs-en
* https://github.com/ceford/cefjdemos-data-jdm-help-en
* https://github.com/ceford/cefjdemos-data-jdm-user-en

Unpack each in its appropriate directory. For example, unpack the .../user/en
data set in the manuals/user/ directory. You will get a folder with the same
name as the repository

**Change the name of the folder** to the **language** abbreviation. You 
should end up with a directort structure like this:
```
/home/username/manuals/user/en/articles/
/home/username/manuals/user/en/images/
/home/username/manuals/user/en/articles-index.txt
/home/username/manuals/user/en/menus-index.txt
```
If you add data for another language it will follow the same pattern, except
that the articles-index.txt and menus-index.txt files are only present in the
in the **/en/** folder. 

## Jdocmanual Configuration

From the Administrator menu open the Jdocmanual > Manual page. Until the 
component is properly configured you will see this **Installation Notes**
page.

* Select the **Options** button in the Toolbar.
* Enter the full path to the data files, ending in **manuals/**. For example:
```
/home/username/manuals/
```
* If your website is in a sub-directory then enter the sub-directory name 
with a leading slash, for example /jdm4.
* Save & Close

## Enable Manuals and Build Articles and Menus

The four Manuals available for Jdocmanual are included in the database but
not enabled. The procedure for each manual is the same as this example for
the User Manual:

* Select **Components > Manuals > Sources** from the Administrator menu.
* Select the **Jooomla User Manual** to open its Edit page.
* Change the *Status* to **Publihsed** and **Save**
* From the *Actions* dropdown list select **Build Articles**. This may take
  a long time and the only indicator is busy icon in the browser tab. Be 
  patient for 2 minutes or so. Jdocmanual will build all of the articles in
  all of the available languages and it will create responsive images in a
  variety of sizes. There will be a summary message, which include problem
  reports.
* From the *Actions* dropdown list select **Build Menus**. This is much 
  quicker.
* For the *Help* Manual only, select **Build Proxy**. This is very quick. It 
  builds files to use as your own Help server. To use it, edit your
  configuration.php and change the `$helpurl` domain name to that of this
  Joomla installation.

## Test

* Select **Components > Manuals > Manuals** from the Administrator menu.

You should see the first page of the User Manual.

## Maintenance

From time to time there will be updates to the Jdocmanual data files. You
can download the source files and repeat the data installation process:
build the articles and menus **in that order**. This is very easy to do
with **git**: just do `git pull` in the appropriate language folder, the
switch to the *cli* folder and run the cli commands. You can build a cron job 
to do this.

## Build Articles and Menus - Command Line Method

The database jdm_articles and jdm_menus tables are populated by reading
the data files. This can take a long time - at least a couple of minutes
and perhaps much longer on slow hosts. It is no quicker from the command
line but you can be more selective over which manuals and languages to build.


Proceed as follows:

1.  Open a terminal window and cd into the cli folder within your Joomla
    installation root. Here is an example:
    `cd /home/username/public_html/optional-subfolder/cli`
2.  Issue the command to convert data from the markdown source files to
    html in the #__jdm_articles table of the database:<br>
    `php joomla.php jdocmanual:action buildarticles user all`
3.  Issue the command to create the menus:<br>
    `php joomla.php jdocmanual:action buildmenus user all`

In these commands, **user** is the name of the manual to be processed and
**all** is a language specifier, which might be a single such as **en** or
**all** languages. 

The Markdown format is converted to HTML and stored in the database. Also,
if there are any images in the data source a set of 6 responsive images
is created.

If you encounter **out of memory** problems or **out of time** problems
you can replace the `all` parameter with a single language code, one of
`de en es fr nl pt ptbr ru`. Using that method you can build the
database manual by manual and/or language by language.

## Database Population - Cron Method

If you do not have access to the command line, common with shared
hosting, you should be able to call these commands from a cron job. Make
sure you give the full path to the php executable, example:
`/usr/local/opt/php@8.1/bin/php`. You need to find out where the php 
executable is located on your host operating system.

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

## Some Reminders

The System - Joomla Accessibility Checker flags the anchors
as having empty links. So in comment out line 70 of administrator/components/com_jdocmanual/libraries/vendor/league/commonmark/src/Extension/HeadingPermalink/HeadingPermalinkRenderer.php
```php
        //$attrs->set('href', '#' . $fragmentPrefix . $slug);
        $attrs->append('class', $this->config->get('heading_permalink/html_class'));
```

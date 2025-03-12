# Notes

## Build Notes

The notes.html page included in a tab in the Sources page is built from the
README.md file. To generate the notes.html page:

```bash
pandoc -f markdown -t html ~/git/cefjdemos-com-jdm/README.md > ~/git/cefjdemos-com-jdm/com_jdocmanual/admin/tmpl/manual/notes.html
```

## Coding Notes

### Controller

- $this->input : access the input
- $this->app : get currently active application object
- $someModel = $this->getModel('Some', 'Administrator');
- $someView = $this->getView('Some', 'Administrator', 'Html');
- $someController = $this->getMVCFactory()->createController('Some', 'Administrator');
- $someTable = $this->getMVCFactory()->createTable('Some', 'Administrator');

### libraries

To update the libraries from time to time - switch to the libraries folder and use `composer update`:

```sh
composer update
Loading composer repositories with package information
Updating dependencies
Lock file operations: 12 installs, 0 updates, 0 removals
  - Locking dflydev/dot-access-data (v3.0.3)
  - Locking jfcherng/php-color-output (3.0.0)
  - Locking jfcherng/php-diff (6.16.2)
  - Locking jfcherng/php-mb-string (2.0.1)
  - Locking jfcherng/php-sequence-matcher (4.0.3)
  - Locking league/commonmark (2.6.1)
  - Locking league/config (v1.2.0)
  - Locking nette/schema (v1.3.2)
  - Locking nette/utils (v4.0.5)
  - Locking psr/event-dispatcher (1.0.0)
  - Locking symfony/deprecation-contracts (v3.5.1)
  - Locking symfony/polyfill-php80 (v1.31.0)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 0 installs, 6 updates, 0 removals
  - Downloading symfony/polyfill-php80 (v1.31.0)
  - Downloading nette/utils (v4.0.5)
  - Downloading nette/schema (v1.3.2)
  - Downloading dflydev/dot-access-data (v3.0.3)
  - Downloading league/commonmark (2.6.1)
  - Upgrading symfony/polyfill-php80 (v1.29.0 => v1.31.0): Extracting archive
  - Upgrading symfony/deprecation-contracts (v3.5.0 => v3.5.1): Extracting archive
  - Upgrading nette/utils (v4.0.4 => v4.0.5): Extracting archive
  - Upgrading nette/schema (v1.3.0 => v1.3.2): Extracting archive
  - Upgrading dflydev/dot-access-data (v3.0.2 => v3.0.3): Extracting archive
  - Upgrading league/commonmark (2.4.2 => 2.6.1): Extracting archive
1 package suggestions were added by new dependencies, use `composer suggest` to see details.
Generating autoload files
8 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
No security vulnerability advisories found.
```

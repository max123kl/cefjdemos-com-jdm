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

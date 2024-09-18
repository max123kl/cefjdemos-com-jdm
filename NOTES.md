# Notes

## Build Notes

The notes.html page included in a tab in the Sources page is built from the
README.md file. To generate the notes.html page:

```bash
pandoc -f markdown -t html ~/git/cefjdemos-com-jdm/README.md > ~/git/cefjdemos-com-jdm/com_jdocmanual/admin/tmpl/manual/notes.html
```

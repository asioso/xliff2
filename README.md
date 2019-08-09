# Asioso Xliff2.0 Bundle


## Prerequisites
* PHP 7.1 or higher (https://secure.php.net/)
* Composer (https://getcomposer.org/download/)
* A Pimcore  Installation using the pimcore e-commerce framework (v5.7 or higher)

![editor notes][notes]     
![editor view][editor]     

## Installation

```bash
composer require asioso/pimcore-xliff2_0-module
``` 

## Configuration

just enable the bundle in the pimcore extension manager

## Translation Notes

Based on pimcore Notes&Events (https://pimcore.com/docs/5.x/Development_Documentation/Tools_and_Features/Notes_and_Events.html)

add a *translation* type

e.g.: like this:
 
```yml
pimcore_admin:
    documents:
        notes_events:
            types:
                - ''
                - 'content'
                - 'seo'
                - 'warning'
                - 'notice'
                - 'translation'

    dataObjects:
        notes_events:
            types:
                - ''
                - 'content'
                - 'seo'
                - 'warning'
                - 'notice'
                - 'translation'

```

## Xliff 1

xliff1 and xliff2.0 are not compatible. to switch back to xliff1 just disable this bundle again, but make sure you have re-imported all your xliff2 files before doing that.

[editor]: https://github.com/asioso/xliff2/raw/master/documentation/img/xliff_editor.png "xliff editor"
[notes]: https://github.com/asioso/xliff2/raw/master/documentation/img/translation_notes.png "editor-notes"



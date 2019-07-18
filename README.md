# Asioso Xliff2.0 Bundle


## Prerequisites
* PHP 7.1 or higher (https://secure.php.net/)
* Composer (https://getcomposer.org/download/)
* A Pimcore  Installation using the pimcore e-commerce framework (v5.7 or higher)


## Installation

composer-no-brainer ... 

**TODO: add private repor or packagist!** 

## Configuration

just enable the bundle

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


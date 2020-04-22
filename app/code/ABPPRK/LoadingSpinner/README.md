

# Loading Spinner for magento 2

## SYSTEM REQUIREMENTS

## LICENSE

## Release notes

## Installation

### Install manually

Unzip the content in app/code in magento file system.

### Enable the extension in Magento 2

```php -f bin/magento module:enable ABPPRK_LoadingSpinner```

### Setup the extension and refresh cache

```php -f bin/magento setup:upgrade```

```php -f bin/magento cache:flush```

```php -f bin/magento setup:di:compile```

```php -f bin/magento setup:static-content:deploy```

and you are ready to go.

### Install using composer

## Using

Invoque in some layout xml in the most apropiate area (usually as top as possible in the page).

Example:

```<referenceContainer name="after.body.start">```
    ```<block class="ABPPRK\LoadingSpinner\Block\Spinner\Spinner" name="spinner" before="-"></block>```
```</referenceContainer>```

## Support

For any issues or questions please get in touch with our support.

#### Web page
 
#### Email
 
#### Phone

#### Twitter

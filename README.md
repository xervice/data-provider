Xervice: DataProvider
====

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xervice/data-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xervice/data-provider/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/xervice/data-provider/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/xervice/data-provider/?branch=master)
[![Build Status](https://travis-ci.org/xervice/data-provider.svg?branch=master)](https://travis-ci.org/xervice/data-provider)


Data transfer objects for xervice packages.

Installation
------------------
```
composer require xervice/data-provider
```

Configuration
-------------------
You have to define, where to search for schema files and where to create the DTOs.
```php
<?php

use Xervice\DataProvider\DataProviderConfig;

$config[DataProviderConfig::FILE_PATTERN] = '*.dataprovider.xml'; // Default: *.dataprovider.xml
$config[DataProviderConfig::DATA_PROVIDER_GENERATED_PATH] = dirname(__DIR__) . '/src/Generated';
$config[DataProviderConfig::DATA_PROVIDER_PATHS] = [
    dirname(__DIR__) . '/src/',
    dirname(__DIR__) . '/vendor/',
];
```

It will search for all files like *.dataprovider.xml in that example.


Define DTO
-------------------
To define a data provider, you define them in an xml file.

***Example:***
```xml
<?xml version="1.0"?>

<DataProviders
  xmlns="xervice:dataprovider-01"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="xervice:dataprovider-01 http://static.xervice.online/schema/dataprovider.schema.xsd"
>

    <DataProvider name="KeyValue">
        <DataElement name="Key" type="string"/>
        <DataElement name="Value" type="string"/>
    </DataProvider>
</DataProviders>
```

***Possible data types:***
* int
* string
* bool
* double (= float)
* float
* array
* object
* DataProviderInterface
* DataProviderInterface[]
* ___AnyNameOfDataProvider___

With the type "DataProviderInterface" you can set any DataProvider.


***Default values***
You can define default values for the following types:
* int
* float
* double
* string
* bool
* array

For the type array only an empty array is possible as default.
If you want to define an empty string as default for the type string, you have to set the default to ''.

```xml
<?xml version="1.0"?>

<DataProviders
  xmlns="xervice:dataprovider-01"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="xervice:dataprovider-01 http://static.xervice.online/schema/dataprovider.schema.xsd"
>

    <DataProvider name="Default">
        <DataElement name="String" default="Text" type="string"/>
        <DataElement name="EmptyText" default="''" type="string"/>
        <DataElement name="Number" default="5" type="int"/>
        <DataElement name="Boolean" default="true" type="bool"/>
        <DataElement name="Float" default="1.5" type="float"/>
        <DataElement name="List" default="[]" type="array"/>
    </DataProvider>

</DataProviders>
```


Use DTO
--------

```php
    $dataProvider = new DataProvider\KeyValueDataProvider();

    // Set values
    $dataProvider->setKey('keyname');
    $dataProvider->setValue('value');

    // Get values
    $dataProvider->getKey();

    // Isset
    $dataProvider->hasKey();
    
    // you can also work with arrays
    $dataProvider->fromArray([
        'Key' => 'keyname',
        'Value' => 'value'
    ]);
    
    // and back to array
    $dataArray = $dataProvider->toArray();


```

***Extend and sharing***

Multiple schame-files with the same DataProvider name will be merged. Also you can choose another DataProvider as type or collection.

```xml
<?xml version="1.0"?>

<DataProviders
  xmlns="xervice:dataprovider-01"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="xervice:dataprovider-01 http://static.xervice.online/schema/dataprovider.schema.xsd"
>

    <DataProvider name="KeyValue">
        <DataElement name="Key" type="string"/>
        <DataElement name="Values" singleton="Value" type="Value[]"/>
    </DataProvider>
</DataProviders>
```

***Using***
```php
<?php

    $dto = new KeyValue();

    $value = new Value();
    $dto->addValue($value);

    // List
    $dto->setValues(
        [
            $value
        ]
    );

    // Get List
    $dto->getValues();
```

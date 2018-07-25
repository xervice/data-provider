Xervice: DataProvider
====

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xervice/data-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xervice/data-provider/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/xervice/data-provider/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/xervice/data-provider/?branch=master)


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

$config[DataProviderConfig::DATA_PROVIDER_GENERATED_PATH] = dirname(__DIR__) . '/src/Generated';
$config[DataProviderConfig::DATA_PROVIDER_PATHS] = [
    dirname(__DIR__) . '/src/',
    dirname(__DIR__) . '/vendor/',
];
```

It will search for all files like *.dataprovider.xml.


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
* double
* array
* DataProviderInterface
* DataProviderInterface[]
* ___AnyNameOfDataProvider___

With the type "DataProviderInterface" you can set any DataProvider.


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
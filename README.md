Xervice: DataProvider
====

Extens Xervice to create dataprovider schema files and dynamically generate classes from them.  

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

will be:

```php
    $dataProvider = new DataProvider\KeyValueDataProvider();
    $dataProvider->setKey('keyname');
    $dataProvider->setValue('value');
    
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
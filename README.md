# # Very short description of the package
This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require metko/activiko
```

## Usage
### Basic usage
Just use the trait RecordActiviko in the model you want to be recorded.
``` php
use Metko\Activiko\Traits\RecordActiviko;
```
The model will record automatically the following events : `created`, `update` and `deleted`.

### Filter the event
If you want to modify this globally, you can update the `recordableEvents`  key in the config file. Or at the top of your model, just declare a property call recordable events:
``` php
protected static $recordableEvents = ['updated']; // Only updated event will be recorded.
```

Optionally, you can specify the event to be recorded before to execute an action. 
``` php
public function createArtBoard($artist)
{
	app('activiko')->onlyRecordsEvents(['updated']); 
	$artist->create(['awesomeness' => 'yey']);
	$artist->update(['awesomeness' => 'much more']);
	//Only the updated event will trigger the record activity
}
```

### Filter fields
You can also filter fields to not be recorded in the changes property like this: 
``` php
protected static $excludeOfRecords = ['body']; // Only updated event will be recorded.
```
> By default, it will exclude the fields  `id`   , `created_at` and  `updated_at` . You can override this this in the config file.
``` php
protected static $fieldsToRecords = ['name', 'size', 'color']; // Only record this fields
```


You can also specify the fields to be register with the model  before to execute an action like so: 
``` php
 $artist->disableFields(['body']); // Fields body will not be in the changes property
```


### Enable/Disable the record 

You can disable a record for a specific model like so: 
``` php
app('activiko')->disable(); 
app('activiko')->enable(); 
// Or
$artist->disableRecord();
$artist->enableRecord();
```

### Get last changes with comparaison
You can call the method getChanges on activity model or call directly the lastChanges from the model recorded
``` php
$artist->lastChanges(); // Return array 
// [
//		'before' => [`
//			'name' => "title",
//			'body' => 'body'
//		], 
//		'after' => [`
//			'name' => "New title",
//			'body' => 'New body'
//		]
//	 ]


$artist->lastChanges('name');
// [
//		'before' => "title"
//		'after' => 'New title'
//	 ]

$artist->lastChanges('name', 'before');
// "title"

$artist->lastChanges("before"); // Return array 
// [
//		'before' => [`
//			'name' => "title",
//			'body' => 'body'
//		], 
//	 ]

$artist->lastChanges("after"); // Return array 
// [
//		'after' => [`
//			'name' => "New title",
//			'body' => 'New body'
//		], 
//	 ]
```



### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](_CHANGELOG.md_) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](_CONTRIBUTING.md_) for details.

### Security

If you discover any security related issues, please email thomas.moiluiavon@gmail.com instead of using the issue tracker.

## Credits

- [Thomas Moiluiavon](_https://github.com/metko_)
- [All Contributors](_../../contributors_)

## License

The MIT License (MIT). Please see [License File](_LICENSE.md_) for more information.

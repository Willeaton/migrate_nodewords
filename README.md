# migrate_nodewords
Migrates Drupal 6 nodewords to Drupal 8 metatag

This module imports Drupal 6's nodewords node data but also imports from the page_title module. 
We import the data into a single array and save it in the metatag module format.

This module assumes the following things:

1. You have a Drupal 6 installation already migrated into Drupal 8 using the migrate tool
2. The nids match between D6 and D8
3. You have installed the metatag module in D8
4. You have created a field called field_metatags in all node content types that you had it for before.
5. You have set up an extra DB connection in your settings.php called drupal_6. e.g.

```php
$databases['drupal_6']['default'] = array (
  'database' => 'd6',
  'username' => 'd6',
  'password' => 'd6',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);
```

6. Go to /admin/config/search/migrate_nodewords and click the button (poorly called Saved configuration for now)
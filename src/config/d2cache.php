<?php

/*
|--------------------------------------------------------------------------
| Doctrine2j5 :: Cache Configuration
|--------------------------------------------------------------------------
|
| See: https://github.com/jeanbelhache/doctrine2-l5
|
| Caching configuration for Doctrine2. Uses Laravel's own cache configuration
| from config/cache.php to create a Dcotrine2 cache object.
|
| Implemented cache providers include:
|     - MemcacheCache (memcached)
|     - ArrayCache (array, file)
|
| Any cache such as ArrayCache requiring no configuration can be named below.
| Caches requiring configuration will require updating:
|     src/Doctrine2l5/Doctrine2CacheServiceProvider.php
|
| It should be fairly trivial to do this. Please open a pull request when you do!
|
*/

return [

    // config cache in standard Laravel config/cache.php file.

    'namespace' => 'Doctrine2',

];

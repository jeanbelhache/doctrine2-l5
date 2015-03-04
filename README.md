# Doctrine 2 for Laravel 5

What the package supported ?

- Doctrine2
- Laravel Authentication
- Yml metadata mapping
- Some extensions for DQL query like 'acos, sin, degrees, ...'

## configuration

"jeanbelhache/doctrine2-l5": "dev-master",

in config/app:

'Doctrine2l5\Doctrine2CacheServiceProvider',
'Doctrine2l5\Doctrine2ServiceProvider',


./artisan vendor:publish --provider "Doctrine2l5\Doctrine2CacheServiceProvider"
./artisan vendor:publish --provider "Doctrine2l5\Doctrine2ServiceProvider"

## License

Like the Laravel framework itself, this project is open-sourced under the [MIT license](http://opensource.org/licenses/MIT).

## Inspiration

Based on the original package [opensolutions/doctrine2bridge-l5](https://github.com/opensolutions/doctrine2bridge-l5).


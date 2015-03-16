# Doctrine 2 for Laravel 5

[![Join the chat at https://gitter.im/jeanbelhache/doctrine2-l5](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/jeanbelhache/doctrine2-l5?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

What the package supported ?

- Doctrine2
- Laravel Authentication
- Annotations Driver
- Some extensions for DQL query like 'acos, sin, degrees, ...'

## configuration

"jeanbelhache/doctrine2-l5": "dev-master",

in config/app:

<pre>
'Doctrine2l5\Doctrine2CacheServiceProvider',
'Doctrine2l5\Doctrine2ServiceProvider',
</pre>

<pre>
./artisan vendor:publish --provider "Doctrine2l5\Doctrine2CacheServiceProvider"
./artisan vendor:publish --provider "Doctrine2l5\Doctrine2ServiceProvider"
</pre>

## License

Like the Laravel framework itself, this project is open-sourced under the [MIT license](http://opensource.org/licenses/MIT).

## Inspiration

Based on the original package [opensolutions/doctrine2bridge-l5](https://github.com/opensolutions/doctrine2bridge-l5).


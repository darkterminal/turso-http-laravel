<p align="center">
  <a href="https://docs.turso.tech/sdk/php/guides/laravel">
    <img alt="Turso + Laravel" src="https://i.imgur.com/e3Pn6Rx.png" width="1000">
    <h3 align="center">Turso + Laravel</h3>
  </a>
</p>

<p align="center">
  SQLite for Production. Powered by <a href="https://turso.tech/libsql">libSQL</a> and <a href="https://github.com/tursodatabase/turso-driver-laravel">libSQL Extension</a> for PHP.
</p>

<p align="center">
  <a href="https://turso.tech"><strong>Turso</strong></a> ·
  <a href="#installation"><strong>Quickstart</strong></a> ·
  <a href="#usage"><strong>Examples</strong></a> ·
  <a href="#database-configuration"><strong>Docs</strong></a> ·
  <a href="https://discord.com/invite/4B5D7hYwub"><strong>Discord</strong></a> ·
  <a href="https://blog.turso.tech/"><strong>Blog &amp; Tutorials</strong></a>
</p>

---

<h1 id="a-libsql-http-for-laravel" align="center">A LibSQL HTTP for Laravel</h1>

## Requirement

-   Turso CLI - [Installation](https://docs.turso.tech/cli/introduction)
-   Turso Account - [Register Here](https://tur.so/dt)

## Installation

You can install the package via composer:

```bash
composer require tursodatabase/turso-http-laravel
```

Then register the service provider at `bootstrap/providers.php` array:

```php
return [
    App\Providers\AppServiceProvider::class,
    Turso\Http\Laravel\LibSQLHttpServiceProvider::class, // Here
];
```

## Database Configuration

```env
DB_CONNECTION=libsql
DB_DATABASE=<your-turso-database-url>
DB_AUTH_TOKEN=<your-turso-database-auth-token>
```

## Database Configuration

Add this configuration at `config/database.php` inside the `connections` array:

```php
'libsql' => [
    'driver' => 'libsql',
    'url' => env('DB_DATABASE', ''),
    'authToken' => env('DB_AUTH_TOKEN', ''),
    'database' => null,
    'prefix' => '',
],
```

> Copy and Paste and do not change it! Or try to change it and will broke your app or give you malfunction.

## Usage

For database operation usage, everything have same interface like usual when you using `Illuminate\Support\Facades\DB` in your database model. But remember, this is LibSQL they have `sync` method that can be used when you connect with Remote Replica Connection (Embedded Replica).

```php
use Illuminate\Support\Facades\DB;

// Create
DB::table('users')->insert([
    'name' => 'Budi Dalton',
    'email' => 'budi.dalton@duck.com'
]);

// Read
DB::table('users')->get();
DB::table('users')->where('id', 2)->first();
DB::table('users')->orderBy('id', 'DESC')->limit(2)->get();

// Update
DB::table('users')->where('id', 2)->update(['name' => 'Doni Mandala']);

// Delete
DB::table('users')->where('id', 2)->delete();

// Transaction
try {
    DB::beginTransaction();

    $updated = DB::table('users')->where('id', 9)->update(['name' => 'Doni Kumala']);

    if ($updated) {
        echo "It's updated";
        DB::commit();
    } else {
        echo "Not updated";
        DB::rollBack();
    }

    $data = DB::table('users')->orderBy('id', 'DESC')->limit(2)->get();
    dump($data);
} catch (\Exception $e) {
    DB::rollBack();
    echo "An error occurred: " . $e->getMessage();
}

```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Imam Ali Mustofa](https://github.com/darkterminal)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

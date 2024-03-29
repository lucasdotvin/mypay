<h1 align="center">MyPay</h1>

## About

This is an example of a payment platform, created for study purposes.

## Installation

At first, you should copy your `.env.example` file to `.env`:

```
cp .env.example .env
```

Then, you can install the dependencies using a Docker image, so you'll avoid errors with the requirements:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

After that, use the Laravel sail to activate the containers and generate an application key:

```
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

In the end, you should run the migrations and fill the database with mandatory data:

```
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed MandatoryDataSeeder
```

> **Note**
> If the migration command fails, try restart all containers by calling `./vendor/bin/sail stop` and then `./vendor/bin/sail up -d` once more.

## Endpoints

The application endpoints can be checked by opening the Postman file on [docs](docs/postman-collection.json).

## Testing

The test suite can be ran with the given command:

```bash
sail test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Lucas Vinicius](https://github.com/lucasdotvin)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

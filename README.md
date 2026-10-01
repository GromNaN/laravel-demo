# Laravel Queryable Encryption demo

A small Laravel application that exercises the `mongodb:encryption:rewrap-data-keys`
command of `mongodb/laravel-mongodb` against a real server: it creates an
encrypted collection, inserts documents, rewraps the data keys from one local
master key to another, and reads the documents back.

The package is required from the `5.x` branch of
[`mongodb/laravel-mongodb`](https://github.com/mongodb/laravel-mongodb) through
a Composer VCS repository. The `mongodb:encryption:rewrap-data-keys` command is
added by [PR #3602](https://github.com/mongodb/laravel-mongodb/pull/3602), so
the demo only runs once that pull request is merged into `5.x`.

## Requirements

- PHP 8.2+ with `ext-mongodb` 2.4+
- MongoDB 8.0+ on a replica set or a sharded cluster
- The Automatic Encryption Shared Library (`crypt_shared`)

## Two local master keys

A `local` KMS provider holds a single key, and rewrapping needs the old and the
new key at the same time: the data key is unwrapped with the old master key
before being wrapped with the new one. Two local keys therefore have to be
configured as two named providers, `local` and `local:rotated`.

Generate the keys and put them in `.env`:

```sh
php -r 'echo base64_encode(random_bytes(96)), PHP_EOL;'   # MONGODB_LOCAL_MASTER_KEY
php -r 'echo base64_encode(random_bytes(96)), PHP_EOL;'   # MONGODB_ROTATED_MASTER_KEY
```

```dotenv
DB_CONNECTION=mongodb
MONGODB_URI="mongodb://127.0.0.1:27017"
MONGODB_DATABASE=laravel_qe
CRYPT_SHARED_LIB_PATH=/path/to/mongo_crypt_v1.so
MONGODB_LOCAL_MASTER_KEY="..."
MONGODB_ROTATED_MASTER_KEY="..."
```

## Configuration

`config/database.php` declares the `mongodb` connection with
`driver_options.autoEncryption`: the key vault namespace, the two named local
KMS providers, the `crypt_shared` library, and the `encryptedFieldsMap` for the
`patients.ssn` field. A second connection, `mongodb_plain`, points at the same
server without auto encryption, to show what is stored at rest.

The key vault is `encryption.__demoKeyVault`, a namespace of its own so the demo
never rewraps keys that belong to another application.

## Run

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan qe:demo
```

The command:

1. drops and recreates the encrypted `patients` collection and its key vault;
2. inserts three patients with an encrypted `ssn`;
3. reads them back, and prints the data key with the provider wrapping it;
4. rewraps the data keys under `local:rotated`;
5. prints the same data key again: same id, new `keyMaterial`, new provider;
6. reads the patients back and checks they are identical to step 3;
7. reads the collection with the plain connection, showing `ssn` stored as an
   encrypted binary.

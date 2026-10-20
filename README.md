# Laravel demos

Small Laravel applications that show one feature of
[`mongodb/laravel-mongodb`](https://github.com/mongodb/laravel-mongodb) each.

Every demo lives on its own branch, and each branch holds a full application with
its own README. This `main` branch is only the index.

| Demo | Branch | What it shows |
|---|---|---|
| Queryable Encryption | [`auto-encryption`](../../tree/auto-encryption) | Encrypted fields, a key vault of its own, and the rewrap of data keys from one local master key to another |
| Query logging | [`telescope`](../../tree/telescope) | The MongoDB commands of a page recorded by Laravel Telescope and Laravel Debugbar, next to the same queries on SQLite |

## Run a demo

```sh
git clone https://github.com/GromNaN/laravel-demo.git
cd laravel-demo
git switch auto-encryption
```

Then follow the README of the branch. Each one lists the PHP extensions, the
MongoDB version and the server topology it needs.

## Requirements

- PHP 8.2 or later with `ext-mongodb`
- Composer
- A running MongoDB server: 7.0 or later for the query logging demo, 8.0 or
  later on a replica set for the Queryable Encryption demo

## Add a demo

Create the branch from the Laravel skeleton, keep the application runnable on its
own, and add a row to the table above.

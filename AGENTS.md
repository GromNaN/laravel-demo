# Repository layout

This repository holds small Laravel applications, each one demonstrating a
feature of [`mongodb/laravel-mongodb`](https://github.com/mongodb/laravel-mongodb).
The demos do not share code and are not meant to be compared with `git diff`.

| Branch | Content |
|---|---|
| `main` | The index: this file and the README that lists the demos. No application code. |
| one branch per demo | A complete, runnable Laravel application with its own README. |

The demos and their branches are listed in the README of `main`, which is the
only place that keeps that list. Read it instead of looking for the branches
here.

# The branches are orphan branches

Every demo branch has exactly one commit and no parent, and `main` is not an
ancestor of any of them. The branches share no history, so no demo inherits the
files, the dependencies or the database schema of another one, and nothing has
to be rebased when a demo changes.

The rules that follow from this:

- Never merge one demo branch into another, and never merge `main` into a demo.
- Never rebase or cherry-pick across branches. Copy the file instead.
- A demo branch has to be usable on its own: cloning the repository, switching
  to that branch and following its README has to give a running application.
- When a shared file changes (`.gitignore`, `.editorconfig`, the Laravel
  skeleton), apply it branch by branch. There is no common ancestor to merge.

# Add a demo

Create the branch without a parent:

```sh
git switch --orphan my-demo
git rm -rf --cached .    # the files of the previous branch are still on disk
```

Then build the application, commit it once, push it, and add its row to the
table of the README on `main`.

The commit message of a demo branch follows the other ones:

```
Add a Laravel demo of <feature>
```

# Create the application

A demo starts from a stock Laravel application, created in the branch, which is
empty at that point:

```sh
composer global require laravel/installer   # once per machine
laravel new . --no-interaction
```

Keep the skeleton as the installer leaves it: the `.editorconfig`, the
`.gitignore`, the `artisan` script, the `composer.json` scripts and the test
setup are the same in every demo, and a reader recognizes them.

Then add the package and the connection:

```sh
composer require mongodb/laravel-mongodb
```

- The installer lists SQLite, MySQL, MariaDB, PostgreSQL and SQL Server, and no
  MongoDB. Answer `sqlite`, then set `DB_CONNECTION=mongodb` in `.env` and
  declare the connection in `config/database.php`.
- Add the VCS repository to `composer.json` when the demo needs a version of
  `mongodb/laravel-mongodb` that is not released yet, and say so in the README.
- Trim `.env.example` to the variables the demo reads. Comment each MongoDB
  variable with the value it expects.

# The demo page

The feature is shown on a web page. A reader clones the branch, runs
`php artisan serve` and sees the feature work in the browser, without reading
the sources first.

The page stays simple, like the other demos:

- One Blade view per page, with its own `<style>` block. No layout, no CSS
  framework, no JavaScript, no Vite asset.
- The default `welcome.blade.php` stays at `/` and links to the demo pages.
- The page is a list of steps. A step shows the PHP code it runs, then the
  result of that code, so the reader follows the demo on the page alone.
- A button runs the demo again, so the page can be replayed and compared.
- The page inserts its own sample data. It works right after the install steps
  of the README, on a fresh database, and it can be run twice in a row.
- The code lives in a controller, one invokable controller per page. The view
  receives plain arrays and strings, not Eloquent models.
- A console command can complete the page when the output does not fit on a
  page, for instance a long dump or a progress log. The page remains the entry
  point of the demo.

# The README of a demo

Each demo branch documents itself in its README, in this order:

1. What the demo shows, and the pull request of `mongodb/laravel-mongodb` it
   depends on when the feature is not released yet.
2. The requirements: PHP version, `ext-mongodb` version, MongoDB version and
   topology.
3. The install steps and the environment variables to set.
4. The URL of each demo page, and what the page shows.
5. The console commands the demo provides, when it has some.

# The README of main

The README of `main` is the landing page of the repository. It holds the table
of the demos, with the branch name, a link to the branch and a one-line
description. Add a row there when a demo is added, renamed or removed. This
file does not list the demos.

# Conventions

- Write in Plain Language English, and never use an em dash.
- End every text file with a newline.
- Run `phpcbf` then `phpcs` before committing PHP code.

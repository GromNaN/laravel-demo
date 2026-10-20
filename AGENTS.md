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

# The README of a demo

Each demo branch documents itself in its README, in this order:

1. What the demo shows, and the pull request of `mongodb/laravel-mongodb` it
   depends on when the feature is not released yet.
2. The requirements: PHP version, `ext-mongodb` version, MongoDB version and
   topology.
3. The install steps and the environment variables to set.
4. How to run the demo and what the output means.

# The README of main

The README of `main` is the landing page of the repository. It holds the table
of the demos, with the branch name, a link to the branch and a one-line
description. Add a row there when a demo is added, renamed or removed. This
file does not list the demos.

# Conventions

- Write in Plain Language English, and never use an em dash.
- End every text file with a newline.
- Run `phpcbf` then `phpcs` before committing PHP code.

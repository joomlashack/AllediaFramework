# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

The Joomlashack Framework (`lib_allediaframework`): a Joomla **library** extension that other Joomlashack extensions (components, modules, plugins) depend on. It is not runnable on its own. Code only executes inside a Joomla site (every PHP file starts with `defined('_JEXEC') or die();`). A single codebase supports **Joomla 3.9+ and Joomla 4/5/6**, on PHP 7.4+ (`composer.json` and the manifest's `<phpminimum>`).

## Build / tooling

- Unit tests use Codeception: run `composer test` from the repo root (it installs `tests/vendor` first), or `cd tests && composer install && composer test`. Test tooling has its own `tests/composer.json` and gitignored `tests/vendor`, so it never touches the shipped `src/vendor`. `codeception.yml` is at the repo root, and `tests/bootstrap.php` defines `_JEXEC` and registers the framework `AutoLoader`. Only classes that don't need Joomla CMS classes are unit tested. Use `Codeception\Attribute\DataProvider` because Codeception ignores PHPUnit's attribute. There is no linter config or CI.
- Updating test dependencies: run `composer --working-dir=tests update`, never root `composer update` (that rewrites the shipped `src/vendor`). Keep `config.platform.php` (8.2.0) in `tests/composer.json`; without it a lock built on newer PHP breaks older PHP. The pin also keeps `joomla/string`/`joomla/registry` on 3.x, because 4.x needs PHP 8.3. Keep `doctrine/inflector` as a direct dependency so `InflectorTest` covers the Doctrine backend. Commit `tests/composer.lock`, and run `composer test` afterwards.
- Packaging uses Phing via the external **ShackBuilder** project. `build.xml` only imports `${builder.path}/src/build.xml`. Copy `build.dist.properties` to `build.properties` (gitignored) and set `builder.path`. Then run `phing` from the repo root. Zips land in `packages/` (gitignored).
- Composer installs into `src/vendor` (`config.vendor-dir`), and `src/vendor` **is committed** because it ships in the package. Its only runtime dependency is `pimple/pimple`. `psr/container` is `replace`d because Joomla provides it.
- Releases: bump `<version>` and `<creationDate>` in `src/allediaframework.xml`. Dev versions use a `bN` suffix (e.g. `3.8.12b1`), and the release commit sets the final version ("Release vX.Y.Z"). Work happens on `development`, and `main` is the release branch.
- The manifest's `<files>` and `<media>` blocks are whitelists. A new folder under `src/` or `src/media/` is neither packaged nor installed until it is listed there. Media installs to `media/lib_allediaframework/` and is referenced by relative paths such as `lib_allediaframework/fontawesome/css/all.min.css` (see `helpers/html/alledia.php`).
- `_scratch/` is local scratch space. It is not in the manifest, so it never ships, but it is also not gitignored.

## Architecture

**Bootstrap:** consumers load `src/include.php`. It defines `ALLEDIA_FRAMEWORK_LOADED` and `ALLEDIA_FRAMEWORK_PATH`, registers the custom PSR-4 `AutoLoader` for `Alledia\Framework` → `src/Framework`, loads the composer autoloader, adds `src/helpers/html` to `HTMLHelper`, and aliases `Alledia\Framework\Extension` → `Joomla\Extension\Licensed`. On Joomla 3 it also creates J4-namespaced aliases for legacy classes.

**Extension model** (`Framework/Joomla/Extension/`):
- `Generic` reads an extension's DB row and XML manifest, plus its params, update URL and footer markup.
- `Licensed` extends `Generic` and reads the custom `<alledia>` manifest block (`license`, `namespace`). `isPro()`/`isFree()` come from that block. `loadLibrary()` autoloads `Alledia\<namespace>` from the extension's `library/` folder, and Pro code lives under `library/Pro`. This free/pro split drives most consumer behavior.
- `AbstractComponent` (singleton per namespace), `AbstractPlugin` and `AbstractFlexibleModule` are the base classes consumers extend.
- `Factory` extends Joomla's `Factory` and caches extension instances (`getExtension($namespace, $type, $folder)`). It also smooths over J3/J4 differences (`getDatabase`, `getDispatcher`, `triggerEvent`).

**MVC bases** (`Framework/Joomla/Controller|Model|View`): thin layers over Joomla's MVC classes, built from shared traits (`TraitController`, `TraitModel`, `TraitAllediaView`). For controllers, use the `Abstract*` classes. The non-abstract `Base`/`Admin`/`Form`/`Json` controllers are `@deprecated v3.8.1`.

**Form field shims** (`Framework/Joomla/Form/Field/*Field.php`): each one subclasses Joomla's namespaced field class (`Joomla\CMS\Form\Field\XField`). If that class doesn't exist (J3), it first calls `FormHelper::loadFieldClass()` and `class_alias`es the legacy `JFormFieldX`. Each also uses `TraitLayouts`, which prepends a `layouts/` folder next to the *consumer's* field class to the layout search paths. On J3 it prefers a `<layout>_j3.php` variant when one exists. To add a new field shim, copy an existing one such as `FilelistField.php`.

**Compatibility convention:** prefer feature/class detection (`class_exists`, `is_callable`) over `Version::MAJOR_VERSION` checks (see commit "Always check for capability not Joomla version"). `Helper::createClassAliases()` / `createDatabaseClassAliases()` are the shared aliasing helpers.

## Conventions

- Every PHP file carries the standard GPL header block (`@package AllediaFramework`, `@copyright <years> Joomlashack.com`).
- Side effects that must sit before a class declaration (aliasing, `defined('_JEXEC')`) are wrapped in `// phpcs:disable PSR1.Files.SideEffects` … `// phpcs:enable` comments.
- This is a shared dependency of many extensions, so keep public and protected APIs backward compatible. Deprecate with `@deprecated vX.Y.Z` rather than removing.

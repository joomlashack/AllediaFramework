# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

The Joomlashack Framework (`lib_allediaframework`): a Joomla **library** extension that other Joomlashack extensions (components, modules, plugins) depend on. It is not runnable on its own. Code only executes inside a Joomla site (every PHP file starts with `defined('_JEXEC') or die();`). A single codebase supports **Joomla 3.9+ and Joomla 4/5**, on PHP 7.4+ (composer.json). The manifest's `<phpminimum>` still says 7.2.5.

## Build / tooling

- There are no tests, linter config or CI in this repo.
- Packaging uses Phing via the external **ShackBuilder** project. `build.xml` only imports `${builder.path}/src/build.xml`. Copy `build.dist.properties` to `build.properties` (gitignored) and set `builder.path`. Then run `phing` from the repo root. Zips land in `packages/` (gitignored).
- Composer installs into `src/vendor` (`config.vendor-dir`), and `src/vendor` **is committed** because it ships in the package. Its only runtime dependency is `pimple/pimple`. `psr/container` is `replace`d because Joomla provides it.
- Releases: bump `<version>` and `<creationDate>` in `src/allediaframework.xml`. Dev versions use a `bN` suffix (e.g. `3.8.12b1`), and the release commit sets the final version ("Release vX.Y.Z"). Work happens on `development`, and `main` is the release branch.
- `_scratch/` is local scratch space and is not part of the package.

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

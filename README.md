[<img src="https://www.joomlashack.com/images/logo_circle_small.png"/>](https://www.joomlashack.com)

Joomlashack Framework
======================

Shared libraries for extensions development.

The Joomlashack Framework (`lib_allediaframework`) is a Joomla **library** extension. Joomlashack components, modules
and plugins depend on it. It does not run on its own: code only executes inside a Joomla site, and every PHP file
begins with `defined('_JEXEC') or die();`.

| Requirement | Supported                                                                                  |
|-------------|--------------------------------------------------------------------------------------------|
| Joomla      | 3.9+, 4.x, 5.x, 6.x (one codebase)                                                         |
| PHP         | 7.4+ (`composer.json` and the manifest's `<phpminimum>`)                                   |
| PHP ext     | curl, json, mbstring, simplexml                                                            |

This document has two parts:

- **[Part 1: Using the framework](#part-1-using-the-framework)** is for developers building extensions that depend on
  the framework.
- **[Part 2: Contributing to the framework](#part-2-contributing-to-the-framework)** is for developers changing the
  framework itself.

---

Contents
--------

- [Part 1: Using the framework](#part-1-using-the-framework)
    - [Loading the framework](#loading-the-framework)
    - [Namespaces, the `<alledia>` manifest block and Free/Pro](#namespaces-the-alledia-manifest-block-and-freepro)
    - [Extension objects](#extension-objects)
    - [Components](#components)
    - [Plugins](#plugins)
    - [Modules](#modules)
    - [Controllers](#controllers)
    - [Models](#models)
    - [Views](#views)
    - [Tables](#tables)
    - [Form fields](#form-fields)
    - [Events](#events)
    - [Toolbar](#toolbar)
    - [HTML helpers](#html-helpers)
    - [Utility classes](#utility-classes)
    - [Deprecated APIs](#deprecated-apis)
- [Part 2: Contributing to the framework](#part-2-contributing-to-the-framework)
    - [Repository layout](#repository-layout)
    - [Bootstrap internals](#bootstrap-internals)
    - [Cross-version compatibility strategy](#cross-version-compatibility-strategy)
    - [Adding a form field shim](#adding-a-form-field-shim)
    - [Backward compatibility rules](#backward-compatibility-rules)
    - [Coding conventions](#coding-conventions)
    - [Dependencies](#dependencies)
    - [Building a package](#building-a-package)
    - [Release process](#release-process)
    - [Testing](#testing)

---

Part 1: Using the framework
===========================

Loading the framework
---------------------

Include the bootstrap file before you use any framework class. It is safe to include it more than once, and its return
value tells you whether the framework is available:

```php
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

$frameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';

if (!(is_file($frameworkPath) && include $frameworkPath)) {
    Factory::getApplication()->enqueueMessage('[MyExtension] Joomlashack Framework not found', 'error');

    return;
}
```

After loading, the following are available:

| Item                         | Meaning                                                                        |
|------------------------------|--------------------------------------------------------------------------------|
| `ALLEDIA_FRAMEWORK_LOADED`   | Constant set to `1` once the framework has been bootstrapped                   |
| `ALLEDIA_FRAMEWORK_PATH`     | Absolute path to the installed library folder                                  |
| `Alledia\Framework\*`        | All framework classes, autoloaded                                              |
| `Alledia\Framework\Extension`| Alias for `Alledia\Framework\Joomla\Extension\Licensed`                        |
| `HTMLHelper::_('alledia.*')` | The framework's HTML helpers (see [HTML helpers](#html-helpers))               |

On Joomla 3 the bootstrap also creates Joomla 4 names for some legacy classes, so you can write J4-style code against
them:

- `Joomla\CMS\HTML\Helpers\Sidebar` → `JHtmlSidebar`
- `Joomla\Component\Content\Site\Helper\RouteHelper` → `ContentHelperRoute`

Namespaces, the `<alledia>` manifest block and Free/Pro
-------------------------------------------------------

Most framework behavior is driven by a custom `<alledia>` block in your extension's XML manifest:

```xml
<extension type="component" method="upgrade">
    <name>com_myextension</name>
    <!-- ... -->
    <alledia>
        <element>myextension</element>
        <namespace>MyExtension</namespace>
        <name>My Extension</name>
        <license>pro</license>  <!-- "free" or "pro" -->
        <targetplatform>3.9</targetplatform>
        <phpminimum>7.4</phpminimum>
    </alledia>
</extension>
```

- `namespace` sets your PHP namespace root: `Alledia\<namespace>`.
- `license` decides whether `isPro()` returns true. Anything other than `pro`, including a missing value, counts as
  free.

### Library folder layout

When an extension object's `loadLibrary()` runs, it registers a PSR-4 autoloader that maps `Alledia\<namespace>` to the
extension's `library/` folder:

```
administrator/components/com_myextension/
├── myextension.xml
└── library/
    ├── Free/                    → Alledia\MyExtension\Free\...
    │   └── Joomla/
    │       ├── Controller/
    │       │   ├── Admin.php    → Alledia\MyExtension\Free\Joomla\Controller\Admin
    │       │   └── Site.php
    │       ├── Model/
    │       └── Table/
    └── Pro/                     → Alledia\MyExtension\Pro\...  (Pro package only)
```

Free code lives under `library/Free` and Pro-only code under `library/Pro`. Several framework helpers pick a class from the `Free` or `Pro`
sub-namespace based on the license (see [Components](#components)).

Extension objects
-----------------

`Alledia\Framework\Joomla\Extension\Generic` represents one installed extension. It reads the extension's `#__extensions`
row and its XML manifest. `Licensed` extends it with the `<alledia>` block and library loading. You normally get one
from the factory, which caches one instance per namespace, type and folder:

```php
use Alledia\Framework\Factory;

$component = Factory::getExtension('MyExtension', 'component');
$plugin    = Factory::getExtension('MyPlugin', 'plugin', 'system');
$module    = Factory::getExtension('MyModule', 'module');
```

The first argument is the element name without its `com_`/`mod_` prefix. It is case-insensitive: it is lower-cased to
find the element, then replaced by the manifest's `<alledia><namespace>` value.

You can also look one up from a full element name:

```php
use Alledia\Framework\Joomla\Extension\Helper as ExtensionHelper;

$extension = ExtensionHelper::getExtensionForElement('plg_system_myplugin');
$info      = ExtensionHelper::getExtensionInfoFromElement('com_myextension');
// ['prefix' => 'com', 'type' => 'component', 'name' => 'myextension', 'group' => null, 'namespace' => 'myextension']
```

### `Generic` API

| Member                                           | Description                                                          |
|--------------------------------------------------|----------------------------------------------------------------------|
| `$id`, `$name`, `$params` (`Registry`)           | Values from `#__extensions`. `$params` is empty if not installed.    |
| `$manifest` / `$manifestXml`                     | Manifest as a decoded object / `SimpleXMLElement`                    |
| `isEnabled(): bool`                              | Enabled state from the database                                      |
| `getExtensionPath(): string`                     | Install path, e.g. `JPATH_SITE/administrator/components/com_x`       |
| `getElementToDb()` / `getFullElement()`          | `com_x` / `plg_system_x` style element names                         |
| `getManifest($force)` / `getManifestAsSimpleXML($force)` | Cached manifest access                                       |
| `getConfig($force): ?SimpleXMLElement`           | The extension's `config.xml`, if any                                 |
| `getVersion(): ?string`                          | `<version>` from the manifest                                        |
| `getUpdateURL()` / `setUpdateURL($url)`          | Read/write the update site location                                  |
| `storeParams()`                                  | Save `$params` back to `#__extensions`                               |
| `getFooterMarkup(): string`                      | Renders the extension's `customfooter` field (admin footer branding) |

### `Licensed` API

| Member                        | Description                                                              |
|-------------------------------|--------------------------------------------------------------------------|
| `isPro(): bool` / `isFree(): bool` | From `<alledia><license>`                                           |
| `getLibraryPath(): string`    | `<extension path>/library`                                               |
| `getProLibraryPath(): string` | `<extension path>/library/Pro`                                           |
| `loadLibrary(): bool`         | Registers `Alledia\<namespace>` → `library/`. Returns false if the folder or namespace is missing. |

Components
----------

Extend `Alledia\Framework\Joomla\Extension\AbstractComponent`. It is a singleton (`getInstance()`). Its constructor:

- adds `JPATH_COMPONENT/models` and `JPATH_COMPONENT/tables` to Joomla's model and table include paths
- calls `loadLibrary()`

```php
namespace Alledia\MyExtension;

use Alledia\Framework\Joomla\Extension\AbstractComponent;

class Component extends AbstractComponent
{
}
```

Component entry point (`myextension.php`), J3-style:

```php
use Alledia\MyExtension\Component;

defined('_JEXEC') or die();

require_once JPATH_SITE . '/libraries/allediaframework/include.php';

Component::getInstance('MyExtension')->init();
```

`init()` calls `loadController()` and then `executeRedirectTask()`:

1. `loadController()` requires `JPATH_COMPONENT/controller.php`. It then calls
   `Alledia\<namespace>\<License>\Joomla\Controller\<Client>::getInstance($namespace)`. `<License>` is `Free` or `Pro`
   (from `ucfirst()` on the license) and `<Client>` is `Admin` or `Site`. If that class doesn't exist the controller is
   set to `false`.
2. `executeRedirectTask()` runs `$controller->execute($input->getCmd('task'))` and then `redirect()`. If there is no
   controller it redirects to the `referer` input value.

Two helpers resolve license-aware classes first and fall back to Joomla's legacy lookup:

```php
$component = Component::getInstance();

// Alledia\MyExtension\<Free|Pro>\Joomla\Model\Items, else BaseDatabaseModel::getInstance('Items', 'MyExtensionModel')
$model = $component->getModel('Items');

// Alledia\MyExtension\<Free|Pro>\Joomla\Table\Item, else AbstractTable::getInstance('Item', 'MyExtensionTable')
$table = $component->getTable('Item');
```

> Note: `getModel()`/`getTable()` choose `Pro` or `Free` from the license. `loadController()` uses the license string
> itself, so a component must declare a license for its controller to be found.

Plugins
-------

Extend `Alledia\Framework\Joomla\Extension\AbstractPlugin` (a `CMSPlugin`). Set `$namespace`, then call `init()` the first
time the plugin needs its extension data. Loading is deliberately not done in the constructor, so plugins that never
fire pay nothing for it.

```php
use Alledia\Framework\Joomla\Extension\AbstractPlugin;

defined('_JEXEC') or die();

if ((include JPATH_SITE . '/libraries/allediaframework/include.php') == false) {
    return;
}

class PlgSystemMyplugin extends AbstractPlugin
{
    protected $namespace = 'MyPlugin';

    public function onAfterInitialise()
    {
        $this->init();

        if ($this->isPro()) {
            // Pro-only behavior, e.g. new \Alledia\MyPlugin\Pro\Something()
        }
    }
}
```

`init()` loads the `Licensed` extension object into `$this->extension`, calls `loadLibrary()` and loads both the
plugin's language file and its `.sys` language file.

Modules
-------

Extend `Alledia\Framework\Joomla\Extension\AbstractFlexibleModule`. Pass the namespace and Joomla's `$module` object. The
constructor calls `loadLibrary()` and copies `id`, `title`, `module`, `position`, `content`, `showtitle`, `menuid`,
`name`, `style` and `params` from `$module`. `params` is always converted to a `Registry`.

```php
// mod_mymodule.php
use Alledia\MyModule\Free\Module;

defined('_JEXEC') or die();

require_once JPATH_SITE . '/libraries/allediaframework/include.php';

(new Module('MyModule', $module))->init();
```

`init()` requires the layout chosen by the module's `layout` param (`ModuleHelper::getLayoutPath()`). The layout runs
inside the module object, so `$this` there is your module instance.

Controllers
-----------

Extend the abstract controller bases in `Alledia\Framework\Joomla\Controller`:

| Class          | Extends                  | Use for                     |
|----------------|--------------------------|-----------------------------|
| `AbstractBase` | `BaseController`         | Display / generic tasks     |
| `AbstractAdmin`| `AdminController`        | List views (publish, delete…)|
| `AbstractForm` | `FormController`         | Edit views                  |
| `AbstractJson` | `AbstractBase`           | AJAX endpoints              |

All of them use `TraitController`, which sets `$this->app` on J3 as well as J4+ and adds:

- `callerReturn($message = null, $type = null, $return = null)` redirects back to the caller. In order of preference it
  uses a base64 `return` input value, the current `Itemid`, the current `option`/`view`/`layout`, then the site home.
  An array message is joined with `<br>`.
- `errorReturn(string $message, string $type = 'error')` queues a message and redirects to the referrer if it is
  internal, otherwise to `index.php`.
- `getStringInflector()` returns Joomla's `Inflector`.

`AbstractForm::batch()` works out the model from a plural `view` name (e.g. `items` → `item` model) and redirects back
to the list.

`AbstractJson` adds:

- `checkToken()` throws a 403 exception on an invalid token instead of returning false.
- `returnJson($message = null)` sends a `JsonResponse` and ends the request (`jexit()`). If you pass a `Throwable`,
  `success` is false and the response includes the message, file and line.

```php
use Alledia\Framework\Joomla\Controller\AbstractJson;

class MyExtensionControllerAjax extends AbstractJson
{
    public function lookup()
    {
        try {
            $this->checkToken();
            // ...
            $this->returnJson('ok');

        } catch (\Throwable $error) {
            $this->returnJson($error);
        }
    }
}
```

Models
------

| Class                        | Extends             |
|------------------------------|---------------------|
| `AbstractBaseDatabaseModel`  | `BaseDatabaseModel` |
| `AbstractListModel`          | `ListModel`         |
| `AbstractAdminModel`         | `AdminModel`        |

All of them use `TraitModel`:

- `setup()` sets `$this->app`. It is **not** called automatically. Call it from your constructor if you need `$this->app`,
  including indirectly through `garbageCollect()`.
- `whereTextSearch(string $text, $fields, ?string $idField = null): string` builds an OR'ed `LIKE` clause across one or
  more fields. If `$idField` is given, a search such as `id: 42` becomes `$idField = 42`.
- `whereAccess(string $field, ?User $user = null): string` returns `field IN (<view levels>)`. Users with `core.manage`
  get `TRUE`.
- `garbageCollect(string $source, $relations, string $prefix = '#__')` deletes orphaned rows. For example,
  `garbageCollect('items.id', ['item_tags.item_id'])` deletes `item_tags` rows whose `item_id` no longer exists.
- `getDatabase()` returns the database driver on any Joomla version.

`AbstractAdminModel::getItem()` always returns a plain `stdClass`, as it did on Joomla 3. It never returns a `CMSObject`.

Views
-----

All views extend `Alledia\Framework\Joomla\AbstractView` (an `HtmlView` using `TraitAllediaView`). The constructor:

- sets `$this->app`, `$this->document` and `$this->option`
- resolves the component from `option`, loads its `Licensed` object into `$this->extension` and calls `loadLibrary()`

If setup fails, the error is queued and `display()` renders nothing. `display()` wraps the parent's output with
`displayHeader()` and `displayFooter()`, which are empty hooks for subclasses. `setModel($model, true)` also fills
`$this->model` and `$this->state`. `loadDefaultTemplate($name, $layout = 'default')` renders a sub-template from another
layout.

### Admin views (`View\Admin`)

- `AbstractBase`:
    - `displayFooter()` prints the extension's footer markup. If there is no `customfooter` field it falls back to
      `views/footer/tmpl/default.php` or `alledia_views/footer/tmpl/default.php`.
    - `setLayout()` uses versioned layouts. On Joomla 3 it prefers `<layout>.j3.php` when that file exists.
- `AbstractForm`:
    - fills `$this->form` from an `AdminModel`
    - loads `behavior.tabstate` on Joomla 3
- `AbstractList`:
    - provides `$items`, `$pagination`, `$filterForm`, `$activeFilters` and `$sidebar`
    - loads `<option>/admin-default.css` from the component's media folder
    - on Joomla 4+, switches to the `emptystate` layout when there are no items and the model's `getIsEmptyState()`
      returns true. If there is no `emptystate` template it uses `default`.

### Site views (`View\Site`)

- `AbstractBase::setModel()` builds `$this->params`. It starts from the component params and merges in the active menu
  item's params. It also sets `page_heading` from `page_title` or the menu title.
- `AbstractList` is a list variant of `AbstractBase`.

Tables
------

Extend `Alledia\Framework\Joomla\AbstractTable` (a `Table`):

- `getDatabase()` works on every Joomla version.
- `AbstractTable::getComponentInstance($component, $name, $prefix)` loads another component's table. It uses
  `Table::getInstance()` on J3 and the component's MVC factory (`Administrator` side) on J4+. It returns `null` if the
  table can't be loaded.

For loading core Joomla models and tables on any version, see `Alledia\Framework\Helper::getJoomlaModel()`,
`getContentModel()`, `getCategoryModel()` and `getJoomlaTable()` under [Utility classes](#utility-classes).

Form fields
-----------

`Alledia\Framework\Joomla\Form\Field\*Field` are subclassable versions of Joomla's core fields. They work the same way on
J3 and J4+:

`ArticleField`, `CalendarField`, `CheckboxField`, `CheckboxesField`, `ColorField`, `FilelistField`,
`GroupedlistField`, `HiddenField`, `ImagelistField`, `ListField`, `NumberField`, `PredefinedListField`, `RadioField`,
`SpacerField`, `TextField`, `TextareaField`, `UrlField`.

Each one extends `Joomla\CMS\Form\Field\<X>Field`. On J3 that name is aliased to the legacy `JFormField<X>`. Each also
uses `TraitLayouts`, which adds **layout overrides next to your field class**:

```
myextension/form/fields/
├── mylist.php          class MyListField extends \Alledia\Framework\Joomla\Form\Field\ListField
└── layouts/
    ├── joomla/form/field/list.php       used on J4/J5/J6
    └── joomla/form/field/list_j3.php    used on J3 when present
```

`TraitLayouts` puts the `layouts/` folder beside the *subclass's* file at the front of the layout search path. On
Joomla 3 it chooses `<layoutId>_j3.php` when that file exists. You can then ship one field class with separate J3 and
J4+ markup.

Events
------

Trigger events on any Joomla version:

```php
use Alledia\Framework\Factory;

$results = Factory::triggerEvent('onMyExtensionBeforeSave', [$item, $isNew]);
```

On J3 this calls `JEventDispatcher::trigger()`. On J4+ it dispatches an `Event` and returns its `result` array. If the
dispatcher isn't available yet, it returns `[]`.

To register a class's own methods as listeners, use `Alledia\Framework\Joomla\Events\TraitObservable`:

```php
use Alledia\Framework\Joomla\Events\TraitObservable;

class Observer
{
    use TraitObservable;

    public function __construct()
    {
        $this->registerEvents('onMyExtension*');   // every public method starting with "onMyExtension"
        // $this->registerEvents('*Content');      // every public method containing "Content"
        // $this->registerEvents(['onFoo', 'onBar']);
    }

    public function onMyExtensionBeforeSave($item, $isNew)
    {
        return true;
    }
}
```

On J4+, listeners are wrapped by default (`$legacyListeners = true`) so they receive positional arguments and their
return value is added to the event's `result`, matching J3 behavior. Pass `false` to register raw `Event` listeners.

`Alledia\Framework\Joomla\Event\SubscriberInterface` is Joomla's `SubscriberInterface` when it exists. Otherwise (J3) it
is an equivalent interface, so classes can implement it on all versions.

Toolbar
-------

`Alledia\Framework\Joomla\Toolbar\ToolbarHelper` extends Joomla's `ToolbarHelper` and adds:

- `getToolbar(?Document $document = null)` returns the toolbar. It uses `Toolbar::getInstance()` before J5 and
  `$document->getToolbar()` from J5 on.
- `externalLink($url, $title, $icon = null, $attributes = [])` adds a button that opens a link in a new window.
- `shackDocumentation($url, $title)` adds a styled "documentation" link button.
- `exportView($task, $alt, $icon = 'download')` adds a task button that clears the `task` field after submit. Use it for
  downloads that don't reload the page.

HTML helpers
------------

The bootstrap adds `src/helpers/html` to `HTMLHelper`'s include paths.

| Call                                                          | Description                                              |
|---------------------------------------------------------------|----------------------------------------------------------|
| `HTMLHelper::_('alledia.fontawesome')`                        | Loads the bundled Font Awesome CSS                       |
| `HTMLHelper::_('alledia.jqueryValidate')`                     | Loads jQuery and the bundled [jQuery Validation](https://jqueryvalidation.org/) plugin (minified unless site debug is on) |
| `HTMLHelper::_('alledia.tooltip', $title, $text, $body)`      | Bootstrap tooltip span                                   |
| `HTMLHelper::_('alledia.truncate', $string, $limit, '...')`   | Multibyte-safe truncation on a word boundary             |
| `HTMLHelper::_('alledia.modal.renderSelectField', $options)`  | Modal "select item" field that works on any Joomla version |
| `HTMLHelper::_('alledia.modal.renderLink', $text, $id, $params)` | Link that opens a modal                               |
| `HTMLHelper::_('alledia.modal.footerSaveButton', ...)` / `footerApplyButton` / `footerCloseButton` / `footerButton` | Modal footer buttons |
| `HTMLHelper::_('alledia.tab.startTabSet', $selector, $params)` / `addTab` / `endTab` / `endTabset` | Tabs that work on any Joomla version |

`renderSelectField()` requires `id`, `name`, `link`, `function` and `itemType`. Optional keys are `title`, `hint`,
`button` (`['select' => ..., 'clear' => ...]`), `close`, `value`, `required`, `height`, `width`, `bodyHeight` and
`modalWidth`. If a required key is missing it returns an error string instead of markup.

Utility classes
---------------

| Class                                   | Purpose                                                                                     |
|-----------------------------------------|---------------------------------------------------------------------------------------------|
| `Alledia\Framework\Factory`             | Extends Joomla's `Factory`. Adds `getExtension()`, `getDatabase()`, `getDispatcher()`, `triggerEvent()` and `getInput()`, each working on every Joomla version. |
| `Alledia\Framework\Helper`              | `getAllediaExtensions($license)`, `getJoomlaVersionCssClass()` (`joomla3x`, `joomla4x`, …), `createClassAliases()`, `createDatabaseClassAliases()`, `callMethod()`, `createForm()`, `getJoomlaModel()` / `getContentModel()` / `getCategoryModel()`, `getJoomlaTable()`, `errorToException()` |
| `Alledia\Framework\AutoLoader`          | PSR-4 `register($prefix, $baseDir, $prepend)` and camelCase `registerCamelBase($prefix, $baseDir)` loaders |
| `Alledia\Framework\Loader`              | `includeFile($path)`: include a file, logging a missing file to `allediaframework.loader.errors.php` and warning admins |
| `Alledia\Framework\Base`                | Property helpers: `getProperties()`, `setProperties()`, `clearProperties()`, `map()`        |
| `Alledia\Framework\Color`               | `adjust()`, `isTooBright()`, `hexToRgb()`, `nameToHex()`                                    |
| `Alledia\Framework\Content\Text` / `Tag`| Parse `{tag param="x"}content{/tag}` plugin tags from text (`getPluginTags($tagName)`)     |
| `Alledia\Framework\Joomla\String\Inflector` | `pluralize()`, `singularize()`, `isPlural()`, `isSingular()`, with custom rules           |
| `Alledia\Framework\Joomla\User\UserHelper` | `getProfile()` always returns a plain object                                             |
| `Alledia\Framework\Network\Request`     | Simple `post($url, $data)` using cURL, with an `fopen` fallback                             |
| `Alledia\Framework\Profiler`            | Simple memory-usage profiler for debugging (`start()`, `step()`, `echoData()`)              |
| `Alledia\Framework\Exception`           | Exception with `getTraceMessage()`, `getCallStack()`, `setLocation()`. `Exception\NotFound` is a subclass. |

Deprecated APIs
---------------

These still work, but don't use them in new code:

| Deprecated                                                                  | Since  | Use instead                          |
|-----------------------------------------------------------------------------|--------|--------------------------------------|
| `Joomla\Controller\Base`, `Admin`, `Form`, `Json`                           | 3.8.1  | `AbstractBase`, `AbstractAdmin`, `AbstractForm`, `AbstractJson` |
| `HTMLHelper::_('alledia.renderModal', ...)`                                 | 3.3.5  | `alledia.modal.renderSelectField`    |
| `Content\Text::getTags()`                                                   | 1.3.1  | `getPluginTags()`                    |
| `Joomla\Extension\Component` (uses `jimport` and the legacy controller)    | —      | `AbstractComponent`                  |

---

Part 2: Contributing to the framework
=====================================

Repository layout
-----------------

```
.
├── build.xml                 Phing entry point (imports ShackBuilder)
├── build.dist.properties     Copy to build.properties and set builder.path
├── composer.json             Installs into src/vendor
└── src/                      Everything that ships
    ├── allediaframework.xml  Library manifest
    ├── include.php           Bootstrap
    ├── Framework/            Alledia\Framework\* (PSR-4)
    │   ├── Joomla/           Joomla-specific bases: Extension, Controller, Model, View, Form, Toolbar, ...
    │   └── ...               Version-independent utilities
    ├── helpers/html/         HTMLHelper classes (alledia.*)
    ├── media/                Installed to media/lib_allediaframework/
    └── vendor/               Composer dependencies (committed)
```

`_scratch/` is local scratch space. It isn't in the manifest, so it never ships. It is not gitignored.

Bootstrap internals
-------------------

`src/include.php` runs only once (guarded by `ALLEDIA_FRAMEWORK_LOADED`). In order, it:

1. Defines `ALLEDIA_FRAMEWORK_LOADED` and `ALLEDIA_FRAMEWORK_PATH`.
2. Requires `Framework/AutoLoader.php`, unless another copy is already loaded.
3. Includes `vendor/autoload.php`.
4. Registers `Alledia\Framework` → `Framework/` with the framework's own `AutoLoader`.
5. Adds `helpers/html` to `HTMLHelper`.
6. Aliases `Alledia\Framework\Extension` → `Alledia\Framework\Joomla\Extension\Licensed`.
7. On Joomla 3 only, registers `ContentHelperRoute` and creates J4-named aliases for legacy classes.

The custom `AutoLoader` lets one namespace prefix map to several base directories. It tries every possible prefix
split, longest first. The same loader serves consumer libraries through `Licensed::loadLibrary()`.

Cross-version compatibility strategy
------------------------------------

One codebase runs on Joomla 3.9 through 6. Follow these rules:

1. **Detect capabilities, not versions.** Prefer `class_exists()`, `interface_exists()`, `is_callable()` and
   `method_exists()` over `Version::MAJOR_VERSION` (see commit "Always check for capability not Joomla version"):

   ```php
   // Good
   if (is_callable([$app, 'getInput'])) {
       return $app->getInput();
   }
   return $app->input;

   // Avoid
   if (Version::MAJOR_VERSION >= 4) { ... }
   ```

   Some older code still branches on `Version::MAJOR_VERSION`. If you must use a version check, write it so that newer
   versions take the modern path (`< 4` for legacy, `else` for current). Never write `== 4`, because the next major
   version would then skip that branch.

2. **Write code against the namespaced J4+ class names.** Alias them on J3 only when the namespaced class is missing.
   Use the shared helpers:

   ```php
   Helper::createClassAliases([
       \JLegacyClass::class => \Joomla\CMS\Modern\ClassName::class,
   ]);
   Helper::createDatabaseClassAliases(); // JDatabaseDriver/JDatabaseQuery → Joomla\Database\*
   ```

   `createClassAliases()` only creates an alias when the target name doesn't already exist, so calling it on J4+ does
   nothing.

3. **Keep legacy references inside guards.** A bare `\JSomething` reference outside a `class_exists()` or
   `interface_exists()` check will break on versions that have removed the class.

4. **Normalize return types.** When Joomla changed a return type between versions (`CMSObject` vs `stdClass`, for
   example), return the J3-compatible shape. See `AbstractAdminModel::getItem()` and `UserHelper::getProfile()`.

5. **Versioned layouts** follow these conventions: views use `<layout>.j3.php` (`TraitAllediaView`) and form fields use
   `<layout>_j3.php` (`TraitLayouts`).

Adding a form field shim
------------------------

Copy an existing shim such as `src/Framework/Joomla/Form/Field/FilelistField.php`. Then change the class names and the
`loadFieldClass()` argument:

```php
<?php
/** standard GPL header block */

namespace Alledia\Framework\Joomla\Form\Field;

use Joomla\CMS\Form\Field\EmailField as JoomlaEmailField;
use Joomla\CMS\Form\FormHelper;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

if (class_exists(JoomlaEmailField::class) == false) {
    FormHelper::loadFieldClass('Email');

    class_alias(\JFormFieldEMail::class, JoomlaEmailField::class);
}
// phpcs:enable PSR1.Files.SideEffects

class EmailField extends JoomlaEmailField
{
    use TraitLayouts;
}
```

Check the legacy J3 class name against Joomla 3 core. Some names have unusual casing, such as `JFormFieldEMail` and
`JFormfieldPredefinedlist`.

Backward compatibility rules
----------------------------

Many released extensions depend on this library, and sites may run old and new Joomlashack extensions side by side
against one installed copy. Therefore:

- **Never remove or rename public or protected members**, and never change their signatures in a way that breaks
  existing callers.
- Mark replaced APIs `@deprecated vX.Y.Z` and keep them working, usually as a thin wrapper around the replacement.
- New parameters must be optional and go at the end.
- Be careful with `final` and with tightening return types on methods consumers may override.

Coding conventions
------------------

- Every PHP file starts with the standard GPL header block (`@package AllediaFramework`, `@contact`,
  `@copyright <first year>-<current year> Joomlashack.com`, `@license`).
- Every PHP file includes `defined('_JEXEC') or die();`.
- Side effects that must sit before a class declaration (the `_JEXEC` guard, `class_alias` shims) are wrapped in
  `// phpcs:disable PSR1.Files.SideEffects` … `// phpcs:enable PSR1.Files.SideEffects`.
- The manifest's `<files>` and `<media>` blocks are **whitelists**. A new folder under `src/` or `src/media/` is
  neither packaged nor installed until it is listed in `src/allediaframework.xml`.
- Media is installed to `media/lib_allediaframework/`. Reference it with Joomla relative paths, e.g.
  `HTMLHelper::_('stylesheet', 'lib_allediaframework/fontawesome/css/all.min.css', ['relative' => true])`, which
  resolves to `media/lib_allediaframework/css/fontawesome/css/all.min.css`.

Dependencies
------------

Composer installs into `src/vendor` (`config.vendor-dir`). That folder **is committed**, because it ships in the
package. Run `composer install` / `composer update` from the repository root and commit the `src/vendor` changes.

- The only runtime dependency is `pimple/pimple`.
- `psr/container` is `replace`d because Joomla already provides it. Don't add packages that Joomla core also bundles,
  because conflicting versions would be loaded side by side.

Building a package
------------------

Packaging uses [Phing](https://www.phing.info/) through the external **ShackBuilder** project. `build.xml` only imports
`${builder.path}/src/build.xml`.

```sh
cp build.dist.properties build.properties   # gitignored
# edit build.properties and set builder.path=/path/to/ShackBuilder
phing
```

Installable zips are written to `packages/` (gitignored). `build.properties` can also set `project.movePackage` /
`project.movePackageTo` to move the built package, and `builder.phplint=1` to lint all PHP after assembly.

Release process
---------------

- Day-to-day work happens on `development`. `main` is the release branch.
- Development builds use a `bN` suffix in `<version>` in `src/allediaframework.xml`, e.g. `3.8.12b1`. Also update
  `<creationDate>`.
- The release commit sets the final version and date, with the message `Release vX.Y.Z`.

Testing
-------

### Unit tests

Unit tests use [Codeception](https://codeception.com/) and live in `tests/`. They cover the classes that don't need a
running Joomla site: `AutoLoader`, `Base`, `Color`, `Content\Text`/`Tag`, `Exception`, `Inflector`, the element parsing
in `Joomla\Extension\Helper`, parts of `Helper`, and `JhtmlAlledia::truncate()`.

The test tooling has its own `tests/composer.json`, installed into a gitignored `tests/vendor`. This keeps dev
dependencies out of the shipped `src/vendor`. Besides Codeception, it installs the standalone `joomla/string`,
`joomla/registry` and `doctrine/inflector` packages, which some framework classes use.

From the repository root:

```sh
composer test        # installs tests/vendor if needed, then runs the suite
```

Or from `tests/`:

```sh
cd tests
composer install
composer test        # same as: vendor/bin/codecept run -c ..
```

`codeception.yml` is at the repository root. `tests/bootstrap.php` defines `_JEXEC` and registers the framework's own
`AutoLoader` for `Alledia\Framework`. Fixtures live in `tests/Support/Data`.

- The tests need PHP 8.2+ (Codeception 5.3), so they don't run on PHP 7.4.
- For data providers, use Codeception's `#[Codeception\Attribute\DataProvider('method')]`. Codeception ignores
  PHPUnit's `#[DataProvider]` attribute.
- Classes that need Joomla CMS classes (`Factory`, extension objects, MVC bases, form fields, HTML helpers other than
  `truncate()`) aren't covered by unit tests.

### Updating test dependencies

- **Update from `tests/`**, never from the root:

  ```sh
  composer --working-dir=tests update    # or: cd tests && composer update
  ```

  Running `composer update` in the repository root updates the framework's runtime dependencies instead. That
  rewrites the committed `src/vendor`, which ships in the package.
- **Keep the PHP platform pin.** `tests/composer.json` sets `config.platform.php` to `8.2.0`. Composer then resolves
  packages for PHP 8.2 whatever PHP version runs it, so the lock works on 8.2 and every later version. Without the
  pin, a lock built on a newer PHP can pull in packages that fail to parse on older versions. Raise the pin only when
  you deliberately drop an older PHP version for running tests.
- **The pin also chooses the Joomla package versions.** `joomla/string` and `joomla/registry` 4.x require PHP 8.3+,
  so with the 8.2 pin Composer installs 3.x, the versions Joomla 5 ships. To test against 4.x, raise the pin to
  `8.3.0`.
- **Keep `doctrine/inflector` as a direct dependency.** `Inflector` uses Doctrine when it is installed and falls back
  to Joomla's inflector otherwise. Removing it would change which backend `InflectorTest` exercises.
- **Commit `tests/composer.lock`.** `tests/vendor` is gitignored, so the lock file is what gives everyone the same
  versions.
- **Run `composer test` after every update.** After a Codeception or PHPUnit major upgrade, also confirm that the
  data-provider tests still receive their data. A test failing with `ArgumentCountError: Too few arguments` means
  the providers aren't being applied.

### Manual testing

Everything else must be tested manually. Install the built package on real Joomla sites with at least one dependent
Joomlashack extension. Cover every supported major version your change could affect:

- Joomla 3.9/3.10
- Joomla 4.x
- Joomla 5.x
- Joomla 6.x

Joomla 6 support follows from the capability-detection approach above but hasn't been confirmed on a live install. Pay
particular attention to toolbar buttons, list empty states and fancy-select list fields. These still use version checks
that assume J4/J5 behavior.

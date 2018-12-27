TransliterableBundle
====================

A Symfony bundle to facilitate the transliteration of Doctrine entity string fields.


___


Overview
--------

TransliterableBundle provides an [embeddable](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/tutorials/embeddables.html) entity, `Transliterable`, to enable mapping of a single Doctrine entity field to corresponding `_original` and `_transliteration` fields.

For example, a Doctrine entity class, `Person`, with a `$firstname` property mapped to the `Transliterable` embedded entity will result in a table with `firstname_original` and `firstname_transliteration` columns.


___


Installation
------------

1. Require the latest stable version of TransliterableBundle by running the following console command from the root of your Symfony project:
   > `$ composer require tomchkk/transliterable-bundle`
2. Enable the bundle by adding a reference to it in the array returned by `config/bundles.php`:
    ```php
    // config/bundles.php

    return [
        // ...
        Tomchkk\TransliterableBundle\TomchkkTransliterableBundle::class => ['all' => true],
    ];
    ```

___


Usage
-----


### Embeddable Entity

An entity field can be made _transliterable_ by including a Doctrine `Embedded` annotation, with a _class_ value of the fully-qualified `Transliterable` class name.

```php
// src/Entity/Person.php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Person
{
    /**
     * @ORM\Embedded(class="Tomchkk\TransliterableBundle\Embeddable\Transliterable")
     */
    private $firstname;
}
```

For the same reasons indicated in the documentation for Doctrine Embeddables, `Transliterable` [fields should be initialized](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/tutorials/embeddables.html#initializing-embeddables) as such, to guarantee returning an embedded `Transliterable` instance - e.g.:

```php
// src/Entity/Person.php

use Doctrine\ORM\Mapping as ORM;
use Tomchkk\TransliterableBundle\Embeddable\Transliterable;

/**
 * @ORM\Entity()
 */
class Person
{
    public function __construct()
    {
        $this->firstname = new Transliterable();
    }
```


### Transliteration

An embbedded `Transliterable` field with an _original_ value but without a _transliteration_ value will be transliterated when the entity is first persisted, or when updated.

**Transliterator**

By default TransliterableBundle uses [PHP's built-in Transliterator class](http://php.net/manual/en/class.transliterator.php) - decorated with a simple caching mechanism - as the transliteration engine. The default transliterator can be overridden in configuration by a custom service implementing `Tomchkk\TransliterableBundle\Service\TransliteratorInterface` - e.g.:

```yaml
// config/packages/tomchkk_transliterable.yaml

tomchkk_transliterable:
    transliterator:       App\Service\CustomTransliterator
```


### Rulesets

In order to perform a transliteration, the transliterator requires a _ruleset_ identifier, which is used to create a particular transliterator instance. [More on ruleset identifiers](http://userguide.icu-project.org/transforms/general#TOC-Transliterator-Identifiers).

**Global ruleset**

The default transliterator provides a ruleset which will be applied to all transliterations if no other ruleset is provided; this default can be overridden by setting the `global_ruleset` value in configuration - e.g.:

```yaml
// config/packages/tomchkk_transliterable.yaml

tomchkk_transliterable:
    global_ruleset:       Any-Latin
```

**Annotation rulesets**

A `Transliterable` annotation is also available to enable a _ruleset_ to be set at class- or property-level, the most specific of which will be applied to the transliteration of a field's value, overriding the global ruleset.

```php
// src/Entity/Person.php

use Doctrine\ORM\Mapping as ORM;
use Tomchkk\TransliterableBundle\Annotation as Tomchkk;

/**
 * @ORM\Entity()
 * @Tomchkk\Transliterable(ruleset="Any-Latin")
 */
class Person
{
    /**
     * @ORM\Embedded(class="Tomchkk\TransliterableBundle\Embeddable\Transliterable")
     */
    private $firstname;

    /**
     * @ORM\Embedded(class="Tomchkk\TransliterableBundle\Embeddable\Transliterable")
     * @Tomchkk\Transliterable(ruleset="Russian-Latin/BGN; Any-Latin")
     */
    private $lastname;
```

In the above example, `$firstname` would be transliterated according by the class ruleset; `$lastname` by the property ruleset.


### Frontend

**Form Type**

A `TransliterableType` form type is included, providing an `original` and `transliteration` field - each extending the built-in [TextType](https://symfony.com/doc/current/reference/forms/types/text.html). By default, the `required` option of the `transliteration` field is set to `false` since this field, if empty, will be populated when the entity is persisted.

The following configuration options are available for `TransliterableType`:

|          Option           |  Type   | Default |                                    Use                                     |
|---------------------------|---------|---------|----------------------------------------------------------------------------|
| `exclude_transliteration` | boolean |  false  |      Whether or not to exclude the transliteration field in the form       |
|         `options`         |  array  | [empty] | Standard `TextType` options applicable to both `TransliterableType` fields |
|    `original_options`     |  array  | [empty] |    Standard `TextType` options applicable to just the `original` field     |
| `transliteration_options` |  array  | [empty] | Standard `TextType` options applicable to just the `transliteration` field |


A `Transliterable` entity field without a transliteration value will be transliterated when the entity is first persisted, or when updated.

**Twig Extension**

The `transliterate` twig filter, with optional _ruleset_ argument - enables direct transliteration of strings - e.g.:

```twig
Firstname: {{ 'Илья'|transliterate }}
<!-- Firstname: Ilʹâ -->

Firstname: {{ 'Илья'|transliterate('Russian-Latin/BGN') }}
<!-- Firstname: Ilʹya -->
```

# Using phconf

## Creating a new instance

```php
use Ulrichsg\PhConf\PhConf;

$phconf = new PhConf();
```

## Importing config values from the filesystem

```php
$phconf->file('config.yml');
```

phconf supports three types of config files: JSON, YAML and INI. If the filename given to `file()` has a known extension
(`.json`, `.yml`, `.yaml` or `.ini`), it will be treated as the corresponding format. Otherwise phconf will try to detect
the format, falling back to INI as a last resort. If the format cannot be identified or if parsing fails, an exception of
the type `Ulrichsg\PhConf\Error\ParseError` is thrown. Failure to read from the given path results in a
`Ulrichsg\PhConf\Error\FileNotFound`.

When importing values from multiple files or other sources, order of invocation matters - if the same key is defined in
more than one source, the last imported source will override the earlier one(s).

### Importing from INI files with sections

When importing data from INI files, phconf will by default behave like PHP's
[`parse_ini_file()`](https://secure.php.net/manual/en/function.parse-ini-file.php) - that is, it will ignore any `[section]`s
present in the file and interpret the data as a flat key-value map. Just like with `parse_ini_file()`, this behavior can be
changed by passing a second argument to `file()`:

```php
$phconf->file('config.ini', PhConf::INI_WITH_SECTIONS);
```

The data will then be interpreted as a two-level hierarchy instead. For instance, for an INI file with the contents:

```
[foo]
bar = baz
```

the value `baz` will be available under the key `foo:bar`, not under just `bar` as it would without the `INI_WITH_SECTIONS` argument.

## Importing config values from the environment

```php
$phconf->env();
```

The values available through `env()` are the same that can be found in PHP's `$_ENV` superglobal.

## Setting default values

```php
$phconf->defaults([
    'foo' => 'bar'
]);
```

The `defaults()` method seeds phconf with an initial structure of config values. 

Defaults never override values from other sources, no matter whether `defaults()` is called before or after the other
import methods.

## Retrieving config values

```php
$value = $phconf->get('foo:bar');
```

The `:` character is used to traverse the config structure hierarchically (see the example for INI files above).
If no value has been set for the given key, `get()` will return `null`.

## Setting individual config values

In case you need to add or change specific config values directly, you can do it: 

```php
$phconf->set('foo:bar', 'baz');
```

Note that if you assign a new value to a key that has a hierarchy below it, the entire hierarchy will be lost:

```php
$phconf->set('foo:bar', 'baz');
$phconf->set('foo', 'quux');
```

After the second `set()`, the key `foo:bar` and the associated value cannot be accessed any longer.

## Using the fluent interface

When importing config data from multiple sources, this syntax may be preferred:

```php
$phconf
    ->defaults(['foo' => 'bar'])
    ->env()
    ->file('config.yml');
```


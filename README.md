# Realodix\Timezone

![PHPVersion](https://img.shields.io/badge/PHP-8.1-777BB4.svg?style=flat-square)
[![GitHub license](https://img.shields.io/github/license/realodix/timezone?style=flat-square)](/LICENSE)

A PHP library that provides an easy way to generate HTML select boxes for timezones. It offers features to filter timezones by continent, group them, display offsets, and customize the output.

## Features
- **Comprehensive Timezone List:** Generates a complete list of all available timezones.
- **Array Representation:** Retrieves timezones as an array, suitable for use in any PHP application.
- **Continent-Based Grouping:** Optionally groups timezones by continent for better organization.
- **Flexible Filtering:** Includes or excludes specific continents/groups based on your needs.
- **UTC Offset Display:** Displays timezone offsets (e.g., UTC+05:30) for easy reference.
- **Customizable Output:** Offers control over the format of the timezone names.


## Installation

You can install this package via Composer:

```bash
composer require realodix/timezone
```

### Project status & release process
While this library is still under development, it is well tested and should be stable enough to use in production environments.

The current releases are numbered `0.x.y`. When a non-breaking change is introduced (adding new methods, optimizing existing code, etc.), `y` is incremented.

**When a breaking change is introduced, a new `0.x` version cycle is always started.** It is therefore safe to lock your project to a given release cycle, such as `0.1.*`. If you need to upgrade to a newer release cycle, check the [release history](https://github.com/realodix/timezone/releases) for a list of changes introduced by each further `0.x.0` version.

## Usage

### Create HTML select box

```php
/**
 * @param string $name The name attribute of the select tag
 * @param string|null $selected The value of the option to be pre-selected
 * @param array|null $attrs Additional HTML attributes
 */
public function toSelectBox(string $name, ?string $selected = null, ?array $attrs = null): string
```

```php
use Realodix\Timezone\Timezone;

$tz = new Timezone;
$attributes = ['class' => 'form-control', 'id' => 'timezone-select'];

// Generate a select box with the name "timezone", pre-selecting "America/New_York",
// and using the specified attributes.
$tz->toSelectBox('timezone', 'America/New_York', $attributes);
```

Output:
```html
<select name="timezone" class="form-control" id="timezone-select">
    <optgroup label="General">
        <option value="UTC">(UTC+00:00) UTC</option>
    </optgroup>
    ...
    <optgroup label="America">
        ...
        <option value="America/Nassau">(UTC-05:00) Nassau</option>
        <option value="America/New_York" selected>(UTC-05:00) New York</option>
        <option value="America/Nome">(UTC-09:00) Nome</option>
        ...
    </optgroup>
    ...
    <optgroup label="Asia">
        <option value="Asia/Aden">(UTC+03:00) Aden</option>
        <option value="Asia/Almaty">(UTC+05:00) Almaty</option>
        ...
    </optgroup>
    ...
</select>
```


### Create timezone list array

```php
use Realodix\Timezone\Timezone;

$tz = new Timezone;
$tz->toArray();
```

Output:
```php
[
    'UTC' => '(UTC+00:00) UTC',
    'America' => [
        'Nassau' => '(UTC-05:00) Nassau',
        'New_York' => '(UTC-05:00) New York',
        'Nome' => '(UTC-09:00) Nome'
        // ...
    ],
    // ...
]
```

### Filtering and Grouping Timezones
`Realodix\Timezone` provides a powerful set of methods to customize the timezone list:

- `onlyGroups(array $groups)`: Includes only the specified continent/group names. For example, `$tz->onlyGroups(['America', 'Europe'])` will return only the timezones within the America and Europe continents.
- `excludeGroups(array $groups)`: Excludes the specified continent/group names. For example, `$tz->excludeGroups(['Arctic'])` will omit all timezones from Arctic.
- `flatten()`: Flattens the timezone list, removing the continental grouping.
- `omitOffset()`: Removes the UTC offset from the displayed timezone names.

**Timezone Groups**

The `onlyGroups()` and `excludeGroups()` methods utilize timezone groups to filter the list. Here are the available groups:

- **General:** For the `UTC` timezone.
- Africa
- America
- Antarctica
- Arctic
- Asia
- Atlantic
- Australia
- Europe
- Indian
- Pacific

**Explanation:** Timezones are organized into groups, mainly based on continents. The `General` group contains only the `UTC` timezone. You can use these groups with the filtering methods.

**Examples:**

```php
use Realodix\Timezone\Timezone;

$tz = new Timezone;

// Only include timezones from the America and Europe groups.
$tz->onlyGroups(['America', 'Europe'])->toArray();

// Output:
// [
//     'America' => [
//         'Nassau' => '(UTC-05:00) Nassau',
//         'New_York' => '(UTC-05:00) New York',
//         // ...
//     ],
//     'Europe' => [
//         'London' => '(UTC+00:00) London',
//         // ...
//     ],
// ]

// Exclude timezones from the Arctic and Africa groups.
$tz->excludeGroups(['Arctic', 'Africa'])->toArray();

// Output (will not include any timezones from the Arctic or Africa groups):
// [
//     'General' => [
//         'UTC' => '(UTC+00:00) UTC'
//     ]
//     'America' => [
//         'Nassau' => '(UTC-05:00) Nassau',
//         'New_York' => '(UTC-05:00) New York',
//         // ...
//     ],
//     // ...
// ]
```

## License
This package is licensed using the [MIT License](/LICENSE).

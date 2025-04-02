<?php

namespace Realodix\Timezone;

final class CompactTimezone
{
    /**
     * @var list<string>
     */
    private const TIMEZONE_OFFSET = [
        '-12:00',
        '-11:00',
        '-10:00',
        '-09:30',
        '-09:00',
        '-08:00',
        '-07:00',
        '-06:00',
        '-05:00',
        '-04:00',
        '-03:30',
        '-03:00',
        '-02:00',
        '-01:00',
        '+00:00',
        '+01:00',
        '+02:00',
        '+03:00',
        '+03:30',
        '+04:00',
        '+04:30',
        '+05:00',
        '+05:30',
        '+05:45',
        '+06:00',
        '+06:30',
        '+07:00',
        '+08:00',
        '+08:45',
        '+09:00',
        '+09:30',
        '+10:00',
        '+10:30',
        '+11:00',
        '+11:30',
        '+12:00',
        '+12:45',
        '+13:00',
    ];

    /**
     * Creates an HTML select box of timezones.
     *
     * @param string $name The name attribute of the select tag
     * @param string $selected The value of the option to be pre-selected
     * @param array|null $attrs Additional HTML attributes
     */
    public function toSelectBox(string $name, string $selected = '+00:00', ?array $attrs = null): string
    {
        $attributes = collect($attrs)
            ->map(fn($value, $key) => "{$key}=\"{$value}\"")
            ->implode(' ');

        $options = [];
        foreach (self::TIMEZONE_OFFSET as $tzOffset) {
            $attrs = ($selected === $tzOffset) ? ' selected' : '';
            $display = 'UTC'.$tzOffset;

            $options[] = "<option value=\"{$tzOffset}\"{$attrs}>{$display}</option>";
        }

        return "<select name=\"{$name}\" {$attributes}>".implode('', $options).'</select>';
    }

    public function toArray(): array
    {
        return self::TIMEZONE_OFFSET;
    }
}

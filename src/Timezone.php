<?php

namespace Realodix\Timezone;

final class Timezone
{
    const HTML_WHITESPACE = '&nbsp;';
    const GROUP_GENERAL = 'General';

    /**
     * Array of continents and their corresponding DateTimeZone constants.
     *
     * @var array<string, int>
     */
    const CONTINENTS = [
        'Africa'     => \DateTimeZone::AFRICA,
        'America'    => \DateTimeZone::AMERICA,
        'Antarctica' => \DateTimeZone::ANTARCTICA,
        'Arctic'     => \DateTimeZone::ARCTIC,
        'Asia'       => \DateTimeZone::ASIA,
        'Atlantic'   => \DateTimeZone::ATLANTIC,
        'Australia'  => \DateTimeZone::AUSTRALIA,
        'Europe'     => \DateTimeZone::EUROPE,
        'Indian'     => \DateTimeZone::INDIAN,
        'Pacific'    => \DateTimeZone::PACIFIC,
    ];

    /**
     * List of groups to include/exclude in the timezone list. An empty array
     * indicates all groups should be included.
     *
     * @var list<string>
     */
    private array $activeGroups = [];

    /**
     * Indicates whether timezones should be grouped by continent.
     */
    private bool $isGrouped = true;

    /**
     * Whether to display the timezone offset.
     */
    private bool $showOffset = true;

    /**
     * Creates an HTML select box of timezones.
     *
     * When `$this->isGrouped` is `true`, the select box will contain `<optgroup>` elements,
     * where:
     * - Each `<optgroup>` represents a continent.
     * - Each `<option>` inside an `<optgroup>` represents a timezone within that continent.
     *
     * When `$this->isGrouped` is `false`, the select box will contain a flat list of
     * `<option>` elements, where:
     * - Each `<option>` represents a timezone.
     *
     * @param string $name The name attribute of the select tag
     * @param string|null $selected The value of the option to be pre-selected
     * @param array|null $attrs Additional HTML attributes
     */
    public function toSelectBox(string $name, ?string $selected = null, ?array $attrs = null): string
    {
        if ($selected) {
            $this->validateTimezone($selected);
        }

        $attributes = collect($attrs)
            ->map(fn($value, $key) => "{$key}=\"{$value}\"")
            ->implode(' ');

        $options = [];
        if ($this->hasGeneralGroup()) {
            $options[] = $this->isGrouped ? '<optgroup label="General">' : '';
            $options[] = $this->makeOptionTag('UTC', $selected);
            $options[] = $this->isGrouped ? '</optgroup>' : '';
        }

        foreach ($this->loadContinents() as $continent => $mask) {
            $options[] = $this->isGrouped ? '<optgroup label="'.$continent.'">' : '';
            foreach (timezone_identifiers_list($mask) as $timezoneId) {
                $options[] = $this->makeOptionTag($timezoneId, $selected);
            }
            $options[] = $this->isGrouped ? '</optgroup>' : '';
        }

        return "<select name=\"{$name}\" {$attributes}>".implode('', $options).'</select>';
    }

    /**
     * Generates an array of timezones.
     *
     * When `$this->isGrouped` is `true`, the array will be a multidimensional array,
     * where:
     * - The first-level keys are continent names (e.g., 'Africa', 'America').
     * - The second-level keys are timezone identifiers (e.g., 'America/New_York').
     * - The second-level values are the formatted timezone names (e.g., '(UTC-05:00) New York').
     *
     * When `$this->isGrouped` is `false`, the array will be a flat array,
     * where:
     * - The keys are timezone identifiers (e.g., 'America/New_York').
     * - The values are the formatted timezone names (e.g., '(UTC-05:00) America / New York').
     */
    public function toArray(): array
    {
        $list = [];
        if ($this->hasGeneralGroup()) {
            if ($this->isGrouped) {
                $list[self::GROUP_GENERAL]['UTC'] = $this->formatTimezone('UTC');
            } else {
                $list['UTC'] = $this->formatTimezone('UTC');
            }
        }

        foreach ($this->loadContinents() as $continent => $mask) {
            foreach (timezone_identifiers_list($mask) as $timezoneId) {
                if ($this->isGrouped) {
                    $list[$continent][$timezoneId] = $this->formatTimezone($timezoneId);
                } else {
                    $list[$timezoneId] = $this->formatTimezone($timezoneId);
                }
            }
        }

        return $list;
    }

    /**
     * Sets the filter to include only the specified continent/group names.
     *
     * @param list<string> $groups The continent/group names to include.
     * @return $this
     */
    public function onlyGroups(array $groups)
    {
        $this->activeGroups = $this->processGroupNames($groups);

        return $this;
    }

    /**
     * Sets the filter to exclude the specified continent/group names.
     *
     * @param list<string> $groups The continent/group names to exclude
     * @return $this
     */
    public function excludeGroups(array $groups)
    {
        $groups = $this->processGroupNames($groups);

        $this->activeGroups = $this->getGroups()
            ->diff($groups)
            ->all();

        return $this;
    }

    /**
     * Flattens the timezone list, removing the continental grouping.
     *
     * @return $this
     */
    public function flatten()
    {
        $this->isGrouped = false;

        return $this;
    }

    /**
     * Removes the UTC offset from the displayed timezone names.
     *
     * @return $this
     */
    public function omitOffset()
    {
        $this->showOffset = false;

        return $this;
    }

    /**
     * Generate HTML <option> tag.
     *
     * @param string $timezoneId Timezone identifier (e.g. "America/New_York")
     * @param string|null $selected The value of the option to be pre-selected
     */
    private function makeOptionTag(string $timezoneId, ?string $selected): string
    {
        $attrs = ($selected === $timezoneId) ? ' selected' : '';
        $display = $this->formatTimezone($timezoneId, true);

        return "<option value=\"{$timezoneId}\"{$attrs}>{$display}</option>";
    }

    /**
     * Checks if the general group should be included.
     */
    private function hasGeneralGroup(): bool
    {
        return empty($this->activeGroups) || in_array(self::GROUP_GENERAL, $this->activeGroups);
    }

    /**
     * Loads the filtered list of continents based on the current group filter.
     *
     * If `$this->activeGroups` is empty, all continents are returned. Otherwise,
     * only the continents specified in `$this->activeGroups` are returned.
     *
     * @return array<string, int>
     */
    private function loadContinents(): array
    {
        return collect(self::CONTINENTS)
            ->when(!empty($this->activeGroups), fn($c) => $c->only($this->activeGroups))
            ->all();
    }

    /**
     * Formats a timezone name for display, optionally including the continent name
     * and offset.
     *
     * @param string $timezoneId Timezone identifier (e.g. "America/New_York")
     * @param bool $htmlEncode Whether to HTML-encode the output
     */
    private function formatTimezone(string $timezoneId, bool $htmlEncode = false): string
    {
        $rawTzName = $this->isGrouped ? Util::extractLocation($timezoneId) : $timezoneId;
        $fmtTzName = str_replace(['St_', '/', '_'], ['St. ', ' / ', ' '], $rawTzName);

        if (!$this->showOffset) {
            return $fmtTzName;
        }

        $offset = (new \DateTime('', new \DateTimeZone($timezoneId)))->format('P');
        $separator = $htmlEncode ? str_repeat(self::HTML_WHITESPACE, 3) : ' ';

        return "(UTC{$offset})".$separator.$fmtTzName;
    }

    /**
     * Get all defined group names (continents and the general).
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function getGroups()
    {
        return collect(self::CONTINENTS)
            ->keys()
            ->push(self::GROUP_GENERAL);
    }

    /**
     * Processes the given group names by uppercasing the first character
     * and ensuring they are valid.
     *
     * @param array<int, string> $groups Array of group names.
     * @return array<int, string> Return array of valid groups names.
     */
    private function processGroupNames(array $groups): array
    {
        $groups = array_map(fn($group) => ucfirst($group), $groups);

        // When the groups are invalid
        $invalidGroups = array_diff($groups, $this->getGroups()->all());
        if (!empty($invalidGroups)) {
            throw new \Realodix\Timezone\Exceptions\InvalidGroupException($invalidGroups);
        }

        return $groups;
    }

    /**
     * Validate a timezone name and check if it is within the specified groups.
     *
     * @param string $timezoneId Timezone identifier (e.g. "America/New_York")
     * @return void
     */
    private function validateTimezone(string $timezoneId)
    {
        // When the timezone is invalid
        if (!Util::isTimezone($timezoneId)) {
            throw new \Realodix\Timezone\Exceptions\InvalidTimezoneException($timezoneId);
        }

        // When the timezone is out of scope
        $groupId = $timezoneId === 'UTC' ? self::GROUP_GENERAL : Util::extractContinent($timezoneId);
        if (!empty($this->activeGroups) && !in_array($groupId, $this->activeGroups)) {
            throw new \Realodix\Timezone\Exceptions\TimezoneOutOfScopeException(
                $timezoneId, $this->activeGroups,
            );
        }
    }
}

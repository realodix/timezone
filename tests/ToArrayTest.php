<?php

namespace Realodix\Timezone\Test;

use PHPUnit\Framework\Attributes as PHPUnit;
use PHPUnit\Framework\TestCase;
use Realodix\Timezone\Timezone;
use Realodix\Timezone\Util;

class ToArrayTest extends TestCase
{
    private Timezone $tz;

    protected function setUp(): void
    {
        $this->tz = new Timezone;
    }

    #[PHPUnit\Test]
    public function noGroup_noFilter()
    {
        $result = $this->tz
            ->flatten()
            ->toArray();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        // Check if keys are timezone strings and values are formatted timezone strings
        foreach ($result as $timezone => $formattedTimezone) {
            $this->assertIsString($timezone);
            $this->assertIsString($formattedTimezone);
            $this->assertStringContainsString('(UTC', $formattedTimezone);
        }

        $this->assertArrayHasKey('America/New_York', $result);
        $this->assertArrayNotHasKey('America', $result);
    }

    #[PHPUnit\Test]
    public function withGroup_noFilter()
    {
        $result = $this->tz->toArray();

        $this->assertIsArray($result);
        $this->assertSame(
            $this->withGroup_noFilter_baseline(),
            collect($result)->map(fn($item) => array_slice($item, 0, 1))->toArray(),
        );
    }

    protected function withGroup_noFilter_baseline(): array
    {
        return [
            'General'    => ['UTC' => '(UTC+00:00) UTC'],
            'Africa'     => ['Africa/Abidjan' => '(UTC+00:00) Abidjan'],
            'America'    => ['America/Adak' => '(UTC'.Util::getOffset('America/Adak').') Adak'],
            'Antarctica' => ['Antarctica/Casey' => '(UTC'.Util::getOffset('Antarctica/Casey').') Casey'],
            'Arctic'     => ['Arctic/Longyearbyen' => '(UTC'.Util::getOffset('Arctic/Longyearbyen').') Longyearbyen'],
            'Asia'       => ['Asia/Aden' => '(UTC+03:00) Aden'],
            'Atlantic'   => ['Atlantic/Azores' => '(UTC'.Util::getOffset('Atlantic/Azores').') Azores'],
            'Australia'  => ['Australia/Adelaide' => '(UTC'.Util::getOffset('Australia/Adelaide').') Adelaide'],
            'Europe'     => ['Europe/Amsterdam' => '(UTC'.Util::getOffset('Europe/Amsterdam').') Amsterdam'],
            'Indian'     => ['Indian/Antananarivo' => '(UTC'.Util::getOffset('Indian/Antananarivo').') Antananarivo'],
            'Pacific'    => ['Pacific/Apia' => '(UTC'.Util::getOffset('Pacific/Apia').') Apia'],
        ];
    }

    #[PHPUnit\Test]
    public function noGroup_withFilter()
    {
        $result = $this->tz
            ->flatten()
            ->onlyGroups(['General', 'Arctic'])
            ->toArray();

        $this->assertIsArray($result);
        $this->assertSame(
            [
                'UTC' => '(UTC+00:00) UTC',
                'Arctic/Longyearbyen' => '(UTC'.Util::getOffset('Arctic/Longyearbyen').') Arctic / Longyearbyen',
            ],
            $result,
        );
    }

    #[PHPUnit\Test]
    public function withGroup_withFilter()
    {
        $result = $this->tz
            ->onlyGroups(['General', 'Arctic', 'Atlantic'])
            ->toArray();

        $this->assertSame($this->filter_baseline(), $result);
    }

    #[PHPUnit\Test]
    public function excludeGroup()
    {
        $result = $this->tz
            ->excludeGroups([
                'Africa', 'America', 'Antarctica', 'Asia', 'Australia',
                'Europe', 'Indian', 'Pacific',
            ])->toArray();

        $this->assertSame($this->filter_baseline(), $result);
    }

    protected function filter_baseline(): array
    {
        return [
            'General' => [
                'UTC' => '(UTC+00:00) UTC',
            ],
            'Arctic' => [
                'Arctic/Longyearbyen' => '(UTC'.Util::getOffset('Arctic/Longyearbyen').') Longyearbyen',
            ],
            'Atlantic' => [
                'Atlantic/Azores' => '(UTC'.Util::getOffset('Atlantic/Azores').') Azores',
                'Atlantic/Bermuda' => '(UTC'.Util::getOffset('Atlantic/Bermuda').') Bermuda',
                'Atlantic/Canary' => '(UTC'.Util::getOffset('Atlantic/Canary').') Canary',
                'Atlantic/Cape_Verde' => '(UTC'.Util::getOffset('Atlantic/Cape_Verde').') Cape Verde',
                'Atlantic/Faroe' => '(UTC'.Util::getOffset('Atlantic/Faroe').') Faroe',
                'Atlantic/Madeira' => '(UTC'.Util::getOffset('Atlantic/Madeira').') Madeira',
                'Atlantic/Reykjavik' => '(UTC'.Util::getOffset('Atlantic/Reykjavik').') Reykjavik',
                'Atlantic/South_Georgia' => '(UTC'.Util::getOffset('Atlantic/South_Georgia').') South Georgia',
                'Atlantic/St_Helena' => '(UTC'.Util::getOffset('Atlantic/St_Helena').') St. Helena',
                'Atlantic/Stanley' => '(UTC'.Util::getOffset('Atlantic/Stanley').') Stanley',
            ],
        ];
    }
}

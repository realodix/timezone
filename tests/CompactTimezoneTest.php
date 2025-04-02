<?php

namespace Realodix\Timezone\Test;

use PHPUnit\Framework\TestCase;
use Realodix\Timezone\CompactTimezone;

class CompactTimezoneTest extends TestCase
{
    public function testDefaultSelectBox()
    {
        $timezone = new CompactTimezone;
        $result = $timezone->toSelectBox('timezone');
        $this->assertStringStartsWith('<select name="timezone" >', $result);
        $this->assertStringContainsString('</select>', $result);
    }

    public function testCustomAttributes()
    {
        $timezone = new CompactTimezone;
        $attrs = ['class' => 'form-control', 'id' => 'timezone-select'];
        $result = $timezone->toSelectBox('timezone', attrs: $attrs);
        $this->assertStringStartsWith('<select name="timezone" class="form-control" id="timezone-select">', $result);
        $this->assertStringContainsString('</select>', $result);
    }

    public function testPreSelectedOption()
    {
        $timezone = new CompactTimezone;
        $selected = '+00:00';
        $result = $timezone->toSelectBox('timezone', $selected);
        $this->assertStringContainsString('<option value="+00:00" selected>UTC+00:00</option>', $result);
    }

    public function testInvalidPreSelectedOption()
    {
        $timezone = new CompactTimezone;
        $selected = ' invalid ';
        $result = $timezone->toSelectBox('timezone', $selected);
        $this->assertStringNotContainsString(' selected', $result);
    }

    public function testEmptyAttributes()
    {
        $timezone = new CompactTimezone;
        $attrs = [];
        $result = $timezone->toSelectBox('timezone', attrs: $attrs);
        $this->assertStringStartsWith('<select name="timezone" >', $result);
        $this->assertStringContainsString('</select>', $result);
    }

    public function testToArrayReturnsArray()
    {
        $compactTimezone = new CompactTimezone;
        $result = $compactTimezone->toArray();
        $this->assertIsArray($result);
        $this->assertCount(38, $result);
    }
}

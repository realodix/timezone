<?php

require_once __DIR__.'/../vendor/autoload.php';

$tz = new \Realodix\Timezone\CompactTimezone;
echo $tz->toSelectBox('timezone_default');

$tz = new \Realodix\Timezone\Timezone;
echo '<br><br>';
echo $tz->toSelectBox('timezone_default');

$tz = new \Realodix\Timezone\Timezone;
echo '<br><br>';
echo 'toSelectBox(\'timezone_default\', \'America/New_York\') <br>';
echo $tz->toSelectBox('timezone_default', 'America/New_York');

$tz = new \Realodix\Timezone\Timezone;
echo '<br><br>';
echo 'flatten() <br>';
echo $tz->flatten()
    ->toSelectBox('timezone_default', 'America/New_York');

$tz = new \Realodix\Timezone\Timezone;
echo '<br><br>';
echo 'onlyGroups([\'America\', \'Asia\']) <br>';
echo $tz->onlyGroups(['America', 'Asia'])
    ->toSelectBox('timezone_default');

$tz = new \Realodix\Timezone\Timezone;
echo '<br><br>';
echo 'flatten()->onlyGroups([\'America\', \'Asia\']) <br>';
echo $tz->flatten()->onlyGroups(['America', 'Asia'])
    ->toSelectBox('timezone_default', 'America/New_York');

$tz = new \Realodix\Timezone\Timezone;
echo '<br><br>';
echo 'onlyGroups([\'Arctic\'])->toSelectBox(\'timezone_only_america\', \'UTC\') <br>';
echo $tz->onlyGroups(['Arctic'])
    ->toSelectBox('timezone_only_america', 'UTC');

$tz = new \Realodix\Timezone\Timezone;
echo '<br><br>';
echo $tz->excludeGroups([
    'Africa', 'America', 'Antarctica', 'Arctic', 'Asia',
    'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacificz', 'foo',
    'General',
])->toSelectBox('exclude_all');

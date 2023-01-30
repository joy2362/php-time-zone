<?php
namespace Joy2362\PhpTimezone;

use DateTime;
use DateTimeZone;

class TimeZoneService
{
    /**
     * @var array
     */
    private array $regions = [
        'Africa' => DateTimeZone::AFRICA,
        'America' => DateTimeZone::AMERICA,
        'Antarctica' => DateTimeZone::ANTARCTICA,
        'Asia' => DateTimeZone::ASIA,
        'Atlantic' => DateTimeZone::ATLANTIC,
        'Australia' => DateTimeZone::AUSTRALIA,
        'Europe' => DateTimeZone::EUROPE,
        'Indian' => DateTimeZone::INDIAN,
        'Pacific' => DateTimeZone::PACIFIC,
    ];

    /**
     * @return array
     */
    public function getRegions(): array
    {
        $list = [];
        foreach ($this->regions as $key => $region) {
            $list [] = $key;
        }
        return $list;
    }

    /**
     * @var array|string[]
     */
    private array $supportedTimeZone = ['GMT', 'UTC'];

    /**
     * @return array
     */
    public function getSupportedTimeZone(): array
    {
        return $this->supportedTimeZone;
    }

    /**
     * @return array
     */
    public function list(): array
    {
        $list = [];
        $label = config('Timezone.LABEL_FIELD_NAME') ?? 'label';
        $value = config('Timezone.VALUE_FIELD_NAME') ?? 'value';

        foreach ($this->regions as $region) {
            $timezones = DateTimeZone::listIdentifiers($region);
            foreach ($timezones as $timezone) {
                $data[$label] = $this->getLabel($timezone);
                $data[$value] = $timezone;
                $list[] = $data;
            }
        }

        return $list;
    }

    /**
     * @return array
     */
    public function listWithoutLabel(): array
    {
        $list = [];

        foreach ($this->regions as $region) {
            $timezones = DateTimeZone::listIdentifiers($region);
            foreach ($timezones as $timezone) {
                $list[] = $timezone;
            }
        }
        return $list;
    }

    /**
     * @return array
     */
    public function listWithoutValue(): array
    {
        $list = [];

        foreach ($this->regions as $region) {
            $timezones = DateTimeZone::listIdentifiers($region);
            foreach ($timezones as $timezone) {
                $list[] = $this->getLabel($timezone);
            }
        }
        return $list;
    }

    public function listByRegion($region): array
    {
        if (!array_key_exists($region , $this->regions)){
            return [];
        }
        $list = [];
        $label = config('Timezone.LABEL_FIELD_NAME') ?? 'label';
        $value = config('Timezone.VALUE_FIELD_NAME') ?? 'value';

        $timezones = DateTimeZone::listIdentifiers($this->regions[$region]);
        foreach ($timezones as $timezone) {
            $data[$label] = $this->getLabel($timezone);
            $data[$value] = $timezone;
            $list[] = $data;
        }

        return $list;
    }

    /**
     * @param $label
     * @return string
     */
    public function getValueFromLabel($label): string
    {
        $str_zone = explode(') ' , $label);
        return str_replace(' ' , '_' , $str_zone[1]);
    }

    /**
     * @param $value
     * @return string
     */
    public function getLabelFromValue($value): string
    {
        return $this->getLabel($value);
    }

    /**
     * @param $timezone
     * @return string
     */
    private function getLabel($timezone): string
    {
        try {
            $time = new DateTime(null, new DateTimeZone($timezone));
            $time_diff = $this->getTimeDiff($time);
            $zone = $this->getZone($time);
            $defaultTimeZone = config('Timezone.DEFAULT_TIME_ZONE') ?? 'GMT';
            $defaultTimeZone = in_array($defaultTimeZone, $this->supportedTimeZone) ? $defaultTimeZone : $this->supportedTimeZone[0];
            return "({$defaultTimeZone} {$time_diff}) {$zone}";
        } catch (\Exception $ex) {
            return '';
        }
    }

    /**
     * @param $time
     * @return string
     */
    private function getTimeDiff($time): string
    {
        $time_diff_symbol = config('Timezone.TIME_DIFF_SYMBOL') ?? '.';
        $str_time_diff = $time->format('p');
        return str_replace(':', $time_diff_symbol, $str_time_diff);
    }

    /**
     * @param $time
     * @return string
     */
    private function getZone($time): string
    {
        $str_zone = $time->format('e');
        return str_replace('_', ' ', $str_zone);
    }


}

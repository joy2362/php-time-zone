<?php

namespace Joy2362\PhpTimezone;

use DateTime;
use Exception;
use DateTimeZone;
use Illuminate\Support\Facades\Config;

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
     * @var array|string[]
     */
    private array $supportedTimeZone = ['GMT', 'UTC'];

    /**
     * @return array
     */
    public function getRegions(): array
    {
        return array_keys($this->regions);
    }

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
        foreach ($this->regions as $region) {
            $list = array_merge($list, $this->getTimeZoneList(DateTimeZone::listIdentifiers($region) ?? []));
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
            $list = array_merge($list, $this->getTimeZoneList(DateTimeZone::listIdentifiers($region) ?? [], 'value'));
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
            $list = array_merge($list, $this->getTimeZoneList(DateTimeZone::listIdentifiers($region) ?? [], 'label'));
        }
        return $list;
    }

    public function listByRegion($region): array
    {
        if (!array_key_exists($region, $this->regions)) {
            return [];
        }

        return $this->getTimeZoneList(DateTimeZone::listIdentifiers($this->regions[$region]) ?? []);
    }

    /**
     * @param $label
     * @return string
     */
    public function getValueFromLabel($label): string
    {
        $str_zone = explode(') ', $label);
        return str_replace(' ', '_', $str_zone[1]);
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
            $time = new DateTime('', new DateTimeZone($timezone));
            $time_diff = $this->getTimeDiff($time);
            $zone = $this->getZone($time);

            $defaultTimeZone = Config::get('Timezone.DEFAULT_TIME_ZONE', 'GMT');

            $defaultTimeZone = Config::get('Timezone.DEFAULT_TIME_ZONE', 'GMT');
            $defaultTimeZone = in_array($defaultTimeZone, $this->supportedTimeZone) ? $defaultTimeZone : $this->supportedTimeZone[0];
            return "({$defaultTimeZone} {$time_diff}) {$zone}";
        } catch (Exception $ex) {
            return '';
        }
    }

    /**
     * @param $time
     * @return string
     */
    private function getTimeDiff($time): string
    {
        $time_diff_symbol = Config::get('Timezone.TIME_DIFF_SYMBOL', '.');
        $time_diff_symbol = Config::get('Timezone.TIME_DIFF_SYMBOL', '.');
        $str_time_diff = $time->format('p');
        return str_replace(':', $time_diff_symbol, $str_time_diff);
    }

    /**
    * @param $time
    * @return string
    */
    private function getZone($time): string
    {
        return str_replace('_', ' ', $time->format('e'));
    }

    /**
     * @param array $timezones
     * @param bool $isLabel
     * @param bool $isValue
     * @return array
    */
    private function getTimeZoneList(array $timezones, string $type = 'list'): array
    {
        $label = Config::get('Timezone.LABEL_FIELD_NAME', 'label');
        $value = Config::get('Timezone.VALUE_FIELD_NAME', 'value');

        $data = [];

        foreach ($timezones as $timezone) {

            switch ($type) {
                case 'label':
                    $data[] = $this->getLabel($timezone);
                    break;

                case 'value':
                    $data[] = $timezone;
                    break;

                default:
                    $zone = [
                        "{$label}" => $this->getLabel($timezone),
                        "{$value}" => $timezone,
                    ];
                    $data[] = $zone;
                    break;
            }
        }
        return $data;
    }
}

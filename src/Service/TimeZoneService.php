<?php

namespace Joy2362\PhpTimezone\Service;

use DateTime;
use Exception;
use DateTimeZone;
use Illuminate\Support\{Collection, Facades\Config, Str};
use Joy2362\PhpTimezone\Contract\TimeZoneManager;

class TimeZoneService implements TimeZoneManager
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
    public function getRegions(?string $search = ''): array
    {
        return $this->search(array_keys($this->regions), '', $search);
    }

    /**
     * @return array
     */
    public function getSupportedTimeZone(?string $search = ''): array
    {
        return $this->search($this->supportedTimeZone, '', $search);
    }

    /**
     * @return array
     */
    public function list(?string $search = ''): array
    {
        $list = [];
        foreach ($this->regions as $region) {
            $list = array_merge($list, $this->getTimeZoneList(DateTimeZone::listIdentifiers($region) ?? []));
        }

        return $this->search($list, 'label', $search);
    }

    /**
     * @return array
     */
    public function listWithoutLabel(?string $search = ''): array
    {
        $list = [];
        foreach ($this->regions as $region) {
            $list = array_merge($list, $this->getTimeZoneList(DateTimeZone::listIdentifiers($region) ?? [], 'value'));
        }

        return $this->search($list, '', $search);
    }

    /**
     * @return array
     */
    public function listWithoutValue(?string $search = ''): array
    {
        $list = [];
        foreach ($this->regions as $region) {
            $list = array_merge($list, $this->getTimeZoneList(DateTimeZone::listIdentifiers($region) ?? [], 'label'));
        }

        return $this->search($list, '', $search);
    }

    public function listByRegion(string $region, ?string $search = ''): array
    {
        if (!array_key_exists(ucfirst($region), $this->regions)) {
            return [];
        }

        $list = $this->getTimeZoneList(DateTimeZone::listIdentifiers($this->regions[ucfirst($region)]) ?? []);
        return $this->search($list, 'label', $search);
    }

    /**
     * @param string $label
     * @return string
     */
    public function getValueFromLabel(string $label): string
    {
        $zone = explode(') ', $label);
        return  isset($zone[1]) ? str_replace(' ', '_', $zone[1]) : '';
    }

    /**
     * @param $value
     * @return string
     */
    public function getLabelFromValue(string $value): string
    {
        return $this->getLabel($value);
    }

    /**
     * @param $timezone
     * @return string
     */
    private function getLabel(string $timezone): string
    {
        try {
            $time = new DateTime('', new DateTimeZone(ucfirst($timezone)));
            $time_diff = $this->getTimeDiff($time);
            $zone = $this->getZone($time);
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
    private function getTimeDiff(DateTime $time): string
    {
        $time_diff_symbol = Config::get('Timezone.TIME_DIFF_SYMBOL', '.');
        $str_time_diff = $time->format('p');
        return str_replace(':', $time_diff_symbol, $str_time_diff);
    }

    /**
    * @param $time
    * @return string
    */
    private function getZone(DateTime $time): string
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

    /**
     * @param array $list
     * @param ?string $search
     * @return array
    */
    private function search(array $list, ?string $key = '', ?string $search): array 
    {
        return collect($list)->when(!empty($search), function (Collection $collection) use($search, $key){
            return $collection->filter(fn($item) => Str::contains(Str::lower(!empty($key) ? $item[$key] :$item), Str::lower($search)));
        })->values()->toArray();
    }
}
<?php

namespace Joy2362\PhpTimezone\Service;

use DateTime;
use Exception;
use DateTimeZone;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
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
    public function getRegions(?string $search): array
    {
        $regions = collect(array_keys($this->regions));
       
        $regions = $regions->when(!empty($search), function (Collection $regions) use($search){
            return $regions->filter(function($item) use($search){
                return Str::contains(Str::lower($item), Str::lower($search));
            });
        });

        return $regions->values()->all();
    }

    /**
     * @return array
     */
    public function getSupportedTimeZone(?string $search): array
    {
        $supportedTimeZone = collect($this->supportedTimeZone);
       
        $supportedTimeZone = $supportedTimeZone->when(!empty($search), function (Collection $supportedTimeZone) use($search){
            return $supportedTimeZone->filter(function($item) use($search){
                return Str::contains(Str::lower($item), Str::lower($search));
            });
        });

        return $supportedTimeZone->values()->all();
    }

    /**
     * @return array
     */
    public function list(?string $search = null): array
    {
        $list = [];
        foreach ($this->regions as $region) {
            $list = array_merge($list, $this->getTimeZoneList(DateTimeZone::listIdentifiers($region) ?? []));
        }
        $listCollection = collect($list);
        $listCollection = $listCollection->when(!empty($search), function (Collection $listCollection) use($search){
            return $listCollection->filter(function($item) use($search){
                return Str::contains(Str::lower($item['label']), Str::lower($search));
            });
        });

        return $listCollection->values()->toArray();
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
     * @param string $label
     * @return string
     */
    public function getValueFromLabel(string $label): string
    {
        $str_zone = explode(') ', $label);
        return str_replace(' ', '_', $str_zone[1]);
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
            $time = new DateTime('', new DateTimeZone($timezone));
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
}
<?php

namespace Joy2362\PhpTimezone\Contract;

interface TimeZoneManager {
    /**
     * Get the list of regions.
     *
     * @return array
     */
    public function getRegions(): array;

    /**
     * Get the list of supported time zones.
     *
     * @return array
     */
    public function getSupportedTimeZone(): array;

     /**
     * List all time zones with labels and values.
     *
     * @return array
     */
    public function list(): array;

    /**
     * List time zones by region.
     *
     * @param string $region
     * @return array
     */
    public function listByRegion(string $region): array;

    /**
     * Get the value from the label.
     *
     * @param string $label
     * @return string
     */
    public function getValueFromLabel(string $label): string;

    /**
     * Get the label from the value.
     *
     * @param string $value
     * @return string
     */
    public function getLabelFromValue(string $value): string;

}
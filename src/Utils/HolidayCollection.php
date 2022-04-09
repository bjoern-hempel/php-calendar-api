<?php

declare(strict_types=1);

/*
 * This file is part of the bjoern-hempel/php-calendar-api project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Utils;

use App\Command\CreateHolidayCommand;
use App\Entity\Holiday;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;

/**
 * Class HolidayCollection
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-09)
 * @package App\Utils
 */
class HolidayCollection
{
    /**
     * Gets given holidays grouped.
     *
     * @param Collection<int, Holiday> $holidays
     * @return Array<int, Collection<int, Holiday>>
     * @throws Exception
     */
    public static function getHolidaysGrouped(Collection $holidays): array
    {
        $holidayGrouped = [];

        /* Group all holidays. */
        foreach ($holidays as $holiday) {
            $year = intval($holiday->getDate()->format('Y'));

            if (!array_key_exists($year, $holidayGrouped)) {
                if ($year === CreateHolidayCommand::YEARLY_YEAR) {
                    $holidayGrouped[CreateHolidayCommand::YEARLY_YEAR] = new ArrayCollection();
                } else {
                    $holidayGrouped[$year] = new ArrayCollection();
                }
            }

            $holidayGrouped[$year]->add($holiday);
        }

        /* Add yearly holidays to each year. */
        if (array_key_exists(CreateHolidayCommand::YEARLY_YEAR, $holidayGrouped)) {
            $holidaysYearly = $holidayGrouped[CreateHolidayCommand::YEARLY_YEAR];

            unset($holidayGrouped[CreateHolidayCommand::YEARLY_YEAR]);

            foreach ($holidayGrouped as $year => $holidayGroup) {
                /** @var Holiday $holidayYearly */
                foreach ($holidaysYearly as $holidayYearly) {
                    $holidayYearlyClone = clone $holidayYearly;

                    $date = new DateTime();
                    $date->setDate(
                        $year,
                        intval($holidayYearlyClone->getDate()->format('n')),
                        intval($holidayYearlyClone->getDate()->format('j')),
                    );

                    $holidayYearlyClone->setDate($date);
                    $holidayGroup->add($holidayYearlyClone);
                }
            }
        }

        /* Sort holidays */
        foreach ($holidayGrouped as $year => &$holidayGroup) {
            $iterator = $holidayGroup->getIterator();
            /** @phpstan-ignore-next-line → Method uasort exists. */
            $iterator->uasort(function ($a, $b) {
                return ($a->getDate() < $b->getDate()) ? -1 : 1;
            });
            $holidayGroup = new ArrayCollection(iterator_to_array($iterator));
        }

        return $holidayGrouped;
    }
}

<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wcf\system\cache\builder;

use wcf\system\WCF;

/**
 * Builds statistics for the current WCF.
 */
class JCoinsStatisticCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritdoc
     */
    protected $maxLifetime = 900;

    /**
     * @inheritdoc
     */
    public function rebuild(array $parameters)
    {
        $statistic = [];

        // get user stats
        $userCache = UserStatsCacheBuilder::getInstance()->getData();

        $statistic['overallJCoins'] = $this->fetchOverallJCoins();
        $statistic['JCoinsPerUser'] = \round($statistic['overallJCoins'] / $userCache['members'], 2);

        return $statistic;
    }

    /**
     * Returns the overall JCoins.
     *
     * @return integer
     * @throws \wcf\system\database\DatabaseException
     */
    public function fetchOverallJCoins()
    {
        $stmt = WCF::getDB()->prepareStatement("SELECT SUM(jCoinsAmount) FROM wcf" . WCF_N . "_user");
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}

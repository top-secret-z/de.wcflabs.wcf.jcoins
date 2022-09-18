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
namespace wcf\system\box;

use DateTime;
use wcf\system\WCF;

/**
 * JCoins personal history box controller.
 */
class JCoinsPersonalHistoryPlotSidebarBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];

    /**
     * @inheritDoc
     */
    protected function loadContent()
    {
        if (!MODULE_JCOINS || !WCF::getUser()->userID) {
            return;
        }

        $dateTime = DateTime::createFromFormat('Y-m-d h:i', \date('Y-m-d') . " 00:00");
        for ($i = 0; $i < 30; $i++) {
            $dataArray[$dateTime->getTimestamp()] = 0;
            $dateTime->modify('-1 day');
        }

        $stmt = WCF::getDB()->prepareStatement("SELECT FROM_UNIXTIME(time, '%Y-%m-%d') AS day, SUM(amount) as amount FROM wcf" . WCF_N . "_jcoins_statement WHERE userID = ? AND time > ? GROUP BY day ORDER BY day DESC LIMIT 30");
        $stmt->execute([
            WCF::getUser()->userID,
            $dateTime->getTimestamp(),
        ]);

        while ($row = $stmt->fetchArray()) {
            $dataArray[\strtotime($row['day'])] = $row['amount'];
        }

        WCF::getTPL()->assign([
            'dataArray' => $dataArray,
            'minValue' => \min(\min($dataArray), 0),
        ]);

        $this->content = WCF::getTPL()->fetch('boxJCoinsHistoryPlot');
    }
}

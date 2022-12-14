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

use wcf\data\DatabaseObject;
use wcf\data\user\UserProfileList;
use wcf\system\cache\builder\JCoinsRichestMembersCacheBuilder;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Box controller for a list of JCoins users.
 */
class JCoinsUserListBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    public $defaultLimit = 5;

    public $maximumLimit = 50;

    /**
     * @inheritDoc
     */
    protected static $supportedPositions = [
        'sidebarLeft',
        'sidebarRight',
    ];

    /**
     * maps special sort fields to cache builders
     */
    public $cacheBuilders = ['jCoinsAmount' => JCoinsRichestMembersCacheBuilder::class];

    /**
     * @inheritDoc
     */
    protected $sortFieldLanguageItemPrefix = 'wcf.acp.box.jcoins';

    /**
     * ids of the shown users loaded from cache
     */
    public $userIDs;

    /**
     * @inheritDoc
     */
    public $validSortFields = ['jCoinsAmount'];

    /**
     * @inheritDoc
     */
    public function getLink()
    {
        if (MODULE_MEMBERS_LIST) {
            $parameters = '';
            if ($this->box->sortField) {
                $parameters = 'sortField=' . $this->box->sortField . '&sortOrder=' . $this->box->sortOrder;
            }

            return LinkHandler::getInstance()->getLink('MembersList', [], $parameters);
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    protected function getObjectList()
    {
        // use specialized cache builders
        if ($this->box->sortOrder && $this->box->sortField && isset($this->cacheBuilders[$this->box->sortField])) {
            $this->userIDs = \call_user_func([$this->cacheBuilders[$this->box->sortField], 'getInstance'])->getData([
                'limit' => $this->box->limit,
                'sortOrder' => $this->sortOrder,
            ]);
        }

        if ($this->userIDs !== null) {
            UserProfileRuntimeCache::getInstance()->cacheObjectIDs($this->userIDs);
        }

        return new UserProfileList();
    }

    /**
     * @inheritDoc
     */
    protected function getTemplate()
    {
        $userProfiles = [];
        if ($this->userIDs !== null) {
            $userProfiles = UserProfileRuntimeCache::getInstance()->getObjects($this->userIDs);

            // filter `null` values of users that have been deleted in the meantime
            $userProfiles = \array_filter($userProfiles, static function ($userProfile) {
                return $userProfile !== null;
            });

            DatabaseObject::sort($userProfiles, $this->sortField, $this->sortOrder);
        }

        return WCF::getTPL()->fetch('boxJCoinsUserList', 'wcf', [
            'boxUsers' => $this->userIDs !== null ? $userProfiles : $this->objectList->getObjects(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function hasContent()
    {
        $hasContent = parent::hasContent();

        if ($this->userIDs !== null) {
            return !empty($this->userIDs);
        }

        return $hasContent;
    }

    /**
     * @inheritDoc
     */
    public function hasLink()
    {
        return MODULE_MEMBERS_LIST == 1;
    }

    /**
     * @inheritDoc
     */
    public function readObjects()
    {
        if ($this->userIDs === null) {
            parent::readObjects();
        } else {
            EventHandler::getInstance()->fireAction($this, 'readObjects');
        }
    }
}

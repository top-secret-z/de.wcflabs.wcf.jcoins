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
namespace wcf\data\jcoins\statement;

use Exception;
use wcf\data\conversation\Conversation;
use wcf\data\conversation\message\ConversationMessage;
use wcf\system\WCF;

/**
 * Represents a JCoins Statement entry for global statement list.
 */
class GlobaleJCoinsStatement extends JCoinsStatement
{
    /**
     * @inheritdoc
     */
    public function getLink()
    {
        // modify link for conversations
        if ($this->getObject() instanceof ConversationMessage || $this->getObject() instanceof Conversation) {
            return '';
        }

        return parent::getLink();
    }

    /**
     * Returns the reason for the statement.
     */
    public function getReason()
    {
        // conversation
        if ($this->getObject() instanceof ConversationMessage || $this->getObject() instanceof Conversation) {
            return WCF::getLanguage()->getDynamicVariable('wcf.jcoins.statement.reasonGlobal.' . (($this->reverseMode) ? 'reverse.' : '') . $this->getObjectType()->objectType, [
                'statement' => $this,
                'object' => $this->getObject(),
            ]);
        }

        // transfer
        if ($this->getObjectType()->objectType == 'de.wcflabs.jcoins.statement.transfer') {
            return WCF::getLanguage()->getDynamicVariable('wcf.jcoins.statement.reasonGlobal.' . (($this->reverseMode) ? 'reverse.' : '') . $this->getObjectType()->objectType, [
                'statement' => $this,
                'object' => $this->getObject(),
            ]);
        }

        // reactions
        if (isset($this->additionalData['objectClassName'])) {
            if ($this->objectType->objectType == 'de.wcflabs.jcoins.statement.reaction') {
                try {
                    $object = null;
                    $className = $this->additionalData['objectClassName'];

                    if (isset($className) && \class_exists($className)) {
                        if (\is_subclass_of($className, '\wcf\data\DatabaseObjectDecorator')) {
                            $baseClassName = \call_user_func([$className, 'getBaseClass']);
                            $object = new $baseClassName($this->objectID);
                        } else {
                            $object = new $className($this->objectID);
                        }
                    }
                } catch (Exception $e) {
                    $object = null;
                }

                if (!$object || !$object->getObjectID()) {
                    return WCF::getLanguage()->getDynamicVariable('wcf.jcoins.statement.reason.' . (($this->reverseMode) ? 'reverse.' : '') . $this->getObjectType()->objectType . '.noObject', [
                        'statement' => $this,
                        'object' => null,
                    ]);
                }
            }
        }

        return WCF::getLanguage()->getDynamicVariable('wcf.jcoins.statement.reason.' . (($this->reverseMode) ? 'reverse.' : '') . $this->getObjectType()->objectType, [
            'statement' => $this,
            'object' => $this->getObject(),
        ]);
    }
}

<?php
// {{{ICINGA_LICENSE_HEADER}}}
/**
 * This file is part of Icinga 2 Web.
 *
 * Icinga 2 Web - Head for multiple monitoring backends.
 * Copyright (C) 2013 Icinga Development Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @copyright 2013 Icinga Development Team <info@icinga.org>
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt GPL, version 2
 * @author    Icinga Development Team <info@icinga.org>
 */
// {{{ICINGA_LICENSE_HEADER}}}

namespace Monitoring\Form\Command;

use Icinga\Web\Form\Element\DateTime;
use \DateTime as PhpDateTime;
use \DateInterval;
use Icinga\Web\Form\Element\Note;

/**
 * Form for acknowledge commands
 */
class AcknowledgeForm extends ConfirmationForm
{
    /**
     * Interface method to build the form
     * @see ConfirmationForm::create
     */
    protected function create()
    {
        $this->addElement($this->createAuthorField());

        $this->addElement(
            'textarea',
            'comment',
            array(
                'label'    => t('Comment'),
                'rows'     => 4,
                'required' => true
            )
        );

        $this->addElement(
            'checkbox',
            'persistent',
            array(
                'label' => t('Persistent comment'),
                'value' => false
            )
        );

        $expireCheck = $this->createElement(
            'checkbox',
            'expire',
            array(
                'label'    => t('Use expire time')
            )
        );

        $now = new PhpDateTime();
        $interval = new DateInterval('PT1H'); // Add 3600 seconds
        $now->add($interval);

        $expireTime = new DateTime(
            array(
                'name'  => 'expiretime',
                'label' => t('Expire time'),
                'value' => $now->format($this->getDateFormat())
            )
        );

        $expireNote = new Note(
            array(
                'name'  => 'expirenote',
                'value' => t('If the acknowledgement should expire, check the box and enter an expiration timestamp.')
            )
        );

        $this->addElements(array($expireNote, $expireCheck, $expireTime));

        $this->addDisplayGroup(
            array(
                'expirenote',
                'expire',
                'expiretime'
            ),
            'expire_group',
            array(
                'legend' => t('Expire acknowledgement')
            )
        );

        $this->addElement(
            'checkbox',
            'sticky',
            array(
                'label' => t('Sticky acknowledgement'),
                'value' => false
            )
        );

        $this->addElement(
            'checkbox',
            'notify',
            array(
                'label' => t('Send notification'),
                'value' => false
            )
        );

        $this->setSubmitLabel(t('Acknowledge problem'));

        parent::create();
    }

    /**
     * Add validator for dependent fields
     * @see Form::preValidation
     * @param array $data
     */
    protected function preValidation(array $data)
    {
        if (isset($data['expire']) && intval($data['expire']) === 1) {
            $expireTime = $this->getElement('expiretime');
            $expireTime->setRequired(true);
            $expireTime->addValidator($this->createDateTimeValidator(), true);
        }
    }
}

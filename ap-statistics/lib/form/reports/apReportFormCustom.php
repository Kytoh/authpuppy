<?php

// +------------------------------------------------------------------------+
// | AuthPuppy Authentication Server                                        |
// | ===============================                                        |
// |                                                                        |
// | AuthPuppy is the new generation of authentication server for           |
// | a wifidog based captive portal suite                                   |
// +------------------------------------------------------------------------+
// | PHP version 5 required.                                                |
// +------------------------------------------------------------------------+
// | Homepage:     http://www.authpuppy.org/                                |
// | Launchpad:    http://www.launchpad.net/authpuppy                       |
// +------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify   |
// | it under the terms of the GNU General Public License as published by   |
// | the Free Software Foundation; either version 2 of the License, or      |
// | (at your option) any later version.                                    |
// |                                                                        |
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// |                                                                        |
// | You should have received a copy of the GNU General Public License along|
// | with this program; if not, write to the Free Software Foundation, Inc.,|
// | 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.            |
// +------------------------------------------------------------------------+


/**
 * apReportFormCustom
 * Form for custom report, where user enters the data he wants to have reported
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportFormCustom extends apReportFormBase
{

  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    $this->addDateRange("range", true);
    $this->addTextField('select');
    $this->addTextField('where');
    $this->addTextField('group by');
    
    $this->widgetSchema->setHelp('select', 'Comma-separated part of the select query, as if you were executing a select query on the connection table');
    $this->widgetSchema->setHelp('where', 'Where clause on the connection table');
    $this->widgetSchema->setHelp('group by', 'Group By clause of this query on the connection table');
    
    
  }

}
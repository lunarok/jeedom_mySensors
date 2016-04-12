<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";

if (init('apikey') != config::byKey('api') || config::byKey('api') == '') {
	connection::failed();
	echo 'Clef API non valide, vous n\'etes pas autorisé à effectuer cette action (jeeApi)';
	die();
}

$gateway = init('gateway');
$nodeid = init('sender');
$sensor = init('sensor');
$type = init('type');
$value = init('payload');
$messagetype = init('messagetype');

switch ($messagetype) {
  case 'saveValue' : mySensors::saveValue($gateway, $nodeid,$sensor,$type, $value); break;//saveValue($gateway, $nodeid,$sensor,$type, $value)
  case 'saveSketchName' : mySensors::saveSketchNameEvent($gateway, $nodeid, $value); break;//saveSketchVersion($gateway, $nodeid, $value)
  case 'saveSketchVersion' : mySensors::saveSketchVersion($gateway, $nodeid, $value); break;//saveSketchVersion($gateway, $nodeid, $value)
  case 'saveLibVersion' : mySensors::saveLibVersion($gateway, $nodeid, $value); break;//saveLibVersion($gateway, $nodeid, $value)
  case 'saveSensor' : mySensors::saveSensor($gateway, $nodeid, $sensor, $type); break;//saveSensor($gateway, $nodeid, $sensor, $value)
  case 'saveBatteryLevel' : mySensors::saveBatteryLevel($gateway, $nodeid, $value); break; // saveBatteryLevel($gateway, $nodeid, $value)
  case 'saveGateway' : mySensors::saveGateway($gateway, $value); break;//saveGateway($gateway, $value)
  case 'getValue' : mySensors::getValue($gateway,$nodeid,$sensor,$type); break;//getValue($gateway,$nodeid,$sensor,$type)
  case 'getNextSensorId' : mySensors::getNextSensorId($gateway); break;//getNextSensorId($gateway)
}

return true;
?>

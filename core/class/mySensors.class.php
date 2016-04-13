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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';


class mySensors extends eqLogic {

  public static function health() {
    $return = array();
    $statusGateway=false;
    $statusGateway = config::byKey('gateway','mySensors');
    $libVer = config::byKey('gateLib','mySensors');
    if ($libVer=='') {
      $libVer = '{{inconnue}}';
    }
    $return[] = array(
      'test' => __('Gateway', __FILE__),
      'result' => ($statusGateway) ? $libVer : __('NOK', __FILE__),
      'advice' => ($statusGateway) ? '' : __('Indique si la gateway est connectée avec sa version', __FILE__),
      'state' => $statusGateway,
    );
    return $return;
  }


  public static $_dico =
  array(
    'C' => array(
      0=>'Présentation',
      1=>'Paramétrage',
      2=>'Récupération',
      3=>'Interne',
      4=>'OTA',
    ),
    'I' => array(
      'I_BATTERY_LEVEL'=> 0,
      'I_TIME'=> 1,
      'I_VERSION'=> 2,
      'I_ID_REQUEST'=> 3,
      'I_ID_RESPONSE'=> 4,
      'I_INCLUSION_MODE'=> 5,
      'I_CONFIG'=> 6,
      'I_PING'=> 7,
      'I_PING_ACK'=> 8,
      'I_LOG_MESSAGE'=> 9,
      'I_CHILDREN'=> 10,
      'I_SKETCH_NAME'=> 11,
      'I_SKETCH_VERSION'=> 12,
      'I_REBOOT'=> 13,
    ),
    'N' => array( // Type de donnée
      0=>'Température',
      1=>'Humidité',
      2=>'Relais',
      3=>'Dimmer',
      4=>'Pression',
      5=>'Prévision',
      6=>'Niveau de pluie',
      7=>'Débit de pluie',
      8=>'Vitesse de vent',
      9=>'Rafale de vent',
      10 =>'Direction du vent',
      11 =>'UV',
      12 =>'Poids',
      13 =>'Distance',
      14 =>'Impédance',
      15 =>'Sécurité activée',
      16=>'Activation',
      17=>'Puissance',
      18=>'KWh',
      19=>'Activation Scène',
      20=>'Désactivation Scène',
      21=>'Mode de chauffage',
      22=>'Radiateur',
      23=>'Niveau de Lumière',
      24=>'Variable1',
      25=>'Variable2',
      26=>'Variable3',
      27=>'Variable4',
      28=>'Virtuel',
      29=>'Lever',
      30=>'Descente',
      31=>'Arrêt',
      32=>'Envoi IR',
      33=>'Réception IR',
      34=>'Débit Eau',
      35=>'Volume Eau',
      36=>'Verrou',
      37=>'Poussière',
      38=>'Voltage',
      39=>'Courant',
      40=>'RVB',
      41=>'RVBB',
      42=>'ID',
      43=>'Prefix Unit',
      44=>'Seuil Froid',
      45=>'Seuil Chaud',
      46=>'Mode',
      97=>'Connexion',
      98=>'Inactivité',
      99=>'Batterie'
    ),
    'S' => array( // 'S_TYPE', 'Nom', 'widget', 'variable, 'unité', 'historique', 'affichage'
      0 => array('S_DOOR','Ouverture','door','binary','','','1','OPENING',),
      1 => array('S_MOTION','Mouvement','presence','binary','','','1','PRESENCE',),
      2 => array('S_SMOKE','Fumée','tile','binary','','','1','SMOKE',),
      3 => array('S_LIGHT','Relais','light','binary','','','','ENERGY_STATE',),
      4 => array('S_DIMMER','Variateur','light','numeric','%','','','ENERGY_STATE',),
      5 => array('S_COVER','Store','store','binary','','','1','FLAP_STATE',),
      6 => array('S_TEMP','Température','tile','numeric','°C','1','1','TEMPERATURE',),
      7 => array('S_HUM','Humidité','tile','numeric','%','1','1','HUMIDITY',),
      8 => array('S_BARO','Baromètre','tile','string','Pa','1','1','PRESSURE',),
      9 => array('S_WIND','Vent','tile','numeric','','','1','WIND_SPEED',),
      10 => array('S_RAIN','Pluie','badge','numeric','cm','1','1','RAIN_CURRENT',),
      11 => array('S_UV','UV','badge','numeric','uvi','1','1','UV',),
      12 => array('S_WEIGHT','Poids','badge','numeric','kg','1','1','GENERIC',),
      13 => array('S_POWER','Energie','badge','numeric','','1','1','POWER',),
      14 => array('S_HEATER','Radiateur','heatPiloteWire','binary','','','1','HEATING_STATE',),
      15 => array('S_DISTANCE','Distance','badge','numeric','cm','','1','GENERIC',),
      16 => array('S_LIGHT_LEVEL','Luminosité','tile','numeric','','','1','GENERIC',),
      17 => array('S_ARDUINO_NODE','Noeud Arduino','tile','string','','','1','GENERIC',),
      18 => array('S_ARDUINO_RELAY','Noeud Répéteur','tile','string','','','1','GENERIC',),
      19 => array('S_LOCK','Verrou','lock','binary','','','1','LOCK_STATE',),
      20 => array('S_IR','Infrarouge','tile','string','','','1','GENERIC',),
      21 => array('S_WATER','Eau','badge','numeric','','1','1','CONSUMPTION',),
      22 => array('S_AIR_QUALITY','Qualité d Air','badge','numeric','','1','1','GENERIC',),
      23 => array('S_CUSTOM','Custom','tile','string','','','1','GENERIC',),
      24 => array('S_DUST','Poussière','badge','numeric','mm','1','1','GENERIC',),
      25 => array('S_SCENE_CONTROLLER','Controleur de Scène','alert','binary','','','1','GENERIC',),
      26 => array('S_RGB_LIGHT','Lampe RVB','tile','string','','','1','LIGHT_COLOR',),
      27 => array('S_RGBW_LIGHT','Lampe RVB+B','tile','string','','','1','LIGHT_COLOR',),
      28 => array('S_COLOR_SENSOR','Capteur de couleur','tile','string','','','1','LIGHT_COLOR',),
      29 => array('S_HVAC','Thermostat','tile','string','','','1','HEATING_STATE',),
      30 => array('S_MULTIMETER','Multimètre','tile','string','','','1','GENERIC',),
      31 => array('S_SPRINKLER','Arrosage','tile','string','','1','1','GENERIC',),
      32 => array('S_WATER_LEAK','Fuite d eau','badge','binary','','1','1','FLOOD',),
      33 => array('S_SOUND','Son','tile','numeric','','','1','GENERIC',),
      34 => array('S_VIBRATION','Vibration','tile','numeric','mm','1','1','GENERIC',),
      35 => array('S_MOISTURE','Humidité','tile','numeric','','','1','GENERIC',),
      97 => array('GATEWAY','Connexion avec Gateway','tile','string','','','','GENERIC',),
      98 => array('INNA_NODE','Inactivité des Nodes','tile','string','','','','GENERIC',),
      99 => array('BATTERIE','Etat de la batterie','Sky-progressBar','numeric','%','','1','BATTERY',)
    )

  );

  public static function deamon_info() {
    $return = array();
    $return['log'] = 'mySensors_node';
    $return['state'] = 'nok';
    $pid = trim( shell_exec ('ps ax | grep "mySensors/node/mysensors.js" | grep -v "grep" | wc -l') );
    if ($pid != '' && $pid != '0') {
      $return['state'] = 'ok';
    }
    $return['launchable'] = 'ok';
    if ((config::byKey('nodeGateway', 'mySensors') == 'none' || config::byKey('nodeGateway', 'mySensors') == '') && (config::byKey('netgate','mySensors') == '')) {
      $return['launchable'] = 'nok';
      $return['launchable_message'] = __('Aucune gateway configurée', __FILE__);
    }
    return $return;
  }

  public static function deamon_start($_debug = false) {
    self::deamon_stop();
    $deamon_info = self::deamon_info();
    if ($deamon_info['launchable'] != 'ok') {
      throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
    }

    if (!config::byKey('internalPort')) {
      $url = config::byKey('internalProtocol') . config::byKey('internalAddr') . config::byKey('internalComplement') . '/plugins/mySensors/core/api/jeeSensors.php?apikey=' . config::byKey('api');
    } else {
      $url = config::byKey('internalProtocol') . config::byKey('internalAddr'). ':' . config::byKey('internalPort') . config::byKey('internalComplement') . '/plugins/mySensors/core/api/jeeSensors.php?apikey=' . config::byKey('api');
    }

    //launching serial service
    if (config::byKey('nodeGateway', 'mySensors') != 'none' && config::byKey('nodeGateway', 'mySensors') != '') {
      $usbGateway = jeedom::getUsbMapping(config::byKey('nodeGateway', 'mySensors'));
      if ($usbGateway == '' ) {
        throw new Exception(__('Le port : ', __FILE__) . $port . __(' n\'existe pas', __FILE__));
      }
      log::add('mySensors','info','Lancement du démon mySensors : Gateway ' . $usbGateway);

      if ($usbGateway != "none") {
        exec('sudo chmod -R 777 ' . $usbGateway);
      }

      if (config::byKey('jeeNetwork::mode') != 'master') { //Je suis l'esclave
        $url  = config::byKey('jeeNetwork::master::ip') . '/plugins/mySensors/core/api/jeeSensors.php?apikey=' . config::byKey('jeeNetwork::master::apikey');
        $gateway = config::byKey('internalAddr') . ' ' . $usbGateway . ' serial';
      } else {
        $gateway = 'master ' . $usbGateway . ' serial';
      }
      mySensors::launch_svc($url, $gateway);
    }

    if (config::byKey('netgate','mySensors') != '') {
      $net = explode(";", config::byKey('netgate','mySensors'));
      foreach ($net as $value) {
        $gate = explode(';', $value);
        $gateway = $gate[0] . ' ' . $value . ' network';
        mySensors::launch_svc($url, $gateway);
      }
    }

  }

  public static function launch_svc($url, $gateway) {
    if ($_debug = true) {
      $log = "1";
    } else {
      $log = "0";
    }
    $sensor_path = realpath(dirname(__FILE__) . '/../../node');

    $cmd = 'nice -n 19 nodejs ' . $sensor_path . '/mysensors.js ' . $url . ' ' . $gateway . ' ' . $log;

    log::add('mySensors', 'debug', 'Lancement démon mySensors : ' . $cmd);

    $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('mySensors_node') . ' 2>&1 &');
    if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
      log::add('mySensors', 'error', $result);
      return false;
    }

    $i = 0;
    while ($i < 30) {
      $deamon_info = self::deamon_info();
      if ($deamon_info['state'] == 'ok') {
        break;
      }
      sleep(1);
      $i++;
    }
    if ($i >= 30) {
      log::add('mySensors', 'error', 'Impossible de lancer le démon mySensors, vérifiez le port', 'unableStartDeamon');
      return false;
    }
    message::removeAll('mySensors', 'unableStartDeamon');
    log::add('mySensors', 'info', 'Démon mySensors lancé');
    return true;
  }

  public static function deamon_stop() {
    exec('kill $(ps aux | grep "mySensors/node/mysensors.js" | awk \'{print $2}\')');
    log::add('mySensors', 'info', 'Arrêt du service mySensors');
    $deamon_info = self::deamon_info();
    if ($deamon_info['state'] == 'ok') {
      sleep(1);
      exec('kill -9 $(ps aux | grep "mySensors/node/mysensors.js" | awk \'{print $2}\')');
    }
    $deamon_info = self::deamon_info();
    if ($deamon_info['state'] == 'ok') {
      sleep(1);
      exec('sudo kill -9 $(ps aux | grep "mySensors/node/mysensors.js" | awk \'{print $2}\')');
    }
    config::save('gateway', '0',  'mySensors');
  }

  public static function dependancy_info() {
    $return = array();
    $return['log'] = 'mySensors_dep';
    $serialport = realpath(dirname(__FILE__) . '/../../node/node_modules/serialport');
    $request = realpath(dirname(__FILE__) . '/../../node/node_modules/request');
    $return['progress_file'] = '/tmp/mySensors_dep';
    if (is_dir($serialport) && is_dir($request)) {
      $return['state'] = 'ok';
    } else {
      $return['state'] = 'nok';
    }
    return $return;
  }

  public static function dependancy_install() {
    log::add('mySensors','info','Installation des dépéndances nodejs');
    $resource_path = realpath(dirname(__FILE__) . '/../../resources');
    passthru('/bin/bash ' . $resource_path . '/nodejs.sh ' . $resource_path . ' > ' . log::getPathToLog('mySensors_dep') . ' 2>&1 &');
  }

  public static function sendCommand( $gateway, $destination, $sensor, $command, $acknowledge, $type, $payload ) {
    //default master
    $ip = '127.0.0.1';
    $port = '8019';

    $jeeNetwork = jeeNetwork::byId($gateway);
    if (is_object($jeeNetwork)) {
      $ip = $jeeNetwork->getIp();
    }
    if (config::byKey('netgate','mySensors') != '') {
      $net = explode(";", config::byKey('netgate','mySensors'));
      foreach ($net as $value) {
        $gate = explode(";", $value);
        if ($gateway == $gate[0]) {
          $ip = $gate[0];
          $port = $gate[1];
        }
      }
    }
    $msg = $destination . ";" . $sensor . ";" . $command . ";" . $acknowledge . ";" .$type . ";" . $payload;
    mySensors::sendToController($ip,$port,$msg);
  }

  public static function sendToController( $ip, $port, $msg ) {
    log::add('mySensors', 'info', $msg);
    $fp = fsockopen($ip, $port, $port, $errstr);
    if (!$fp) {
      echo "ERROR: $errno - $errstr<br />\n";
    } else {
      fwrite($fp, $msg);
      fclose($fp);
    }
  }

  public static function saveNetGate($value) {
    config::save('netgate', $value,  'mySensors');
  }

  public static function getValue($gateway,$nodeid,$sensor,$type) {
    $cmdId = 'Sensor'.$sensor;
    $elogic = self::byLogicalId($nodeid, 'mySensors');
    if (is_object($elogic)) {
      $elogic->setStatus('lastCommunication', date('Y-m-d H:i:s'));
      $elogic->save();
      $cmdlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$cmdId);
      if (is_object($cmdlogic)) {
        if ($cmdlogic->getConfiguration('sensorCategory') == "23" && $type == "28") {
          $idvirt = str_replace("#","",$cmdlogic->getConfiguration('value'));
          $cmdvirt = cmd::byId($idvirt);

          if (is_object($cmdvirt)) {
            //echo $cmdvirt->execCmd();
            mySensors::sendCommand( $gateway, $nodeid, $sensor, '1', '0', $type, $cmdvirt->execCmd() );
            log::add('mySensors', 'debug', 'Valeur virtuelle transmise');
          } else {
            //echo "Virtuel KO";
            //echo $cmdlogic->getCmdValue();
            log::add('mySensors', 'error', 'Valeur virtuelle non définie' . $cmdlogic->getConfiguration('value'));
          }
        } else {
          //echo $cmdlogic->execCmd();
          mySensors::sendCommand( $gateway, $nodeid, $sensor, '1', '0', $type, $cmdlogic->execCmd() );
          log::add('mySensors', 'debug', 'Valeur de capteur transmise');
        }
      }else{
        //echo "Valeur KO";
        log::add('mySensors', 'error', 'Valeur non définie');
      }
      $cmdlogic->event($value);
    }
  }

  public static function getNextSensorId($gateway) {
    if (config::byKey('include_mode','mySensors') == 1) {
      $id = 1;
      $limit = 254;
      while ($id < 255) {
        $exist = 0;
        foreach( self::byType( 'mySensors' ) as $elogic) {
          if ($elogic->getConfiguration('nodeid') == $id) {
            $exist = 1;
          }
        }
        if ($exist == 0) {
          break;
        } else {
          $id++;
        }
      }
      //doit cibler seulement la bonne gateway avec sendToController
      mySensors::sendCommand( $gateway, '255', '255', '3', '0', '4', $id );
    }
  }

  public static function saveValue($gateway, $nodeid,$sensor,$type, $value) {
    $cmdId = 'Sensor'.$sensor;
    $elogic = self::byLogicalId($nodeid, 'mySensors');
    if (is_object($elogic)) {
      $elogic->setStatus('lastCommunication', date('Y-m-d H:i:s'));
      $elogic->save();
      $cmdlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$cmdId);
      if (is_object($cmdlogic)) {
        $cmdlogic->setConfiguration('value', $value);
        $cmdlogic->setConfiguration('sensorType', $type);
        $cmdlogic->save();
        $cmdlogic->event($value);
      }
    }
  }

  public static function saveBatteryLevel($gateway, $nodeid, $value) {
    $elogic = self::byLogicalId($nodeid, 'mySensors');
    if (is_object($elogic)) {
      $elogic->setConfiguration('battery',$value);
      $elogic->batteryStatus($value);
      $elogic->setStatus('lastCommunication', date('Y-m-d H:i:s'));
      $elogic->save();
    }

  }

  public static function saveSketchNameEvent($gateway, $nodeid, $value) {
    if (config::byKey('include_mode','mySensors') == 1) {
      $elogic = self::byLogicalId($nodeid, 'mySensors');
      if (is_object($elogic)) {
        if ( $elogic->getConfiguration('SketchName', '') != $value ) {
          $elogic->setConfiguration('SketchName',$value);
          //si le sketch a changé sur le node, alors on set le nom avec le sketch
          $elogic->setName($value.' - '.$nodeid);
          $elogic->save();
        }
      }
      else {
        $mys = new mySensors();
        $mys->setEqType_name('mySensors');
        $mys->setLogicalId($nodeid);
        $mys->setConfiguration('nodeid', $nodeid);
        $mys->setConfiguration('gateway', $gateway);
        $mys->setConfiguration('SketchName',$value);
        $mys->setName($value.' - '.$nodeid);
        $mys->setIsEnable(true);
        $mys->save();
        event::add('mySensors::includeDevice', $mys->getId());
      }
    }
  }

  public static function saveGateway($gateway, $status) {
    config::save('gateway', $status,  'mySensors');
  }

  public static function saveSketchVersion($gateway, $nodeid, $value) {
    $elogic = self::byLogicalId($nodeid, 'mySensors');
    sleep(1);
    if (is_object($elogic)) {
      if ( $elogic->getConfiguration('SketchVersion', '') != $value ) {
        $elogic->setConfiguration('SketchVersion',$value);
        $elogic->save();
      }
    }
  }

  public static function saveLibVersion($gateway, $nodeid, $value) {
    sleep(1);
    if ($nodeid == '0') {
      config::save('gateLib', $value,  'mySensors');
      log::add('mySensors', 'info', 'Gateway Lib ' . $value . config::byKey('gateLib','mySensors'));
    }
    $elogic = self::byLogicalId($nodeid, 'mySensors');
    if (is_object($elogic)) {
      if ( $elogic->getConfiguration('LibVersion', '') != $value ) {
        $elogic->setConfiguration('LibVersion',$value);
        $elogic->save();
      }
    }
  }

  public static function saveSensor($gateway, $nodeid, $sensor, $value) {
    sleep(1);
    //exemple : 0 => array('S_DOOR','Ouverture','door','binary','','','1',),
    $name = self::$_dico['S'][$value][1];
    if ($name == false ) {
      $name = 'UNKNOWN';
    }
    $unite = self::$_dico['S'][$value][4];
    $sType = $value;
    $info = self::$_dico['S'][$value][3];
    $widget = self::$_dico['S'][$value][2];
    $history = self::$_dico['S'][$value][5];
    $visible = self::$_dico['S'][$value][6];
    $generictype = self::$_dico['S'][$value][7];
    $cmdId = 'Sensor'.$sensor;
    $elogic = self::byLogicalId($nodeid, 'mySensors');
    if (is_object($elogic)) {
      $cmdlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$cmdId);
      if (is_object($cmdlogic)) {
        if ( $cmdlogic->getConfiguration('sensorCategory', '') != $sType ) {
          $cmdlogic->setConfiguration('sensorCategory', $sType);
          $cmdlogic->save();
        }
      }
      else {
        $mysCmd = new mySensorsCmd();
        $cmds = $elogic->getCmd();
        $order = count($cmds);
        $mysCmd->setOrder($order);
        $mysCmd->setConfiguration('sensorCategory', $sType);
        $mysCmd->setConfiguration('sensor', $sensor);
        $mysCmd->setEqLogic_id($elogic->getId());
        $mysCmd->setEqType('mySensors');
        $mysCmd->setLogicalId($cmdId);
        $mysCmd->setType('info');
        $mysCmd->setSubType($info);
        $mysCmd->setName( $name . " " . $sensor );
        $mysCmd->setUnite( $unite );
        $mysCmd->setIsVisible($visible);
        if ($info != 'string') {
          $mysCmd->setIsHistorized($history);
        }
        $mysCmd->setTemplate("mobile",$widget );
        $mysCmd->setTemplate("dashboard",$widget );
        $mysCmd->setDisplay('generic_type',$generictype);
        $mysCmd->save();
      }
      if ($name == 'Relais') {
        $relonId = 'Relais'.$sensor.'On';
        $reloffId = 'Relais'.$sensor.'Off';
        $onlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$relonId);
        $offlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$reloffId);
        $cmdlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$cmdId);
        $cmId = $cmdlogic->getId();
        if (!is_object($offlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '0');
          $mysCmd->setConfiguration('cmdtype', '2');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($reloffId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setTemplate("dashboard","light" );
          $mysCmd->setTemplate("mobile","light" );
          $mysCmd->setDisplay('parameters',array('displayName' => 1));
          $mysCmd->setName( "Off ". $sensor );
          $mysCmd->setDisplay('generic_type','ENERGY_OFF');
          $mysCmd->save();
        }
        if (!is_object($onlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '1');
          $mysCmd->setConfiguration('cmdtype', '2');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($relonId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setTemplate("dashboard","light" );
          $mysCmd->setTemplate("mobile","light" );
          $mysCmd->setDisplay('parameters',array('displayName' => 1));
          $mysCmd->setName( "On " . $sensor );
          $mysCmd->setDisplay('generic_type','ENERGY_ON');
          $mysCmd->save();
        }

      }
      if ($name == 'Verrou') {
        $relonId = 'Verrou'.$sensor.'On';
        $reloffId = 'Verrou'.$sensor.'Off';
        $onlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$relonId);
        $offlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$reloffId);
        $cmdlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$cmdId);
        $cmId = $cmdlogic->getId();
        if (!is_object($offlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '1');
          $mysCmd->setConfiguration('cmdtype', '36');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($reloffId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setTemplate("dashboard","lock" );
          $mysCmd->setTemplate("mobile","lock" );
          $mysCmd->setDisplay('parameters',array('displayName' => 1));
          $mysCmd->setName( "Off ". $sensor );
          $mysCmd->setDisplay('generic_type','LOCK_CLOSE');
          $mysCmd->save();
        }
        if (!is_object($onlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '0');
          $mysCmd->setConfiguration('cmdtype', '36');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($relonId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setTemplate("dashboard","lock" );
          $mysCmd->setTemplate("mobile","lock" );
          $mysCmd->setDisplay('parameters',array('displayName' => 1));
          $mysCmd->setName( "On " . $sensor );
          $mysCmd->setDisplay('generic_type','LOCK_OPEN');
          $mysCmd->save();
        }

      }
      if ($name == 'Variateur') {
        $dimmerId = 'Dimmer'.$sensor;
        $dimlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$dimmerId);
        $cmdlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$cmdId);
        $cmId = $cmdlogic->getId();
        if (!is_object($dimlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '#slider#');
          $mysCmd->setConfiguration('cmdtype', '3');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($dimmerId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('slider');
          $mysCmd->setValue($cmId);
          $mysCmd->setTemplate("dashboard","light" );
          $mysCmd->setTemplate("mobile","light" );
          $mysCmd->setDisplay('parameters',array('displayName' => 1));
          $mysCmd->setName( "Set " . $sensor );
          $mysCmd->setDisplay('generic_type','ENERGY_SLIDER');
          $mysCmd->save();
        }
        $relonId = 'Dimmer'.$sensor.'On';
        $reloffId = 'Dimmer'.$sensor.'Off';
        $onlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$relonId);
        $offlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$reloffId);
        $cmdlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$cmdId);
        $cmId = $cmdlogic->getId();
        if (!is_object($offlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '0');
          $mysCmd->setConfiguration('cmdtype', '3');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($reloffId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setName( "Off Dimmer ". $sensor );
          $mysCmd->setDisplay('generic_type','ENERGY_OFF');
          $mysCmd->save();
        }
        if (!is_object($onlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '100');
          $mysCmd->setConfiguration('cmdtype', '3');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($relonId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setName( "On Dimmer " . $sensor );
          $mysCmd->setDisplay('generic_type','ENERGY_ON');
          $mysCmd->save();
        }
      }
      if ($name == 'Inrarouge') {
        $dimmerId = 'EnvoiIR'.$sensor;
        $dimlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$dimmerId);
        $cmdlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$cmdId);
        $cmId = $cmdlogic->getId();
        if (!is_object($dimlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '');
          $mysCmd->setConfiguration('cmdtype', '32');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($dimmerId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('message');
          $mysCmd->setValue($cmId);
          $mysCmd->setName( "Envoi IR " . $sensor );
          $mysCmd->save();
        }
      }
      if ($name == 'Radiateur') {
        $relonId = 'Radiateur'.$sensor.'On';
        $reloffId = 'Radiateur'.$sensor.'Off';
        $onlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$relonId);
        $offlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$reloffId);
        $cmdlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$cmdId);
        $cmId = $cmdlogic->getId();
        if (!is_object($offlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '0');
          $mysCmd->setConfiguration('cmdtype', '22');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($reloffId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setName( "Off Radiateur ". $sensor );
          $mysCmd->setDisplay('generic_type','HEATING_OFF');
          $mysCmd->save();
        }
        if (!is_object($onlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '1');
          $mysCmd->setConfiguration('cmdtype', '22');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($relonId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setName( "On Radiateur " . $sensor );
          $mysCmd->setDisplay('generic_type','HEATING_ON');
          $mysCmd->save();
        }
      }
      if ($name == 'Store') {
        $relonId = 'Store'.$sensor.'Up';
        $reloffId = 'Store'.$sensor.'Down';
        $relstopId = 'Store'.$sensor.'Stop';
        $onlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$relonId);
        $offlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$reloffId);
        $stoplogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$relstopId);
        $cmdlogic = mySensorsCmd::byEqLogicIdAndLogicalId($elogic->getId(),$cmdId);
        $cmId = $cmdlogic->getId();
        if (!is_object($offlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '1');
          $mysCmd->setConfiguration('cmdtype', '29');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($reloffId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setName( "Relever Store ". $sensor );
          $mysCmd->setDisplay('generic_type','FLAP_UP');
          $mysCmd->save();
        }
        if (!is_object($onlogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '1');
          $mysCmd->setConfiguration('cmdtype', '30');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($relonId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setName( "Baisser Store " . $sensor );
          $mysCmd->setDisplay('generic_type','FLAP_DOWN');
          $mysCmd->save();
        }
        if (!is_object($stoplogic)) {
          $mysCmd = new mySensorsCmd();
          $cmds = $elogic->getCmd();
          $order = count($cmds);
          $mysCmd->setOrder($order);
          $mysCmd->setConfiguration('cmdCommande', '1');
          $mysCmd->setConfiguration('request', '1');
          $mysCmd->setConfiguration('cmdtype', '31');
          $mysCmd->setConfiguration('sensor', $sensor);
          $mysCmd->setEqLogic_id($elogic->getId());
          $mysCmd->setEqType('mySensors');
          $mysCmd->setLogicalId($relstopId);
          $mysCmd->setType('action');
          $mysCmd->setSubType('other');
          $mysCmd->setValue($cmId);
          $mysCmd->setName( "Arrêt Store " . $sensor );
          $mysCmd->setDisplay('generic_type','FLAP_STOP');
          $mysCmd->save();
        }
      }
    }
  }
}

class mySensorsCmd extends cmd {
  public function execute($_options = null) {
    switch ($this->getType()) {
      case 'info' :
      return $this->getConfiguration('value');
      break;
      case 'action' :
      $request = $this->getConfiguration('request');
      switch ($this->getSubType()) {
        case 'slider':
        $request = str_replace('#slider#', $_options['slider'], $request);
        break;
        case 'color':
        $request = str_replace('#color#', $_options['color'], $request);
        break;
        case 'message':
        if ($_options != null)  {
          $replace = array('#title#', '#message#');
          $replaceBy = array($_options['title'], $_options['message']);
          if ( $_options['title'] == '') {
            throw new Exception(__('Le sujet ne peuvent être vide', __FILE__));
          }
          $request = str_replace($replace, $replaceBy, $request);
        }
        else
        $request = 1;
        break;
        default : $request == null ?  1 : $request;
      }

      $eqLogic = $this->getEqLogic();

      $result = mySensors::sendCommand(
        $eqLogic->getConfiguration('gateway'),
        $eqLogic->getConfiguration('nodeid'),
        $this->getConfiguration('sensor'),
        $this->getConfiguration('cmdCommande'),
        1,
        $this->getConfiguration('cmdtype'),
        $request
      );

      $result = $request;
      return $result;
    }
    return true;
  }
}

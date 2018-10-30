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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  include_file('desktop', '404', 'php');
  die();
}
?>


<form class="form-horizontal">
  <div class="form-group">
    <fieldset>

      <div id="div_local" class="form-group">
        <label class="col-lg-4 control-label">{{Gateway série maître}} :</label>
        <div class="col-lg-4">
          <select id="select_port" style="margin-top:5px" class="configKey form-control" data-l1key="nodeGateway">
            <option value="none">{{Aucune}}</option>
            <option value="network">{{Réseau client}}</option>
            <option value="networkServer">{{Réseau serveur}}</option>
            <?php
            foreach (jeedom::getUsbMapping('', true) as $name => $value) {
              echo '<option value="' . $name . '">' . $name . ' (' . $value . ')</option>';
            }
            ?>
          </select>

        </div>
      </div>

      <div id="netgate" class="form-group">
        <label class="col-lg-4 control-label" id="clientMode">{{Gateway réseau}} :</label>
        <label class="col-lg-4 control-label" id="serverMode">{{Interface d'écoute}} :</label>
        <div class="col-lg-4 div_network">
          <input class="configKey form-control" data-l1key="network" placeholder="192.168.1.1:5003"/>
        </div>
      </div>
    </fieldset>
  </div>
</form>
<?php
if (config::byKey('jeeNetwork::mode') == 'master') {
  foreach (jeeNetwork::byPlugin('mySensors') as $jeeNetwork) {
    ?>
    <form class="form-horizontal slaveConfig" data-slave_id="<?php echo $jeeNetwork->getId(); ?>">
      <fieldset>
        <div class="form-group">
          <label class="col-lg-4 control-label">{{Gateway série esclave}} <?php echo $jeeNetwork->getName() ?></label>
          <div class="col-lg-4">
            <select class="slaveConfigKey form-control" data-l1key="nodeGateway">
              <option value="none">{{Aucune}}</option>
              <?php
              foreach ($jeeNetwork->sendRawRequest('jeedom::getUsbMapping', array('gpio' => true)) as $name => $value) {
                echo '<option value="' . $name . '">' . $name . ' (' . $value . ')</option>';
              }
              ?>
            </select>
          </div>
        </div>

      </fieldset>
    </form>
    <?php
  }
}
?>

<script>

$( "#select_port" ).change(function() {
  let mode = $("#select_port option:selected").val();
  if ( mode == "network"){
    $("#netgate").show();
    $("#clientMode").show();
    $("#serverMode").hide();
  } else if (mode == "networkServer"){
    $("#netgate").show();
    $("#clientMode").hide();
    $("#serverMode").show();
  } else {
    $("#netgate").hide();
  }
});

</script>

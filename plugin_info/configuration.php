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
        <label class="col-lg-4 control-label">{{Gateway série}} :</label>
        <div class="col-lg-4">
          <select id="select_port" style="margin-top:5px" class="configKey form-control" data-l1key="nodeGateway">
            <option value="none">{{Aucune}}</option>
            <?php
            foreach (jeedom::getUsbMapping('', true) as $name => $value) {
              echo '<option value="' . $name . '">' . $name . ' (' . $value . ')</option>';
            }
            ?>
          </select>

          <input id="manual_port" class="configKey form-control" data-l1key="nodeAdress" style="margin-top:5px;display:none" placeholder="ex: 192.168.1.1:5003"/>
        </div>
      </div>

    </fieldset>
  </form>
  <?php
  if (config::byKey('jeeNetwork::mode') == 'master') {
    foreach (jeeNetwork::byPlugin('mySensors') as $jeeNetwork) {
      ?>
      <form class="form-horizontal slaveConfig" data-slave_id="<?php echo $jeeNetwork->getId(); ?>">
        <fieldset>
          <legend>{{MySensors sur l'esclave}} <?php echo $jeeNetwork->getName() ?></legend>
          <div class="form-group">
            <label class="col-lg-4 control-label">{{Gateway série}}</label>
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
    $( "#select_port option:selected" ).each(function() {
      if ($("#select_port option:selected").val() == "network"){
        $("#manual_port").show();
      }
      else {
        $("#manual_port").hide();
      }
    });
  });


  </script>
</div>
</fieldset>
</form>

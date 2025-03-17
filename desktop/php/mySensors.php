<?php

if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'mySensors');
$eqLogics = eqLogic::byType('mySensors');
$dico = mySensors::$_dico;
foreach($dico['S'] as &$value){
  $value = __($value[1],__FILE__);
}
foreach($dico['N'] as &$value){
  $value = __($value,__FILE__);
}
foreach($dico['C'] as &$value){
  $value = __($value,__FILE__);
}
sendVarToJS('mySensorDico', $dico);
$state = config::byKey('include_mode', 'mySensors');
echo '<div id="div_inclusionAlert"></div>';
if ($state == 1) {
  echo '<div class="alert jqAlert alert-warning" id="div_inclusionAlert" style="margin : 0px 5px 15px 15px; padding : 7px 35px 7px 15px;">{{Vous êtes en mode inclusion. Cliquez à nouveau sur le bouton d\'inclusion pour sortir de ce mode}}</div>';
}

?>

<div class="row row-overflow">
  <div class="col-lg-2 col-sm-3 col-sm-4" id="hidCol" style="display: none;">
    <div class="bs-sidebar">
      <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
        <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
        <?php
        foreach ($eqLogics as $eqLogic) {
          echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
        }
        ?>
      </ul>
    </div>
  </div>

  <div class="col-lg-12 eqLogicThumbnailDisplay" id="listCol">
    <legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
    <div class="eqLogicThumbnailContainer logoPrimary">

      <?php
      if ($state == 1) {
        ?>
          <div class="cursor logoPrimary changeIncludeState card" data-state="0">';
          <i class="fas fa-plus-circle"></i>
          <br/>
          <span>{{Arrêter l\'inclusion}}</span>
        <?php
      } else {
        ?>
          <div class="cursor logoSecondary changeIncludeState card" data-state="1">';
          <i class="fas fa-plus-circle"></i>
          <br/>
          <span>{{Inclusion}}</span>
        <?php
      }
      ?>

      </div>
      <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
        <i class="fas fa-wrench"></i>
        <br/>
        <span>{{Configuration}}</span>
      </div>
      <div class="cursor logoSecondary" id="bt_healthmySensors">
          <i class="fas fa-medkit"></i>
        <br/>
        <span>{{Santé}}</span>
      </div>

    </div>

    <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />

    <legend><i class="fas fa-home" id="butCol"></i> {{Mes Equipements}}</legend>
    <div class="eqLogicThumbnailContainer">
      <?php
      foreach ($eqLogics as $eqLogic) {
        $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
        echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff ; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
        echo "<center>";
        echo '<img src="plugins/mySensors/plugin_info/mySensors_icon.png" height="105" width="95" />';
        echo "</center>";
        echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
        echo '</div>';
      }
      ?>
    </div>
  </div>


<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
 <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a>
 <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
 <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a>
 <ul class="nav nav-tabs" role="tablist">
  <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
  <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
  <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
</ul>
<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
  <div role="tabpanel" class="tab-pane active" id="eqlogictab">
          <form class="form-horizontal">
            <fieldset>
              <div class="form-group">
                <label class="col-sm-3 control-label">{{Nom du Node}}</label>
                <div class="col-sm-3">
                  <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                  <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement mySensors}}"/>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                <div class="col-sm-3">
                  <select class="form-control eqLogicAttr" data-l1key="object_id">
                    <option value="">{{Aucun}}</option>
                    <?php
                    foreach (jeeObject::all() as $object) {
                      echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">{{Catégorie}}</label>
                <div class="col-sm-8">
                  <?php
                  foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                    echo '<label class="checkbox-inline">';
                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                    echo '</label>';
                  }
                  ?>

                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label" ></label>
                <div class="col-sm-8">
                  <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
                  <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">{{Délai max entre 2 messages}}</label>
                <div class="col-sm-3">
                  <input class="eqLogicAttr form-control" data-l1key="timeout" placeholder="Délai maximum autorisé entre 2 messages (en mn)"/>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">{{Type de piles}}</label>
                <div class="col-sm-3">
                  <input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="battery_type" placeholder="Doit être indiqué sous la forme : 3x AA"/>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">{{Commentaire}}</label>
                <div class="col-sm-3">
                  <textarea class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="commentaire" ></textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">{{ID du Node}}</label>
                <div class="col-sm-3">
                  <span id="nodeId" class="eqLogicAttr" data-l1key="configuration" data-l2key="nodeid"></span>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">{{Version mySensors}}</label>
                <div class="col-sm-3">
                  <span class="eqLogicAttr" data-l1key="configuration" data-l2key="LibVersion"></span>
                </div>

              </div>

              <div id="infoSketch" class="form-group">
                <label class="col-sm-3 control-label">{{Nom du Sketch}}</label>
                <div class="col-sm-3">
                  <span class="eqLogicAttr" data-l1key="configuration" data-l2key="SketchName"></span>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">{{Version du Sketch}}</label>
                <div class="col-sm-3">
                  <span class="eqLogicAttr" data-l1key="configuration" data-l2key="SketchVersion"></span>
                </div>

              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">{{Dernière Activité}}</label>
                <div class="col-sm-3">
                  <span class="eqLogicAttr" data-l1key="configuration" data-l2key="updatetime"></span>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">{{Redémarrer le Node}}</label>
                <div class="col-sm-3">
                  <a class="btn btn-default" id="bt_restartEq"><i class="fas fa-power-off"></i> Redémarrer</a>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">{{Catégorie du noeud}}</label>
                <div class="col-sm-3">
                  <select id="sel_icon" class="form-control eqLogicAttr" data-l1key="configuration" data-l2key="icone">
                    <option value="">{{Aucun}}</option>
                    <option value="433">{{RF433}}</option>
                    <option value="barometre">{{Baromètre}}</option>
                    <option value="boiteauxlettres">{{Boite aux Lettres}}</option>
                    <option value="chauffage">{{Chauffage}}</option>
                    <option value="compteur">{{Compteur}}</option>
                    <option value="contact">{{Contact}}</option>
                    <option value="feuille">{{Culture}}</option>
                    <option value="custom">{{Custom}}</option>
                    <option value="dimmer">{{Dimmer}}</option>
                    <option value="energie">{{Energie}}</option>
                    <option value="garage">{{Garage}}</option>
                    <option value="humidity">{{Humidité}}</option>
                    <option value="humiditytemp">{{Humidité et Température}}</option>
                    <option value="hydro">{{Hydrométrie}}</option>
                    <option value="ir2">{{Infra Rouge}}</option>
                    <option value="jauge">{{Jauge}}</option>
                    <option value="light">{{Luminosité}}</option>
                    <option value="meteo">{{Météo}}</option>
                    <option value="motion">{{Mouvement}}</option>
                    <option value="multisensor">{{Multisensor}}</option>
                    <option value="prise">{{Prise}}</option>
                    <option value="relay">{{Relais}}</option>
                    <option value="rfid">{{RFID}}</option>
                    <option value="teleinfo">{{Téléinfo}}</option>
                    <option value="temp">{{Température}}</option>
                    <option value="thermostat">{{Thermostat}}</option>
                    <option value="volet">{{Volet}}</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <div style="text-align: center">
                  <img name="icon_visu" src="" width="160" height="200"/>
                </div>
              </div>


            </fieldset>
          </form>
        </div>
        <div role="tabpanel" class="tab-pane" id="commandtab">

          <form class="form-horizontal">
            <fieldset>
              <div class="form-actions">
                <a class="btn btn-success btn-sm cmdAction" id="bt_addmySensorsInfo"><i class="fas fa-plus-circle"></i> {{Ajouter une commande info}}</a>
                <a class="btn btn-success btn-sm cmdAction" id="bt_addmySensorsAction"><i class="fas fa-plus-circle"></i> {{Ajouter une commande action}}</a>
              </div>
            </fieldset>
          </form>
          <br />

          <script>
          $('#bt_restartEq').on('click', function () {
            var nodeId = $('#nodeId').text();
            var gateway = $('#gateway').text();
            $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/mySensors/core/ajax/mySensors.ajax.php", // url du fichier php
            data: {
              action: "restartEq",
              node: nodeId,
              gateway: gateway,
            },
            dataType: 'json',
            error: function (request, status, error) {
              handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
              $('#div_alert').showAlert({message: data.result, level: 'danger'});
              return;
            }
            $('#div_alert').showAlert({message: 'Le node a été relancé', level: 'success'});
          }
        });
      });
      </script>
      <table id="table_cmd" class="table table-bordered table-condensed">
        <thead>
          <tr>
            <th style="width: 50px;">#</th>
            <th style="width: 150px;">{{Nom}}</th>
            <th style="width: 80px;">{{Type Jeedom}}</th>
            <th style="width: 80px;">{{ID Capteur}}</th>
            <th style="width: 150px;">{{Type Capteur}}</th>
            <th style="width: 150px;">{{Valeur}}</th>
            <th style="width: 100px;">{{Type Donnée}}</th>
            <th style="width: 50px;">{{Unité}}</th>
            <th style="width: 150px;">{{Paramètres}}</th>
            <th style="width: 50px;"></th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>

    </div>
  </div>
</div>
</div>

<?php include_file('desktop', 'mySensors', 'js', 'mySensors'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>

<script>
$( "#sel_icon" ).change(function(){
  var text = 'plugins/mySensors/plugin_info/node_' + $("#sel_icon").val() + '.png';
  //$("#icon_visu").attr('src',text);
  document.icon_visu.src=text;
});
</script>

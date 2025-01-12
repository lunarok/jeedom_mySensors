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
$("#butCol").click(function () {
  $("#hidCol").toggle("slow");
  document.getElementById("listCol").classList.toggle('col-lg-12');
  document.getElementById("listCol").classList.toggle('col-lg-10');
});

$(".li_eqLogic").on('click', function (event) {
  if (event.ctrlKey) {
    var type = $('body').attr('data-page')
    var url = '/index.php?v=d&m=' + type + '&p=' + type + '&id=' + $(this).attr('data-eqlogic_id')
    window.open(url).focus()
  } else {
    jeedom.eqLogic.cache.getCmd = Array();
    if ($('.eqLogicThumbnailDisplay').html() != undefined) {
      $('.eqLogicThumbnailDisplay').hide();
    }
    $('.eqLogic').hide();
    if ('function' == typeof (prePrintEqLogic)) {
      prePrintEqLogic($(this).attr('data-eqLogic_id'));
    }
    if (isset($(this).attr('data-eqLogic_type')) && isset($('.' + $(this).attr('data-eqLogic_type')))) {
      $('.' + $(this).attr('data-eqLogic_type')).show();
    } else {
      $('.eqLogic').show();
    }
    $(this).addClass('active');
    $('.nav-tabs a:not(.eqLogicAction)').first().click()
    $.showLoading()
    jeedom.eqLogic.print({
      type: isset($(this).attr('data-eqLogic_type')) ? $(this).attr('data-eqLogic_type') : eqType,
      id: $(this).attr('data-eqLogic_id'),
      status: 1,
      error: function (error) {
        $.hideLoading();
        $('#div_alert').showAlert({ message: error.message, level: 'danger' });
      },
      success: function (data) {
        $('body .eqLogicAttr').value('');
        if (isset(data) && isset(data.timeout) && data.timeout == 0) {
          data.timeout = '';
        }
        $('body').setValues(data, '.eqLogicAttr');
        if ('function' == typeof (printEqLogic)) {
          printEqLogic(data);
        }
        if ('function' == typeof (addCmdToTable)) {
          $('.cmd').remove();
          for (var i in data.cmd) {
            addCmdToTable(data.cmd[i]);
          }
        }
        $('body').delegate('.cmd .cmdAttr[data-l1key=type]', 'change', function () {
          jeedom.cmd.changeType($(this).closest('.cmd'));
        });

        $('body').delegate('.cmd .cmdAttr[data-l1key=subType]', 'change', function () {
          jeedom.cmd.changeSubType($(this).closest('.cmd'));
        });
        addOrUpdateUrl('id', data.id);
        $.hideLoading();
        modifyWithoutSave = false;
        setTimeout(function () {
          modifyWithoutSave = false;
        }, 1000)
      }
    });
  }
  return false;
});

$("#bt_addmySensorsInfo").on('click', function (event) {
  var _cmd = { type: 'info' };
  addCmdToTable(_cmd);
});

$("#bt_addmySensorsAction").on('click', function (event) {
  var _cmd = { type: 'action' };
  addCmdToTable(_cmd);
});

$('#bt_healthmySensors').on('click', function () {
  $('#md_modal').dialog({ title: "{{Santé mySensors}}" });
  $('#md_modal').load('index.php?v=d&plugin=mySensors&modal=health').dialog('open');
});

$('.changeIncludeState').on('click', function () {
  var el = $(this);
  jeedom.config.save({
    plugin: 'mySensors',
    configuration: { include_mode: el.attr('data-state') },
    error: function (error) {
      $('#div_alert').showAlert({ message: error.message, level: 'danger' });
    },
    success: function () {
      if (el.attr('data-state') == 1) {
        $.hideAlert();
        $('.changeIncludeState:not(.card)').removeClass('btn-default').addClass('btn-success');
        $('.changeIncludeState').attr('data-state', 0);
        $('.changeIncludeState.card').css('background-color', '#8000FF');
        $('.changeIncludeState.card span center').text('{{Arrêter l\'inclusion}}');
        $('.changeIncludeState:not(.card)').html('<i class="fas fa-sign-in fa-rotate-90"></i> {{Arreter inclusion}}');
        $('#div_inclusionAlert').showAlert({ message: '{{Vous etes en mode inclusion. Recliquez sur le bouton d\'inclusion pour sortir de ce mode}}', level: 'warning' });
      } else {
        $.hideAlert();
        $('.changeIncludeState:not(.card)').addClass('btn-default').removeClass('btn-success btn-danger');
        $('.changeIncludeState').attr('data-state', 1);
        $('.changeIncludeState:not(.card)').html('<i class="fas fa-sign-in fa-rotate-90"></i> {{Mode inclusion}}');
        $('.changeIncludeState.card span center').text('{{Mode inclusion}}');
        $('.changeIncludeState.card').css('background-color', '#ffffff');
        $('#div_inclusionAlert').hideAlert();
      }
    }
  });
});

$('body').on('mySensors::includeDevice', function (_event, _options) {
  //console.log('on mySensors::includeDevice', _event, _options);
  var counter = 11,
    timeout = setTimeout(includeDevice, 1000);
  function includeDevice() {
    counter--;
    $.hideAlert();
    $('#div_inclusionAlert').showAlert({ message: '{{Nouveau noeud en cours d\'inclusion, veuillez patientez ' + counter + ' s}}', level: 'warning' });
    if (counter == 0) {
      if (_options == '') {
        window.location.reload();
      } else {
        window.location.href = 'index.php?v=d&p=mySensors&m=mySensors&id=' + _options;
      }
    } else {
      timeout = setTimeout(includeDevice, 1000);
    }
  }
});

$("#table_cmd").delegate(".listEquipementInfo", 'click', function () {
  var el = $(this);
  jeedom.cmd.getSelectModal({ cmd: { type: 'info' } }, function (result) {
    var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
    calcul.atCaret('insert', result.human);
  });
});

$("#table_cmd").delegate(".listEquipementAction", 'click', function () {
  var el = $(this);
  var subtype = $(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').value();
  jeedom.cmd.getSelectModal({ cmd: { type: 'action', subType: subtype } }, function (result) {
    var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.attr('data-input') + ']');
    calcul.atCaret('insert', result.human);
  });
});

$("#table_cmd").sortable({ axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true });

function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
    var _cmd = { configuration: {} };
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {};
  }

  if (init(_cmd.type) == 'info') {
    var disabled = (init(_cmd.configuration.virtualAction) == '1') ? 'disabled' : '';
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id"></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom du capteur}}"></td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="sensor">';
    tr += '</td>';
    tr += '<td>';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="sensorCategory">';
    $.each(mySensorDico['S'], function (index, item) {
      tr += '<option value="' + index + '">' + index + ' - ' + item + '</option>';
    })
    tr += '</select>';
    tr += '</td>';
    tr += '<td>';
    if (isset(_cmd.configuration.sensorCategory) && _cmd.configuration.sensorCategory == "23") {
      tr += '<span style="width : 40%;display : inline-block;">{{Valeur}} :</span>';
      tr += '<textarea class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="value"  style="height : 33px;width : 100%;display : inline-block;" ' + disabled + ' placeholder="{{Valeur}}"></textarea></br>';
      tr += '<a class="btn btn-default cursor listEquipementInfo btn-sm" data-input="value"><i class="fas fa-list-alt "></i> {{Rechercher équipement}}</a><br/>';
    }
    tr += '</td>';
    tr += '<td>';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="sensorType">';
    $.each(mySensorDico['N'], function (index, item) {
      tr += '<option value="' + index + '">' + index + ' - ' + item + '</option>';
    })
    tr += '</select>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite">';
    tr += '</td>';
    tr += '<td>';
    if (_cmd.subType == 'numeric' || _cmd.subType == 'binary') {
      tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
    }
    if (_cmd.subType == 'binary') {
      tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="display" data-l2key="invertBinary" checked/>{{Inverser}}</label></span> ';
    }
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    if (_cmd.subType == 'numeric') {
      tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width : 40px;"> ';
      tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width : 40px;">';
    }
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
  }

  if (init(_cmd.type) == 'action') {
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id"></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<div class="row">';
    tr += '<div class="col-lg-6">';
    tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> Icone</a>';
    tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
    tr += '</div>';
    tr += '<div class="col-lg-6">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name">';
    tr += '</div>';
    tr += '</div>';
    tr += '<select class="cmdAttr form-control tooltips input-sm" data-l1key="value" style="display : none;margin-top : 5px;" title="{{La valeur de la commande vaut par dÃ©faut la commande}}">';
    tr += '<option value="">Aucune</option>';
    tr += '</select>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled style="margin-bottom : 5px;" />';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '" style=""></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="sensor">';
    tr += '</td>';
    tr += '<td>';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="cmdCommande">';
    $.each(mySensorDico['C'], function (index, item) {
      tr += '<option value="' + index + '">' + index + ' - ' + item + '</option>';
    })
    tr += '</select>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="request">';
    tr += '</td><td>';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="cmdtype">';
    $.each(mySensorDico['N'], function (index, item) {
      tr += '<option value="' + index + '">' + index + ' - ' + item + '</option>';
    })
    tr += '</select></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '</td><td>';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';

    $('#table_cmd tbody').append(tr);
    var tr = $('#table_cmd tbody tr').last()
    jeedom.eqLogic.buildSelectCmd({
      id: $('.eqLogicAttr[data-l1key=id]').value(),
      filter: { type: 'info' },
      error: function (error) {
        $('#div_alert').showAlert({ message: error.message, level: 'danger' });
      },
      success: function (result) {
        tr.find('.cmdAttr[data-l1key=value]').append(result);
        tr.setValues(_cmd, '.cmdAttr');
        jeedom.cmd.changeType(tr, init(_cmd.subType));
      }
    });

  }
}

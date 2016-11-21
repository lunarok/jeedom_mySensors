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

$("#bt_addmySensorsInfo").on('click', function(event) {
    var _cmd = {type: 'info'};
    addCmdToTable(_cmd);
});

$("#bt_addmySensorsAction").on('click', function(event) {
    var _cmd = {type: 'action'};
    addCmdToTable(_cmd);
});

$('#bt_healthmySensors').on('click', function () {
    $('#md_modal').dialog({title: "{{Santé mySensors}}"});
    $('#md_modal').load('index.php?v=d&plugin=mySensors&modal=health').dialog('open');
});

$('.changeIncludeState').on('click', function () {
    var el = $(this);
    jeedom.config.save({
        plugin : 'mySensors',
        configuration: {include_mode: el.attr('data-state')},
        error: function (error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function () {
            if (el.attr('data-state') == 1) {
                $.hideAlert();
                $('.changeIncludeState:not(.card)').removeClass('btn-default').addClass('btn-success');
                $('.changeIncludeState').attr('data-state', 0);
                $('.changeIncludeState.card').css('background-color','#8000FF');
                $('.changeIncludeState.card span center').text('{{Arrêter l\'inclusion}}');
                $('.changeIncludeState:not(.card)').html('<i class="fa fa-sign-in fa-rotate-90"></i> {{Arreter inclusion}}');
                $('#div_inclusionAlert').showAlert({message: '{{Vous etes en mode inclusion. Recliquez sur le bouton d\'inclusion pour sortir de ce mode}}', level: 'warning'});
            } else {
                $.hideAlert();
                $('.changeIncludeState:not(.card)').addClass('btn-default').removeClass('btn-success btn-danger');
                $('.changeIncludeState').attr('data-state', 1);
                $('.changeIncludeState:not(.card)').html('<i class="fa fa-sign-in fa-rotate-90"></i> {{Mode inclusion}}');
                $('.changeIncludeState.card span center').text('{{Mode inclusion}}');
                $('.changeIncludeState.card').css('background-color','#ffffff');
                $('#div_inclusionAlert').hideAlert();
            }
        }
    });
});

$('body').on('mySensors::includeDevice', function (_event,_options) {
    //console.log('on mySensors::includeDevice', _event, _options);
    var counter = 11,
    timeout = setTimeout(includeDevice, 1000);
    function includeDevice() {
        counter--;
        $.hideAlert();
        $('#div_inclusionAlert').showAlert({message: '{{Nouveau noeud en cours d\'inclusion, veuillez patientez ' + counter + ' s}}', level: 'warning'});
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

function sortDico(obj) {
    var arr = [];
    $.each(obj, function(index, item) {
        arr.push({id: index, item: item});
    });
    arr.sort(function(a, b) {
        return a.item < b.item ? -1 : a.item > b.item ? 1 : 0;
    });
    var select = '';
    arr.forEach(function(item) {
        select += '<option value="' + item.id + '">' + item.id + ' - ' + item.item + '</option>';
    });
    return select;
}
var dicos = {
    C: sortDico(mySensorDico.C),
    N: sortDico(mySensorDico.N),
    S: sortDico(mySensorDico.S)
};

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

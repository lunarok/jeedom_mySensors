var net = require('net');
var fs = require('fs');
var request = require('request');

var urlJeedom = '';
var gwAddress = '';
var type = '';
var appendedString="";

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

process.argv.forEach(function(val, index, array) {
	switch ( index ) {
		case 2 : urlJeedom = val; break;
		case 3 : gateway = val; break;
		case 4 : gwAddress = val; break;
		case 5 : type = val; break;
		case 6 : log = val; break;
	}
});

urlJeedom = urlJeedom + '&gateway=' + gateway;

const BROADCAST_ADDRESS				= 255;
const NODE_SENSOR_ID				= 255;

const C_PRESENTATION				= 0;
const C_SET							= 1;
const C_REQ							= 2;
const C_INTERNAL					= 3;
const C_STREAM						= 4;

const I_BATTERY_LEVEL				= 0;
const I_TIME						= 1;
const I_VERSION						= 2;
const I_ID_REQUEST					= 3;
const I_ID_RESPONSE					= 4;
const I_INCLUSION_MODE				= 5;
const I_CONFIG						= 6;
const I_PING						= 7;
const I_PING_ACK					= 8;
const I_LOG_MESSAGE					= 9;
const I_CHILDREN					= 10;
const I_SKETCH_NAME					= 11;
const I_SKETCH_VERSION				= 12;
const I_REBOOT						= 13;

function encode(destination, sensor, command, acknowledge, type, payload) {
	var msg = destination.toString(10) + ";" + sensor.toString(10) + ";" + command.toString(10) + ";" + acknowledge.toString(10) + ";" + type.toString(10) + ";";
	if (command == 4) {
		for (var i = 0; i < payload.length; i++) {
			if (payload[i] < 16)
			msg += "0";
			msg += payload[i].toString(16);
		}
	} else {
		msg += payload;
	}
	msg += '\n';
	return msg.toString();
}

function connectJeedom(messagetype, sender, sensor, type, payload) {
	jeeApi = urlJeedom + "&messagetype="+messagetype.toString()+"&sender="+sender.toString()+"&sensor=" + sensor.toString() +"&type=" + type.toString() + "&payload="+payload;
	if (log == 'debug') {console.log((new Date()) + " : " + jeeApi);}
	request(jeeApi, function (error, response, body) {
		if (!error && response.statusCode == 200) {
			//if (log == 'debug') {console.log((new Date()) + " - Return OK from Jeedom");}
		}else{
			console.log((new Date()).toLocaleString(), error);
		}
	});
}

function sendTime(destination, sensor, gw) {
	var payload = new Date().getTime()/1000;
	var td = encode(destination, sensor, C_INTERNAL, "0", I_TIME, payload);
	console.log('-> ' + td.toString());
	gw.write(td);
}

function sendConfig(destination, gw) {
	var td = encode(destination, NODE_SENSOR_ID, C_INTERNAL, "0", I_CONFIG, "M");
	console.log('-> ' + td.toString());
	gw.write(td);
}

function appendData(str, gw) {
    pos=0;
    while (str.charAt(pos) != '\n' && pos < str.length) {
        appendedString=appendedString+str.charAt(pos);
        pos++;
    }
    if (str.charAt(pos) == '\n') {
        rfReceived(appendedString.trim(), gw);
				console.log((new Date()) + " : " + appendedString.trim());
        appendedString="";
    }
    if (pos < str.length) {
        appendData(str.substr(pos+1,str.length-pos-1), gw);
    }
}

function rfReceived(data,gw) {
	if ((data != null) && (data != "")) {
		//LogDate("debug", "-> "  + td.toString() );
		// decoding message
		var datas = data.toString().split(";");
		var sender = +datas[0];
		var sensor = +datas[1];
		var command = +datas[2];
		var ack = +datas[3];
		var type = +datas[4];
		var rawpayload="";
		if (datas[5]) {
			rawpayload = datas[5].trim();
		}
		var payload;
		if (command == C_STREAM) {
			payload = [];
			for (var i = 0; i < rawpayload.length; i+=2)
			payload.push(parseInt(rawpayload.substring(i, i + 2), 16));
		} else {
			payload = rawpayload;
		}
		// decision on appropriate response
		switch (command) {
			case C_PRESENTATION:
			if (sensor == NODE_SENSOR_ID)
			//	saveProtocol(sender, payload); //arduino ou arduino relay
			;
			else
			connectJeedom('saveSensor', sender, sensor, type, payload);
			//connectJeedom('saveLibVersion', sender, sensor, type, payload);
			break;
			case C_SET:
			connectJeedom('saveValue', sender, sensor, type, payload);
			break;
			case C_REQ:
			connectJeedom('getValue', sender, sensor, type, payload);
			break;
			case C_INTERNAL:
			switch (type) {
				case I_BATTERY_LEVEL:
				connectJeedom('saveBatteryLevel', sender, sensor, type, payload);
				break;
				case I_TIME:
				sendTime(sender, sensor, gw);
				break;
				case I_VERSION:
				connectJeedom('saveLibVersion', sender, sensor, type, payload);
				break;
				case I_ID_REQUEST:
				connectJeedom('getNextSensorId', sender, sensor, type, payload);
				break;
				case I_ID_RESPONSE:
				break;
				case I_INCLUSION_MODE:
				break;
				case I_CONFIG:
				sendConfig(sender, gw);
				break;
				case I_PING:
				break;
				case I_PING_ACK:
				break;
				case I_LOG_MESSAGE:
				break;
				case I_CHILDREN:
				break;
				case I_SKETCH_NAME:
				connectJeedom('saveSketchName', sender, sensor, type, payload);
				break;
				case I_SKETCH_VERSION:
				connectJeedom('saveSketchVersion', sender, sensor, type, payload);
				break;
				case I_REBOOT:
				break;
			}
			break;
			case C_STREAM:
/*			switch (type) {
				case ST_FIRMWARE_CONFIG_REQUEST:
				break;
				case ST_FIRMWARE_CONFIG_RESPONSE:
				break;
				case ST_FIRMWARE_REQUEST:
				break;
				case ST_FIRMWARE_RESPONSE:
				break;
				case ST_SOUND:
				break;
				case ST_IMAGE:
				break;
			}
*/			break;
		}
	}
}

console.log((new Date()) + " - Jeedom url : " + urlJeedom + ", gwAddress : " + gwAddress);

var relaunchGw = false,
    attempsGw = 0;
function launchGateway() {
  var gw;
  //pour la connexion avec Jeedom => Node
  var pathsocket = '/tmp/mysensor.sock';
  fs.unlink(pathsocket, function () {
  	var server = net.createServer(function(c) {
		console.log((new Date()) + " - Server connected");
		c.on('error', function(e) {
			console.log((new Date()) + " - Error server disconnected");
		});
		c.on('close', function() {
			console.log((new Date()) + " - Connexion closed");
		});
		c.on('data', function(data) {
			console.log((new Date()) + " - Response: " + data);
			gw.write(data.toString() + '\n');
		});
	});
	server.listen(8019, function(e) {
		console.log((new Date()) + " - server bound on 8019");
	});
  });
  console.log("Connection type " + type);
  if (type == 'serial') {
	var SerialPort = require('serialport');
	gw = new SerialPort(gwAddress);
        //compatibilité avec la nouvelle verion de serialport
        if ( gw.settings.baudRate ){
                gw.settings.baudRate=115200;
        }else{
                gw.settings.baudrate=115200;
        }
  	gw.on('open', function() {
  		console.log((new Date()) + " - connected to serial gateway at " + gwAddress);
  		connectJeedom('saveGateway', 0, 0, 0, 1);
      relaunchGw = true;
      attemptsGw = 0;
	})
	addGatewayBehavior(gw);  
  } else if (type=='networkServer') {
    var tmp = gwAddress.split(':');
  	var serverGw = net.createServer(function(c) {
		console.log((new Date()) + " - Server connected");
		connectJeedom('saveGateway', 0, 0, 0, 1);
		relaunchGw = false;
		attemptsGw = 0;
		c.setEncoding('ascii');
		addGatewayBehavior(c);
		gw=c;
	});
	serverGw.listen(tmp[1], function(e) {
		console.log((new Date()) + " - server bound on "+tmp[1]);
	});


  }else {
    var tmp = gwAddress.split(':');
  	gw = require('net').Socket();
  	gw.connect({port: tmp[1], host: tmp[0]});
  	gw.setEncoding('ascii');
  	gw.on('connect', function() {
  		console.log((new Date()) + " - connected to network gateway at " + gwAddress + ":" + type);
  		connectJeedom('saveGateway', 0, 0, 0, 1);
      relaunchGw = true;
      attemptsGw = 0;
  	})
	addGatewayBehavior(gw);  
  }
  //common task
	connectJeedom('saveGateway', 0, 0, 0, 0);
}

function addGatewayBehavior(gw) {
	gw.on('data', function(rd) {
		if (log == 'debug') {console.log((new Date()) + " : "  + rd);}
		appendData(rd.toString(), gw);
	  }).on('end', function() {
		console.log((new Date()) + " - disconnected from gateway");
		connectJeedom('saveGateway', 0, 0, 0, 0);
	  }).on('error', function(err) {
		if (attempsGw < 5 && relaunchGw) {
		console.log((new Date()) + ' Tentative de reconnexion de la gateway...');
		setTimeout(function() {
			detroyGw(type, gw);
			attempsGw++;
			launchGateway();
			}, 5000);
		} else if (attempsGw >= 5) {
			console.log((new Date()) + ' 5 tentatives de connexion à la gateway ('+gwAddress+') ont échouées...');
			detroyGw(type, gw);
		} else {
			console.log((new Date()) + ' Error gateway: ' + err.toString());
			console.log((new Date()) + ' ' + err.stack);
		}
		});
}

function detroyGw(type, gw) {
	if (type == 'serial') gw.close();
	else gw.destroy();
}
launchGateway();

process.on('uncaughtException', function ( err ) {
  console.log((new Date()) + ' - An uncaughtException was found, the program will end');
  console.log((new Date()) + ' ' + err.stack);
	//process.exit(1);
});

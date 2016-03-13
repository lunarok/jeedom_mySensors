var net = require('net');
var fs = require('fs');
var request = require('request');

var urlJeedom = '';
var gwType = 'Serial';
var gwAddress = '';
var gwPort = '';
var inclusion = '';
var logLevel = new Array();
logLevel['info'] = 1;
logLevel['debug'] = 1;

process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

// print process.argv
process.argv.forEach(function(val, index, array) {

	switch ( index ) {
		case 2 : urlJeedom = val; break;
		case 3 : gwAddress = val; break;
		case 4 : gwAddress = val; break;
		case 5 : type = val; break;
		case 6 : inclusion = val; break;
	}

});



const gwBaud = 115200;

const fwHexFiles 					= [  ];
const fwDefaultType 				= 0xFFFF; // index of hex file from array above (0xFFFF

const FIRMWARE_BLOCK_SIZE			= 16;
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



const ST_FIRMWARE_CONFIG_REQUEST	= 0;
const ST_FIRMWARE_CONFIG_RESPONSE	= 1;
const ST_FIRMWARE_REQUEST			= 2;
const ST_FIRMWARE_RESPONSE			= 3;
const ST_SOUND						= 4;
const ST_IMAGE						= 5;

const P_STRING						= 0;
const P_BYTE						= 1;
const P_INT16						= 2;
const P_UINT16						= 3;
const P_LONG32						= 4;
const P_ULONG32						= 5;
const P_CUSTOM						= 6;

var fs = require('fs');
var appendedString="";

Date.prototype.getFullDay = function () {
   if (this.getDate() < 10) {
       return '0' + this.getDate();
   }
   return this.getDate();
};

Date.prototype.getFullMonth = function () {
   t = this.getMonth() + 1;
   if (t < 10) {
       return '0' + t;
   }
   return t;
};

Date.prototype.getFullHours = function () {
   if (this.getHours() < 10) {
       return '0' + this.getHours();
   }
   return this.getHours();
};

Date.prototype.getFullMinutes = function () {
   if (this.getMinutes() < 10) {
       return '0' + this.getMinutes();
   }
   return this.getMinutes();
};

Date.prototype.getFullSeconds = function () {
   if (this.getSeconds() < 10) {
       return '0' + this.getSeconds();
   }
   return this.getSeconds();
};

function LogDate(Type, Message) {
 if ( logLevel[Type] == 0 ) return;
   var ceJour = new Date();
//       var ceJourJeedom = ceJour.getDate() + "/" + ceJour.getMonth() + "/" + ceJour.getFullYear() + " " + ceJour.getHours() + ":" + ceJour.getMinutes() + ":" + ceJour.getSeconds();
       var ceJourJeedom = ceJour.getFullDay() + "-" + ceJour.getFullMonth() + "-" + ceJour.getFullYear() + " " + ceJour.getFullHours() + ":" + ceJour.getFullMinutes() + ":" + ceJour.getFullSeconds();
       console.log(ceJourJeedom + " | " + Type + " | " + Message);
}

function crcUpdate(old, value) {
	var c = old ^ value;
	for (var i = 0; i < 8; ++i) {
		if ((c & 1) > 0)
			c = ((c >> 1) ^ 0xA001);
		else
			c = (c >> 1);
	}
	return c;
}

function pullWord(arr, pos) {
	return arr[pos] + 256 * arr[pos + 1];
}

function pushWord(arr, val) {
	arr.push(val & 0x00FF);
	arr.push((val  >> 8) & 0x00FF);
}

function pushDWord(arr, val) {
	arr.push(val & 0x000000FF);
	arr.push((val  >> 8) & 0x000000FF);
	arr.push((val  >> 16) & 0x000000FF);
	arr.push((val  >> 24) & 0x000000FF);
}

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

function saveProtocol(sender, payload, db) {
	/*db.collection('node', function(err, c) {
		c.update({
			'id': sender
		}, {
			$set: {
				'protocol': payload
			}
		}, {
			upsert: true
		}, function(err, result) {
			if (err)
				console.log("Error writing protocol to database");
		});
	});*/
}

function saveSensor(sender, sensor, type) {
	LogDate("info", "Save saveSensor : Value-" + sender.toString() + "-" + sensor.toString()+ "-" + type.toString() );

	url = urlJeedom + "&messagetype=saveSensor&type=mySensors&id="+sender.toString()+"&sensor=" + sensor.toString() + "&value="+type;

	if (inclusion == 'on' ) {
	request(url, function (error, response, body) {
	  if (!error && response.statusCode == 200) {
		LogDate("debug", "Got response saveSensor: " + response.statusCode);
	  }
	});
	}
	else {
		LogDate("debug", "Inclusion Off");
	}
}

function saveGateway(status) {
	LogDate("info", "Save Gateway Status " + status);

	url = urlJeedom + "&messagetype=saveGateway&type=mySensors&status="+status;

	request(url, function (error, response, body) {
	  if (!error && response.statusCode == 200) {
		LogDate("debug", "Got response saveSensor: " + response.statusCode);
	  }
	});
}

function saveValue(sender, sensor, ack, type, payload) {
	LogDate("info", "Save Value : Value-" + payload.toString() + "-" + sender.toString() + "-" + sensor.toString() );

	url = urlJeedom + "&messagetype=saveValue&type=mySensors&id="+sender.toString()+"&sensor=" + sensor.toString() +"&donnees=" + type.toString() + "&value="+payload;

	LogDate("debug", url);
	request(url, function (error, response, body) {
	  if (!error && response.statusCode == 200) {
		LogDate("debug", "Got response Value: " + response.statusCode);
	  }else{

	  	LogDate("debug", "SaveValue Error : "  + error );
	  }
	});
}

function getValue(sender, sensor, ack, type, gw) {
	LogDate("info", "Get Value : for " + sender.toString() + "-" + sensor.toString() );

	url = urlJeedom + "&messagetype=getValue&type=mySensors&id="+sender.toString()+"&sensor=" + sensor.toString() +"&donnees=" + type.toString();

	LogDate("info", url);
	request(url, function (error, response, body) {
	  if (!error && response.statusCode == 200) {
		LogDate("debug", "Got response Value: " + response.statusCode);
		var command = C_SET;
		var acknowledge = 0; // no ack
		var td = encode(sender, sensor, command, acknowledge, type, body);
		LogDate("debug", "-> "  + td.toString() );
		gw.write(td);
	  }else{

	  	LogDate("debug", "GetValue Error : "  + error );
	  }
	});
}

function saveBatteryLevel(sender, payload ) {
	 LogDate("info", "Save BatteryLevel : Value-" + sender.toString() + "-" + payload );

		url = urlJeedom + "&messagetype=saveBatteryLevel&type=mySensors&id="+sender.toString()+"&value="+payload;
	LogDate("debug", url);
		request(url, function (error, response, body) {
	  if (!error && response.statusCode == 200) {
		 LogDate("debug", "Got response saveBatteryLevel: " + response.statusCode);
	  }
	});
}

function saveSketchName(sender, payload) {

	LogDate("info", "Save saveSketchName : Value-" + sender.toString() + "-" + payload );

		url = urlJeedom + "&messagetype=saveSketchName&type=mySensors&id="+sender.toString()+"&value="+payload;
	LogDate("debug", url);
		request(url, function (error, response, body) {
	  if (!error && response.statusCode == 200) {
		LogDate("debug", "Got response saveSketchName: " + response.statusCode);
	  }
	});

}

function saveSketchVersion(sender, payload ) {

	LogDate("info", "Save saveSketchVersion : Value-" + sender.toString() + "-" + payload );

		url = urlJeedom + "&messagetype=saveSketchVersion&type=mySensors&id="+sender.toString()+"&value="+payload;
	LogDate("info", url);
		request(url, function (error, response, body) {
	  if (!error && response.statusCode == 200) {
		LogDate("info", "Got response saveSketchVersion: " + response.statusCode);
	  }
	});
}

function saveLibVersion(sender, payload ) {

	LogDate("info", "Save saveLibVersion : Value-" + sender.toString() + "-" + payload );

		url = urlJeedom + "&messagetype=saveLibVersion&type=mySensors&id="+sender.toString()+"&value="+payload;
	LogDate("debug", url);
		request(url, function (error, response, body) {
	  if (!error && response.statusCode == 200) {
		LogDate("debug", "Got response saveLibVersion: " + response.statusCode);
	  }
	});
}

function sendTime(destination, sensor, gw) {
	var payload = new Date().getTime()/1000;
 	var command = C_INTERNAL;
 	var acknowledge = 0; // no ack
 	var type = I_TIME;
 	var td = encode(destination, sensor, command, acknowledge, type, payload);
 	console.log('-> ' + td.toString());
 	gw.write(td);
}

function sendNextAvailableSensorId( gw) {
	if (inclusion == 'on' ) {
	var destination = BROADCAST_ADDRESS;
	var sensor = NODE_SENSOR_ID;
	var command = C_INTERNAL;
	var acknowledge = 0; // no ack
	var type = I_ID_RESPONSE;
	var payload = Math.floor((Math.random() * 200) + 1);
	var td = encode(destination, sensor, command, acknowledge, type, payload);
	LogDate("debug", "-> "  + td.toString() );
	gw.write(td);
	}


}

function sendConfig(destination, gw) {
	var payload = "M";
	var sensor = NODE_SENSOR_ID;
	var command = C_INTERNAL;
	var acknowledge = 0; // no ack
	var type = I_CONFIG;
	var td = encode(destination, sensor, command, acknowledge, type, payload);
	LogDate("debug", "-> "  + td.toString() );
	gw.write(td);
}

function saveRebootRequest(destination, db) {
	db.collection('node', function(err, c) {
		c.update({
			'id': destination
		}, {
			$set: {
				'reboot': 1
			}
		}, function(err, result) {
			if (err)
				console.log("Error writing reboot request to database");
		});
	});
}

function checkRebootRequest(destination, db, gw) {
	db.collection('node', function(err, c) {
		c.find({
			'id': destination
		}, function(err, item) {
			if (err)
				console.log('Error checking reboot request');
			else if (item.reboot == 1)
				sendRebootMessage(destination, gw);
		});
	});
}

function sendRebootMessage(destination, gw) {
	var sensor = NODE_SENSOR_ID;
        var command = C_INTERNAL;
        var acknowledge = 0; // no ack
        var type = I_REBOOT;
        var payload = "";
        var td = encode(destination, sensor, command, acknowledge, type, payload);
        LogDate("debug", "-> "  + td.toString() );
        gw.write(td);
}


function appendData(str, db, gw) {
    pos=0;
    while (str.charAt(pos) != '\n' && pos < str.length) {
        appendedString=appendedString+str.charAt(pos);
        pos++;
    }
    if (str.charAt(pos) == '\n') {
        rfReceived(appendedString.trim(), db, gw);
        appendedString="";
    }
    if (pos < str.length) {
        appendData(str.substr(pos+1,str.length-pos-1), db, gw);
    }
}

function rfReceived(data, db, gw) {
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
			//	saveProtocol(sender, payload, db); //arduino ou arduino relay
				;
			else
				saveSensor(sender, sensor, type);
				saveLibVersion(sender, payload);
			break;
		case C_SET:
			saveValue(sender, sensor, ack, type, payload);
			break;
		case C_REQ:
			getValue(sender, sensor, ack, type, gw);
			break;
		case C_INTERNAL:
			switch (type) {
			case I_BATTERY_LEVEL:
				saveBatteryLevel(sender, payload, db);
				break;
			case I_TIME:
				sendTime(sender, sensor, gw);
				break;
			case I_VERSION:
				saveLibVersion(sender, payload);
				break;
			case I_ID_REQUEST:
				sendNextAvailableSensorId(gw);
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
				saveSketchName(sender, payload);
				break;
			case I_SKETCH_VERSION:
				saveSketchVersion(sender, payload);
				break;
			case I_REBOOT:
				break;
			}
			break;
		case C_STREAM:
			switch (type) {
					case ST_FIRMWARE_CONFIG_REQUEST:
							//var fwtype = pullWord(payload, 0);
							//var fwversion = pullWord(payload, 2);
							//sendFirmwareConfigResponse(sender, fwtype, fwversion, db, gw);
							break;
					case ST_FIRMWARE_CONFIG_RESPONSE:
							break;
					case ST_FIRMWARE_REQUEST:
							//var fwtype = pullWord(payload, 0);
							//var fwversion = pullWord(payload, 2);
							//var fwblock = pullWord(payload, 4);
							//sendFirmwareResponse(sender, fwtype, fwversion, fwblock, db, gw);
							break;
					case ST_FIRMWARE_RESPONSE:
							break;
					case ST_SOUND:
							break;
					case ST_IMAGE:
							break;
			}
			break;
		}
		//checkRebootRequest(sender, db, gw);

	}
}

LogDate("info", "Jeedom url : " + urlJeedom);
LogDate("info", "gwPort : " + gwPort);
LogDate("info", "gwType : " + gwType);
LogDate("info", "gwAddress : " + gwAddress);
LogDate("info", "Inclusion : " + inclusion);

	var db = null;

	//pour la connexion avec Jeedom => Node
	var pathsocket = '/tmp/mysensor.sock';
	fs.unlink(pathsocket, function () {
	  var server = net.createServer(function(c) {

		LogDate("debug", "server connected");

		c.on('error', function(e) {
		  LogDate("error", "Error server disconnected");
		});

		c.on('close', function() {
		  LogDate("debug", "server disconnected");
		});

		c.on('data', function(data) {
			LogDate("info", "Response: " + data);
			gw.write(data.toString() + '\n');
		});

	  });
	  server.listen(8019, function(e) {
		LogDate("info", "server bound on 8019");
	  });
	});

	if (gwType == 'Network') {
		gw = require('net').Socket();
		gw.connect(gwPort, gwAddress);
		gw.setEncoding('ascii');
		gw.on('connect', function() {
			LogDate("info", "connected to network gateway at " + gwAddress + ":" + gwPort);
			saveGateway('1');
		}).on('data', function(rd) {
			appendData(rd.toString(), db, gw);
		}).on('end', function() {
			LogDate("error", "disconnected from network gateway");
			saveGateway('0');
		}).on('error', function() {
			LogDate("error", "connection error - trying to reconnect");
			saveGateway('0');
			gw.connect(gwPort, gwAddress);
			gw.setEncoding('ascii');
		});
	} else if (gwType == 'serial') {

		var serialPort = require("serialport");
		var SerialPort = require('serialport').SerialPort;
		gw = new SerialPort(gwAddress, { baudrate: gwBaud });
     	gw.open();
		gw.on('open', function() {
			LogDate("info", "connected to serial gateway at " + gwAddress);
			saveGateway('1');
		}).on('data', function(rd) {
			appendData(rd.toString(), db, gw);
		}).on('end', function() {
			LogDate("error", "disconnected from serial gateway");
			saveGateway('0');
		}).on('error', function(error) {
            LogDate("error", "connection error - trying to reconnect: " + error);
            saveGateway('0');
            setTimeout(function() {gw.open();}, 5000);
		});
	}


	process.on('uncaughtException', function ( err ) {
    console.error('An uncaughtException was found, the program will end.');
    //hopefully do some logging.
    //process.exit(1);
});

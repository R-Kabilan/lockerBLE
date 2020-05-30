<?php

$BLEName="";

$BLEName = $_GET["name"];

?>

<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sample illustrating the use of Web Bluetooth / Write Descriptor.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Locker</title>
    <script async="" src="./Web Bluetooth _ Write Descriptor Sample_files/analytics.js.download"></script><script>
      // Add a global error event listener early on in the page load, to help ensure that browsers
      // which don't support specific functionality still end up displaying a meaningful message.
      window.addEventListener('error', function(error) {
        if (ChromeSamples && ChromeSamples.setStatus) {
          console.error(error);
          ChromeSamples.setStatus(error.message + ' (Your browser may not support this feature.)');
          error.preventDefault();
        }
      });
    </script>
    
   
  </head>

  <body>
   Device name:<?php echo $BLEName; ?>
<script>
  if('serviceWorker' in navigator) {
    navigator.serviceWorker.register('service-worker.js');
  }
</script>


<p>
  <button id="readButton">connect</button>
</p>
<p>
    
  <input id="inputPassword" type="text" placeholder="Characteristic User Description">
  <button id="writeButton" disabled="">Submit</button>
</p>
<form id = "for_data" ><input type="hidden" id="name_data" value="<?php echo $BLEName;?>" ></form>

<script>
  var ChromeSamples = {
    log: function() {
      var line = Array.prototype.slice.call(arguments).map(function(argument) {
        return typeof argument === 'string' ? argument : JSON.stringify(argument);
      }).join(' ');

      document.querySelector('#log').textContent += line + '\n';
    },

    clearLog: function() {
      document.querySelector('#log').textContent = '';
    },

    setStatus: function(status) {
      document.querySelector('#status').textContent = status;
    },

    setContent: function(newContent) {
      var content = document.querySelector('#content');
      while(content.hasChildNodes()) {
        content.removeChild(content.lastChild);
      }
      content.appendChild(newContent);
    }
  };
</script>


<div id="output" class="output">
  <div id="content"></div>
  <div id="status"></div>
  <pre id="log"></pre>
</div>


<script>
  if (/Chrome\/(\d+\.\d+.\d+.\d+)/.test(navigator.userAgent)){
    // Let's log a warning if the sample is not supposed to execute on this
    // version of Chrome.
    if (58 > parseInt(RegExp.$1)) {
      ChromeSamples.setStatus('Warning! Keep in mind this sample has been tested with Chrome ' + 58 + '.');
    }
  }
</script>
    
<script>
var bluetoothDevice;
var myDescriptor; 
var myCharacteristic;
var myReadCharacteristic;
var name_device = document.getElementById("name_data").value; 
var status;
function onReadButtonClick() {
  
  let serviceUuid = "6e400001-b5a3-f393-e0a9-e50e24dcca9e";
  let characteristicUuid = "6e400002-b5a3-f393-e0a9-e50e24dcca9e";
  let readcharacteristicUuid = "6e400003-b5a3-f393-e0a9-e50e24dcca9e";

  log('connecting to device');
  navigator.bluetooth.requestDevice({
  filters:[{name: name_device}],
      optionalServices: [serviceUuid]})
  .then(device => {
    bluetoothDevice = device;
    bluetoothDevice.addEventListener('gattserverdisconnected', onDisconnected);
    return device.gatt.connect();
  })
  .then(server => {
    
    return server.getPrimaryService(serviceUuid);
  })
  .then(service => {
    
    myReadCharacteristic = service.getCharacteristic(readcharacteristicUuid);
    return service.getCharacteristic(characteristicUuid);
  })
  .then(characteristic => {
    log('enter the passcode')
    document.querySelector('#writeButton').disabled =false;
     myCharacteristic = characteristic;
     
     })
  .catch(error => {
    document.querySelector('#writeButton').disabled = true;
    log(error);
  });
}

function onDisconnected() {
  log('place the parcel doors open');
  window.location = "open.html";
}

function onWriteButtonClick() {
  if (false) {
    return;
  }
  
  let encoder = new TextEncoder('utf-8');
  let value = document.querySelector('#inputPassword').value;
  myCharacteristic.writeValue(encoder.encode(value))
  .then(value => {
    log("retry wrong passcode")
    let decoder = new TextDecoder('utf-8');
  })
  .catch(error => {
    log('Argh! ' + error);
  });
  
}
</script>
    
<script>
  document.querySelector('#readButton').addEventListener('click', function() {
    if (isWebBluetoothEnabled()) {
      ChromeSamples.clearLog();
      onReadButtonClick();
    }
  });
  document.querySelector('#writeButton').addEventListener('click', function() {
    onWriteButtonClick();
  });
</script>

<script>
  log = ChromeSamples.log;

  function isWebBluetoothEnabled() {
    if (navigator.bluetooth) {
      return true;
    } else {
      ChromeSamples.setStatus('Web Bluetooth API is not available.\n' +
          'Please make sure the "Experimental Web Platform features" flag is enabled.');
      return false;
    }
  }
</script>
  
</body></html>
var IPUtil = {
  getIP: function(){
    //call native handler to get ip address
    cordova.exec(
      IPUtil.displayIP, //success callback
      function(err){alert("Error:" + err);}, //error callback
      "GetIP", //native service name
      "get_device_ip", //action name
      ["get_device_ip"] //arguments for native side
    );
  },

  displayIP: function(ip){
    var element = document.getElementById("ip");
    element.innerHTML = "Your IP Address is: " + ip + "<br />";

    //create QR code
    var qr = document.getElementById("IPQRCode");
    new QRCode(qr, ip);
  }
};

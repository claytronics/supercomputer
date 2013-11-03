/*
 *  @author Kevin Ku
 *  @date Nov 3, 2013
 *  Interface to native code to get device IP
 */

var IPUtil = {
  /* getIP: returns the device's IP to successCallback when the device is ready */
  getIP: function(successCallback){
    //check device is ready
    var ready = document.getElementById("deviceready").getAttribute("value");

    if(ready === "0"){
      //not ready - add event listener
      var lazy = function(){ IPUtil.getDeviceIP(successCallback); };
      document.addEventListener("deviceready", lazy, false);
    }
    else{
      //ready 
      IPUtil.getDeviceIP(successCallback);
    }
  },

  /*  getDeviceIP: interface to native ip call */
  getDeviceIP: function(successCallback){
    //call native handler to get ip address
    cordova.exec(
      successCallback, //success callback
      function(err){alert("Error:" + err);}, //error callback
      "GetIP", //native service name
      "get_device_ip", //action name
      ["get_device_ip"] //arguments for native side
    );

    console.log("get ip complete");
  },

  /*  QRObject: hold the object displaying the QR Code on the screen */
  QRObject: null,

  /*  updateIPLabel: updates the IP of <p> with id = "ip". If ip === "", 
   *    then hide the <p>
   */
  updateIPLabel: function(ip){ 
    var ipLabel = document.getElementById("ip");
    if(ipLabel == null){
      console.log("div with id = 'ip' not found");
      return;
    }
    else if(ip === ""){
      ipLabel.setAttribute("style", "display: none;");
    }
    else{
      ipLabel.innerHTML = "Your IP Address is: " + ip + "<br />";
      ipLabel.setAttribute("style", "display: block;");
    }
  },

  /*  updateIPQR: updates the QR code of device ip in a <div> with id = "ip_qr",
   *    if ip === "", then hide the QR code
   */
  updateIPQR: function(ip){
    var ipQR = document.getElementById("ip_qr");
    if(ipQR == null){
      console.log("div with id = 'ip_qr' not found");
      return;
    }
    else if(ip === ""){ 
      ipQR.setAttribute("style", "display: none;");
    }
    else{
      ipQR.setAttribute("style", "display: block;");

      if(IPUtil.QRObject == null || typeof IPUtil.QRObject === 'undefined'){
        IPUtil.QRObject = new QRCode(ipQR, ip);
      }
      else{
        IPUtil.QRObject.clear();
        IPUtil.QRObject.makeCode(ip);
      }
    }
  },

  /* 
   *  displayIP: display ip as a QR code in a <div> with id "ip_qr" as well as
   *    text in <p> with id "ip" 
   */
  displayIP: function(ip){
    console.log("start displaying device ip");
    
    IPUtil.updateIPLabel(ip);
    IPUtil.updateIPQR(ip);

    console.log("device IP displayed");
  },

  /* showIP: wrapper for getIP using displayIP as the successCallback */
  showIP: function(){
    IPUtil.getIP(IPUtil.displayIP); 
  },

  /* hideIP: wrapper for hiding ip label and ip qr code */
  hideIP: function(){
    IPUtil.updateIPLabel("");
    IPUtil.updateIPQR("");
  }
};

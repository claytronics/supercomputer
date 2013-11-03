/*
 *  @author: Kevin Ku
 *  @date: Nov. 1, 2013
 *  Wrapper for phonegap barcode scanner
 */

var barcodeScanner = {
  /* launch the scanner */
  scan: function(){
    console.log("barcode scanner launched");

    cordova.plugins.barcodeScanner.scan(
      function(result){
        if(!result.cancelled){
          alert("Scan Result\n" +
            "Result: " + result.text + "\n" +
            "Format: " + result.format + "\n"
            );
        }
      }, 
      function(error){
        alert("Scanning failed: " + error);
      }
    );
  }
}

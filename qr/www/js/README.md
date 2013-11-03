Javascript Libraries
====================

# barcode-scanner-interface.js
- Uses Phonegap barcode scanner to scan for a barcode
- Usage Example <pre><code>barcodeScanner.scane()</code></pre>

# ip-util.js
- Get device's IP from native platform code. Ex:
<pre><code>function callback(ip){ alert(ip); }; 
IPUtil.getIP(callback);
</code></pre>
- Helper functions to display the IP in QR code and text. Ex:
  1. Define <br />
    ```
    <p id="qr"></p> <br /><br />
    <div id="ip_qr"></div>
    ```
    <br />
    in HTML
  2. Use <pre><code>IPUtil.showIP();</code></pre> to display IP address in the paragrpah and div defined above

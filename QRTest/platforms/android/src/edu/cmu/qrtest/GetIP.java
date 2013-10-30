/*
 *  PhoneGap Android plugin to get device IP address
 *  @author Kevin Ku
 *  @date 10/20 2013
 */
package edu.cmu.qrtest;

import java.net.InetAddress;
import java.net.NetworkInterface;
import java.net.SocketException;
import java.util.Collections;
import java.util.List;

import org.apache.cordova.CordovaPlugin;
import org.apache.cordova.CallbackContext;
import org.apache.http.conn.util.InetAddressUtils;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class GetIP extends CordovaPlugin{
  @Override
  public boolean execute(String action, JSONArray args, 
    CallbackContext callback) throws JSONException{
    if(action.equals("get_device_ip")){
      getDeviceIP(callback);
      return true; 
    }
    return false;
  }

  private void getDeviceIP(CallbackContext callback){
    try{
      List<NetworkInterface> interfaces = Collections.list(
        NetworkInterface.getNetworkInterfaces());

      if(interfaces == null){
        callback.error("No network interfaces found");
        return;
      }

      for(NetworkInterface ni : interfaces){
        List<InetAddress> addrs = Collections.list(ni.getInetAddresses());
        for(InetAddress addr : addrs){
          if(!addr.isLoopbackAddress()){
            String ip = addr.getHostAddress();
            boolean ipv6 = InetAddressUtils.isIPv6Address(ip);
            if(!ipv6){
              //drop ipv6 port suffix
              int idx = ip.indexOf("%");
              ip = idx < 0 ? ip : ip.substring(0, idx);
            }
            callback.success(ip);
          }
        }
      }

      callback.error("No IP address found");
    }catch(SocketException ex){
      callback.error("Socket Exception"); 
    }
  }
}

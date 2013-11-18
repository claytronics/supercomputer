/*
 *  @author Kevin Ku
 *  @date Nov 17, 2013
 *  Interface to server
 */

var server = {
  /* server address */
  address: "http://192.168.1.2/",

  /* getJSON: convert reponse into a json object */
  getJSON: function(s){
    try{
      var jobj = jQuery.parseJSON(s);
      return jobj;
    }catch(err){
      return null;
    }
  },

  /* query: send a ajax request to the server and call the appropriate 
   *  callback functions 
   */
  query: function(url, data, success, fail){
    var request = $.ajax({
      url: url,
      type: "get",
      data: data,
      dataType: "json"
    });

    request.done(success);
    request.fail(fail);
  },

  /* register: register a device's ip with the server. returns the device's id */
  register: function(){
    console.log("register device");

    var url = server.address + "register.php";
    var data = {};
    var success = function(response, textStatus, jqXHR){
      var jobj = server.getJSON(response);      
      if(jobj.success == null){
        console.log("registration failed");
      }
      else if(jobj.success){
        console.log("registred user id: " + jobj.user_id);
      }
      else{
        console.log("registration failed");
      }
    };

    var fail = function(jqXHR, textStatus, error){
      console.log(error);
    };

    server.query(url, data, success, fail);
  },

  /* addNeighbor: add a neighbor by supplying the neighbor's ip */
  addNeighbor: function(neighbor_ip){
    console.log("adding neighbor with ip " + neighbor_ip);

    var url = server.address + "neighbor.php";
    var data = {};
    data['neighbor_ip'] = neighbor_ip;
    var success = function(response, textStatus, jqXHR){
      var jobj = server.getJSON(response);      
      if(jobj.success == null){
        console.log("failed to add neighbor");
      }
      else if(jobj.success){
        console.log("added neighbor id: " + jobj.neighbor_id);
      }
      else{
        console.log("failed to add neighbor");
      }
    };

    var fail = function(jqXHR, textStatus, error){
      console.log(error);
    };

    server.query(url, data, success, fail);
  }
};

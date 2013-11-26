/*
 *  @author Kevin Ku
 *  @date Nov 17, 2013
 *  Interface to server
 */

var server = {
  /* server address */
  address: "http://192.168.1.2",

  /* keep alive interval time (ms) */
  keep_alive_interval_time: 60000, /* 1 min */

  /* keep alive interval */
  keep_alive_interval: null,

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

  /* register: register a device's ip with the server. 
   * returns the device's id */
  register: function(){
    console.log("register device");

    var url = server.address + "/register.php";
    var data = {};
    var success = function(response, textStatus, jqXHR){
      if(response == null){
        console.log("no response from server for registration");
      }
      else if(response.success){
        console.log("registred user id: " + response.user_id);
      }
      else{
        console.log("registration failed");
      }
    };

    var fail = function(jqXHR, textStatus, error){
      console.log(error);
    };

    server.query(url, data, success, fail);

    /* schedule keep alive */
    if(server.keep_alive_interval == null){
      server.keep_alive_interval = window.setInterval(server.keepAlive, 
        server.keep_alive_interval_time);
    }
  },

  /* addNeighbor: add a neighbor by supplying the neighbor's ip */
  addNeighbor: function(neighbor_ip){
    console.log("adding neighbor with ip " + neighbor_ip);

    var url = server.address + "/neighbor.php";
    var data = {};
    data['neighbor_ip'] = neighbor_ip;
    var success = function(response, textStatus, jqXHR){
      if(response == null){
        console.log("no response from server for adding neighbor");
      }
      else if(response.success){
        console.log("added neighbor id: " + response.neighbor_id);
      }
      else{
        console.log("failed to add neighbor");
      }
    };

    var fail = function(jqXHR, textStatus, error){
      console.log(error);
    };

    server.query(url, data, success, fail);
  },

  /* tell the server that this client is still alive */
  keepAlive: function(){
    console.log("sending keep alive message to server");

    var url = server.address + "/keep_alive.php";

    var success = function(response, textStatus, jqXHR){
      if(response == null){
        console.log("no response from server for keep alive message");

        /* stop sending keep alive */
        window.clearInterval(server.keep_alive_interval);
        server.keep_alive_interval = null;
      }
      else if(response.success){
        console.log("keep alive success");
      }
      else{
        console.log("server rejected keep alive message");

        /* stop sending keep alive */
        window.clearInterval(server.keep_alive_interval);
        server.keep_alive_interval = null;
      }
    };

    var fail = function(jqXHR, textStatus, error){
      console.log(error);
    };

    server.query(url, {}, success, fail);
  }
};

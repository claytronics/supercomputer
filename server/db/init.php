<?php
/* 
 * @author kku
 * @comment 
 *  server database initialization script
 */
?>
<?php
include("sql.php");
?>
<html>
<head>
</head>
<body>
<?php	
	if(isset($_POST['id']) && isset($_POST['pw'])){
		dbInit();
		echo("Server initialization complete!");
	}
	else{
		echo "
			Enter MySQL credentials: <br/>
			<form method='POST' action='init.php'>
				Root username: <input type='text' name='id' size='30'><br/><br/>
				Root password: <input type='password' name='pw' size='30'><br/>
				<input type='submit' name='upload' value='Login to Initialize'>
			</form>
		";
	}
	
	function dbInit(){
		require_once("db_config.php");
	
		$db_name = DB_NAME;
		$db_host = DB_HOST;
		
		//create database 
		$link = mysql_connect($db_host, "".$_POST['id'], "".$_POST['pw']);
		$query = "CREATE DATABASE IF NOT EXISTS {$db_name} CHARACTER SET utf8 
      COLLATE utf8_general_ci";
		$result = mysql_query($query , $link);
		if($result == ""){
			basic_redirect("Unable to create database. Please try again.", 
        "init.php");
		}
		
		//create database user account
		try{
			$db = sql_connect_db($db_host, "".$_POST['id'], "".$_POST['pw'], 
        "{$db_name}");
		}catch(Exception $e){
			basic_redirect("Unable to connect to database {$db_name}. 
        Please try again", "init.php");
		}
		
		$admin_account = DB_USER;
		$admin_pw = DB_PASSWORD;
		
		$query = "CREATE USER '{$admin_account}'@'{$db_host}' IDENTIFIED BY  
      '{$admin_pw}'";
		if(!$db->query($query)){
			echo $db->error;
			basic_redirect("Unable to create {$admin_account} database account. 
        Please try again.", "init.php");
		}
		
		$query = "GRANT ALL ON {$db_name}.* TO '{$admin_account}'@'{$db_host}' 
      IDENTIFIED BY  '{$admin_pw}' WITH MAX_QUERIES_PER_HOUR 0 
      MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 
      MAX_USER_CONNECTIONS 0";
		if(!$db->query($query)){
			echo $db->error;
			basic_redirect("Unable to grant {$admin_account} database privilege. 
        Please try again.", "init.php");
		}	
		
		$query = "GRANT FILE ON * . * TO  '{$admin_account}'@'{$db_host}'";
		if(!$db->query($query)){
			echo $db->error;
			basic_redirect("Unable to grant {$admin_account} FILE privilege. 
        Please try again.", "init.php");
		}
		
		//create tables
		//node table
		$query = "CREATE TABLE IF NOT EXISTS nodes(
			user_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      user_ip VARCHAR(255) NOT NULL,
			user_join_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (user_id),
      UNIQUE (user_ip)
			)";
		if(!$db->query($query)){
			echo $db->error;
			basic_redirect("Unable to create nodes table. Please try again.", 
        "init.php");
		}
		
		//edge table
		$query = "CREATE TABLE IF NOT EXISTS edges(
			src_id INT(11) UNSIGNED NOT NULL,
			target_id INT(11) UNSIGNED NOT NULL,
			PRIMARY KEY (src_id, target_id)
		)";
		if(!$db->query($query)){
			echo $db->error;
			basic_redirect("Unable to create edges table. Please try again.", 
        "init.php");
		}
			
		sql_close($db);
	}
	
	function basic_redirect($msg, $target){
		echo "<script>";
		if(isset($msg))
			echo "alert('{$msg}');";				
		if(isset($target))
			echo "location.replace('{$target}');";
		echo"</script>";
		exit(0);
	}
?>
</body>
</html>

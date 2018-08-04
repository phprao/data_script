<?php
header('content-type:text/html; charset=utf-8');
header("Expires:-1");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma:no-cache");
/*
 * action : Register
 * param: user、password、sex
 * return: uid、sex、money、rmb、coin、login_days、is_get、history[2] (total_win、total_lose)、total_borad、total_win、exp
 */

   //var_dump($_POST);
?>
<div style="width:600px;height:300px;padding:10px;border:1px solid red;margin-left:100px; word-break: normal; ">
<form action="" method="post">
  选择操作接口：</br></br>
  <select name="action" style="width:150px;height:30px;">
  	  <option value="basicdatacache" <?php if(isset($_POST['action'])&&$_POST['action']=='basicdatacache') echo 'selected'; ?> >basicdatacache</option>
      <option value="login" <?php if(isset($_POST['action'])&&$_POST['action']=='login') echo 'selected'; ?> >login</option>
      <option value="regsiter" <?php if(isset($_POST['action'])&&$_POST['action']=='regsiter') echo 'selected'; ?> >regsiter</option>
      <option value="clublist" <?php if(isset($_POST['action'])&&$_POST['action']=='clublist') echo 'selected'; ?> >clublist</option>
      <option value="clubgamelist" <?php if(isset($_POST['action'])&&$_POST['action']=='clubgamelist') echo 'selected'; ?> >clubgamelist</option>
      <option value="clubroomlist" <?php if(isset($_POST['action'])&&$_POST['action']=='clubroomlist') echo 'selected'; ?> >clubroomlist</option>
      <option value="clubinfo" <?php if(isset($_POST['action'])&&$_POST['action']=='clubinfo') echo 'selected'; ?> >clubinfo</option>
      <option value="clubdel" <?php if(isset($_POST['action'])&&$_POST['action']=='clubdel') echo 'selected'; ?> >clubdel</option>
      <option value="clubplayerinfo" <?php if(isset($_POST['action'])&&$_POST['action']=='clubplayerinfo') echo 'selected'; ?> >clubplayerinfo</option>
      <option value="clubroominfo" <?php if(isset($_POST['action'])&&$_POST['action']=='clubroominfo') echo 'selected'; ?> >clubroominfo</option>
      <option value="clubdeskinfo" <?php if(isset($_POST['action'])&&$_POST['action']=='clubdeskinfo') echo 'selected'; ?> >clubdeskinfo</option>
      <option value="recordclubdesk" <?php if(isset($_POST['action'])&&$_POST['action']=='recordclubdesk') echo 'selected'; ?> >recordclubdesk</option>
      <option value="queryroom" <?php if(isset($_POST['action'])&&$_POST['action']=='queryroom') echo 'selected'; ?> >queryroom</option>
      <option value="playerroom" <?php if(isset($_POST['action'])&&$_POST['action']=='playerroom') echo 'selected'; ?> >playerroom</option>
      <option value="initroom" <?php if(isset($_POST['action'])&&$_POST['action']=='initroom') echo 'selected'; ?> >initroom</option>
      <option value="recordmoneychang" <?php if(isset($_POST['action'])&&$_POST['action']=='recordmoneychang') echo 'selected'; ?> >recordmoneychang</option>
      <option value="gamerecord" <?php if(isset($_POST['action'])&&$_POST['action']=='gamerecord') echo 'selected'; ?> >gamerecord</option>
      <option value="wxlogin" <?php if(isset($_POST['action'])&&$_POST['action']=='wxlogin') echo 'selected'; ?> >wxlogin</option>
      <option value="notice" <?php if(isset($_POST['action'])&&$_POST['action']=='notice') echo 'selected'; ?> >notice</option>
      <option value="clubrulelist" <?php if(isset($_POST['action'])&&$_POST['action']=='clubrulelist') echo 'selected'; ?> >clubrulelist</option>
      <option value="changeplayerinfo" <?php if(isset($_POST['action'])&&$_POST['action']=='changeplayerinfo') echo 'selected'; ?> >changeplayerinfo</option>
      <option value="bindmobile" <?php if(isset($_POST['action'])&&$_POST['action']=='bindmobile') echo 'selected'; ?> >bindmobile</option>
      <option value="sendcode" <?php if(isset($_POST['action'])&&$_POST['action']=='sendcode') echo 'selected'; ?> >sendcode</option>
      <option value="mobilelogin" <?php if(isset($_POST['action'])&&$_POST['action']=='mobilelogin') echo 'selected'; ?> >mobilelogin</option>
      <option value="goodsinfo" <?php if(isset($_POST['action'])&&$_POST['action']=='goodsinfo') echo 'selected'; ?> >goodsinfo</option>
      <option value="playerinfo" <?php if(isset($_POST['action'])&&$_POST['action']=='playerinfo') echo 'selected'; ?> >playerinfo</option>
      <option value="orderadd" <?php if(isset($_POST['action'])&&$_POST['action']=='orderadd') echo 'selected'; ?> >orderadd</option>
      <option value="gamerecordstat" <?php if(isset($_POST['action'])&&$_POST['action']=='gamerecordstat') echo 'selected'; ?> >gamerecordstat</option>
      <option value="playerstatistical" <?php if(isset($_POST['action'])&&$_POST['action']=='playerstatistical') echo 'selected'; ?> >playerstatistical</option>
      <option value="mainscript" <?php if(isset($_POST['action'])&&$_POST['action']=='mainscript') echo 'selected'; ?> >mainscript</option>
      <option value="weixinpay" <?php if(isset($_POST['action'])&&$_POST['action']=='weixinpay') echo 'selected'; ?> >weixinpay</option>
      <option value="userranking" <?php if(isset($_POST['action'])&&$_POST['action']=='userranking') echo 'selected'; ?> >userranking</option>
      <option value="feedback" <?php if(isset($_POST['action'])&&$_POST['action']=='feedback') echo 'selected'; ?> >feedback</option>
      <option value="gamebeatlog" <?php if(isset($_POST['action'])&&$_POST['action']=='gamebeatlog') echo 'selected'; ?> >gamebeatlog</option>
      <option value="promoterewardinfo" <?php if(isset($_POST['action'])&&$_POST['action']=='promoterewardinfo') echo 'selected'; ?> >promoterewardinfo</option>
      <option value="ordercallback" <?php if(isset($_POST['action'])&&$_POST['action']=='ordercallback') echo 'selected'; ?> >ordercallback</option>
      <option value="getusercommon" <?php if(isset($_POST['action'])&&$_POST['action']=='getusercommon') echo 'selected'; ?> >getusercommon</option>
      <option value="authorizedlogin" <?php if(isset($_POST['action'])&&$_POST['action']=='authorizedlogin') echo 'selected'; ?> >authorizedlogin</option>
      <option value="appleiappay" <?php if(isset($_POST['action'])&&$_POST['action']=='appleiappay') echo 'selected'; ?> >appleiappay</option>
      <option value="realname" <?php if(isset($_POST['action'])&&$_POST['action']=='realname') echo 'selected'; ?> >realname</option>
      <option value="clubroomdesklist" <?php if(isset($_POST['action'])&&$_POST['action']=='clubroomdesklist') echo 'selected'; ?> >clubroomdesklist</option>
      <option value="usertokenranking" <?php if(isset($_POST['action'])&&$_POST['action']=='usertokenranking') echo 'selected'; ?> >usertokenranking</option>
      <option value="clubplayerinfolist" <?php if(isset($_POST['action'])&&$_POST['action']=='clubplayerinfolist') echo 'selected'; ?> >clubplayerinfolist</option>
      <option value="playertransfercoins" <?php if(isset($_POST['action'])&&$_POST['action']=='playertransfercoins') echo 'selected'; ?> >playertransfercoins</option>
      <option value="queryplayersafebox" <?php if(isset($_POST['action'])&&$_POST['action']=='queryplayersafebox') echo 'selected'; ?> >queryplayersafebox</option>
      <option value="playeremoticon" <?php if(isset($_POST['action'])&&$_POST['action']=='playeremoticon') echo 'selected'; ?> >playeremoticon</option>
      <option value="updateplayerloginip" <?php if(isset($_POST['action'])&&$_POST['action']=='updateplayerloginip') echo 'selected'; ?> >updateplayerloginip</option>
      <option value="queryplayerloginip" <?php if(isset($_POST['action'])&&$_POST['action']=='queryplayerloginip') echo 'selected'; ?> >queryplayerloginip</option>
      <option value="updateplayeremoticonusedtimes" <?php if(isset($_POST['action'])&&$_POST['action']=='updateplayeremoticonusedtimes') echo 'selected'; ?> >updateplayeremoticonusedtimes</option>
      <option value="playermessage" <?php if(isset($_POST['action'])&&$_POST['action']=='playermessage') echo 'selected'; ?> >playermessage</option>
      <option value="playergamerecord" <?php if(isset($_POST['action'])&&$_POST['action']=='playergamerecord') echo 'selected'; ?> >playergamerecord</option>
  </select>
  
  机器码：<input style="width:150px;height:30px;" type="text" name="iphone_id" value=""/>
  <button style="cursor:pointer;height:30px;" type="submit" value="提交">提交</button>
</form>

<?php
if(empty($_POST)){
exit;
} 
//var_dump($_POST);
include_once("../basic/httpclient.php");
include_once('../basic/basiclogger.php');
include_once('../basic/functions.php');
include_once('config.php');
//include_once('RandomNickNameList.lua');
//192.168.1.21/dcapi/action.php?param={"action":"register","version":"v10001","key_value":1,"flag_value":1,"data_value":{"username":"dc0001","password":"112233","puid":"123456"}}
Config::build_srv_url();
$url_prefix = Config::$url_prefix;
//
$api = $_POST["action"];
//

if($api=='login'){
	$action = "login";
	//$k=$name[ mt_rand(0, count($name) - 1) ];
	//$k=trim($k);
	$param = array();
	//$param['user'] = "dc001" . rand(1, 100000);
	//$param['user'] = "dc0002";
	// $param['user'] = "robot".$i;
	// $param['password'] = "112233";
	//$param['name'] = "$k";
	$param['username'] = "guest_10916945787";
	$param['puid'] = "1244556654";
    $param['platform'] = 1;
	$param['password'] = "112233";

	$query = array();
	$query['param']['action'] = $action;
	$query['param']['version'] = Config::$ver;
	$query['param']['key_value'] = 1;
	$query['param']['flag_value'] = 1;
  $query['param']['sign_value'] = time();
	$query['param']['data_value'] = $param;
	$query['param'] = json_encode($query['param']);
	$url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
	
}elseif($api=='regsiter'){
	$action = "regsiter";
	//$k=$name[ mt_rand(0, count($name) - 1) ];
	//$k=trim($k);
	$param = array();
	//$param['user'] = "dc001" . rand(1, 100000);
	//$param['user'] = "dc0002";
	// $param['user'] = "robot".$i;
	// $param['password'] = "112233";
	//$param['name'] = "$k";
	$param['pcid'] = time();
    $param['platform'] = 1;
	//$param['puid'] = "1244556654";
	$query = array();
	$query['param']['action'] = $action;
	$query['param']['version'] = Config::$ver;
	$query['param']['key_value'] = 1;
	$query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
	$query['param']['data_value'] = $param;
	$query['param'] = json_encode($query['param']);
	
	$url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
	
}else if($api == 'clublist') {
	$action = "clublist";
	$param  = array();
	$param['page_index'] = 0;
	$param['player_id'] = 1091721;
  $param['type'] = 1;
  $param['key'] = '2';
	$param['player_token'] = 'UVdiWU1SbFFodGlNeHA1b0pBbXRnNWY3SUtIWVdZbklvUT09';
	$query  = array();
	$query['param']['action'] = $action;
	$query['param']['version'] = Config::$ver;
	$query['param']['key_value'] = 1;
	$query['param']['flag_value'] = 1;
  $query['param']['sign_value'] = time();
	$query['param']['data_value'] = $param;
	$query['param'] = json_encode($query['param']);
	$url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'clubdel') {
  $action = "clubmanage";
  $param  = array();
  $param['page_index'] = 0;
  $param['player_id'] = 1091721;
  $param['club_id'] = 1;
  $param['player_token'] = 'UVdiWU1SbFFodGlNeHA1b0pBbXRnNWY3SUtIWVdZbklvUT09';
  $query  = array();
  $query['param']['action'] = $action;
  $query['param']['version'] = Config::$ver;
  $query['param']['key_value'] = 1;
  $query['param']['flag_value'] = 1;
  $query['param']['sign_value'] = time();
  $query['param']['data_value'] = $param;
  $query['param'] = json_encode($query['param']);
  $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'clublistbyplayer') {
  $action = "clublistbyplayer";
  $param = array();
  $param['page_index'] = 0;
  $param['player_id'] = 1091721;
  $param['player_token'] = 'UVdiWU1SbFFodGlNeHA1b0pBbXRnNWY3SUtIWVdZbklvUT09';

  $query = array();
  $query['param']['action'] = $action;
  $query['param']['version'] = Config::$ver;
  $query['param']['key_value'] = 1;
  $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
  $query['param']['data_value'] = $param;
  $query['param'] = json_encode($query['param']);
  
  $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api =='clubgamelist') {
	$action = "clubgamelist";
	$param = array();
	$param['page_index'] = 0;
	$param['club_id'] = 1;
	$param['player_id'] = 1091721;
	$param['player_token'] = 'UVdiWU1SbFFodGlNeHA1b0pBbXRnNWY3SUtIWVdZbklvUT09';

	$query = array();
	$query['param']['action'] = $action;
	$query['param']['version'] = Config::$ver;
	$query['param']['key_value'] = 1;
	$query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
	$query['param']['data_value'] = $param;
	$query['param'] = json_encode($query['param']);
	
	$url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'basicdatacache') {
	$action = "basicdatacache";
	$param = array();
	$param['page_index'] = 0;
	$param['club_id'] = 1;
	$param['player_id'] = 1091721;
	$param['player_token'] = 'UVdiWU1SbFFodGlNeHA1b0pBbXRnNWY3SUtIWVdZbklvUT09';

	$query = array();
	$query['param']['action'] = $action;
	$query['param']['version'] = Config::$ver;
	$query['param']['key_value'] = 1;
	$query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
	$query['param']['data_value'] = $param;
	$query['param'] = json_encode($query['param']);
	
	$url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'clubroomlist') {
	$action = "clubroomlist";
	$param = array();
	$param['page_index'] = 0;
	$param['club_id'] = 0;
	$param['game_id'] = 10000005;
    $param['rule_id'] = 0;
    $param['room_level'] = 0;
    $param['room_type'] = 0;
	$param['player_id'] = 1091721;
	$param['player_token'] = 'UVdiWU1SbFFodGlNeHA1b0pBbXRnNWY3SUtIWVdZbklvUT09';

	$query = array();
	$query['param']['action'] = $action;
	$query['param']['version'] = Config::$ver;
	$query['param']['key_value'] = 1;
	$query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
	$query['param']['data_value'] = $param;
	$query['param'] = json_encode($query['param']);
	
	$url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'clubinfo') {
	$action = "clubinfo";
	$param = array();
	$param['club_id'] = 1;
	$param['player_id'] = 1091721;
	$param['player_token'] = 'UVdiWU1SbFFodGlNeHA1b0pBbXRnNWY3SUtIWVdZbklvUT09';
  $param['index'] = 0;
 	$query = array();
	$query['param']['action'] = $action;
	$query['param']['version'] = Config::$ver;
	$query['param']['key_value'] = 1;
	$query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
	$query['param']['data_value'] = $param;
	$query['param'] = json_encode($query['param']);
	
	$url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'clubplayerinfo') {
    $action = "clubplayerinfo";
    $param = array();
    $param['club_id'] = 3;
    $param['player_id'] = 1091721;
    $param['player_token'] = 'UVdiWU1SbFFodGlNeHA1b0pBbXRnNWY3SUtIWVdZbklvUT09';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'clubplayerinfolist') {
    $action = "clubplayerinfolist";
    $param = array();
    $param['club_id'] = 1;
    $param['player_id'] = [600021,600021];
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'clubroominfo') {
	$action = "clubroominfo";
    $param = array();
    $param['club_room_id'] = 1;
    $param['club_room_club_id'] = 1;
    $param['player_id'] = 1091721;
    $param['player_token'] = 'UVdiWU1SbFFodGlNeHA1b0pBbXRnNWY3SUtIWVdZbklvUT09';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'clubdeskinfo') {
	$action = "clubdeskinfo";
    $param = array();
    $param['club_room_id'] = 19;
    $param['club_room_club_id'] = 0;
    $param['club_desk_id'] = 55;
    $param['club_desk_rule_id'] = 0;
    $param['club_desk_param'] = '';
    $param['player_id'] = 1091721;
    $param['player_token'] = 'UVdiWU1SbFFodGlNeHA1b0pBbXRnNWY3SUtIWVdZbklvUT09';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'recordclubdesk') {
	$action = "recordclubdesk";
    $param = array();
    $param['club_room_id'] = 1;
    $param['club_id'] = 1;
    $param['club_desk_id'] = 55;
    $param['player_id'] = 600021;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'queryroom') {
    $action = "queryroom";
    $param = array();
    $param['room_no'] = 937938;
    $param['player_id'] = 600021;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'playerroom') {
    $action = "playerroom";
    $param = array();
    $param['player_id'] = 600021;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'initroom') {
    $action = "initroom";
    $param = array();
    $param['room_id'] = 1;
    $param['player_id'] = 600021;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'recordmoneychang') {
    $action = "recordmoneychang";

    $user_list = array();
    $user_item = array('change_money_player_id'=>601919,'change_money_time'=>1515233817);
    array_push($user_list, $user_item);


    $param = array();
    $param['user_list'] = $user_list;
    $param['player_id'] = 601919;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'wxlogin') {
    $action = "wxlogin";
    $param = array();
    $param['code'] = '只有u3d的需要传';
    $param['puid'] = 'xxxxxx';
    $param['login_type'] = '1是u3d ,2是友间';
    $param['platform'] = '1是ios ,2是安卓';
    $param['access_token'] = '只有友间的需要传递';
    $param['player_pcid'] = 'test';
    $param['unionid'] = '';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'gamerecord') {
    $action = "gamerecord";
    $user_list = array();
    array_push($user_list, 601919);
    array_push($user_list, 600021);
    $param = array();
    $param['game_id'] = 10000007;
    $param['room_id'] = 5;
    $param['desk_id'] = 1;
    $param['time_value'] = time();
    $param['user_list'] = $user_list;

    $param['player_id'] = 600021;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'notice') {
    $action = "notice";
    $param = array();
    $param['notice_type'] = 1;
    $param['player_id'] = 600021;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);

}else if($api == 'changeplayerinfo') {
    $action = "changeplayerinfo";
    $param = array();
    $param['player_signature'] = '我是个好人，你是个备胎';
    $param['player_id'] = 601702;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);

}else if($api == 'clubrulelist') {
     $action = "clubrulelist";
    $param = array();
    $param['club_room_club_id'] = 0;
    $param['club_room_game_id'] = 10000007;
    $param['player_id'] = 601702;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);

} else if($api == 'bindmobile') {
    $action = "bindmobile";
    $param = array();
    $param['code'] = rand(1000,9999);
    $param['mobile'] = '15361057179';
    $param['player_id'] = 601702;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'sendcode') {
    $action = "sendcode";
    $param = array();
    $param['code'] = rand(1000,9999);
    $param['operation'] = 'login';
    $param['mobile'] = '15361057179';
    $param['player_id'] = 601702;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'mobilelogin') {
    $action = "mobilelogin";
    $param = array();
    $param['code'] = rand(1000,9999);
    $param['mobile'] = '15361057179';
    $param['player_id'] = 601702;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}
else if($api == 'goodsinfo') {
    $action = "goodsinfo";
    $param = array();
    $param['product_type'] = 0;
    $param['page_index'] = 0;
    $param['player_id'] = 601702;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
} else if($api == 'playerinfo') {
    $action = "playerinfo";

    $param = array();
    $param['player_id'] = 601702;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
} else if($api == 'orderadd') {
    $action = "orderadd";

    $param = array();
    $param['player_id'] = 601702;
    $param['goods_id'] = 2;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);

} else if($api == 'gamerecordstat') {
    $action = "gamerecordstat";

    $param = array();
    //$param['player_id'] = 601702;
    //$param['goods_id'] = 2;
    //$param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver_crons;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);

}else if($api == 'playerstatistical') {
    $action = "playerstatistical";

    $param = array();
    //$param['player_id'] = 601702;
    //$param['goods_id'] = 2;
    //$param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver_crons;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'mainscript') {
    $action = "mainscript";

    $param = array();
    //$param['player_id'] = 601702;
    //$param['goods_id'] = 2;
    //$param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = 'script';
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);

}else if($api == 'weixinpay') {
    $action = "orderadd";
    $param = array();
    $param['player_id'] = 601702;
    $param['goods_id'] = 1;
    $param['extension'] = '';
    $param['pay_type'] = 1;
    $param['pay_channel'] = 1;
    //$param['goods_id'] = 2;
    //$param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'feedback') {
    $action = "feedback";
    $param = array();
    $param['player_id'] = 601703;
    $param['feedback_content'] = "友间麻将很好玩。";
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'userranking') {
    $action = "userranking";
    $param = array();
    $param['player_id'] = 601703;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';
    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'usertokenranking') {
    $action = "usertokenranking";
    $param = array();
    $param['player_id'] = 601703;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';
    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'gamebeatlog') {
    $action = "gamebeatlog";
    $param = array();
    $param['player_id'] = 601703;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';
    $param['page'] = 1;
    $param['type'] = 0;
    $param['money_type'] = 1;

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'promoterewardinfo') {
    $action = "promoterewardinfo";
    $param = array();
    $param['player_id'] = 1075948;
    $param['page'] = 1;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'ordercallback') {
    $action = "ordercallback";
    $param = array();
    $param['event_id'] = 'weixin_pay';
    $param['order_id'] = 0;
    $param['order_no'] = 'a594ec16b1fdbe6b86511e78';
    $param['pay_type'] = 0;
    $param['pay_psw'] = 'bs1029384756';
    $param['pay_data'] = '{"out_trade_no":"a594ec16b1fdbe6b86511e78"}';
    $param['pay_param'] = '';
    $param['player_id'] = 600021;
     $param['time_stamp'] = 1517627845;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';
    $param['sign_value'] = '1C42D4ABF9858200CD9921CE646EEF9C';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
}else if($api == 'getusercommon') {



    $action = "getusercommon";
    $param = array();
    $param['player_list'] = "[601710,601714]";

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);

}else if($api == 'authorizedlogin') {
    $action = "authorizedlogin";
    $param = array();
    $param['player_id'] = 1082259;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);

}else if($api == 'appleiappay' ) {
  $action = "appleiappay";
    $param = array();
    $param['order_no'] = '1f2276792630c4a2b476532e';
    $param['pay_code'] = '09fdknrgj90wqj0fvnodfsj0a09jvdsmnovdj09dfsjni9sdfaji90vdni9fdhi9sdf9h89';
    $param['player_id'] = 1075948;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
	
}else if($api == 'realname') {
	$action = "realname";
    $param = array();
    $param['player_id'] = 1083021;
    $param['real_name'] = "井汝钰";
    $param['card_num'] = 511111198104137653;
    $param['player_token'] = 'kdioewjohvgnoewolfojnewo';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
	
}else if($api == 'clubroomdesklist') {
  $action = "clubroomdesklist";
  $param = array();
  $param['player_id'] = 1083021;
  $param['player_token'] = 'kdioewjohvgnoewolfojnewo';
  $param['club_id'] = 0;
  $param['club_room_id'] = 28;
  $param['club_room_desk_list'] = '[52,53,55]';

  $query = array();
  $query['param']['action'] = $action;
  $query['param']['version'] = Config::$ver;
  $query['param']['key_value'] = 1;
  $query['param']['flag_value'] = 1;
  $query['param']['sign_value'] = time();
  $query['param']['data_value'] = $param;
  $query['param'] = json_encode($query['param']);
  $url = $url_prefix . http_build_query($query);
  echo urldecode($url);
  $client = new httpclient();
  echo "<pre />";
  echo $client->get($url);
}else if ($api == 'playertransfercoins') {
    $action = "playertransfercoins";
    $param = array();
    $param['player_id'] = 1083170;
    $param['player_token'] = 'SFNtQW9meEhxZVVZbTYwa3hRNHg0RVVwbVhVUHZaZkpqQT09';
    $param['transfer_coins'] = 100;

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
} else if ($api == 'queryplayersafebox') {
    $action = "queryplayersafebox";
    $param = array();
    $param['player_id'] = 1083170;
    $param['player_token'] = 'SFNtQW9meEhxZVVZbTYwa3hRNHg0RVVwbVhVUHZaZkpqQT09';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
} else if ($api == 'playeremoticon') {
    $action = "playeremoticon";
    $param = array();
    $param['player_id'] = 1083170;
    $param['player_token'] = 'SFNtQW9meEhxZVVZbTYwa3hRNHg0RVVwbVhVUHZaZkpqQT09';
    $param['other_player_id'] = 0;

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
} else if ($api == 'updateplayerloginip') {
    $action = "updateplayerloginip";
    $param = array();
    $param['player_id'] = 1083170;
    $param['player_token'] = 'SFNtQW9meEhxZVVZbTYwa3hRNHg0RVVwbVhVUHZaZkpqQT09';

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
} else if ($api == 'queryplayerloginip') {
    $action = "queryplayerloginip";
    $param = array();
    $param['player_id'] = 1083170;

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
} else if ($api == 'updateplayeremoticonusedtimes') {
    $action = "updateplayeremoticonusedtimes";
    $param = array();
    $param['player_id'] = 1083170;
    $param['player_token'] = 'SFNtQW9meEhxZVVZbTYwa3hRNHg0RVVwbVhVUHZaZkpqQT09';
    $param['prop_id'] = 3;
    $param['prop_num'] = 1;

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
} else if ($api == 'playermessage') {
    $action = "playermessage";
    $param = array();
    $param['player_id'] = 1083170;
    $param['player_token'] = 'SFNtQW9meEhxZVVZbTYwa3hRNHg0RVVwbVhVUHZaZkpqQT09';
    $param['action_type'] = 'select';
    $param['attach_params'] = "";

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
} else if ($api == 'playergamerecord') {
    $action = "playergamerecord";
    $param = array();
    $param['player_id'] = 1083170;
    $param['player_token'] = 'SFNtQW9meEhxZVVZbTYwa3hRNHg0RVVwbVhVUHZaZkpqQT09';
    $param['action_type'] = 'select';
    $param['attach_params'] = "";

    $query = array();
    $query['param']['action'] = $action;
    $query['param']['version'] = Config::$ver;
    $query['param']['key_value'] = 1;
    $query['param']['flag_value'] = 1;
    $query['param']['sign_value'] = time();
    $query['param']['data_value'] = $param;
    $query['param'] = json_encode($query['param']);
    $url = $url_prefix . http_build_query($query);
    echo urldecode($url);
    $client = new httpclient();
    echo "<pre />";
    echo $client->get($url);
} else {
	echo '参数错误！';
	exit;
}
//notice  playbigrecord



?>
</div>
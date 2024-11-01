<?php
/*
Plugin Name: SugarCRM Integration
Plugin URI: http://sugarcrm-integration.bitweise.net/
Description: This plugin integrates your SugarCRM Admin Panel to your Wordpress Admin Panel.
Version: 1.4
Author: bitweise.NET
Author URI: http://www.bitweise.net
*/

add_action('admin_menu', 'sugarcrmIntegrationMenu');

function sugarcrmIntegrationMenu() {
	// add_menu_page('page_title, menu_title, capability, menu_slug, function, icon_url, position');
	add_menu_page('SugarCRM', 'SugarCRM', 10, 'sugarcrm_panel', 'sugarcrmPanel');
	// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	add_submenu_page('sugarcrm_panel', 'Panel', 'Panel', 10, 'sugarcrm_panel', 'sugarcrmPanel');
	add_submenu_page('sugarcrm_panel', 'Settings', 'Settings', 10, 'sugarcrm_settings', 'sugarcrmSettings');
}


function sugarcrmPanel() {

	// lay the foundations
	require_once('lib/nusoap.php'); 
	$sugarcrm_options = get_option("plugin_sugarcrm_options");

	// configuration of login data
	$sugarcrm_config['login'] = array(
		'user_name' => $sugarcrm_options['username'],
		'password' => $sugarcrm_options['password']
	);

	// Create a new soapClient 
	$sugarcrm_client = new nusoap_client($sugarcrm_options['url'].'/soap.php?wsdl', 'wsdl');

	// login and create sugarcrm session
	$sugarcrm_client_proxy = $sugarcrm_client->getProxy();
	$result = $sugarcrm_client_proxy->login($sugarcrm_config['login'], 'sugarcrm');
	$sugarcrm_session_id = $result['id'];

	// activate seamless login
	$result = $sugarcrm_client_proxy->seamless_login($sugarcrm_session_id);

	echo '<iframe style="width:100%; height: 1400px;" src="'.$sugarcrm_options['url'].'/index.php?module=Home&action=index&MSID='.$sugarcrm_session_id.'" scrolling="no" frameborder="0" ></iframe>';
}


function sugarcrmSettings() {

	$sugarcrm_options = get_option("plugin_sugarcrm_options");
	
	if (!is_array( $sugarcrm_options )) { // Pruefe ob variable KEIN array ist
		$sugarcrm_options = array(
			'url' => '', // schublade url = standard wert
			'username' => '', // schublade username = standard wert
			'password' => '' // schublade password = standard wert
		);
	} // fertig
	
	// wenn form abgeschickt
	if ($_POST['crm-settings-save']) { // daten uebergeben in...
		$sugarcrm_options['url'] = htmlspecialchars($_POST['crm_url']); // variable
		$sugarcrm_options['username'] = htmlspecialchars($_POST['crm_user']); // variable
		$sugarcrm_options['password'] = md5(htmlspecialchars($_POST['crm_pwd'])); // variable
		update_option("plugin_sugarcrm_options", $sugarcrm_options); // in option
	}
?>

<div class="crm-settings-panel">
<h2>SugarCRM Integration Plugin</h2>
This plugin allows you to integrate your existing SugarCRM to your Wordpress admin panel.<br />
<br />
<b>Explanation:</b><br />
<br />
<ol>
<li>Enter the path to your SugarCRM installation and your login data below.</li>
<li>Click on the save button below.</li>
<li>Visit the SugarCRM panel on the left sidebar.</li>
<li>Enjoy!</li>
</ol>
<br />
  <h3>SugarCRM Settings</h3>
  <div class="crmform">
    <div class="metabox-prefs">
      <form action="" method="post">
        <label for="crm_url">URL : </label>
        <input type="text" name="crm_url" id="crm_url" value="<?php echo ($sugarcrm_options['url']); ?>" size="31" />
		<i>e.g. "http://www.domain.com/sugarcrm" (without the "/" at the end)
        <br />
        <label for="crm_user">Username : </label>
        <input type="text" name="crm_user" id="crm_user" value="<?php echo ($sugarcrm_options['username']); ?>" size="25" /> 
		<i>e.g. "admin"</i>
        <br />
        <label for="crm_pwd">Password : </label>
        <input type="password" name="crm_pwd" id="crm_pwd" size="25" />
		<i>e.g. "secret"</i>
        <br />
        <input type="submit" name="crm-settings-save" id="crm-settings-save" value="Save" class="button" />
        <!--<input type="hidden" name="redirect_to" value="blub.php" />-->
      </form>
    </div>
  </div>
	<br /><i>The entries have to be done without the quotation marks ("..."). Please make sure that you enter all data in order to save them correctly.</i>
</div>
<?php 
}

function include_sugarcrm() { 
	// do something
}
?>
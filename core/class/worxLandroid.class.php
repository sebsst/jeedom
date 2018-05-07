<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/
/*
 -- info -- 
 https://github.com/mjiderhamn/worx-landroid-nodejs (Home automation integration for Worx Landroid robotic mowers)
 https://hackaday.io/project/6717-project-landlord (Open source firmware for Worx Landroid robotic mower.)
 https://www.worxlandroid.com/en/software-update (firmware update)
 https://github.com/ldittmar81/ioBroker.landroid
 //Redpine Signals, Inc.
 
/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class worxLandroid extends eqLogic {
	public static $_widgetPossibility = array('custom' => true);
	/*     * *************************Attributs****************************** */
	
	public static $_infosMap = array();
	public static $_actionMap = array();
	/*     * ***********************Methode static*************************** */

	/*
	* Fonction exécutée automatiquement toutes les minutes par Jeedom
	public static function cron() {

	}
	*/

	public static function initInfosMap(){
		
		self::$_actionMap = array(
			'refresh' => array(
				'name' => 'Rafraichir',
			),
			'start' => array(
				'name' => 'Démarrage',
				'cmd' => 'data=%5B%5B%22settaggi%22%2C11%2C1%5D%5D', //data=[["settaggi",{},1]]
			),
			'stop' => array(
				'name' => 'Stop',
				'cmd' => 'data=%5B%5B%22settaggi%22%2C12%2C1%5D%5D', //data=[["settaggi",{},1]]
			),
			'startZoneTraining' => array(
				'name' => 'Démarrage Entrainement',
				'cmd' => 'data=%5B%5B%22settaggi%22%2C11%2C1%5D%5D', //data=[["settaggi",{},1]]
			),
// 			'setWorkingTimePercent' => array(
// 				'name' => "Définir Poucentage de temps de travail",
// 				'linkedInfo' => 'WorkingTimePercent',
// 				'subtype' => 'slider',
// 				'cmd' => 'data=%5B%5B%22percent_programmatore%22%2C0%2C[[[VALUE]]]%5D%5D', //data=[["percent_programmatore",0,100]]
// 			),
				/* a verifier		
				// 11 = start
				// 12 = stop (& return to base)
				// 13 = charging complete
				// 14 = manual stop
				// 15 = going home
				*/
		);
	
		self::$_infosMap = array(
	 		//'default' => array(
	 		//	'type' => 'info',
	 		//	'subtype' => 'numeric',
	 		//	'isvisible' => true,
	 		//	'restkey' =>'',
	 		//),
	 		'communicationStatus' => array(
				'name' => "Status de connexion",
				'subtype' => 'binary',
				'isvisible' => true,
			),
			'firmwareVersion' => array(
				'name' => "Version du firmware",
				'restkey' =>'versione_fw', //"versione_fw": 2.45,
				'isvisible' => true,
			),
			'language' => array(
				'name' => "code Langue",
				'restkey' =>'lingua',//"lingua": 2,
			),
			'languageStr' => array(
				'name' => "Langue",
				'restkey' =>'lingua',//"lingua": 2,
				'subtype' => 'string',
				'cbTransform' => function ($rawValue)
				{
					$langue =  array(
						'0' =>"Anglais",
						'1' =>"Italien",
						'2' =>"Allemand",
						'3' =>"Français",
						'4' =>"Espagnol",
						'5' =>"Portugais",
						'6' =>"Danois",
						'7' =>"Néerlandais",
						'8' =>"Finnois",
						'9' =>"Norvégien",
						'10' =>"Suédois",
					);
					return ($langue[$rawValue]);
				},
			),
			'batteryPercentage' => array(
				'name' => "Pourcentage de batterie",
				'restkey' =>'perc_batt', //	"perc_batt": "100",
				'isvisible' => true,
				'unite' => '%',
			),
			'workingTimePercent' => array(
				'name' => "Poucentage de temps de travail",
				//'linkedAction' => 'setWorkingTimePercent',
				'restkey' =>'percent_programmatore', //"percent_programmatore": 0,
				'isvisible' => true,
				'unite' => '%',
			),
			'totalMowingHours' => array(
				'name' => "Temps total de tonte",
				'restkey' =>'ore_movimento', //"ore_movimento": 626, // Provided as 0.1h
				'isvisible' => true,
			),
			'timeFormat' => array(
				'name' => "Format heure",
				'restkey' =>'time_format', //"time_format": 1,
			),
			'dateFormat' => array(
				'name' => "Format date",
				'restkey' =>'date_format', //"date_format": 0,
			),
			'rit_pioggia' => array(
				'name' => "Tondre apres la pluie",
				'unite' => 'min',
				'restkey' =>'rit_pioggia', //"rit_pioggia": 180,
				'isvisible' => true,
			),
			'area' => array(
				'name' => "Area",
				'restkey' =>'area', //"area": 0,
			),
			'enab_bordo' => array(
				'name' => "Activé la coupe des bordures",
				'restkey' =>'enab_bordo',
			),
			'indice_area' => array(
				'name' => "Taille du jardin",
				'restkey' =>'indice_area',//"indice_area": 9, 
				'unite' => 'm²',
				'cbTransform' => function ($rawValue)
				{
					return ($rawValue * 100) + 100;
				},
			),
			'tempo_frenatura' => array(
				'name' => "Temps de freinage",
				'restkey' =>'tempo_frenatura',//"tempo_frenatura": 20,
			),
			'perc_rallenta_max' => array(
				'name' => "Pourcentage de ralentissement max",
				'restkey' =>'perc_rallenta_max', //"perc_rallenta_max": 70,
			),
			'canale' => array(
				'name' => "Canal",
				'restkey' =>'canale', //"canale": 0,
			),
			'num_ricariche_batt' => array(
				'name' => "Nombres de recharge de la batterie",
				'restkey' =>'num_ricariche_batt', //"num_ricariche_batt": 0,
			),
			'num_aree_lavoro' => array(
				'name' => "Numéro de la zone de travail",
				'restkey' =>'num_aree_lavoro', //"num_aree_lavoro": 1,
			),
			'area_in_lavoro' => array(
				'name' => "Zone de travail",
				'restkey' =>'area_in_lavoro', //	"area_in_lavoro": 0,
			),
			'email' => array(
				'name' => "Email",
				'subtype' => 'string',
				'restkey' =>'email', //"email": "xxxxxxx@xxxxxx.xxx",
			),
			'ver_proto' => array(
				'name' => "ver_proto",
				'restkey' =>'ver_proto', //"ver_proto": 1,
			),
			'state' => array(
				'name' => "Status",
				'subtype' => 'string',
				'restkey' =>'state', //"state": "home","grass cutting","following wire"
				'isvisible' => true,
			),
			'workReq' => array(
				'name' => "workReq",
				'subtype' => 'string',
				'restkey' =>'workReq', //"workReq": "user req grass cut",
			),
			'message' => array(
				'name' => "Message",
				'subtype' => 'string',
				'restkey' =>'message', //"message": "none",
			),
			'batteryChargerState' => array(
				'name' => "Status du chargeur de batterie",
				'subtype' => 'string',
				'restkey' =>'batteryChargerState', //"batteryChargerState": "idle",
			),
			'distance' => array(
				'name' => "Distance",
				'restkey' =>'distance', //"distance": 0
			),
			'mac' => array(
				'name' => "Mac adresse",
				'subtype' => 'string',
				'restkey' =>'mac', //"mac": [0, 35, 167, 164, 213, 71],
				'cbTransform' => function ($rawValue)
				{
					return strtoupper(implode(':',array_map("sprintf",array_fill(0,6,'%02x'),$rawValue)));
				},
			),
			'ore_funz' => array(// Decides for how long the mower will work each day, probably expressed as 0,1 h
				'name' => "Heures de fonctionement",
				'subtype' => 'string',
				'restkey' =>'ore_funz', //"ore_funz": [0, 0, 0, 0, 0, 0, 0],
				'cbTransform' => function ($rawValue)
				{
					return json_encode(array_combine(array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'),$rawValue));
				},
			),
			
			'ora_on' => array(// Hour of day that the Landroid should mowing, per weekday
				'name' => "Heure de la tonte par jours",
				'subtype' => 'string',
				'restkey' =>'ora_on', //"ora_on": [0, 0, 0, 0, 0, 0, 0],
				'cbTransform' => function ($rawValue)
				{
					return json_encode(array_combine(array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'),$rawValue));
				},
			),
			'min_on' => array(// Minutes on the hour (above) that the Landroid should start mowing, per weekday
				'name' => "Minute de la tonte par jours",
				'subtype' => 'string',
				'restkey' =>'min_on', //"min_on": [0, 0, 0, 0, 0, 0, 0],
				'cbTransform' => function ($rawValue)
				{
					return json_encode(array_combine(array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'),$rawValue));
				},
			),
			'allarmi' => array( // Alarms - flags set to 1 when alarm is active
				'name' => "Alarmes",
				'subtype' => 'string',
				'restkey' =>'allarmi', //"allarmi": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				'cbTransform' => function ($rawValue)
				{
					$alarmStr = array(
						0 => "Blade blocked",                                      
						1 => "Repositioning error",                                
						2 => "Wire bounced",                                       
						3 => "Blade blocked",                                      
						4 => "Outside wire",// ("Outside working area")              
						5 => "Mower lifted",// ("Lifted up")                         
						6 => "Error 6",                                            
						7 => "Upside down",// (Set when "Lifted up" - "Upside down"?)
						8 => "Error 8",                                            
						9 => "Collision sensor blocked",                           
						10 => "Mower tilted",                                      
						11 => "Charge error",// (Set when "Lifted up"?)              
						12 => "Battery error",                                    
					);
					$alarm = '';
					foreach($rawValue as $idx=>$alarmed)
					{
						if(isset($alarmStr[$idx]) && ($alarmed==1))
							$alarm .= $alarmStr[$idx].';';
					}
					return $alarm;
					//return json_encode($rawValue);
				},
// 				"allarmi": [ // Alarms - flags set to 1 when alarm is active
// 					0, // [0] "Blade blocked"                                               ERROR_MESSAGES[0] = "Blade blocked";
// 					0, // [1] "Repositioning error"                                         ERROR_MESSAGES[1] = "Repositioning error";
// 					0, // [2] "Wire bounced"                                                ERROR_MESSAGES[WIRE_BOUNCED_ALARM_INDEX] = "Wire bounced";
// 					0, // [3] "Blade blocked"                                               ERROR_MESSAGES[3] = "Blade blocked";
// 					0, // [4] "Outside wire" ("Outside working area")                       ERROR_MESSAGES[4] = "Outside wire";
// 					0, // [5] "Mower lifted" ("Lifted up")                                  ERROR_MESSAGES[5] = "Mower lifted";
// 					0, // [6] "error 6"                                                       ERROR_MESSAGES[6] = "Alarm 6";
// 					0, // [7] "Upside down" (Set when "Lifted up" - "Upside down"?)               ERROR_MESSAGES[7] = "Upside down";
// 					0, // [8] "error 8"                                                       ERROR_MESSAGES[8] = "Alarm 8";
// 					0, // [9] "Collision sensor blocked"                                    ERROR_MESSAGES[8] = "Collision sensor blocked";
// 					0, // [10] "Mower tilted"                                               ERROR_MESSAGES[10] = "Mower tilted";
// 					0, // [11] "Charge error" (Set when "Lifted up"?)                       ERROR_MESSAGES[11] = "Charge error";
// 					0, // [12] "Battery error"                                              ERROR_MESSAGES[12] = "Battery error";
// 					0, // Reserved for future use?
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0, // -- " --
// 					0  // -- " --
// 				],
			),
			'settaggi' => array(// Settings / state
				'name' => "Parametres",
				'subtype' => 'string',
				'restkey' =>'settaggi', //"settaggi": [0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				'cbTransform' => function ($rawValue)
				{
					return json_encode($rawValue);
				},
// 				"settaggi": [ // Settings / state
// 					0,
// 					0,
// 					0,
// 					0,
// 					1,
// 					0, // "in base" ("charging" or "charging completed", see [13])
// 					0,
// 					1,
// 					1,
// 					1,
// 					0,
// 					0, // "start"
// 					0, // "stop"
// 					0, // "charging completed"
// 					0, // "manual stop"
// 					0, // "going home"
// 					0,
// 					0,
// 					0,
// 					0,
// 					0,
// 					0,
// 					0,
// 					0,
// 					0,
// 					0,
// 					0,
// 					0,
// 					0,
// 					0,
// 					0
// 				],
			),
			'dist_area' => array( // Distance in meters to the zone starts
				'name' => "Distance en metre de la zone de depart",
				'subtype' => 'string',
				'restkey' =>'dist_area', //"dist_area": [1, 1, 1, 1],
				'cbTransform' => function ($rawValue)
				{
					return json_encode($rawValue);
				},
			),
			'perc_per_area' => array( // Percentage per zone, expressed in 10% increments (i.e. 3 = 30%)
				'name' => "Pourcentage par zone",
				'subtype' => 'string',
				'restkey' =>'perc_per_area', //"perc_per_area": [1, 1, 1, 1],
				'cbTransform' => function ($rawValue)
				{
					return json_encode($rawValue);
				},
			),
		);
	}
	
	public static function cron() {
		foreach (eqLogic::byType('worxLandroid') as $worxLandroid) 
		{
			$worxLandroid->getInformations();
			$mc = cache::byKey('worxLandroidWidgetmobile' . $worxLandroid->getId());
			$mc->remove();
			$mc = cache::byKey('worxLandroidWidgetdashboard' . $worxLandroid->getId());
			$mc->remove();
			$worxLandroid->toHtml('mobile');
			$worxLandroid->toHtml('dashboard');
			$worxLandroid->refreshWidget();
		}
	}

	/*
	* Fonction exécutée automatiquement toutes les heures par Jeedom
	public static function cronHourly() {

	}
	*/

	/*
	* Fonction exécutée automatiquement tous les jours par Jeedom
	public static function cronDayly() {

	}
	*/

	/*     * *********************Méthodes d'instance************************* */
	
	public function refresh() {
		try {
			$this->getInformations();
		} catch (Exception $exc) {
			log::add('worxLandroid', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
		}
	}
	
	public function getInformations($jsondata=null)
	{
		if ($this->getIsEnable() == 1)
		{
			$equipement = $this->getName();

			if(is_null($jsondata))
			{
				$ip = $this->getConfiguration('addressip');
				$user = $this->getConfiguration('user','admin');
				$pin = $this->getConfiguration('pincode');
			
				$url = "http://{$user}:{$pin}@{$ip}/jsondata.cgi";
				log::add('worxLandroid', 'debug', __METHOD__.' '.__LINE__.' requesting '.$url);
				
				//$jsondata = file_get_contents($url);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$jsondata = curl_exec($ch);
				curl_close($ch);
			}
 			
 			log::add('worxLandroid', 'debug', __METHOD__.' '.__LINE__.' $jsondata '.$jsondata);

 			$json = json_decode($jsondata,true);
 			
 			if (is_null($json))
 			{
				log::add('worxLandroid', 'info', 'Connexion KO for '.$equipement.' ('.$ip.')');
				$this->checkAndUpdateCmd('communicationStatus',false);
				return false;
			}
			if (!isset($json['allarmi']))
			{
				log::add('worxLandroid', 'error', 'Check PinCode for '.$equipement.' ('.$ip.')');
				$this->checkAndUpdateCmd('communicationStatus',false);
				return false;
			}
			
			$this->checkAndUpdateCmd('communicationStatus',true);
			
			self::initInfosMap();
			
			//update cmdinfo value
			foreach(self::$_infosMap as $cmdLogicalId=>$params)
			{
				if(isset($params['restkey'], $json[$params['restkey']]))
				{
					//log::add('worxLandroid', 'debug',  __METHOD__.' '.__LINE__.' '.$cmdLogicalId.' => '.json_encode($json[$params['restkey']]));
					$value = $json[$params['restkey']];
					if(isset($params['cbTransform']) && is_callable($params['cbTransform']))
					{
						$value = call_user_func($params['cbTransform'], $value);
						//log::add('worxLandroid', 'debug', __METHOD__.' '.__LINE__.' Transform to => '.json_encode($value));
					}
					
					$this->checkAndUpdateCmd($cmdLogicalId,$value);
				}
			}
			return true;
		}
	}

	public function postSave() {
		self::initInfosMap();
		$order = 0;
		
		//Cmd Actions
		foreach(self::$_actionMap as $cmdLogicalId => $params)
		{
			$worxLandroidCmd = $this->getCmd('action', $cmdLogicalId);
			if (!is_object($worxLandroidCmd))
			{
				log::add('worxLandroid', 'debug', __METHOD__.' '.__LINE__.' cmdAction create '.$cmdLogicalId.'('.__($params['name'], __FILE__).') '.($params['subtype'] ?: 'subtypedefault'));
				$worxLandroidCmd = new worxLandroidCmd();
			
				$worxLandroidCmd->setLogicalId($cmdLogicalId);
				$worxLandroidCmd->setEqLogic_id($this->getId());
				$worxLandroidCmd->setName(__($params['name'], __FILE__));
				$worxLandroidCmd->setType($params['type'] ?: 'action');
				$worxLandroidCmd->setSubType($params['subtype'] ?: 'other');
				$worxLandroidCmd->setIsVisible($params['isvisible'] ?: true);
				if(isset($params['tpldesktop']))
					$worxLandroidCmd->setTemplate('dashboard',$params['tpldesktop']);
				if(isset($params['tplmobile']))
					$worxLandroidCmd->setTemplate('mobile',$params['tplmobile']);
				$worxLandroidCmd->setOrder($order++);
				
				if(isset($params['linkedInfo']))
					$worxLandroidCmd->setValue($this->getCmd('info', $params['linkedInfo']));
				
				$worxLandroidCmd->save();
			}
		}
		
		//Cmd Infos
		foreach(self::$_infosMap as $cmdLogicalId=>$params)
		{
			$worxLandroidCmd = $this->getCmd('info', $cmdLogicalId);
			if (!is_object($worxLandroidCmd))
			{
				log::add('worxLandroid', 'debug', __METHOD__.' '.__LINE__.' cmdInfo create '.$cmdLogicalId.'('.__($params['name'], __FILE__).') '.($params['subtype'] ?: 'subtypedefault'));
				$worxLandroidCmd = new worxLandroidCmd();
				
				$worxLandroidCmd->setLogicalId($cmdLogicalId);
				$worxLandroidCmd->setEqLogic_id($this->getId());
				$worxLandroidCmd->setName(__($params['name'], __FILE__));
				$worxLandroidCmd->setType($params['type'] ?: 'info');
				$worxLandroidCmd->setSubType($params['subtype'] ?: 'numeric');
				$worxLandroidCmd->setIsVisible($params['isvisible'] ?: false);
				if(isset($params['unite']))
					$worxLandroidCmd->setUnite($params['unite']);
				$worxLandroidCmd->setTemplate('dashboard',$params['tpldesktop']?: 'badge');
				$worxLandroidCmd->setTemplate('mobile',$params['tplmobile']?: 'badge');
				$worxLandroidCmd->setOrder($order++);

				$worxLandroidCmd->save();
			}
		}
		
		//refreshcmdinfo
		$this->getInformations();
	}
	
	public function toHtml($_version = 'dashboard') {
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) {
			return $replace;
		}
		$version = jeedom::versionAlias($_version);
		$cmd_html = '';
		$br_before = 0;
		foreach ($this->getCmd(null, null, true) as $cmd) {
			if (isset($replace['#refresh_id#']) && $cmd->getId() == $replace['#refresh_id#']) {
				continue;
			}
			if ($br_before == 0 && $cmd->getDisplay('forceReturnLineBefore', 0) == 1) {
				$cmd_html .= '<br/>';
			}
			
			$cmd_html .= $cmd->toHtml($_version, '', $replace['#cmd-background-color#']);
			$br_before = 0;
			if ($cmd->getDisplay('forceReturnLineAfter', 0) == 1) {
				$cmd_html .= '<br/>';
				$br_before = 1;
			}
		}
		$replace['#cmd#'] = $cmd_html;
		return template_replace($replace, getTemplate('core', $version, 'worxLandroid', 'worxLandroid'));
	}
	
	/*     * **********************Getteur Setteur*************************** */
}

class worxLandroidCmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */

	/*
	* Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
	public function dontRemoveCmd() {
	return true;
	}
	*/
	public function execute($_options = array())
 	{
		log::add('worxLandroid', 'debug', __METHOD__.'('.json_encode($_options).') Type: '.$this->getType().' logicalId: '.$this->getLogicalId());
		
		if ($this->getLogicalId() == 'refresh') 
		{
			$this->getEqLogic()->refresh();
			return;
		}

		if( $this->getType() == 'action' )
		{
		
			if( $this->getSubType() == 'slider' && $_options['slider'] == '')
				return;
				
			worxLandroid::initInfosMap();
			if (isset(worxLandroid::$_actionMap[$this->getLogicalId()]))
			{
				$params = worxLandroid::$_actionMap[$this->getLogicalId()];

				if(isset($params['callback']) && is_callable($params['callback']))
				{
					log::add('worxLandroid', 'debug', __METHOD__.'calling back');
					call_user_func($params['callback'], $this);
				}elseif(isset($params['cmd']))
				{
					$cmdval = $params['cmd'];
					if($this->getSubType() == 'slider')
						$cmdval = str_replace('[[[VALUE]]]',$_options['slider'],$cmdval);
					
					$eqLogic = $this->getEqLogic();
					$ip = $eqLogic->getConfiguration('addressip');
					$user = $eqLogic->getConfiguration('user','admin');
					$pin = $eqLogic->getConfiguration('pincode');
					$url = "http://{$user}:{$pin}@{$ip}/jsondata.cgi";
				
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS,$cmdval);
					$jsondata = curl_exec($ch);
					curl_close($ch);
					log::add('worxLandroid', 'debug', __METHOD__.'('.$url.' with '.$cmdval.') '.$jsondata);
					
					$eqLogic->getInformations($jsondata);
				}

				return true;
			}
		} else {
			throw new Exception(__('Commande non implémentée actuellement', __FILE__));
		}
		return false;
	}
	
	/*     * **********************Getteur Setteur*************************** */
}
/*
{
	"versione_fw": 2.45,
	"lingua": 2,
	"ore_funz": [0, 0, 0, 0, 0, 0, 0],
	"ora_on": [0, 0, 0, 0, 0, 0, 0],
	"min_on": [0, 0, 0, 0, 0, 0, 0],
	"allarmi": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
	"settaggi": [0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
	"mac": [0, 35, 167, 164, 213, 71],
	"time_format": 1,
	"date_format": 0,
	"rit_pioggia": 180,
	"area": 0,
	"enab_bordo": 1,
	"percent_programmatore": 0,
	"indice_area": 9, //taille du jardin 
	"tempo_frenatura": 20,
	"perc_rallenta_max": 70,
	"canale": 0,
	"num_ricariche_batt": 0,
	"num_aree_lavoro": 1,
	"dist_area": [1, 1, 1, 1],
	"perc_per_area": [1, 1, 1, 1],
	"area_in_lavoro": 0,
	"email": "xxxxxxx@xxxxxx.xxx",
	"perc_batt": "100",
	"ver_proto": 1,
	"state": "home",
	"workReq": "user req grass cut",
	"message": "none",
	"batteryChargerState": "idle",
	"distance": 0
}

{
	"CntProg": 95, // Firmware version?????
	"lingua": 0, // Language in use
	"ore_funz": [ // Decides for how long the mower will work each day, probably expressed as 0,1 h
		100,
		122,
		100,
		120,
		110,
		40,
		50
	],
	"ora_on": [ // Hour of day that the Landroid should mowing, per weekday
		4,
		4,
		2,
		3,
		3,
		2,
		2
	],
	"min_on": [ // Minutes on the hour (above) that the Landroid should start mowing, per weekday
		0,
		0,
		0,
		0,
		0,
		0,
		0
	],
	"allarmi": [ // Alarms - flags set to 1 when alarm is active
		0, // [0] "Blade blocked"
		0, // [1] "Repositioning error"
		0, // [2] "Outside wire" ("Outside working area") 
		0, // [3] "Blade blocked"
		0, // [4] "Outside wire" ("Outside working area")
		0, // [5] "Mower lifted" ("Lifted up")
		0, // [6] "error"
		0, // [7] "error" (Set when "Lifted up" - "Upside down"?)
		0, // [8] "error"
		0, // [9] "Collision sensor blocked"
		0, // [10] "Mower tilted"
		0, // [11] "Charge error" (Set when "Lifted up"?)
		0, // [12] "Battery error"
		0, // Reserved for future use?
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0, // -- " --
		0  // -- " --
	],
	"settaggi": [ // Settings / state
		0,
		0,
		0,
		0,
		1,
		0, // "in base" ("charging" or "charging completed", see [13])
		0,
		1,
		1,
		1,
		0,
		0, // "start"
		0, // "stop"
		0, // "charging completed"
		0, // "manual stop"
		0, // "going home"
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0
	],
	"mac": [ // The MAC address of the Landroid WiFi
		...,
		...,
		...,
		...,
		...,
		...
	],
	"time_format": 1, // Time format
	"date_format": 2, // Date format
	"rit_pioggia": 180, // Time to wait after rain, in minutes
	"area": 0,
	"enab_bordo": 1, // Enable edge cutting
	"g_sett_attuale": 1, // Is charging???
	"g_ultimo_bordo": 0,
	"ore_movimento": 626, // Total time the mower has been mowing, expressed in 0,1 h 
	"percent_programmatore": 50, // Working time percent (increase)
	"indice_area": 9, // taille jardin
	
	
	"tipo_lando": 8,
	"beep_hi_low": 0,
	"gradi_ini_diritto": 30, // Something "right"?
	"perc_cor_diritto": 103, // Something "right"?
	"coef_angolo_retta": 80, // Something "straigt line"?
	"scost_zero_retta": 1,   // Something "straigt line"?
	"offset_inclinometri": [ // Probably the calibration of the sensors?
		2039,
		2035,
		2672
	],
	"gr_rall_inizio": 80,
	"gr_rall_finale": 300,
	"gr_ini_frenatura": 130,
	"perc_vel_ini_frenatura": 50, // Something "brake" (battery percent when returning to charger???)
	"tempo_frenatura": 20,
	"perc_rallenta_max": 50,
	"canale": 0,
	"num_ricariche_batt": 0,
	"num_aree_lavoro": 4, // Number of zones in use
	"Dist_area": [ // Distance in meters to the zone starts
		18,
		71,
		96,
		129
	],
	"perc_per_area": [ // Percentage per zone, expressed in 10% increments (i.e. 3 = 30%)
		1,
		2,
		3,
		4
	],
	"area_in_lavoro": 5,
	"email": "...", // The e-mail address used to log into the app
	"perc_batt": "100" // Charge level of the battery
}
*/
?>

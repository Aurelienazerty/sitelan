<?php
/**
 *
 * photos extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2013 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace aurelienazerty\sitelan\event;

/**
 * Event listener
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface {

	/** @var \phpbb\user $user */
	protected $user;
	
	/** @var \phpbb\language\language $language */
	protected $language;
	
	/** @var \phpbb\template\template $template */
	protected $template;

	/**
	 * Constructor
	 *
	 * @param \phpbb\user	$user	user object
   * @param \phpbb\language\language $language language object
	 * @return \aurelienazerty\sitelan\event\listener
	 * @access public
	 */
	public function __construct(\phpbb\template\template $template, \phpbb\user $user, \phpbb\language\language $language) {
		$this->template = $template;
		$this->user = $user;
		$this->language = $language;
	}

	static public function getSubscribedEvents() {
		return array(
			'core.viewtopic_cache_user_data'	=> 'add_compteur_lan_user_post',
			'core.viewtopic_modify_post_row'	=> 'modify_post_row',
			'core.memberlist_view_profile'		=> 'add_compteur_lan_user_profil'
		);
	}
	
	private function getUrlStat($user_id) {
		return '/html/lan/rechercher-lan-de-' . $user_id . '.html';
	}
	
	public function add_compteur_lan_user_profil($event) {
		$member = $event['member'];
		$user_id = $member['user_id'];
		$this->language->add_lang('common', 'aurelienazerty/sitelan');
		$this->template->assign_var('VIEW_USER_LAN'	, $this->getUrlStat($user_id));
		$this->template->assign_var('USER_NB_LAN'	, getNbLANUser($user_id));
	}
	
	public function modify_post_row($event) {
		if ($this->guest)
		{
			return;
		}
		$user_id = $event['post_row']['POSTER_ID'];
		//var_dump($event);die;
		
		$event['post_row'] = array_merge($event['post_row'], [
			'VIEW_USER_LAN' => $this->getUrlStat($user_id),
			'USER_NB_LAN' 	=> getNbLANUser($user_id),//count
		]);
	}
	
	public function add_compteur_lan_user_post($event) {
		$user_data = $event['user_cache_data'];
		$this->language->add_lang('common', 'aurelienazerty/sitelan');
		
		$user_data = array_merge($user_data, [
			'view_user_lan' => $event['row']['VIEW_USER_LAN'],//url
			'user_nb_lan' 	=> $event['row']['USER_NB_LAN'],//count
		]);
		$event['user_cache_data'] = $user_data;
	}
	
}

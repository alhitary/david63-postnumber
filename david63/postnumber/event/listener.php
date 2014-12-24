<?php
/**
*
* @package Post Number Extension
* @copyright (c) 2014 david63
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace david63\postnumber\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\template\twig\twig */
	protected $template;

	/**
	* Constructor for listener
	*
	* @param \phpbb\template\twig\twig $template phpBB template
	* @access public
	*/
	public function __construct($template)
	{
		$this->template	= $template;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'								=> 'load_language_on_setup',
			'core.viewtopic_post_rowset_data'				=> 'post_row_data',
			'core.viewtopic_modify_post_row'				=> 'modify_post_row',
			'core.viewtopic_modify_post_action_condition'	=> 'modify_topic_array',
		);
	}

	/**
	* Load common post number language files during user setup
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function load_language_on_setup($event)
	{
		$lang_set_ext	= $event['lang_set_ext'];
		$lang_set_ext[]	= array(
			'ext_name' => 'david63/postnumber',
			'lang_set' => 'postnumber',
		);

		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	* Modify the post row data
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function post_row_data($event)
	{
		$rowset_data				= $event['rowset_data'];
		$count_post					= ($rowset_data['hide_post'] == false) ? 1 : 0;
		$rowset_data['count_post']	= $count_post;

		$event->offsetSet('rowset_data', $rowset_data);
	}

	/**
	* Modify the topic array
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function modify_topic_array($event)
	{
		$topic_data					= $event['topic_data'];
		$topic_data['post_count']	= 0;

		$event->offsetSet('topic_data', $topic_data);
	}

	/**
	* Modify the post row
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function modify_post_row($event)
	{
		$row						= $event['row'];
		$post_row					= $event['post_row'];
		$topic_data 				= $event['topic_data'];
		$topic_data['post_count']	= $topic_data['post_count'] + $row['count_post'];

		$post_row = array_merge($post_row, array(
			'POST_COUNT' => $topic_data['post_count'],
		));

		$event->offsetSet('topic_data', $topic_data);
		$event->offsetSet('post_row', $post_row);
	}
}

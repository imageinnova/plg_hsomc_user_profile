<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('checkbox');

/**
 * Provides input for TOS
 *
 * @since  2.5.5
 */
class JFormFieldTos extends JFormFieldCheckbox
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  2.5.5
	 */
	protected $type = 'Tos';

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   2.5.5
	 */
	protected function getLabel() {
		return '';
	}
	protected function getInput() {
		$html = '';
		
		if ($this->hidden)
		{
			return $html;
		}
		
		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;
		
		// Set required to true as this field is not displayed at all if not required.
		$this->required = true;
		
		$tosarticle = $this->element['article'] > 0 ? (int) $this->element['article'] : 0;
		
		if ($tosarticle)
		{
			JLoader::register('ContentHelperRoute', JPATH_BASE . '/components/com_content/helpers/route.php');
		
			$attribs          = array();
			$attribs['data-toggle'] = 'modal';
			$attribs['data-target'] = '#modalTOS';
		
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, alias, catid, language')
			->from('#__content')
			->where('id = ' . $tosarticle);
			$db->setQuery($query);
			$article = $db->loadObject();
		
			if (JLanguageAssociations::isEnabled())
			{
				$tosassociated = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $tosarticle);
			}
		
			$current_lang = JFactory::getLanguage()->getTag();
		
			if (isset($tosassociated) && $current_lang != $article->language && array_key_exists($current_lang, $tosassociated))
			{
				$url  = ContentHelperRoute::getArticleRoute($tosassociated[$current_lang]->id, $tosassociated[$current_lang]->catid);
				$link = JRoute::_($url . '&tmpl=component&lang=' . $tosassociated[$current_lang]->language);
			}
			else
			{
				$slug = $article->alias ? ($article->id . ':' . $article->alias) : $article->id;
				$url  = ContentHelperRoute::getArticleRoute($slug, $article->catid);
				$link = JRoute::_($url . '&tmpl=component&lang=' . $article->language);
			}
		}
		else
		{
			$link = $text;
		}
		
		$stem = JText::_($this->element['description']);
		$linked_text = JText::_($this->element['label']);

		$html = "<div class=\"checkbox\">";
		$html .= "<label>";
		$html .= "<input type=\"checkbox\" id=\"{$this->id} value=\"1\">";
		$html .= "{$stem} <a type=\"button\" data-toggle=\"modal\" data-target=\"#{$this->id}-txt\">{$linked_text}</a>";
		$html .= "</label>";
		$html .= "</div>";
		$html .= "<div id=\"{$this->id}-txt\" class=\"modal fade\" tabindex=\"-1\">";
		$html .= "<div class=\"modal-dialog\">";
		$html .= "<div class=\"modal-content\">";
		$html .= "<div class=\"modal-header\">";
		$html .= "<button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>";
		$html .= "<h4 id=\"myModalLabel\" class=\"modal-title\">{$linked_text}</h4>";
		$html .= "</div>";
		$html .= "<div class=\"modal-body\">";
		$html .= "<iframe width=\"100%\" class=\"modal-body\" src=\"{$link}\"></iframe>";
		$html .= "</div>";
		$html .= "<div class=\"modal-footer\">";
		$html .= "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>";
		$html .= "</div>";
		$html .= "</div>";
		$html .= "</div>";
		$html .= "</div>";
		
		return $html;
	}
}

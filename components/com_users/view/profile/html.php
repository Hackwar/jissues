<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Profile view class for Users.
 *
 * @package     Joomla.Site
 * @subpackage  com_users
 * @since       1.6
 */
class UsersViewProfileHtml extends JViewHtml
{
	protected $data;

	protected $form;

	/**
	 * @var JRegistry
	 */
	protected $params;

	protected $state;

	/**
	 * Redefine the model so the correct type hinting is available.
	 *
	 * @var     UsersModelProfile
	 * @since   1.0
	 */
	protected $model;

	/**
	 * Method to display the view.
	 */
	public function render($tpl = null)
	{
		if (JFactory::getUser()->guest)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			return '';
		}

		// Get the view data.
		$this->data = $this->model->getData();
		$this->form = $this->model->getForm();
		$this->state = $this->model->getState();
		$this->params = $this->state->get('com_users.params');

		// Check if a user was found.
		if (!$this->data->id)
		{
			JError::raiseError(404, JText::_('JERROR_USERS_PROFILE_NOT_FOUND'));
			return false;
		}

		// Check for layout override
		$active = JFactory::getApplication()->getMenu()->getActive();

		if (isset($active->query['layout']))
		{
			$this->setLayout($active->query['layout']);
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->prepareDocument();

		UsersHelperSidebar::prepare();

		return parent::render();
	}

	/**
	 * Prepares the document
	 *
	 * @since    1.6
	 */
	protected function prepareDocument()
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$user = JFactory::getUser();
		$title = null;
		$document = JFactory::getDocument();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $user->name));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_USERS_PROFILE'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}

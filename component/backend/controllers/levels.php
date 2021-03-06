<?php
/**
 *  @package AkeebaSubs
 *  @copyright Copyright (c)2010-2015 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AkeebasubsControllerLevels extends F0FController
{
	public function copy($cacheable = false)
	{
		// Load the old item
		$model = $this->getThisModel();
		$ids = $model->setIDsFromRequest()->getIds();

		// Default messages
		$msgType = 'message';
		$msg = JText::_('COM_AKEEBASUBS_LEVELS_MSG_COPIED');

		if(empty($ids)) {
			$msgType = JText::_('COM_AKEEBASUBS_LEVELS_ERR_NOTCOPIEDNOTHERE');
		} else foreach($ids as $id) {
			$oldItem = $model->getItem($id);
			if($oldItem->akeebasubs_level_id > 0) {
				$data = $oldItem->getData();
				$data['akeebasubs_level_id'] = 0;

				$counter = 0;
				$gotSlug = false;
				while(!$gotSlug) {
					$counter++;
					$slug = $data['slug'].'-'.$counter;
					$existingItems = F0FModel::getTmpInstance('Levels','AkeebasubsModel')
						->slug($slug)
						->getList(true);
					if(empty($existingItems)) $gotSlug = true;
				}

				$data['slug'] = $slug;
				$data['title'] = $data['title']." ($counter)";

				$table = $model->getTable();
				$table->reset();
				$table->save($data);
			}
		}
		$url = 'index.php?option=com_akeebasubs&view=levels';
		$this->setRedirect($url, $msg, $msgType);
	}

	public function recurringpublish($cacheable = false)
	{
		$url = 'index.php?option=' . $this->component . '&view=' . F0FInflector::pluralize($this->view);

		$this->setRedirect($url);

		return true;
	}

	public function recurringunpublish($cacheable = false)
	{
		$url = 'index.php?option=' . $this->component . '&view=' . F0FInflector::pluralize($this->view);

		$this->setRedirect($url);

		return true;
	}
}
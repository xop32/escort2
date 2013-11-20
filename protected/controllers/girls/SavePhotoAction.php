<?php

class SavePhotoAction extends EscAction
{
	/**
	 * Declares class-based actions.
	 */
	public function run()
	{
		//Including css and js and initializing models with false
		$this->getAssets();
		$images = false;

		//Initializing $girl and $images models with appropriate values
		//in case if current user has Girl with specified id
		if(isset($_GET['id']) && isset($_GET['type']))
		{

			$photos = new Photo();



			$user = User::model()->with('girls')->findByPk(Yii::app()->user->id);
			$girls = $user->girls;
			foreach($girls as $grl)
			{
				if($grl->g_id === $_GET['id'])
				{
					$girl = $grl;
				}
			}

			if(!$girl)
			{
				Yii::app()->user->setFlash('cantFind','Cant find specified girl.');
			}
			elseif ($girl instanceof Girl)
			{
				$images = $this->_getImages($girl->g_id);
			}
		}

		if(isset($_POST['Girl']))
		{
			if(Yii::app()->request->isAjaxRequest)
			{
				$this->saveGirlByAjax($girl);
			}
			$this->_saveGirl($girl);
		}

		if(isset($_FILES['Girl']))
		{
			if(Yii::app()->request->isAjaxRequest)
			{
				$this->saveImagesByAjax($girl);
			}
			$this->_saveImages($girl);
			$images = $this->_getImages($girl->g_id);
		}

		if (!$_POST && isset($_GET['sent']))
		{
			Yii::app()->user->setFlash('cantFind','Something went wrong. Probably the images that you downloaded was too big. Please, try again.');
		}

		$this->controller->render('edit', array('girl' => $girl, 'images' => $images));
	}


	public function saveGirlByAjax($girl)
	{
		$this->_saveGirl($girl);
		$images = $this->_getImages($girl->g_id);
		echo $this->controller->renderPartial('edit', array('girl' => $girl, 'images' => $images), true);
		Yii::app()->end();
	}

	public function saveImagesByAjax($girl)
	{
		$this->_saveImages($girl);
		$images = $this->_getImages($girl->g_id);
		echo $this->controller->renderPartial('forms/imageForm', array('girl' => $girl, 'images' => $images), true);
		Yii::app()->end();
	}


	//Saving all girl properties without images
	private function _saveGirl($girl)
	{
		$girl->attributes = $_POST['Girl'];
		$girl->save();
	}

	//Saving all images for specified girl
	private function _saveImages($girl)
	{
		$girl->g_photo = CUploadedFile::getInstances($girl,'g_photo');
		if($girl->save())
		{
			if ($girl->g_photo)
			{
				$message = $this->saveImage($girl->g_photo, $girl->g_id, Yii::app()->params['photoG']);

				if($message !== true)
				{
					$girl->addError('g_photo', $message);
				}
			}
		}
	}

}
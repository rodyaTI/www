<?php

class RedactorController extends CExtController {
    function run($actionID) {
		if(Yii::app()->user->getState("isAdmin")){
			$type = Yii::app()->request->getQuery('type');

			Controller::disableProfiler(); // yii-debug-toolbar disabler

			if($type == 'imageUpload' || $type == 'fileUpload'){
				if (!empty($_FILES['file']['name']) && !Yii::app()->user->isGuest) {
					//$dir = Yii::getPathOfAlias('webroot.upload') . '/' . Yii::app()->user->id . '/';
					$dir = Yii::getPathOfAlias('webroot.uploads') . '/';
					if (!is_dir($dir))
						@mkdir($dir, '0777', true);

					$file = CUploadedFile::getInstanceByName('file');
					if ($file) {
						if($type == 'imageUpload'){
							$new_name = md5(time()) . '.' . $file->extensionName;
							$file->saveAs($dir . $new_name);
							echo '<img src="'.Yii::app()->getBaseUrl(false).'/uploads/' . $new_name.'" />';
						} else {
							$new_name = $file->getName();
							if(!is_file($dir . $new_name)){
								$file->saveAs($dir . $new_name);
								echo '<a href="'.Yii::app()->getBaseUrl(false).'/uploads/'.$new_name.'"
									rel="'.$new_name.'"
									class="redactor_file_link redactor_file_ico_'.$this->_getIcon($file->extensionName).'">'.$new_name.'</a>';
							} else {
								echo tc('Uploading error! File already exists');
							}
						}
						Yii::app()->end();
					}
				}
			}
			
			if($type == 'fileDelete'){
				$fname = Yii::getPathOfAlias('webroot.uploads').'/'.$_GET['delete'];
				if(is_file($fname) && dirname($fname) == Yii::getPathOfAlias('webroot.uploads')){
					unlink($fname);
				}
			}
			
			if($type == 'fileDownload'){
				if (isset($_GET['file'])){
					$fname = Yii::getPathOfAlias('webroot.uploads').'/'.$_GET['file'];
					if(is_file($fname) && dirname($fname) == Yii::getPathOfAlias('webroot.uploads')){
						$this->_download($fname);
					}
				}
			}
		}
    }

	private function _getIcon($type){
		$fileicons = array('other' => 0, 'avi' => 'avi', 'doc' => 'doc', 'docx' => 'doc', 'gif' => 'gif', 'jpg' => 'jpg', 'jpeg' => 'jpg', 'mov' => 'mov', 'csv' => 'csv', 'html' => 'html', 'pdf' => 'pdf', 'png' => 'png', 'ppt' => 'ppt', 'rar' => 'rar', 'rtf' => 'rtf', 'txt' => 'txt', 'xls' => 'xls', 'xlsx' => 'xls', 'zip' => 'zip');

		if (isset($fileicons[$type])){
			return $fileicons[$type];
		} else {
			return 'other';
		}
	}

	private function _download($filename, $filenamef = false, $mimetype='application/octet-stream'){
		if (!file_exists($filename)) die('File not found');

		$from = $to = 0;
		$cr = NULL;

		if (isset($_SERVER['HTTP_RANGE'])){
			$range = substr($_SERVER['HTTP_RANGE'], strpos($_SERVER['HTTP_RANGE'], '=')+1);
			$from = strtok($range, '-');
			$to = strtok('/');
			if ($to>0) $to++;
			if ($to) $to-=$from;
			header('HTTP/1.1 206 Partial Content');
			$cr = 'Content-Range: bytes ' . $from . '-' . (($to)?($to . '/' . $to+1):filesize($filename));
		}
		else header('HTTP/1.1 200 Ok');

		if ($filenamef === false) $filenamef = $filename;

		$etag = md5($filename);
		$etag = substr($etag, 0, 8) . '-' . substr($etag, 8, 7) . '-' . substr($etag, 15, 8);
		header('ETag: "' . $etag . '"');
		header('Accept-Ranges: bytes');
		header('Content-Length: ' . (filesize($filename)-$to+$from));
		if ($cr) header($cr);
		header('Connection: close');
		header('Content-Type: ' . $mimetype);
		header('Last-Modified: ' . gmdate('r', filemtime($filename)));
		$f = fopen($filename, 'r');
		header('Content-Disposition: attachment; filename="' . basename($filenamef) . '";');
		if ($from) fseek($f, $from, SEEK_SET);
		if (!isset($to) || empty($to)) $size=filesize($filename)-$from;
		else $size=$to;
		$downloaded = 0;
		while(!feof($f) && !connection_status() && ($downloaded<$size)){
			echo fread($f, 512000);
			$downloaded+=512000;
			flush();
		}
		fclose($f);
	}
}
<?php

namespace yii2lab\helpers\yii;

use Yii;
use yii\helpers\BaseFileHelper;

class FileHelper extends BaseFileHelper
{
	public static function getDataUrl($fileName) {
		$content = FileHelper::load($fileName);
		$mimeType = FileHelper::getMimeType($fileName);
		$base64code = 'data:'.$mimeType.';base64, ' . base64_encode($content);
		return $base64code;
	}

	public static function isEqualContent($sourceFile, $targetFile) {
		return self::load($sourceFile) === self::load($targetFile);
	}

	public static function copy($sourceFile, $targetFile, $dirAccess = 0777) {
		$sourceData = FileHelper::load($sourceFile);
		FileHelper::save($targetFile, $sourceData, null, null, $dirAccess);
	}

	public static function save($fileName, $data, $flags = null, $context = null, $dirAccess = 0777) {
		$fileName = self::normalizePath($fileName);
		$dirName = dirname($fileName);
		if(!is_dir($dirName)) {
			self::createDirectory($dirName, $dirAccess);
		}
		return file_put_contents($fileName, $data, $flags, $context);
	}

	public static function load($fileName, $flags = null, $context = null, $offset = null, $maxLen = null) {
		$fileName = self::normalizePath($fileName);
		if(!self::has($fileName)) {
			return null;
		}
		return file_get_contents($fileName, $flags, $context, $offset);
	}
	
	public static function has($fileName) {
		$fileName = self::normalizePath($fileName);
		return is_file($fileName);
	}

	public static function scanDir($dir, $options = null) {
		$pathes = scandir($dir);
		ArrayHelper::removeByValue('.', $pathes);
		ArrayHelper::removeByValue('..', $pathes);
		if(empty($pathes)) {
			return [];
		}
		$result = [];
		//$options = self::normalizeOptions($options);
		foreach($pathes as $path) {
			if (static::filterPath($path, $options)) {
				$result[] = $path;
			}
		}
		return $result;
	}
	
	function dirFromTime($level=3,$time=null) {
		if(empty($time)) $time = TIMESTAMP;
		if($level >= 1) $format[] = 'Y';
		if($level >= 2) $format[] = 'm';
		if($level >= 3) $format[] = 'd';
		if($level >= 4) $format[] = 'H';
		if($level >= 5) $format[] = 'i';
		if($level >= 6) $format[] = 's';
		$name = date(implode('/',$format));
		$name = self::normalizePath($name);
		return $name;
	}

	function fileFromTime($level=5,$time=null,$delimiter='.',$delimiter2='_') {
		if(empty($time)) $time = TIMESTAMP;
		$format = '';
		if($level >= 1) $format .= 'Y';
		if($level >= 2) $format .= $delimiter.'m';
		if($level >= 3) $format .= $delimiter.'d';
		if($level >= 4) $format .= $delimiter2.'H';
		if($level >= 5) $format .= $delimiter.'i';
		if($level >= 6) $format .= $delimiter.'s';
		$name = date($format);
		return $name;
	}
	
	function findFilesWithPath($source_dir, $directory_depth = 0, $hidden = FALSE, $empty_dir = false) {
		if(empty($source_dir)) {
		 $source_dir = '.';
		}
		static $source_dir1;
		if(!isset($source_dir1)) {
			$source_dir1 = $source_dir;
		}
		if(!file_exists($source_dir) || !is_dir($source_dir)) {
			return false;
		}
		if($fp = @opendir($source_dir)) {
			$filedata	= array();
			$new_depth	= $directory_depth - 1;
			$source_dir	= rtrim($source_dir, DS).DS;
			while(FALSE !== ($file = readdir($fp))) {
				// Remove '.', '..', and hidden files [optional]
				if( ! trim($file, '.') OR ($hidden == FALSE && $file[0] == '.')) {
					continue;
				}
				$dd = substr($source_dir,mb_strlen($source_dir1));
				$dd = ltrim($dd,DS);
				if(($directory_depth < 1 OR $new_depth > 0) && @is_dir($source_dir.$file)) {
					$dir_cont = self::findFilesWithPath($source_dir.$file.DS, $new_depth, $hidden, $empty_dir);
					if(!empty($dir_cont)) {
						$filedata = array_merge($filedata,$dir_cont);
					} else {
						if($empty_dir) {
							$filedata[] = $dd.$file.DS;
						}
					}
				} else {
					
					if(@is_dir($source_dir.$file)) {
						$filedata[] = $dd.$file;
					} else {
						$filedata[] = $dd.$file;
					}
				}
			}
			closedir($fp);
			return $filedata;
		}
		return FALSE;
	}

}

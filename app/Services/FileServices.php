<?php

namespace App\Services;

class FileServices {
	public function saveFile($file): string {
		return $file->store('chain-files');
    }

	public function generateLink($storePath) {
		if(!$storePath) {
			return null;
		}
		return $storePath;
	}

	public function checkIsImage($imagePath): bool {
		$imagePath = __DIR__.'/../../storage/app/'.$imagePath;
		$image_info = getimagesize($imagePath);
		if ($image_info) {
			return true;
		} else {
			return false;
		}
	}

	public function checkIsVideo($videoPath): bool {
		$videoPath = __DIR__.'/../../storage/app/'.$videoPath;
		$video_extensions = array('mp4', 'avi', 'mov');
		$file_extension = pathinfo($videoPath, PATHINFO_EXTENSION);
		if (in_array($file_extension, $video_extensions)) {
			return true;
		} else {
			return false;
		}
	}
}
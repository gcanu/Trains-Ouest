<?php

function resizeImage($imageName, $width=0, $height=0) {

	if ($width > 0 || $height > 0) {
		$matches = array();
		preg_match("/([^\/]+)\.([A-Za-z]+)$/", $imageName, $matches);

		if (count($matches) > 0) {
			$fileName = $matches[1];
			$extension = $matches[2];
			$path = str_replace($fileName.".".$extension, "", $imageName); 
			
			// on vérifie la validité de l'image
			if(!file_exists($imageName))
				return false;
			
			// création de l'image
			switch (strtolower($extension)) {
				case "jpg":
					$image = imagecreatefromjpeg($imageName);
					break;
				case "png":
					$image = imagecreatefrompng($imageName);
					break;
				case "gif":
					$image = imagecreatefromgif($imageName);
					break;
				default:
					$image = "";
			}

			if ($image != "" && $image != false) {
				// calcul du ratio de l'image
				$imageWidth = imagesx($image);
				$imageHeight = imagesy($image);
				$ratio = $imageHeight / $imageWidth;

				if ($width > 0)
					$imageResizedWidth = $width;
				else
					$imageResizedWidth = round($height / $ratio);

				if ($height > 0)
					$imageResizedHeight = $height;
				else
					$imageResizedHeight = round($width * $ratio);
					
				$newFileName = $path.$fileName."_".$imageResizedWidth."x".$imageResizedHeight.".".$extension;
		
				// on vérifie si le fichier existe déjà
				if(!file_exists($newFileName)) {

					// création de la nouvelle image
					$resizedImage = imagecreatetruecolor($imageResizedWidth, $imageResizedHeight);

					// resampling de l'image d'origine
					imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $imageResizedWidth, $imageResizedHeight, $imageWidth, $imageHeight);

					// Affichage de l'image
					switch (strtolower($extension)) {
						case "jpg":
							imagejpeg($resizedImage, $newFileName);
							return $newFileName;
							break;
						case "png":
							imagepng($resizedImage, $newFileName);
							return $newFileName;
							break;
						case "gif":
							imagegif($resizedImage, $newFileName);
							return $newFileName;
							break;
					}
				}
				else
					return $newFileName;
			}
		}
	}
	return false;
}
?>

<?php

function resizeImage($imageName, $width=0, $height=0) {

	if ($width > 0 && $height > 0) {
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
				$ratio = $imageWidth / $imageHeight;
				$idealRatio = $width / $height;

				if($ratio == $idealRatio) {
					$imageResizedWidth = $width;
					$imageResizedHeight = $height;
				}
				elseif ($ratio > $idealRatio) {
					// transfo sur la largeur
					$imageResizedWidth = $width;
					$imageResizedHeight = round($imageResizedWidth / $ratio);
				}
				elseif ($ratio < $idealRatio) {
					// transfo sur la hauteur
					$imageResizedHeight = $height;
					$imageResizedWidth = round($imageResizedHeight * $ratio);
				}
					
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
							break;
						case "png":
							imagepng($resizedImage, $newFileName);
							break;
						case "gif":
							imagegif($resizedImage, $newFileName);
							break;
					}
				}

				return array("filename" => $newFileName, "width" => $imageResizedWidth, "height" => $imageResizedHeight);
			}
		}
	}
	return false;
}
?>

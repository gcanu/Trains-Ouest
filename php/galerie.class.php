<?php

class Galerie {

    var $images;
    // attributs relatifs aux chemins
    var $baseURL;
    var $path;
    var $relative_path;
    var $zoom_path;
    var $th_path;

    function Galerie() {
        $this->images = array();

        $this->relative_path = "/images/galerie/";

        if ($_SERVER['HTTP_HOST'] == "localhost")
            $this->baseURL = "http://" . $_SERVER['HTTP_HOST'] . "/decobac" . $this->relative_path;
        else
            $this->baseURL = "http://" . $_SERVER['HTTP_HOST'] . $this->relative_path;

        $this->path = getcwd() . "/" . $this->relative_path;
        $this->zoom_path = "zoom/";
        $this->th_path = "thumbnails/";

        $this->getImages();
    }

    function affiche() {
        $html = "";

        $html .= "<div class=\"gallery\">\n";
        
        // alternance 3 images puis 2 images
        $nb_img = array(2, 3, 2, 3, 2, 3, 3, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 2, 3, 2, 2, 2, 3, 1);
        $nb_line = 0;
        $nb_img_line = 0;
        
        for ($x = 0; $x < count($this->images); $x++) {
            if($nb_img_line == 0)
                $html .= "<div class=\"gallery-line-{$nb_img[$nb_line]}\">\n";
            
            $html .= "<img src=\"" . $this->baseURL . $this->th_path . urlencode($this->images[$x]) . "\" onclick=\"modalbox('" . $this->baseURL . $this->zoom_path . urlencode($this->images[$x]) . "')\" />\n";
            $nb_img_line++;
            
            if ($nb_img_line == $nb_img[$nb_line]) {
                $html .= "</div>\n";
                $nb_line++;
                $nb_img_line = 0;
            }
            else
                $html .= "<div class=\"gallery-separator\"></div>";
        }

        $html .= "</div>\n";

        return $html;
    }

    function getImages() {
        $dh = opendir($this->path . $this->th_path);

        if (!$dh)
            return false;

        while (false !== ($filename = readdir($dh))) {
            if (!is_dir($this->zoom_path . $filename) && preg_match("/^\./", $filename) == 0)
                array_push($this->images, $filename);
        }
        
        sort($this->images);
    }
}

?>

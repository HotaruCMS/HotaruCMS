<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<h4>Pages in <em>/content/pages</em> folder</h4>
<br/>

    <?php
        $directory = CONTENT . 'pages';

        if ( ! is_dir($directory)) {
            exit('Invalid diretory path');
        }

        $files = array();

        echo '<ul>';
        foreach (scandir($directory) as $file) {
            if ('.' === $file) continue;
            if ('..' === $file) continue;

            $files[] = $file;
            $page = trim($file, '.php');
            echo "<li><a href='" . $h->url(array($page)) . "' target='_blank'>" . $page . '</a></li>';
        }
        echo '</ul>';
        
        //var_dump($files);
    ?>



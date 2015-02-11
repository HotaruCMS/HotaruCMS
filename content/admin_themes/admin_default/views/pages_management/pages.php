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

        if (!is_dir($directory)) {
            exit('Invalid directory path');
        }

        $files = array();

        echo '<ul>';
        foreach (scandir($directory) as $file) {
            if ('.' === $file) { continue; }
            if ('..' === $file) { continue; }

            $files[] = $file;
            $page = trim($file, '.php');
            echo "<li>";
            echo "<a href='" . $h->urlPage($page) . "' target='_blank'>" . $page . "</a>";
            echo "      <a href='/admin_index.php?page=pages_management_edit&filename=" . $page . "' >Edit</a>";
            echo "</li>";
        }
        echo '</ul>';

<?php
    /**
    * ColorNamer 1.0
    * Copyright 2011 Tufan Baris YILDIRIM
    *
    * Website: http://me.tufyta.com
    *
    * $Id: example.php 2011-07-14 04:51:02Z tfnyldrm $
    */

    /**
    * Including ColorNamer first.
    */
    include 'ColorName.php';

    /**
    * Preparing class, load the .dat file first.
    */
    ColorName::Prepare('colors.dat');

    #analizing..

    #random hex value, 00 - FF
    function rh()
    {
        return strtoupper(str_pad(dechex(rand(0,255)),2,0));
    }

    #generate random color code.
    function randomColor()
    {
        return '#' . rh() . rh() . rh();
    }

?>
<html>
    <head>
        <title>Color Namer Example</title>
    </head>
    <body>


        <table>
            <tr><td colspan="2">Code</td><td colspan="2">More Silimar To</td><td>Similarity</td></tr>
            <?php
                for($i = 0;$i < 10; $i ++):
                    $colorCode = randomColor();
                    $colorInfo = ColorName::GetInfo($colorCode);
                ?>

                <tr>
                    <td><?php echo $colorCode; ?></td>
                    <td><span style="width: 50px;height:20px;background: <?php echo $colorCode?>;"></span></td>
                    <td><?php echo $colorInfo->name ?>&nbsp;(#<?php echo $colorInfo->code; ?>)</td>
                    <td><span style="width: 50px;height:20px;background: #<?php echo $colorInfo->code?>;"></span></td>
                    <td>%<?php echo $colorInfo->similarity; ?></td>
                  </tr>
                <?php
                    endfor;
            ?>
        </table>
    </body>
    </html>
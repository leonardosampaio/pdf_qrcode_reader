<?php

foreach(scandir(__DIR__) as $file)
{
    if (strpos($file, '.pdf') !== false)
    {
        echo $file;
        $command = 'php ' . __DIR__.'/../reader.php ' . __DIR__ . '/' . $file;
        $output = shell_exec($command);
        // var_dump($output);
        if (strpos($output,'FOUNDONPAGE') !== false)
        {
            echo ' ok';
        }
        else {
            echo ' error';
        }
        echo PHP_EOL;
    }
}
<?php

/*

PDF qrcode extractor

Leonardo Sampaio - Upwork - 2021-05-26
leonardors@gmail.com

Usage:

php reader.php /path/to/input.pdf /path/to/output.txt (optional)

*/

if (php_sapi_name() !== 'cli')
{
    die('This should run in cli');
}

if (!isset($argv[1]))
{
    die('Inform the input file');
}
else if (!file_exists($argv[1]))
{
    die('Input file not found');
}

//https://imagemagick.org/script/command-line-options.php
//image scaling, smaller values make the process faster
// but may cause false negatives in the extraction
$imageMagickImageDensity = 160;

$tempDir =                  sys_get_temp_dir();
$isWindows =                strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$pathSeparator =            $isWindows ? '\\' : '/';

$zbarExecutable =           'zbarimg';
$jarExecutable =            'java -jar '.__DIR__.$pathSeparator.'java'.$pathSeparator.'dist'.$pathSeparator.'zxing-cmd-1.0-jar-with-dependencies.jar';
$imageMagickExecutable =    $isWindows ? 'magick' : 'convert';
$rmExecutable =             $isWindows ? 'del' : 'rm';

$filePath = explode($pathSeparator, $argv[1]);
$fileName = end($filePath);

//extract images
exec($imageMagickExecutable . ' -density '.$imageMagickImageDensity.' -background white -alpha remove -define png:color-type=6 ' . $argv[1] . ' ' . $tempDir . $pathSeparator . $fileName .'page%04d.png');

$textOutput = '';
$totalPages = 0;
foreach(scandir($tempDir) as $file)
{
    if (strpos($file,$fileName)!==false)
    {
        $totalPages+=1;
        
        preg_match('/page([0-9]+)\.png/', $file, $matches);
        $page = ((int)$matches[1]) + 1;

        $tempPngFile = $tempDir . $pathSeparator . $file;

        //qrcode data with zbarimg
        $qrcodeData = shell_exec($zbarExecutable . ' --quiet ' . $tempPngFile);

        if ($qrcodeData == null || $qrcodeData == '')
        {
            //fallback, qrcode data with zxing
            $qrcodeData = shell_exec($jarExecutable . ' ' . $tempPngFile);
        }

        if ($qrcodeData)
        {
            $textOutput .= 'FOUNDONPAGE ' . $page . PHP_EOL;
            $textOutput .= trim(str_replace('QR-Code:','',$qrcodeData));
            $textOutput .= PHP_EOL;
        }
    }
}

if ($totalPages)
{
    $textOutput .= 'TOTALPAGES ' . $totalPages . PHP_EOL;
}

//delete temporary images
exec($rmExecutable . ' ' . $tempDir . $pathSeparator . $fileName .'*.png');

echo $textOutput;

if (isset($argv[2]) && $textOutput)
{
    file_put_contents($argv[2], $textOutput);
}
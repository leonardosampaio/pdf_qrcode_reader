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

preg_match('/^[^*?"<>|]*$/',$argv[1],$matches);
$input = $matches ? $matches[0] : null;

if (!isset($input))
{
    die('Inform the input file');
}
else if (!file_exists($input))
{
    die('Input file not found');
}

//https://imagemagick.org/script/command-line-options.php
//image scaling, smaller values make the process faster
// but may cause false negatives in the extraction
$imageMagickImageDensity = 170;

$isWindows =                strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$pathSeparator =            $isWindows ? '\\' : '/';
$tempDir =                  sys_get_temp_dir() . $pathSeparator .rand(100000, 999999);

$zbarExecutable =           'zbarimg';
$jarExecutable =            'java -jar '.__DIR__.$pathSeparator.'java'.$pathSeparator.'dist'.$pathSeparator.'zxing-cmd-1.0-jar-with-dependencies.jar';
$imageMagickExecutable =    $isWindows ? 'magick' : 'convert';

$fileName = pathinfo($input)['filename'];

//create temporary dir
exec('mkdir '.$tempDir);

//extract images
exec($imageMagickExecutable . ' -density '.$imageMagickImageDensity.' -background white -define png:color-type=6 ' . $input . ' -alpha remove ' . $tempDir . $pathSeparator . $fileName 
.'page%04d.png');

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
        $qrcodeData = utf8_decode(shell_exec($zbarExecutable . ' --quiet ' . $tempPngFile));

        if ($qrcodeData == null || $qrcodeData == '')
        {
            //fallback, qrcode data with zxing
            $qrcodeData = utf8_encode(shell_exec($jarExecutable . ' ' . $tempPngFile));
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

//delete temporary dir
if ($isWindows)
{
    exec("del $tempDir$pathSeparator$fileName*.png");
    exec("rmdir $tempDir");
}
else {
    exec("rm -R $tempDir");
}

echo $textOutput;

if (isset($argv[2]))
{
    preg_match('/^[^*?"<>|]*$/',$argv[2],$matches);
    $output = $matches ? $matches[0] : null;

    if (isset($output) && $textOutput)
    {
        file_put_contents($output, $textOutput);
    }
}
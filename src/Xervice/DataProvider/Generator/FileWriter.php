<?php


namespace Xervice\DataProvider\Generator;


use Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable;

class FileWriter implements FileWriterInterface
{
    /**
     * @var string
     */
    private $generatedPath;

    /**
     * FileWriter constructor.
     *
     * @param string $path
     *
     * @throws \Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable
     */
    public function __construct(string $path)
    {
        $this->generatedPath = $path;
        if (
            !is_writable($this->generatedPath)
            && !mkdir($this->generatedPath, '0777', true)
            && !is_dir($this->generatedPath)
        ) {
            throw new GenerateDirectoryNotWriteable($this->generatedPath);
        }
    }

    /**
     * @param $filename
     * @param $content
     */
    public function writeToFile($filename, $content)
    {
        file_put_contents($this->generatedPath . '/' . $filename, '<?php' . PHP_EOL . $content);
    }


}
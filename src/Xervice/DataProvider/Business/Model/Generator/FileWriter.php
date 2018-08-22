<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Business\Model\Generator;


use Xervice\DataProvider\Business\Exception\GenerateDirectoryNotWriteable;

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
     * @throws \Xervice\DataProvider\Business\Exception\GenerateDirectoryNotWriteable
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
     * @param string $filename
     * @param string $content
     */
    public function writeToFile(string $filename, string $content): void
    {
        file_put_contents(
            $this->generatedPath . '/' . $filename,
            sprintf(
                '<?php%1$sdeclare(strict_types=1);%1$s%2$s',
                PHP_EOL,
                $content
            )
        );
    }


}
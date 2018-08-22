<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Business\Exception;

use Xervice\Core\Business\Exception\XerviceException;

class GenerateDirectoryNotWriteable extends XerviceException
{
    /**
     * GenerateDirectoryNotWriteable constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        $message = 'Generate directory is not writeable: ' . $message;
        parent::__construct($message, $code, $previous);
    }

}
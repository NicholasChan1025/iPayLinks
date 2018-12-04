<?php

namespace IPayLinks\Kernel\Http;

use IPayLinks\Kernel\Exceptions\InvalidArgumentException;
use IPayLinks\Kernel\Support\File;

class StreamResponse extends Response
{
    /**
     * @param string $directory
     * @param string $filename
     *
     * @return bool|int
     *
     * @throws \IPayLinks\Kernel\Exceptions\InvalidArgumentException
     */
    public function save(string $directory, string $filename = '')
    {
        $this->getBody()->rewind();

        $directory = rtrim($directory, '/');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true); // @codeCoverageIgnore
        }

        if (!is_writable($directory)) {
            throw new InvalidArgumentException(sprintf("'%s' is not writable.", $directory));
        }

        $contents = $this->getBody()->getContents();

        if (empty($filename)) {
            if (preg_match('/filename="(?<filename>.*?)"/', $this->getHeaderLine('Content-Disposition'), $match)) {
                $filename = $match['filename'];
            } else {
                $filename = md5($contents);
            }
        }

        if (empty(pathinfo($filename, PATHINFO_EXTENSION))) {
            $filename .= File::getStreamExt($contents);
        }

        file_put_contents($directory.'/'.$filename, $contents);

        return $filename;
    }

    /**
     * @param string $directory
     * @param string $filename
     *
     * @return bool|int
     *
     * @throws \IPayLinks\Kernel\Exceptions\InvalidArgumentException
     */
    public function saveAs(string $directory, string $filename)
    {
        return $this->save($directory, $filename);
    }
}

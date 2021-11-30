<?php

namespace Src;

/**
 * Micro functions to interact with the filesystem
 * 
 * @author Boudouma Mohamed Ilies <medilies.contact@gmail.com>
 */
class Fs
{
    /**
     * Returns an absolute path if the file exist
     * 
     * @param string $path
     * 
     * @return string
     * Absolute path
     * 
     * @throws \Exception
     * When path doesn't exist
     *
     * @uses realpath()
     * @uses file_exists()
     */
    public static function getRealPathIfExist(string $path): string
    {
        $real_path = realpath($path);

        if (!$real_path || !file_exists($real_path)) {
            throw new \Exception("$path is invalid");
        }

        return $real_path;
    }

    public static function getFileExtension(string $real_path): string
    {
        return pathinfo($real_path)['extension'];
    }

    /**
     * @param string $real_path
     * Absolute path to a directory
     *
     * @return bool
     */
    public static function isDir(string $real_path): bool
    {
        if (is_dir($real_path)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $real_path
     * Absolute path to an HTML file
     *
     * @return bool
     */
    public static function isHtml(string $real_path): bool
    {
        return strcasecmp(self::getFileExtension($real_path), 'html') === 0;
    }

    /**
     * @param string $real_path
     * Absolute path to a CSV file
     *
     * @return bool
     */
    public static function isCsv(string $real_path): bool
    {
        return strcasecmp(self::getFileExtension($real_path), 'csv') === 0;
    }

    /**
     * @param string $real_path
     * Absolute path to an ODS file
     *
     * @return bool
     */
    public static function isOds(string $real_path): bool
    {
        return strcasecmp(self::getFileExtension($real_path), 'ods') === 0;
    }

    /**
     * @param string $real_path
     * Absolute path to an XLSX file
     *
     * @return bool
     */
    public static function isXlsx(string $real_path): bool
    {
        return strcasecmp(self::getFileExtension($real_path), 'xlsx') === 0;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 09/10/2018
 * Time: 14.13
 */
namespace Derasy\DerasyBundle\Common;

use DirectoryIterator;

class System {

    const OS_WINDOWS = 'OS_WINDOWS';
    const OS_LINUX = 'OS_LINUX';
    const OS_MAC = 'OS_MAC';

    /**
     * Query the Server OS
     * @return string
     */
    public static function getOs() {

        $phpOs = strtoupper(substr(PHP_OS, 0, 3));

        switch ($phpOs) {
            case 'WIN':
                return System::OS_WINDOWS;
                break;
            case 'LIN':
                return System::OS_LINUX;
                break;
            case 'DAR' :
                return System::OS_MAC;
                break;
        }

    }

    /**
     * Find subfolders in a folder.
     * If onlyname true, returns simple array of names
     * If onlyname false, returns associative array of path,
     * with subfolder names as key
     *
     * @param $path
     * @param bool $onlyName
     * @return array
     */
    public static function getDirsIn($path, $onlyName=false) {

        $dir = new DirectoryIterator($path);
        $retArr = array();

        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                if ($onlyName) {
                    $retArr[] = $fileinfo->getFilename();
                } else {
                    $retArr[$fileinfo->getFilename()] = $fileinfo->getPathname();
                }
            }
        }

        return $retArr;
    }

    /**
     * Find files only in a folder.
     * If onlyname true, returns simple array of names
     * If onlyname false, returns associative array of path,
     * with subfolder names as key
     *
     * @param $path
     * @param bool $onlyName
     * @return array
     */
    public static function getFilesIn($path, $onlyName=false) {

        $dir = new DirectoryIterator($path);
        $retArr = array();

        foreach ($dir as $fileinfo) {
            if ($fileinfo->isFile() && !$fileinfo->isDot()) {
                if ($onlyName) {
                    $retArr[] = $fileinfo->getFilename();
                } else {
                    $retArr[$fileinfo->getFilename()] = $fileinfo->getPathname();
                }
            }
        }

        return $retArr;
    }

}
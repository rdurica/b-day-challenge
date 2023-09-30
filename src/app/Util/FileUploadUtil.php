<?php

declare(strict_types=1);

namespace App\Util;

/**
 * File directories.
 *
 * @package   App\Util
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class FileUploadUtil
{
    /**
     * Get root directory for uploading.
     *
     * @param bool $fullPath
     * @return string
     */
    public static function getRootDir(bool $fullPath = false): string
    {
        return $fullPath ? '/app/src/www/b_day_challenge/' : 'b_day_challenge/';
    }

    /**
     * Get image directory for assigned task.
     *
     * @param int  $assignedTaskId
     * @param bool $fullPath
     * @return string
     */
    public static function getAssignedTaskImgDir(int $assignedTaskId, bool $fullPath = false): string
    {
        return sprintf('%s%s%s', self::getRootDir($fullPath), $assignedTaskId, '/img/',);
    }

    /**
     * Get video directory for assigned task.
     *
     * @param int  $assignedTaskId
     * @param bool $fullPath
     * @return string
     */
    public static function getAssignedTaskVideoDir(int $assignedTaskId, bool $fullPath = false): string
    {
        return sprintf('%s%s%s', self::getRootDir($fullPath), $assignedTaskId, '/video/',);
    }
}
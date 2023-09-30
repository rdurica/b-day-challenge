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
     * @return string
     */
    public static function getRootDir(): string
    {
        return '/www-data/b_day_challenge/';
    }

    /**
     * Get image directory for assigned task.
     *
     * @param int $assignedTaskId
     * @return string
     */
    public static function getAssignedTaskImgDir(int $assignedTaskId): string
    {
        return sprintf('%s%s%s', self::getRootDir(), $assignedTaskId, '/img/',);
    }

    /**
     * Get video directory for assigned task.
     *
     * @param int $assignedTaskId
     * @return string
     */
    public static function getAssignedTaskVideoDir(int $assignedTaskId): string
    {
        return sprintf('%s%s%s', self::getRootDir(), $assignedTaskId, '/video/',);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: KF
 * Date: 2018/9/23
 * Time: 13:30
 */

namespace app\common\func;


use think\Controller;

/**
 * 处理目录及目录下的文件类集合
 */
class Directory extends Controller
{
    /**
     * 创建目录
     *
     * @param string $dir 创建路径
     * @param integer $mode 设置目录的权限
     * @return bool 是否创建成功
     */
    public function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) {
            return true;
        }

        if (!self::mkdirs(dirname($dir), $mode)) {
            return false;
        }

        return @mkdir($dir, $mode);
    }

    /**
     * 获取文件夹目录大小
     *
     * @param string $path 目录路径
     * @return integer 目录大小
     */
    public function dirsize($path)
    {
        $size = 0;

        $handle = opendir($path);

        while (($item = readdir($handle)) !== false) {
            if ($item == '.' || $item == '..') continue;
            $_path = $path . '/' . $item;
            if (is_file($_path)) $size += filesize($_path);
            if (is_dir($_path)) $size += $this->dirsize($_path);
        }

        closedir($handle);
        clearstatcache();

        return $size;
    }

    /**
     * 复制文件夹目录及其文件
     *
     * @param string $source 被复制的目录路径
     * @param string $dest 要被复制到的目录路径
     * @return void
     */
    public function copydir($source, $dest)
    {
        if (!file_exists($dest)) mkdir($dest);

        $handle = opendir($source);

        while (($item = readdir($handle)) !== false) {
            if ($item == '.' || $item == '..') continue;
            $_source = $source . '/' . $item;
            $_dest = $dest . '/' . $item;
            if (is_file($_source)) copy($_source, $_dest);
            if (is_dir($_source)) $this->copydir($_source, $_dest);
        }

        closedir($handle);
    }

    /**
     * 删除文件夹目录及其文件
     *
     * @param string $path 需要删除的目录路径
     * @return bool 是否删除成功
     */
    public function rmdirs($path)
    {
        $handle = opendir($path);

        while (($item = readdir($handle)) !== false) {
            if ($item == '.' || $item == '..') continue;
            $_path = $path . '/' . $item;
            if (is_file($_path)) unlink($_path);
            if (is_dir($_path)) $this->rmdirs($_path);
        }

        closedir($handle);

        return rmdir($path);
    }
}
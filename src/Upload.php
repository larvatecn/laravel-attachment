<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */
declare (strict_types=1);

namespace Larva\Attachment;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 上传处理
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Upload
{
    /**
     * Storage instance.
     *
     * @var Filesystem
     */
    protected $storage;

    /**
     * Initialize the storage instance.
     *
     * @return $this.
     */
    public static function create()
    {
        return static::driver(config('upload.disk'));
    }

    /**
     * Initialize the storage instance.
     *
     * @param string|null $disk
     * @return $this.
     */
    public static function driver($disk = null)
    {
        return (new static())->disk($disk);
    }

    /**
     * Set disk for storage.
     *
     * @param string $disk Disks defined in `config/filesystems.php`.
     * @return $this|bool
     */
    public function disk(string $disk)
    {
        try {
            $this->storage = Storage::disk($disk);
        } catch (\Exception $exception) {
            return false;
        }
        return $this;
    }

    /**
     * Get getStorage.
     *
     * @return Filesystem
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Get file visit url.
     *
     * @param string $path
     * @return string
     */
    public function url(string $path)
    {
        if (URL::isValidUrl($path)) {
            return $path;
        }
        if ($this->storage) {
            return $this->storage->url($path);
        }
        return Storage::disk(config('upload.disk'))->url($path);
    }

    /**
     * 销毁原始文件
     *
     * @param string $path
     * @return void.
     */
    public function destroy(string $path)
    {
        if ($this->storage->exists($path)) {
            $this->storage->delete($path);
        }
    }
}
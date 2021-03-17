<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */
declare (strict_types=1);

namespace Larva\Attachment;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

/**
 * 上传处理
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Upload
{
    const NAME_UNIQUE = 'unique';
    const NAME_DATETIME = 'datetime';
    const NAME_SEQUENCE = 'sequence';

    /**
     * Upload directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * File name.
     *
     * @var null
     */
    protected $name = null;

    /**
     * Storage instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $storage = '';

    /**
     * Use (unique or datetime or sequence) name for store upload file.
     *
     * @var bool
     */
    protected $generateName = null;

    /**
     * Controls the storage permission. Could be 'private' or 'public'.
     *
     * @var string
     */
    protected $storagePermission;

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
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function defaultDirectory()
    {
        return config('upload.directory.file');
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
     * Specify the directory upload file.
     *
     * @param string $dir
     * @return $this
     */
    public function dir(string $dir)
    {
        if ($dir) {
            $this->directory = $dir;
        }
        return $this;
    }

    /**
     * Set name of store name.
     *
     * @param string|callable $name
     * @return $this
     */
    public function name($name)
    {
        if ($name) {
            $this->name = $name;
        }
        return $this;
    }

    /**
     * Use unique name for store upload file.
     *
     * @return $this
     */
    public function uniqueName()
    {
        $this->generateName = 'unique';
        return $this;
    }

    /**
     * Use datetime name for store upload file.
     *
     * @return $this
     */
    public function datetimeName()
    {
        $this->generateName = 'datetime';
        return $this;
    }

    /**
     * Use sequence name for store upload file.
     *
     * @return $this
     */
    public function sequenceName()
    {
        $this->generateName = 'sequence';
        return $this;
    }

    /**
     * Get getStorage.
     *
     * @return object
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Get store name of upload file.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function getStoreName(UploadedFile $file)
    {
        if ($this->generateName == 'unique') {
            return $this->generateUniqueName($file);
        } elseif ($this->generateName == 'datetime') {
            return $this->generateDatetimeName($file);
        } elseif ($this->generateName == 'sequence') {
            return $this->generateSequenceName($file);
        }

        if ($this->name instanceof \Closure) {
            return call_user_func_array($this->name, [$this, $file]);
        }

        if (is_string($this->name)) {
            return $this->name;
        }

        return $this->generateClientName($file);
    }

    /**
     * Get directory for store file.
     *
     * @return mixed|string
     */
    public function getDirectory()
    {
        if ($this->directory instanceof \Closure) {
            return call_user_func($this->directory);
        }
        return $this->directory ?: $this->defaultDirectory();
    }

    /**
     * Get file type for store file.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function getFileType(UploadedFile $file): string
    {
        // 扩展名
        $extension = $file->guessClientExtension();
        $filetype = 'other';
        foreach (config('upload.file_types') as $type => $pattern) {
            if (preg_match($pattern, $extension) === 1) {
                $filetype = $type;
                break;
            }
        }
        return $filetype;
    }

    /**
     * Get mimeType for store file.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function getMimeType(UploadedFile $file): string
    {
        $mimeType = $file->getClientMimeType();
        $filetype = $this->getFileType($file);
        if ($filetype == 'video') {
            $mimeType = "video/" . $file->guessClientExtension();
        }
        if ($filetype == 'audio') {
            $mimeType = "audio/" . $file->guessClientExtension();
        }
        return $mimeType;
    }

    /**
     * Upload file and delete original file.
     *
     * @param UploadedFile $file
     * @return mixed
     */
    public function upload(UploadedFile $file)
    {
        $this->name = $this->getStoreName($file);
        $this->renameIfExists($file);
        if (!is_null($this->storagePermission)) {
            return $this->storage->putFileAs($this->getDirectory(), $file, $this->name, $this->storagePermission);
        }
        return $this->storage->putFileAs($this->getDirectory(), $file, $this->name);
    }

    /**
     * If name already exists, rename it.
     *
     * @param $file
     * @return void
     */
    public function renameIfExists(UploadedFile $file)
    {
        if ($this->storage->exists("{$this->getDirectory()}/$this->name")) {
            $this->name = $this->generateUniqueName($file);
        }
    }

    /**
     * Get file visit url.
     *
     * @param string $path
     * @return string
     */
    public function objectUrl(string $path)
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
     * Generate a unique name for uploaded file.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function generateUniqueName(UploadedFile $file): string
    {
        return md5(uniqid() . microtime()) . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Generate a datetime name for uploaded file.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function generateDatetimeName(UploadedFile $file): string
    {
        return date('YmdHis') . mt_rand(10000, 99999) . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Generate a sequence name for uploaded file.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function generateSequenceName(UploadedFile $file): string
    {
        $index = 1;
        $extension = $file->getClientOriginalExtension();
        $original = $file->getClientOriginalName();
        $new = sprintf('%s_%s.%s', $original, $index, $extension);
        while ($this->storage->exists("{$this->getDirectory()}/$new")) {
            $index++;
            $new = sprintf('%s_%s.%s', $original, $index, $extension);
        }
        return $new;
    }

    /**
     * Use file'oldname for uploaded file.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function generateClientName(UploadedFile $file): string
    {
        return $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Destroy original files.
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

    /**
     * Set file permission when stored into storage.
     *
     * @param string $permission
     * @return $this
     */
    public function storagePermission(string $permission)
    {
        $this->storagePermission = $permission;
        return $this;
    }
}
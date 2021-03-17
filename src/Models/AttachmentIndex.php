<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Attachment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Larva\Attachment\Upload;

/**
 * 附件索引
 * @property string $id 附件索引
 * @property string $driver 驱动
 * @property string $name 文件名
 * @property string $path 存储路径
 * @property string $mime 文件mime类型
 * @property string $extension 文件后缀
 * @property int $size 文件大小
 * @property string $md5
 * @property string $sha1
 * @property boolean $status 审核状态
 * @property string $create_ip 上传IP
 * @property string $create_port 上传端口
 * @property string $update_ip 更新IP
 * @property string $update_port 更新端口
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property-read string $url 附件访问地址
 * @property-read string $sizeFormat 格式化后的文件大小
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AttachmentIndex byMd5($md5)
 * @method static \Illuminate\Database\Eloquent\Builder|AttachmentIndex bySha1($sha1)
 * @author Tongle Xu <xutongle@gmail.com>
 */
class AttachmentIndex extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attachment_index';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $guarded = [];

    /**
     * 追加字段
     * @var string[]
     */
    protected $appends = [
        'url',
    ];

    /**
     * 属性类型转换
     *
     * @var array
     */
    protected $casts = [
        'size' => 'int',
        'status' => 'boolean'
    ];

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * 模型的默认属性值。
     *
     * @var array
     */
    protected $attributes = [
        'size' => 0,
        'status' => 1,
    ];

    /**
     * 为数组 / JSON 序列化准备日期。
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    /**
     * 多态关联
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * 查询指定MD5的文件
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $md5
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMd5($query, $md5)
    {
        return $query->where('md5', $md5);
    }

    /**
     * 查询指定用户的文章
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $sha1
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySha1($query, $sha1)
    {
        return $query->where('sha1', $sha1);
    }

    /**
     * 获取格式化后的文件大小
     * @return string
     */
    public function getSizeFormatAttribute()
    {
        $sizes = [" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB"];
        if ($this->attributes['size'] == 0) {
            return 'n/a';
        } else {
            return round($this->attributes['size'] / pow(1024, ($i = floor(log($this->attributes['size'], 1024)))), 2) . $sizes[$i];
        }
    }

    /**
     * 获取访问地址
     * @return string
     */
    public function getUrlAttribute()
    {
        if (empty($this->path)) {
            return '';
        }
        if (($upload = Upload::driver($this->driver)) === false) {
            return '';
        }
        return $upload->url($this->path);
    }
}
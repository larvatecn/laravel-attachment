<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Attachment\Observers;

use Illuminate\Support\Facades\Request;
use Larva\Attachment\Models\AttachmentIndex;
use Larva\Attachment\Upload;

/**
 * 附件模型观察者
 * @author Tongle Xu <xutongle@gmail.com>
 */
class AttachmentIndexObserver
{
    /**
     * 处理「creating」事件
     *
     * @param AttachmentIndex $model
     * @return void
     */
    public function creating(AttachmentIndex $model)
    {
        $model->id = md5(mt_rand(100000, 999999) . microtime());
        $model->create_ip = Request::ip();
        $model->create_port = Request::server('REMOTE_PORT');
        $model->update_ip = Request::ip();
        $model->update_port = Request::server('REMOTE_PORT');
    }

    /**
     * 处理「updating」事件
     *
     * @param AttachmentIndex $model
     * @return void
     */
    public function updating(AttachmentIndex $model)
    {
        $model->update_ip = Request::ip();
        $model->update_port = Request::server('REMOTE_PORT');
    }

    /**
     * 处理「deleted」事件
     *
     * @param AttachmentIndex $model
     * @return void
     */
    public function deleted(AttachmentIndex $model)
    {
        //如果没有同 hash 文件就删除实体文件
        if (!AttachmentIndex::bySha1($model->sha1)->exists()) {
            if (($upload = Upload::driver($model->driver)) === false) {
                return;
            }
            $upload->destroy($model->path);
        }
    }
}
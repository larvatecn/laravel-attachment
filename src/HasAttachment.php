<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Attachment;

use Illuminate\Database\Eloquent\Model;
use Larva\Attachment\Models\AttachmentIndex;

/**
 * Trait HasAttachment
 * @mixin Model
 * @author Tongle Xu <xutongle@gmail.com>
 */
trait HasAttachment
{
    /**
     * Attachment Relation
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachments()
    {
        return $this->morphMany(AttachmentIndex::class, 'attachable');
    }
}
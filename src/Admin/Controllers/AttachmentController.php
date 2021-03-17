<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Attachment\Admin\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Grid;
use Larva\Attachment\Models\AttachmentIndex;

/**
 * 附件管理
 * @author Tongle Xu <xutongle@gmail.com>
 */
class AttachmentController extends AdminController
{
    /**
     * Get content title.
     *
     * @return string
     */
    protected function title(): string
    {
        return '附件管理';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        return Grid::make(new AttachmentIndex(), function (Grid $grid) {
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
            $grid->quickSearch(['id']);
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id', 'ID')->sortable();
            $grid->column('name', '文件名');
            $grid->column('size', '文件大小');
            $grid->column('extension', '文件类型');
            $grid->column('status', '状态');
            $grid->column('updated_ip', '上传IP端口')->display(function ($updated_ip) {
                return "{$updated_ip}:{$this->updated_port}";
            });
            $grid->column('updated_at', '上传时间')->sortable();
            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->paginate(15);
        });
    }
}
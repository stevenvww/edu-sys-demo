<?php

namespace App\Admin\Controllers;

use App\Http\Constants\ApplySchoolStatusEnum;
use App\Model\ApplySchoolModel;
use App\Model\TeacherModel;
use Encore\Admin\Admin;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ApplySchoolController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '申请单(学校)管理';

    protected function getTeacherMap() {
        $schools = TeacherModel::select('id', 'name')->get();
        $schoolMap = [];
        if($schools->isNotEmpty()) {
            $schoolMap = $schools->mapWithKeys(function ($item) {
                return [$item['id'] => $item['name']];
            });
        }

        return $schoolMap;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ApplySchoolModel);

        $grid->column('id', __('学生id'));
        $grid->column('teacher_id', __('老师id'));
        $grid->column('school_name', __('学校名称'));
        $grid->column('school_province', __('学校省份'));
        $grid->column('school_city', __('学校城市'));
        $grid->column('school_area', __('学校地区'));
        $grid->column('school_address', __('学校地址'));
        $grid->column('status', __('状态'))->using(ApplySchoolStatusEnum::getStatsDesc());
        $grid->column('reason', __('拒绝原因'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('更新时间'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ApplySchoolModel::findOrFail($id));

        $show->field('id', __('学生id'));
        $show->field('teacher_id', __('老师id'));
        $show->field('school_name', __('学校名称'));
        $show->field('school_province', __('学校省份'));
        $show->field('school_city', __('学校城市'));
        $show->field('school_area', __('学校地区'));
        $show->field('school_address', __('学校地址'));
        $show->field('status', __('状态'))->using(ApplySchoolStatusEnum::getStatsDesc());
        $show->field('reason', __('拒绝原因'));
        $show->field('created_at', __('创建时间'));
        $show->field('updated_at', __('更新时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ApplySchoolModel);

        $form->select('teacher_id', __('老师id'))
            ->options($this->getTeacherMap())
            ->rules('required', ['required' => '请选择老师']);
        $form->text('school_name', __('学校名称'))->rules('required|string|max:20', [
            'max' => '最大20个字',
            'required' => '不能为空',
        ]);
        $form->text('school_province', __('学校省份'))->rules('required|string|max:16', [
            'max' => '最大16个字',
            'required' => '不能为空',
        ]);
        $form->text('school_city', __('学校城市'))->rules('required|string|max:16', [
            'max' => '最大16个字',
            'required' => '不能为空',
        ]);
        $form->text('school_area', __('学校地区'))->rules('required|string|max:16', [
            'max' => '最大16个字',
            'required' => '不能为空',
        ]);
        $form->text('school_address', __('学校地址'))->rules('required|string|max:32', [
            'max' => '最大32个字',
            'required' => '不能为空',
        ]);
        //状态：0待审核,1通过,2拒绝
        $form->radio('status', __('状态'))
            ->options(ApplySchoolStatusEnum::getStatsDesc())
            ->rules('required', ['required' => '请选择状态']);
        $form->text('reason', __('拒绝原因'))->rules('nullable|string', [
            'string' => '请输入字符串'
        ]);

        $saveBeforeStatus = null;
        $form->saving(function (Form $form) use (&$saveBeforeStatus) {
            $saveBeforeStatus = $form->status;
        });
        $form->saved(function (Form $form) use ($saveBeforeStatus) {
            if ($saveBeforeStatus != $form->status && $form->status == ApplySchoolStatusEnum::STATUS_PASS) {
            }
        });
        return $form;
    }
}
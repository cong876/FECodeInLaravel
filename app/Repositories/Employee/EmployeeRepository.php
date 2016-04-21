<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/9/10
 * Time: 上午9:54
 */
namespace App\Repositories\Employee;

use App\Models\Employee;
use App\Repositories\BaseRepository;

class EmployeeRepository extends BaseRepository implements  EmployeeRepositoryInterface {

    protected $model;

    public function __construct(Employee $model)
    {
        $this->model = $model;
    }

    /**
     * 添加新员工
     * @param array $data
     * @return static
     */
    public function createEmployee(array $data)
    {
        $employee = $this->model->create($data);
        return $employee;
    }

    /**
     * 更新员工信息
     * @param Employee $employee
     * @param array $data
     * @return bool|int
     */
    public function updateEmployee(Employee $employee,array $data)
    {
        return $employee->update($data);
    }

    /**
     * 删除该员工信息
     * @param Employee $employee
     * @return bool
     * @throws \Exception
     */
    public function deleteEmployee(Employee $employee)
    {
        $employee->is_available = 0;
        $employee->delete();
        return $employee->save();
    }

    /**
     * 设置员工类别
     * @param Employee $employee
     * @param $type
     * @return bool
     */
    public function setType(Employee $employee, $type)
    {
        $employee->type = $type;
        return $employee->save();
    }

    /**
     * 设置员工等级
     * @param Employee $employee
     * @param $op_level
     * @return bool
     */
    public function setLevel(Employee $employee, $op_level)
    {
        $employee->op_level = $op_level;
        return  $employee->save();
    }

    /**
     * 设置入职日期
     * @param Employee $employee
     * @param $entry_date
     * @return bool
     */
    public function setDate(Employee $employee, $entry_date)
    {
        $employee->entry_date = $entry_date;
        return $employee->save();
    }

    /**
     * 设置其可获得状态
     * @param Employee $employee
     * @return bool
     */
    public function setAvailable(Employee $employee)
    {
        $employee->is_available = true;
        return $employee->save();
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/9/10
 * Time: 上午9:54
 */
namespace App\Repositories\Employee;

use App\Models\Employee;

interface EmployeeRepositoryInterface {

    public function createEmployee(array $data);

    public function updateEmployee(Employee $employee,array $data);

    public function deleteEmployee(Employee $employee);

    public function setType(Employee $employee, $type);

    public function setLevel(Employee $employee, $op_level);

    public function setDate(Employee $employee, $entry_date);

    public function setAvailable(Employee $employee);
}
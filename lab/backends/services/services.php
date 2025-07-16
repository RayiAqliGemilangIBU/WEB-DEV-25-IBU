<?php
require_once __DIR__."/../dao/ExamDao.php";

class ExamService {
    protected $dao;

    public function __construct(){
        $this->dao = new ExamDao();
    }

    /** TODO
    * Implement service method used to get employees performance report
    */
    public function employees_performance_report(){
            return $this->dao->employees_performance_report();
    }

    /** TODO
    * Implement service method used to delete employee by id
    */
    public function delete_employee($employee_id){
      $this->dao->delete_employee($employee_id);
      return ['message' => 'Employee deleted'];
    }

    /** TODO
    * Implement service method used to edit employee data
    */
    public function edit_employee($employee_id, $data){

        return $this->dao->edit_employee($employee_id, $data);

    }

    /** TODO
    * Implement service method used to get orders report
    */
    public function get_orders_report(){

         return $this->dao->get_orders_report();

    }

    /** TODO
    * Implement service method used to get all products in a single order
    */
    public function get_order_details($order_id){

         return $this->dao->get_order_details($order_id);

    }

    public function add_employee($data) {
        
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email'])) {
            
            Flight::halt(400, "First name, last name, and email are required.");
        }
        // return $this->dao->add_employee($data);


        $new_employee = $this->dao->add_employee($data);

    
    $response = [
        'message' => "User berhasil dibuat",
        'data'    => $new_employee
    ];

    
    return $response;
    }
}

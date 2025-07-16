<?php

class ExamDao
{

  private $conn;

  /**
   * constructor of dao class
   */
  public function __construct()
  {

        $servername = "db1.ibu.edu.ba";
    $username = "webfinal_db_user2";
    $password = "webFinal3006";
    $database = "webfinal_db_3006";
    $port = "3306";

    try {
      /** TODO
       * List parameters such as servername, username, password, schema. Make sure to use appropriate port
       */
      /** TODO
       * Create new connection
       */

        $this->conn = new PDO("mysql:host=$servername;port=$port;dbname=$database", $username, $password);
        // set the PDO error mode to exception
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // echo "Connected successfully";
 
    } catch (PDOException $e) {
      echo "Connemployees_performance_reportection failed: " . $e->getMessage();
    }
  }

  /** TODO
   * Implement DAO method used to get employees performance report
   */
      /** TODO
     * This endpoint returns performance report for every employee.
     * It should return array of all employees where every element
     * in array should have following properties
     *   `id` -> employeeNumber of the employee
     *   `full_name` -> concatenated firstName and lastName of the employee
     *   `email` -> email of the employee
     *   `total` -> total amount of money earned for every employee.
     *              aggregated amount from payments table for every employee
     * This endpoint should return output in JSON format
     * 10 points
     */
  public function employees_performance_report() {
    $stmt = $this->conn->prepare("
        SELECT 
        e.employeeNumber AS id,
        CONCAT(e.firstName, ' ' ,e.lastName) AS full_name,
        e.email, 
        SUM( p.amount) AS total
        FROM employees e 
        JOIN customers c ON e.employeeNumber = c.salesRepEmployeeNumber 
        JOIN payments p  ON p.customerNumber = c.customerNumber 
        GROUP BY e.employeeNumber");

      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);



  }

  /** TODO
   * Implement DAO method used to delete employee by id
   */
  public function delete_employee($employee_id) {

        $stmt = $this->conn->prepare("DELETE FROM e employees WHERE employeeNumber = :id ?");
        $stmt->bindParam(':id', $employee_id);
        $stmt->execute();


  }

  /** TODO
   * Implement DAO method used to edit employee data
   */
  public function edit_employee($employee_id, $data) {

     $stmt = $this->conn->prepare("
            UPDATE employees
            SET firstName = :first_name, lastName = :last_name, email = :email
            WHERE employeeNumber = :id
        ");
        $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'id'         => $employee_id
        ]);

      
        $stmt = $this->conn->prepare("SELECT * FROM employees WHERE employeeNumber = :id");
        $stmt->bindParam(':id', $employee_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

  }

  /** TODO
   * Implement DAO method used to get orders report
   */

   /** TODO
     * This endpoint should return the report for every order in the database.
     * For every order we need the amount of money spent for the order. In order
     * to get total money for every order quantityOrdered should be multiplied 
     * with priceEach from the orderdetails table. The data should be summarized
     * in order to get accurate report. Every item returned should 
     * have following properties:
     *   `details` -> the html code needed on the frontend. Refer to `orders.html` page
     *   `order_number` -> orderNumber of the order
     *   `total_amount` -> aggregated amount of money spent per order
     * This endpoint should return output in JSON format
     * 10 points
     */
  public function get_orders_report() {

    $stmt = $this->conn->prepare("
        SELECT 
        o.orderNumber AS order_number,
        SUM(o.quantityOrdered * o.priceEach) 
        FROM orderdetails o
        GROUP BY o.orderNumber");

      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ( $result as &$row) {
        $row['details'] = '<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#order-details-modal" data-bs-id="' . $row['order_number'] . '">Details</button>';
      }

      return $result;




  }

  /** TODO
   * Implement DAO method used to get all products in a single order
   */

      /** TODO
     * This endpoint should return the array of all products in a single 
     * order with the provided id. Every food returned should have 
     * following properties:
     *   `product_name` -> productName from the database
     *   `quantity` -> quantity from the orderdetails table
     *   `price_each` -> priceEach from the orderdetails table
     * This endpoint should return output in JSON format
     * 10 points
     */
  public function get_order_details($order_id) {

    $stmt = $this->conn->prepare("
        SELECT 
        p.productName AS product_name,
        od.quantityOrdered AS quantity,
        od.priceEach AS price_each
        FROM products p
        JOIN orderdetails od ON od.productCode = p.productCode
        WHERE od.orderNumber = :order_id");


      $stmt->bindParam(':order_id', $order_id);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $result;
  }

  public function add_employee($data) {
  
        $stmt_max = $this->conn->prepare("SELECT MAX(employeeNumber) AS max_id FROM employees");
        $stmt_max->execute();
        $max_id_row = $stmt_max->fetch(PDO::FETCH_ASSOC);
        $new_id = $max_id_row['max_id'] + 1;


        $stmt_insert = $this->conn->prepare(
            "INSERT INTO employees (employeeNumber, lastName, firstName, extension, email, officeCode, reportsTo, jobTitle)
             VALUES (:employeeNumber, :lastName, :firstName, 'x100', :email, '1', 1002, 'Sales Rep')"
        );
        
        $stmt_insert->execute([
            'employeeNumber' => $new_id,
            'lastName'       => $data['last_name'],
            'firstName'      => $data['first_name'],
            'email'          => $data['email']
        ]);


        return $this->get_employee_by_id($new_id);
    }
}

<?php

class Customer{
    private $customerID;
    private $customerName;
    private $email;
    private $phoneNo;
    private $accountNo;



    public function __construct( $customerName, $email, $phoneNo, $accountNo)
    {
        $this->customerName = $customerName;
        $this->email = $email;
        $this->phoneNo = $phoneNo;
        $this->accountNo = $accountNo;
    }


    public function getCustomerID(){
        return $this->customerID;
    }

    public function getCustomerName(){
        return $this->customerName;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getPhoneNo(){
        return $this->phoneNo;
    }

    public function getAccountNo(){
        return $this->accountNo;
    }

}

class Bank {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    //display all customers details
    public function displayAllCustomers(){
        $select = "SELECT * FROM customer";
        $result = $this->connection->query($select);

        while ($customer = $result->fetch_assoc()) {
            echo $customer['customerID'] . "\t" .
                $customer['customerName'] . "\t" .
                $customer['email'] . "\t" .
                $customer['phoneNo'] . "\t" .
                $customer['accountNo'] . "\t" .
                $customer['balance'] . "\n";
        }
    }


    //display specific customer using customerID    
    public function searchCustomer($customerID){
        // $searchCustomer->bind_param("i", $customerID);   
        // $searchCustomer->execute();
        // $result = $searchCustomer->get_result();
        
        $searchCustomer = "SELECT * FROM customer WHERE customerID = ?";
        $result = $this->connection->execute_query($searchCustomer, [$customerID]);

    if ($customer = $result->fetch_assoc()) {
        

        echo $customer['customerID'] . "\t" .
             $customer['customerName'] . "\t" .
             $customer['email'] . "\t" .
             $customer['phoneNo'] . "\t" .
             $customer['accountNo'] . "\t" .
             $customer['balance'] . "\n";
    } else {
        echo "Customer not found.\n";
    }

    }

    //deposit amt
    public function deposit($customerID, $amount) {

        if ($amount <= 0) {
            echo "Amount must be greater than 0.\n";
        } else {
            $deposit = $this->connection->prepare("UPDATE customer SET balance = balance + ? WHERE customerID = ?");

            $deposit->bind_param("di", $amount, $customerID);
            $deposit->execute();

            if ($deposit->affected_rows > 0) {
                echo "Deposit successful!\n";
            } else {
                echo "Customer not found or deposit unsuccessful.\n";
            }

            $deposit->close();
        }
    }

    //withdraw amt
    public function withdraw($customerID, $withdrawAmt){
        if ($withdrawAmt <= 0) {
        echo "Withdrawal amount must be greater than 0.\n";
    } else {
        $withdraw = $this->connection->prepare("UPDATE customer SET balance = balance - ? WHERE customerID = ? AND balance >= ?");

        $withdraw->bind_param("did", $withdrawAmt, $customerID, $withdrawAmt);
        $withdraw->execute();

        if ($withdraw->affected_rows > 0) {
            echo "Withdrawal successful!\n";
        } else {
            echo "Customer not found or insufficient balance.\n";
        }

        $withdraw->close();
    }
    }

    //Insert Customer details
    public function insertCustomer($customerName, $email, $phoneNo, $accountNo, $balance){

    $customer = new Customer($customerName, $email, $phoneNo, $accountNo);

    $name = $customer->getCustomerName();
    $email = $customer->getEmail();
    $phone = $customer->getPhoneNo();
    $account = $customer->getAccountNo();

        $insertValue = $this->connection->prepare("INSERT INTO customer (customerName, email, phoneNo, accountNo, balance) values (?,?,?, ?,?)");
        $insertValue->bind_param("ssssd",$name, $email, $phone, $account, $balance); #s - string, i - int
        if($insertValue->execute()){
            echo "Data Inserted Successfully";
        }
        else echo "Error: " . $insertValue->error . "\n";

        $insertValue->close();
    }

    public function deleteCustomer($customerID){
        $delete = "delete from customer where customerID = ?";
        $result = $this->connection->execute_query($delete, [$customerID]);

        if ($result && $this->connection->affected_rows > 0) { 
            echo "Customer deleted successfully!\n";
        } else {
            echo "Customer not found or could not be deleted.\n";
        }
    }
}

$host = "localhost";
$username = "root";
$password = "Dipesh11";
$database = "application";

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}


$bank = new Bank($connection);

echo "===== Horizon Bank =====\n";
echo "Enter 1 to Display All Customers\n";
echo "Enter 2 to Search Customer by ID\n";
echo "Enter 3 to Deposit Money\n";
echo "Enter 4 to Withdraw Money\n";
echo "Enter 5 to Insert Customer Data\n";
echo "Enter 6 to Delete Customer Data\n";
echo "Enter 7 to Exit\n";

$choice = (int) readline("Enter your choice: ");
echo "===== ===== ===== =====\n";

if($choice == 1){
    // echo "Display All Customers";
    $bank->displayAllCustomers();

}
elseif($choice == 2){
    // echo "Search Customer by ID";
    $customerID = (int)readline("Enter CustomerID - ");
    $bank->searchCustomer($customerID);
}
elseif($choice == 3){
    //echo "Deposit Money\n";

        $customerID = (int)readline("Enter Customer ID - ");
        $depositAmt = (float)readline("Enter Deposit Amount - ");

        $bank->deposit($customerID, $depositAmt);
}
elseif($choice == 4){
    //echo "Withdraw Money\n";
    $customerID = (int) readline("Enter Customer ID - ");
    $withdrawAmt = (float) readline("Enter Withdrawal Amount - ");

    $bank->withdraw($customerID, $withdrawAmt);

}
elseif($choice == 5){
        $customerName = (string)readline("Enter customerName - ");
        $email = (string)readline("Enter Email - ");
        $phoneNo = (string)readline("Enter Phone No - ");
        $accountNo = (string)readline("Enter Account No - ");
        $balance = (float)readline("Enter Intial Amount - ");
    $bank->insertCustomer($customerName, $email, $phoneNo, $accountNo, $balance);
}
elseif($choice == 6){
    $customerID = (int)readline("Enter CustomerID - ");
    $bank->deleteCustomer($customerID);
}
elseif($choice == 7){
    echo "Thank you for using Horizon Bank!\n";
}
else{
    echo "Invalid choice.\n";
}



$connection->close();

?>
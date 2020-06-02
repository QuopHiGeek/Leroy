<?php
/**
 * Created by PhpStorm.
 * User: sbortey
 * Date: 16/09/2018
 * Time: 9:16 AM
 */

class Facility
{

    private $conn;
    private $table_name = "facility_details";

    public $facility_id;
    public $facility_name;
    public $facility_code;
    public $other_id;
    public $facility_emails;
    public $report_email;
    public $contact_person;
    public $facility_mobile_numbers;
    public $address;
    public $facility_state;
    public $facility_district;
    public $facility_hub_name;
    public $latitude;
    public $longitude;
    public $facility_type;
    public $status;
    public $current_tresh;
    public $total_tresh;

    public function __construct($db){
        $this->conn = $db;
    }

    function get()
    {

        // select all query
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY facility_id ASC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    function fetch_treshhold(){
        // select all query
        $query = "SELECT a.facility_id, a.facility_name, a.facility_state, a.facility_district, b.current_tresh, b.total_tresh FROM " . $this->table_name . " a, vl_treshhold b where a.facility_id=b.facility_id AND a.status='active'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    function update_treshhold($fac_id,$qty){
        $query = "SELECT current_tresh, total_tresh from vl_treshhold where facility_id = ".$fac_id;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        //$stmt->bind_result($current_tresh, $total_tresh);
        $curtresh = 0;
        $totTresh = 0;
        while ($row = $stmt->fetch()) {

            $curtresh = $row['current_tresh'];
            $totTresh = $row['total_tresh'];
        }
        //$stmt->close();
        if ($curtresh < $totTresh){
            if(($qty + $curtresh) <= $totTresh){
                $totQ = $qty + $curtresh;
                if($this->do_update($totQ,$fac_id)) {
                    return "Updated successfully!!!";
                }else{
                    return "Updated failed!!! Try again Later";
                }
            }else{
                return "Update Not Successful, Total treshhold for facility will be exceeded";
            }
        }else{
            return "Facility Full, please try another facility ";
        }




    }

    function reset_treshhold($fac_id,$qty){
        $query = "SELECT current_tresh, total_tresh from vl_treshhold where facility_id = ".$fac_id;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        //$stmt->bind_result($current_tresh, $total_tresh);
        $curtresh = 0;
        $totTresh = 0;
        while ($row = $stmt->fetch()) {

            $curtresh = $row['current_tresh'];
            $totTresh = $row['total_tresh'];
        }
        //$stmt->close();
        if ($curtresh == 0){
            return "Facility is empty ";
        }else{
            $totQ = $curtresh - $qty;
            if($totQ < 0){
                return "Facility has less samples than what you claim to have treated ";
            }else {
                if ($this->do_reset($totQ, $fac_id)) {
                    return "Treated Samples saved successfully";
                } else {
                    return "Something went wrong, please try again later ";
                }
            }
        }




    }

    function do_update($qantity,$fac_id){
        $query = "UPDATE vl_treshhold SET current_tresh = :qty where facility_id = :fac_id";
        $stmt = $this->conn->prepare($query);
        $qantity=htmlspecialchars(strip_tags($qantity));
        $fac_id=htmlspecialchars(strip_tags($fac_id));
        $stmt->bindParam(':qty', $qantity);
        $stmt->bindParam(':fac_id', $fac_id);

        // execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function do_reset($qantity,$fac_id){
        $query = "UPDATE vl_treshhold SET current_tresh = :qty where facility_id = :fac_id";
        $stmt = $this->conn->prepare($query);
        $qantity=htmlspecialchars(strip_tags($qantity));
        $fac_id=htmlspecialchars(strip_tags($fac_id));
        $stmt->bindParam(':qty', $qantity);
        $stmt->bindParam(':fac_id', $fac_id);

        // execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }
}
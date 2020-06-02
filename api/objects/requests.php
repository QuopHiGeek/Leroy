<?php
class Request{

    // database connection and table name
    private $conn;
    private $table_name = "vl_request_form";

    // object properties
    public $sampleID;
    public $instanceId;
    public $countryID;
    public $sampleCodeTitle;
    public $serialNo;
    public $sampleReOrdered;
    public $sampleCode;
    public $sampleCodeFormat;
    public $sampleCodeKey;
    public $facilityID;
    public $sampleCollectionDate;
    public $patientFirstName;
    public $patientMiddleName;
    public $patientLastName;
    public $patientGender;
    public $patientDOB;
    public $patientAgeInYears;
    public $patientAgeInMonths;
    public $isPatientPregnant;
    public $isPatientBreastfeeding;
    public $patientARTNo;
    public $treatmentInitiationDate;
    public $currentRegimen;
    public $currentRegimenInitiatonDate;
    public $patientMobileNo;
    public $SMSConsent;
    public $sampleType;
    public $pickedBy;
    public $arvAdherence;
    public $vlTestReason;
    public $lastVlTestDate;
    public $lastVlTestResult;
    public $lastVlFailureACDate;
    public $lastVlFailureACResult;
    public $lastVlFailureDate;
    public $lastVlFailureResult;
    public $reqClinician;
    public $reqClinicianPhoneNo;
    public $testRequestedOn;
    public $vlFocalPerson;
    public $vlFocalPersonPhoneNo;
    public $labID;
    public $vlTestPlatform;
    public $sampleReceivedOn;
    public $sampleTestDateAtLab;
    public $resultDispatchedOn;
    public $isSampleRejected;
    public $sampleRejectionReason;
    public $absoluteResultValue;
    public $absoluteDecimalResultValue;
    public $resultValue;
    public $resultApprovedBy;
    public $approverComments;
    public $resultStatus;
    public $requestCreatedBy;
    public $requestCreatedDate;
    public $lastModifiedBy;
    public $lastModifiedDate;
    public $manualResultEntry;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    function get()
    {

        // select all query
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY vl_sample_id DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    function getOne($id){

        // query to read single record
        $query = "SELECT * FROM " . $this->table_name . " WHERE vl_sample_id = ? LIMIT 0,1";

        // prepare query statement
        $stmt = $this->conn->prepare( $query );

        // bind id of product to be updated
        $stmt->bindParam(1, $id);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        // set values to object properties
        $this->sampleID = $row['vl_sample_id'];
        $this->instanceId = $row['vlsm_instance_id'];
        $this->countryID = $row['vlsm_country_id'];
        $this->sampleCodeTitle = $row['sample_code_title'];
        $this->serialNo = $row['serial_no'];
        $this->sampleReOrdered = $row['sample_reordered'];
        $this->sampleCode = $row['sample_code'];
        $this->sampleCodeFormat = $row['sample_code_format'];
        $this->sampleCodeKey = $row['sample_code_key'];
        $this->facilityID = $row['facility_id'];
        $this->sampleCollectionDate = $row['sample_collection_date'];
        $this->patientFirstName = $row['patient_first_name'];
         $this->patientMiddleName = $row['patient_middle_name'];
        $this->patientLastName = $row['patient_last_name'];
        $this->patientGender = $row['patient_gender'];
        $this->patientDOB = $row['patient_dob'];
        $this->patientAgeInYears = $row['patient_age_in_years'];
        $this->patientAgeInMonths = $row['patient_age_in_months'];
        $this->isPatientPregnant = $row['is_patient_pregnant'];
        $this->isPatientBreastfeeding = $row['is_patient_breastfeeding'];
        $this->patientARTNo = $row['patient_art_no'];
        $this->treatmentInitiationDate = $row['treatment_initiated_date'];
        $this->currentRegimen = $row['current_regimen'];
        $this->currentRegimenInitiatonDate = $row['date_of_initiation_of_current_regimen'];
        $this->patientMobileNo = $row['patient_mobile_number'];
        $this->SMSConsent = $row['consent_to_receive_sms'];
        $this->sampleType = $row['sample_type'];
        $this->pickedBy = $row['picked_by'];
        $this->arvAdherence = $row['arv_adherance_percentage'];
        $this->vlTestReason = $row['reason_for_vl_testing'];
        $this->lastVlTestDate = $row['last_vl_date_routine'];
        $this->lastVlTestResult = $row['last_vl_result_routine'];
        $this->lastVlFailureACDate = $row['last_vl_date_failure_ac'];
        $this->lastVlFailureACResult = $row['last_vl_result_failure_ac'];
        $this->lastVlFailureDate = $row['last_vl_date_failure'];
        $this->lastVlFailureResult = $row['last_vl_result_failure'];
        $this->reqClinician = $row['request_clinician_name'];
        $this->reqClinicianPhoneNo = $row['request_clinician_phone_number'];
        $this->testRequestedOn = $row['test_requested_on'];
        $this->vlFocalPerson = $row['vl_focal_person'];
        $this->vlFocalPersonPhoneNo = $row['vl_focal_person_phone_number'];
        $this->labID = $row['lab_id'];
        $this->vlTestPlatform = $row['vl_test_platform'];
        $this->sampleReceivedOn = $row['sample_received_at_vl_lab_datetime'];
        $this->sampleTestDateAtLab = $row['sample_tested_datetime'];
        $this->resultDispatchedOn = $row['result_dispatched_datetime'];
        $this->isSampleRejected = $row['is_sample_rejected'];
        $this->sampleRejectionReason = $row['reason_for_sample_rejection'];
        $this->absoluteResultValue = $row['result_value_absolute'];
        $this->absoluteDecimalResultValue = $row['result_value_absolute_decimal'];
        $this->resultValue = $row['result'];
        $this->resultApprovedBy = $row['result_approved_by'];
        $this->approverComments = $row['approver_comments'];
        $this->resultStatus = $row['result_status'];
        $this->requestCreatedBy = $row['request_created_by'];
        $this->requestCreatedDate = $row['request_created_datetime'];
        $this->lastModifiedBy = $row['last_modified_by'];
        $this->lastModifiedDate = $row['last_modified_datetime'];
        $this->manualResultEntry = $row['manual_result_entry'];
    }
    function getBatch($id){

        // query to read single record
        $query = "SELECT * FROM " . $this->table_name . " WHERE sample_batch_id = ?";

        // prepare query statement
        $stmt = $this->conn->prepare( $query );

        // bind id of product to be updated
        $stmt->bindParam(1, $id);

        // execute query
        $stmt->execute();

        // get retrieved row
        return $stmt;
    }
    // update the product
    function update(){

        // update query
        $query = "UPDATE
                " . $this->table_name . "
            SET
                name = :name,
                price = :price,
                description = :description,
                category_id = :category_id
            WHERE
                id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->price=htmlspecialchars(strip_tags($this->price));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind new values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);

        // execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }
    // delete the product
    function delete(){

        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind id of record to delete
        $stmt->bindParam(1, $this->id);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;

    }
    // search products
    function search($keywords){

        // select all query
        $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            WHERE
                p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
            ORDER BY
                p.created DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $keywords=htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        // bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    // read products with pagination
    public function readPaging($from_record_num, $records_per_page){

        // select query
        $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            ORDER BY p.created DESC
            LIMIT ?, ?";

        // prepare query statement
        $stmt = $this->conn->prepare( $query );

        // bind variable values
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

        // execute query
        $stmt->execute();

        // return values from database
        return $stmt;
    }
    // used for paging products
    public function count(){
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total_rows'];
    }
}
<?php
    /**
     * Purpose: API module get_where_used.php provides information about the application id,
     *          application name, and application version given component information.
     *
     * Input:   supported input parameters are 'component_name, 'component_id'. Both the
     *          parameters can be used as a single input or passed as a combined unit.
     *          component_id = digits only.
     *          component_name = Alpha,digits, space and certain special characters.
     *          component_version = digits, space and certain special characters
     *
     * SAMPLE URL INPUTS
     * http://localhost/sbom2/api/get_owner_by_component.php?component_id=755954
     * http://localhost/sbom2/api/get_owner_by_component.php?component_id=67702376
     * http://localhost/sbom2/api/get_owner_by_component.php?component_name=LTS JSON Libray
     * http://localhost/sbom2/api/get_owner_by_component.php?component_name=kassandra HttpClient
     * http://localhost/sbom2/api/get_owner_by_component.php?component_version=7.5
     * http://localhost/sbom2/api/get_owner_by_component.php?component_version=9.4.76.v70700237
     * Output:  The module outputs data as a json object. The json object also includes HTTP
     *          response code and count of rows parameters passed and data name value pairs.
     *
     * Error Conditions: response code of http 400 is generated when system detects an error condition.
     *                   component_id, component_name, component_version can generate "Invalid request"
     *                   or "Invalid or empty request" for unsupported characters.
     *
     * @author Shahid Iqbal, Isaac Hentges, Nathan Lantaigne-Goetsch, Abdulsalam Geddi
     *
     * The apiUtility class is for various helper functions for the php pages.
     */
    require("./apiUtility.php");

    if(isset($_GET['component_id'])) {
        $component_id = $_GET['component_id'];

        if(!empty($component_id) && preg_match('/^\d*$/', $component_id)) {
            $apiFunctions = new apiUtility();
            $processor = $apiFunctions->get_owner_component_id($component_id);
            $data = [];
            $count = 0;
            if($processor!==false && $processor->num_rows > 0) {
                $count = $processor->num_rows;
                while($row  = $processor->fetch_assoc()){
                    $data[] = $row;
                }
            }
            $res = [];
            response(200, $count, $component_id, $data);
        }
        else if (isset($component_id) && empty($component_id)) {
            invalidResponse("Invalid or Empty input");
        }
        else {
            invalidResponse("Invalid Request");
        }
    } else if(isset($_GET['component_name'])) {
        $component_name = $_GET['component_name'];

        if(!empty($component_name) && preg_match('/^[\d A-Za-z +:-]*$/', $component_name)) {
            $apiFunctions = new apiUtility();
            $processor = $apiFunctions->get_owner_component_name($component_name);
            $data = [];
            $count = 0;
            if($processor!==false && $processor->num_rows > 0) {
                $count = $processor->num_rows;
                while($row  = $processor->fetch_assoc()){
                    $data[] = $row;
                }
            }
            $res = [];
            response(200, $count, $component_name, $data);
        }
        else if (isset($component_name) && empty($component_name)) {
            invalidResponse("Invalid or Empty input");
        }
        else {
            invalidResponse("Invalid Request");
        }
    } else if($_GET['component_version']){
        $component_version = $_GET['component_version'];

        if(!empty($component_version) && preg_match('/^[\d-.,_ ]*$/', $component_version)) {
            $apiFunctions = new apiUtility();
            $processor = $apiFunctions->get_owner_component_version($component_version);
            $data = [];
            $count = 0;
            if($processor->num_rows > 0) {
                $count = $processor->num_rows;
                while($row  = $processor->fetch_assoc()){
                    $data[] = $row;
                }
            }
            response(200, $count, $component_version, $data);
        }
        else if (isset($component_version) && empty($component_version)) {
            invalidResponse("Invalid or Empty input");
        }

        else {
            invalidResponse("Invalid Request");
        }
    }else {
        invalidResponse("Invalid Request");
    }


    function invalidResponse($message) {
        response(400, $message, NULL, NULL);
    }

    function response($responseCode, $message, $string, $data) {
        // Locally cache results for two hours
        header('Cache-Control: max-age=7200');
        // JSON Header
        header('Content-type:application/json;charset=utf-8');
        http_response_code($responseCode);
        $response = array("response_code" => $responseCode, "Records" => $message, "Parameter Value" => $string, "data" => $data);
        $json = json_encode($response, JSON_PRETTY_PRINT);
        echo $json;
    }
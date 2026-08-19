<?php

namespace App\Http\Controllers;

class HrController extends Controller
{

    public static function getEmployeeList()
    {
        return HrController::getHrData('hr/employee');
    }

    public static function getDesignationList()
    {
        return HrController::getHrData('hr/designation');
    }

    public static function getDepartmentList()
    {
        return HrController::getHrData('hr/department');
    }

    public static function getLeaveDetails($empId)
    {
        return HrController::getHrData('hr/leave/' . $empId);
    }

    private static function getHrData($path)
    {
        $url = env('API_FETCH', 'https://jul.ican.net.np:12560/icanird/rest/') . $path;

        $token =  HrController::getToken();
        $curl2 = curl_init($url);
        curl_setopt($curl2, CURLOPT_POST, FALSE);
        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl2, CURLOPT_HTTPHEADER, array('auth:' . $token));

        curl_setopt($curl2, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, 0);
        $response2 = curl_exec($curl2);

        curl_close($curl2);
        $result2 = json_decode($response2);
        return $result2;
    }

     public static function getToken()
    {
        $url = env('API_FETCH', 'https://jul.ican.net.np:12560/icanird/rest/') . 'api/validate-request-exam';
        //dd($url);
        $data = array(
            'username' => '',
            'password' => ''
        );
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$bearerToken));
        $response = curl_exec($curl);
        //  dd($response);
        if (curl_errno($curl)) {
            echo 'Request Error:' . curl_error($curl);
            die();
        }

        curl_close($curl);
        $result = json_decode($response);
        // dd($result);
        return $result->data[0];

    }
}

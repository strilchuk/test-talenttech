<?php

namespace App\Http\Controllers;

use App\Inn;
use DateTime;
use Illuminate\Http\Request;

/**
 * Class InnController
 * @package App\Http\Controllers
 */
class InnController extends Controller
{

    /**
     * Реализация алгоритма проверки ИНН
     * @param string $inn
     * @return bool
     */
    private function innValidateAlgoritmImplementation(string $inn)
    {
        $innArr = str_split($inn); //Непостредственно массив чисел ИНН

        $checkArr1 = [7,2,4,10,3,5,9,4,6,8,0]; // Первый массив весовых коэффициентов
        $sum1 = 0;
        for ($i = 0; $i < count($checkArr1); $i++) {
            $sum1 += $checkArr1[$i] * $innArr[$i];
        }
        $checkD1 = $sum1 % 11;
        if ($checkD1 > 9)
            $checkD1 = $checkD1 % 10;

        $checkArr2 = [3,7,2,4,10,3,5,9,4,6,8,0]; // Второй массив весовых коэффициентов
        $sum2 = 0;
        for ($i = 0; $i < count($checkArr2); $i++) {
            $sum2 += $checkArr2[$i] * $innArr[$i];
        }
        $checkD2 = $sum2 % 11;
        if ($checkD2 > 9)
            $checkD2 = $checkD2 % 10;

        return ($checkD1 == $innArr[10]) && ($checkD2 == $innArr[11]);
    }


    /**
     * Получение результата из базы данных
     * @param string $inn
     * @return array|null
     */
    private function innCheckTaxPayerStatusGetFromDB(string $inn){

        $innDB = Inn::where('inn', $inn)->first();

        $res = null;
        if (isset($innDB)) {
            $dateOfRecord = DateTime::createFromFormat('Y-m-d H:i:s', $innDB->last_check);
            $dateDiff = (int)$dateOfRecord->diff(new DateTime(), true)->format('%a');
            if ($dateDiff > 0)
                $innDB->forceDelete();
            else
                $res = ["status" => $innDB->result, "message" => $innDB->message];
        }

        return $res;
    }


    /**
     * Отправка запроса с проверкой на самозанятого
     * @param string $inn
     * @return mixed
     */
    private function innCheckTaxPayerStatusSendRequest(string $inn){
        $postdata = json_encode(["inn" => $inn, "requestDate" => date("Y-m-d")]);

        $opts = array(
            'http'=>array(
                'method'=>"POST",
                'header'  => 'Content-type: application/json',
                'content' => $postdata,
                "ignore_errors" => true
            )
        );

        $context = stream_context_create($opts);

        $res = json_decode(file_get_contents('https://statusnpd.nalog.ru/api/v1/tracker/taxpayer_status', false, $context));

        $status_line = $http_response_header[0];

        preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);

        $status = $match[1];

        if ($status !== "200") {
            $res->status = false;
        } else {
            $innDb = new Inn();
            $innDb->inn = $inn;
            $innDb->result = $res->status;
            $innDb->message = $res->message;
            $innDb->last_check = time();
            $innDb->save();
        }

        return $res;
    }

    /**
     * Проверка на самозанятого
     * @param string $inn
     * @return array|mixed
     */
    private function innCheckTaxPayerStatus(string $inn)
    {
        $innDB = self::innCheckTaxPayerStatusGetFromDB($inn);
        if (!isset($innDB)){
           $innDB = self::innCheckTaxPayerStatusSendRequest($inn);
        }

        return $innDB;
    }

    /**
     * Валидацие ИНН
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateInn(Request $request)
    {
        $res = [];
        $res['status'] = true;

        if (is_numeric($request['inn'])){
            if (strlen($request['inn']) == 12) {
                if (self::innValidateAlgoritmImplementation($request['inn'])) {
                    $res = self::innCheckTaxPayerStatus($request['inn']);

                } else {
                    $res['status'] = false;
                    $res['message'] = 'Число не является ИНН';
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Данный ИНН не принадлежит физическому лицу';
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'ИНН не является числом';
        }

        return response()->json($res);
    }
}

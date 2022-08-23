<?php


namespace App\Services\IntegratedNumberServices;

use App\Services\MultiplesServices\InfQixFooMultiplesService;
use App\Services\OccurrencesServices\InfQixFooOccurrencesService;

class InfQixFooIntegratedNumberService extends IntegratedNumberService
{
    public $multiples_service;
    public $occurrences_service;

    function __construct(){

        parent::__construct();
        $this->multiples_service = new InfQixFooMultiplesService();
        $this->occurrences_service = new InfQixFooOccurrencesService();
    }


    /*
     * Validates the input number and combines the output of
     * multiples and occurrences service.
     */
    public function multiples_and_occurrences($input_number){

        //Validate input number so that other services dont have to
        $validator_response = $this->validate($input_number);
        if($validator_response) return $validator_response;

        //Get responses
        $multiples_response = $this->multiples_service->multiples($input_number,false);
        $occurrences_response = $this->occurrences_service->occurrences($input_number,false);

        //Check if calculations were successful and return the combined result
        if($multiples_response['success'] && $occurrences_response['success']){
            $combined_result = $multiples_response['result']."; ".$occurrences_response['result'];
            return [
                'success'=>true,
                'input' =>$input_number,
                'result'=>$combined_result
            ];
        }

        //Return error message if calculations were not successful
        return [
            'success'=>false,
            'input' =>$input_number,
            'error'=>'Multiples and occurrences integration error.'
        ];
    }
}

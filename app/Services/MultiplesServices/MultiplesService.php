<?php


namespace App\Services\MultiplesServices;

use App\Services\NumberService;

class MultiplesService extends NumberService
{

    public $multipliers;   //Key value pairs with multiplier values and their associated output
    public $separator;   //String of characters to use as glue if the array needs to be converted to string

    function __construct(){
        $this->multipliers = array_values(collect([
            [
                'multiplier' => 1,
                'output' => 'A'
            ]
        ])->toArray());
    }


    /*
     * Returns a string or strings if input number is a multiple of
     * values associated with the strings. If input number is not a multiple,
     * then return the input number as a string
     */
    public function multiples($input_number, $validate = true){

        //If validate marker is left true, then do input number validation
        if($validate){
            $validator_response = $this->validate($input_number);
            if($validator_response) return $validator_response;
        }

        //Sort multipliers
        $sorted_multipliers = collect($this->multipliers);

        //If input number is 0 then return all outputs without calculation
        $output = $sorted_multipliers->pluck('output')->toArray();
        if($this->separator) $output = join($this->separator, $output);
        if($input_number == 0) return [
            'success'=> true,
            'input'=>$input_number,
            'result'=>$output
        ];


        //Sort multipliers by their value and append to output if the input number is a multiple
        $output = [];   //Array for storing the outputs when searching for multipliers

        foreach ($sorted_multipliers as $multiplier){
            if(!($input_number % $multiplier['multiplier']))
                array_push($output, $multiplier['output']);
        }


        //If there were any multipliers that were suitable, return the array containing outputs
        if($this->separator) $output = join($this->separator, $output);
        if(!empty($output)) return [
            'success'=>true,
            'input'=>$input_number,
            'result'=>$output
        ];

        //If none of the multipliers were suitable for input, then return input number as string
        return [
            'success' => true,
            'input'=>$input_number,
            'result' => (string)$input_number,
        ];

    }
}

<?php

namespace Tests\Feature\MultiplesAndOccurrences;

use App\Services\IntegratedNumberServices\InfQixFooIntegratedNumberService;

class InfQixFooIntegratedNumberServiceTest extends IntegratedNumberServiceTest
{
    private $integrated_number_service; //The class instance that will produce the actual output
    private $multipliers;
    private $digits;

    public function setUp(): void
    {
        parent::setUp();

        //Create the service to use its functions
        $this->integrated_number_service = new InfQixFooIntegratedNumberService();
        $this->multipliers = collect($this->integrated_number_service->multiples_service->multipliers)
            ->sortBy('multiplier');
        $this->digits = $this->integrated_number_service->occurrences_service->digits;
    }



    /**
     * Overridden parent function.
     * Create an integer that has is a multiple of all the multipliers and contains all
     * the necessary digits. Pass it to new service and compare output with expected result.
     * Expected output is in string format and separated by semicolons.
     *
     * @test
     * @return void
     */
    public function multiples_and_occurrences_test()
    {
        //Find a starting value that will always be multiple for all multipliers
        $multiplier_product = $this->multipliers[0]['multiplier'];
        for ($i = 1; $i<count($this->multipliers); $i++){
            $multiplier_product *= $this->multipliers[$i]['multiplier'];
        }

        //Keep incrementing the number by multiplier product until all transform digits are present
        $new_number = $multiplier_product;
        while (!$this->contains_all_transform_digits($new_number)) {
            $new_number += $multiplier_product;
        }

        //Expected multiples part contains all the multiples outputs
        $multiples_output = collect($this->multipliers)->pluck('output')->toArray();

        //Create expected digit occurrence output using new number
        $digits_in_number = str_split((string)$new_number);

        $occurrences_output = [];
        foreach ($digits_in_number as $digit_in_number){
            foreach ($this->digits as $transform_digit){
                if($digit_in_number == (string)$transform_digit['digit'])
                    array_push($occurrences_output,$transform_digit['output']);
            }
        }

        $combined_output = join("; ",$multiples_output)."; ".join("; ",$occurrences_output);

        //Assert that when passing the generated number as input to integrated number service
        //the result is the same as combined output of multiples and occurrences
        $this->assertSame(
            [
                'success'=>true,
                'input'=>$new_number,
                'result'=>$combined_output
            ],
            $this->integrated_number_service->multiples_and_occurrences($new_number)
        );
    }
}

<?php

namespace App\Utils;

use App\Employees;
use App\Utils\TaxUtil;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeUtil extends Util
{
    //Calcular la edad de la persona
    /**
     * Return age employee
     * @param int
     */
    public function getAge($birth_date)
    {
        return Carbon::parse($birth_date)->diff(Carbon::now())->format('%y');
    }

    //Convertir números a letras
    /**
     * Return numbers letters
     * @param string
     */
    public function getNumberLetters($numero)
    {
        $numberLetters = "";
        $numbersArray = [
            'CERO' => '0',
            'UNO' => '1',
            'DOS' => '2',
            'TRES' => '3',
            'CUATRO' => '4',
            'CINCO' => '5',
            'SEIS' => '6',
            'SIETE' => '7',
            'OCHO' => '8',
            'NUEVE' => '9',
            'GUIÓN' => '10'
        ];

        $numbers = str_split($numero);

        foreach ($numbers as $number) {
            if ($number == '-') {
                $found_key = array_search('10', $numbersArray, true);
            } else {
                $found_key = array_search($number, $numbersArray, true);
            }

            if ($found_key != null) {
                $numberLetters = $numberLetters . " " . $found_key;
            } else {
                $numberLetters = $numberLetters . " " . $number;
            }
        }

        return $numberLetters;
    }

    public function getDate($date, $normal_format)
    {
        $day = Carbon::parse($date)->format('d');

        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $month = Carbon::parse($date)->format('m');
        $month = $meses[$month - 1];

        $year = Carbon::parse($date)->format('Y');

        if ($normal_format == true) {
            return strtolower($day . ' de ' . $month . ' ' . $year);
        } else {
            if ($day == '01') {
                \Log::info(mb_strtolower('Al primer día del mes de ' . $month . ' de ' . $year));
                return 'Al ' . mb_strtolower('primer día del mes de ' . $month . ' de ' . $year);
            } else {
                return 'A ' . mb_strtolower('los ' . $day . ' días del mes de ' . $month . ' de ' . $year);
            }
        }
    }

    /**
     * Return date letters
     * @param string
     */
    public function getDateLetters($date, $normal_format)
    {
        $day = Carbon::parse($date)->format('d');
        $num = str_replace(",", "", $day);
        $num = (int)$num;
        $day = $this->miles($num);

        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $month = Carbon::parse($date)->format('m');
        $month = $meses[$month - 1];

        $year = Carbon::parse($date)->format('Y');
        $num = str_replace(",", "", $year);
        $num = (int)$num;
        $year = $this->miles($num);

        if ($normal_format == true) {
            return strtolower($day . ' de ' . $month . ' de ' . $year);
        } else {
            if ($day == '01') {
                return 'Al ' . strtolower('primer día del mes de ' . $month . ' de ' . $year);
            } else {
                return 'A ' . strtolower('los ' . $day . ' días del mes de ' . $month . ' de ' . $year);
            }
        }
    }

    public function unidad($numero)
    {
        switch ($numero) {
            case 9: {
                    $numu = "NUEVE";
                    break;
                }
            case 8: {
                    $numu = "OCHO";
                    break;
                }
            case 7: {
                    $numu = "SIETE";
                    break;
                }
            case 6: {
                    $numu = "SEIS";
                    break;
                }
            case 5: {
                    $numu = "CINCO";
                    break;
                }
            case 4: {
                    $numu = "CUATRO";
                    break;
                }
            case 3: {
                    $numu = "TRES";
                    break;
                }
            case 2: {
                    $numu = "DOS";
                    break;
                }
            case 1: {
                    $numu = "UNO";
                    break;
                }
            case 0: {
                    $numu = "";
                    break;
                }
        }

        return $numu;
    }

    public function decena($numero)
    {
        if ($numero >= 90 && $numero <= 99) {
            $numd = "NOVENTA ";
            if ($numero > 90) $numd = $numd . "Y " . ($this->unidad($numero - 90));
        } else if ($numero >= 80 && $numero <= 89) {
            $numd = "OCHENTA ";
            if ($numero > 80) $numd = $numd . "Y " . ($this->unidad($numero - 80));
        } else if ($numero >= 70 && $numero <= 79) {
            $numd = "SETENTA ";
            if ($numero > 70)
                $numd = $numd . "Y " . ($this->unidad($numero - 70));
        } else if ($numero >= 60 && $numero <= 69) {
            $numd = "SESENTA ";
            if ($numero > 60) $numd = $numd . "Y " . ($this->unidad($numero - 60));
        } else if ($numero >= 50 && $numero <= 59) {
            $numd = "CINCUENTA ";
            if ($numero > 50) $numd = $numd . "Y " . ($this->unidad($numero - 50));
        } else if ($numero >= 40 && $numero <= 49) {
            $numd = "CUARENTA ";
            if ($numero > 40) $numd = $numd . "Y " . ($this->unidad($numero - 40));
        } else if ($numero >= 30 && $numero <= 39) {
            $numd = "TREINTA ";
            if ($numero > 30) $numd = $numd . "Y " . ($this->unidad($numero - 30));
        } else if ($numero >= 20 && $numero <= 29) {
            if ($numero == 20) $numd = "VEINTE ";
            else $numd = "VEINTI" . ($this->unidad($numero - 20));
        } else if ($numero >= 10 && $numero <= 19) {
            switch ($numero) {
                case 10: {
                        $numd = "DIEZ ";
                        break;
                    }
                case 11: {
                        $numd = "ONCE ";
                        break;
                    }
                case 12: {
                        $numd = "DOCE ";
                        break;
                    }
                case 13: {
                        $numd = "TRECE ";
                        break;
                    }
                case 14: {
                        $numd = "CATORCE ";
                        break;
                    }
                case 15: {
                        $numd = "QUINCE ";
                        break;
                    }
                case 16: {
                        $numd = "DIECISEIS ";
                        break;
                    }
                case 17: {
                        $numd = "DIECISIETE ";
                        break;
                    }
                case 18: {
                        $numd = "DIECIOCHO ";
                        break;
                    }
                case 19: {
                        $numd = "DIECINUEVE ";
                        break;
                    }
            }
        } else
            $numd = $this->unidad($numero);
        return $numd;
    }

    public function centena($numc)
    {
        if ($numc >= 100) {
            if ($numc >= 900 && $numc <= 999) {
                $numce = "NOVECIENTOS ";
                if ($numc > 900) $numce = $numce . ($this->decena($numc - 900));
            } else if ($numc >= 800 && $numc <= 899) {
                $numce = "OCHOCIENTOS ";
                if ($numc > 800) $numce = $numce . ($this->decena($numc - 800));
            } else if ($numc >= 700 && $numc <= 799) {
                $numce = "SETECIENTOS ";
                if ($numc > 700) $numce = $numce . ($this->decena($numc - 700));
            } else if ($numc >= 600 && $numc <= 699) {
                $numce = "SEISCIENTOS ";
                if ($numc > 600) $numce = $numce . ($this->decena($numc - 600));
            } else if ($numc >= 500 && $numc <= 599) {
                $numce = "QUINIENTOS ";
                if ($numc > 500) $numce = $numce . ($this->decena($numc - 500));
            } else if ($numc >= 400 && $numc <= 499) {
                $numce = "CUATROCIENTOS ";
                if ($numc > 400) $numce = $numce . ($this->decena($numc - 400));
            } else if ($numc >= 300 && $numc <= 399) {
                $numce = "TRESCIENTOS ";
                if ($numc > 300) $numce = $numce . ($this->decena($numc - 300));
            } else if ($numc >= 200 && $numc <= 299) {
                $numce = "DOSCIENTOS ";
                if ($numc > 200) $numce = $numce . ($this->decena($numc - 200));
            } else if ($numc >= 100 && $numc <= 199) {
                if ($numc == 100) $numce = "CIEN ";
                else $numce = "CIENTO " . ($this->decena($numc - 100));
            }
        } else $numce = $this->decena($numc);

        return $numce;
    }

    public function miles($numero)
    {
        if ($numero >= 1000 && $numero < 2000) {
            $numm = "MIL " . ($this->centena($numero % 1000));
        }
        if ($numero >= 2000 && $numero < 10000) {
            $numm = $this->unidad(Floor($numero / 1000)) . " MIL " . ($this->centena($numero % 1000));
        }
        if ($numero < 1000) $numm = $this->centena($numero);

        return $numm;
    }

    public function generateCorrelative($date_admission, $business_id)
    {
        $ddate = Carbon::parse($date_admission)->format('d');
        $mdate = Carbon::parse($date_admission)->format('m');
        $ydate = Carbon::parse($date_admission)->format('Y');
        $last_correlative = Employees::select(DB::raw('MAX(id) as max'))
            ->where('business_id', $business_id)
            ->where('date_admission', $date_admission)
            ->first();
        if ($last_correlative->max != null) {
            $correlative = $last_correlative->max + 1;
        } else {
            $correlative = 1;
        }

        $generateCorrelative = 'E'.str_pad($ddate, 2, '0', STR_PAD_LEFT).str_pad($mdate, 2, '0', STR_PAD_LEFT).$ydate.str_pad($correlative, 3, '0', STR_PAD_LEFT);
        return $generateCorrelative;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MainController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

    public function gerarExercicios(Request $request)
    {
        $request->validate([
            'check_sum' => 'required_without_all:check_subtraction,check_multiplication,check_division',
            'check_subtraction' => 'required_without_all:check_sum,check_multiplication,check_division',
            'check_multiplication' => 'required_without_all:check_sum,check_subtraction,check_division',
            'check_division' => 'required_without_all:check_sum,check_subtraction,check_division, check_multiplication',
            'number_one' => 'required|integer|min:0|max:999|lt:number_two',
            'number_two' => 'required|integer|min:0|max:999',
            'number_exercises' => 'required|integer|min:5|max:50',

        ]);

        $operacoes = [];
        $operacoes[] = $request->check_sum ? 'sum': '';
        $operacoes[] = $request->check_subtraction ? 'subtraction': '';
        $operacoes[] = $request->check_multiplication ? 'multiplication': '';
        $operacoes[] = $request->check_division ? 'division': '';

        $min = $request->number_one;
        $max = $request->number_two;

        $numeroExerc = $request->number_exercises;

        $exercicios = [];

        for($i = 1; $i <= $numeroExerc; $i++) {
            $operacao = $operacoes[array_rand($operacoes)];
            $num1 = rand($min, $max);
            $num2 = rand($min, $max);

            $exercicio = '';
            $solucao = '';

            switch ($operacao) {
                case 'sum':
                    $exercicio = "$num1 + $num2 =";
                    $solucao = $num1 + $num2;
                    break;
                case 'subtraction':
                    $exercicio = "$num1 - $num2 =";
                    $solucao = $num1 - $num2;
                    break;
                case 'multiplication':
                    $exercicio = "$num1 x $num2 =";
                    $solucao = $num1 * $num2;
                    break;
                case 'division':
                    $exercicio = "$num1 / $num2 =";
                    $solucao = $num1 / $num2;
                    break;
            }

            $exercicios[] = [
                'numero_exercicio' => $i,
                'exercicio' => $exercicio,
                'solucao' => "$exercicio $solucao",
            ];

        }



        dd($exercicios);// se passou da regra, executa codigo dd
    }

    public function imprimeExercicios()
    {
        echo "Imprimir exercicios no navegador";
    }

    public function exportarExercicios()
    {
        echo "Exporta exercicios para download";
    }
}

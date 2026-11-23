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

    public function gerarExercicios(Request $request): View
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
        if ($request->check_sum) $operacoes[] = 'sum';
        if ($request->check_subtraction) $operacoes[] = 'subtraction';
        if ($request->check_multiplication) $operacoes[] = 'multiplication';
        if ($request->check_division) $operacoes[] = 'division';

        $min = $request->number_one;
        $max = $request->number_two;

        $numeroExerc = $request->number_exercises;

        $exercicios = [];

        for ($i = 1; $i <= $numeroExerc; $i++) {
            $exercicios[] = $this->gerarMatematica($i, $operacoes, $min, $max);
        }

        // colocar exercicios na sessão
        // $request->session()->put('exercicios', $exercicios);
        // ou
        session(['exercicios', $exercicios]);

        return view('operacoes', compact('exercicios'));
    }

    public function imprimeExercicios()
    {
        // verificar exercicios na sessao
        if (!session()->has('exercicios')) {
            return redirect()->route('home');
        }

        $exercicios = session('exercicios');

        echo '<pre>';
        echo '<h1>Exercicios de matematica (' . env('APP_NAME') . ')</h1>';
        echo '<hr>';
        foreach ($exercicios as $e) {
            echo "<h2><small>" . $e['numero_exercicio'] . "</small> >> " . $e['exercicio'] . "</h2>";
        }
        echo '<hr>';
        echo '<small>Soluções</small><br>';
        foreach ($exercicios as $e) {
            echo "<small>" . $e['numero_exercicio'] . " >> " . $e['solucao'] . "</small><br>";
        }
    }

    public function exportarExercicios()
    {
        if (!session()->has('exercicios')) {
            return redirect()->route('home');
        }

        $exercicios = session('exercicios');

        // criar arquivo de exportação
        $nomeArquivo = 'exercicios_' . env('APP_NAME') . '_' . date('YmdHis') . '.txt';

        $conteudo = '';
        $conteudo .= "Exercicios de Matematica " . env('APP_NAME') . "\n";
        foreach ($exercicios as $exercicio) {
            $conteudo .= $exercicio['numero_exercicio'] . '>>' . $exercicio['exercicio'] . "\n";
        }
        $conteudo .= "\n";
        $conteudo .= "Soluções\n" . str_repeat('-', 20) . "\n";
        foreach ($exercicios as $exercicio) {
            $conteudo .= $exercicio['numero_exercicio'] . '>>' . $exercicio['solucao'] . "\n";
        }

        return response($conteudo)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename= "'. $nomeArquivo . '"');


    }

    private function gerarMatematica($i, $operacoes, $min, $max): array
    {
        $operacao = $operacoes[array_rand($operacoes)];
        $num1 = rand($min, $max);
        $num2 = rand($min, $max);

        if ($num2 == 0) {
            $num2 = 1;
        }

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
                $exercicio = "$num1 : $num2 =";
                $solucao = $num1 / $num2;
                break;
        }

        if (is_float($solucao)) {
            $solucao = round($solucao, 2);
        }

        return [
            'operacao' => $operacao,
            'numero_exercicio' => str_pad($i, 2, '0', STR_PAD_LEFT),
            'exercicio' => $exercicio,
            'solucao' => "$exercicio $solucao",
        ];
    }
}

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputValue = $_POST['inputValue'];
    $inputId = $_POST['inputId'];

    $expectedValues = [
        'input2' => ['expected' => 'CZTERY', 'h1Text' => 'CZTERY', 'h3Text' => ''],
        'input3' => ['expected' => 'DWA', 'h1Text' => 'DWA', 'h3Text' => ''],
        'input4' => ['expected' => 'TRZY', 'h1Text' => 'TRZY', 'h3Text' => 'Gdyby cyfrę w A2 pomnożyć przez ją samą wynik wynosiłby CZTERY.']
    ];

    if (array_key_exists($inputId, $expectedValues)) {
        $expected = $expectedValues[$inputId]['expected'];
        $h1Text = $expectedValues[$inputId]['h1Text'];
        $h3Text = $expectedValues[$inputId]['h3Text'];

        if ($inputValue === $expected) {
            echo json_encode([
                'result' => true,
                'h1Text' => $h1Text,
                'h3Text' => $h3Text,
            ]);
            exit;
        }
    }

    echo json_encode(['result' => false]);
    exit;
}

echo json_encode(['result' => false, 'error' => 'Nieprawidłowe żądanie']);

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputValue = $_POST['inputValue'];
    $inputId = $_POST['inputId'];

    $expectedValues = [
        'input1' => ['expected' => 'TOWN OF US', 'h1Text' => 'TOWN OF US', 'h3Text' => ''],
        'input2' => ['expected' => 'EMERGENCY MEETING', 'h1Text' => 'EMERGENCY MEETING', 'h3Text' => ''],
        'input3' => ['expected' => 'MIRA HQ', 'h1Text' => 'MIRA HQ', 'h3Text' => ''],
        'input4' => ['expected' => 'EJECTION', 'h1Text' => 'EJECTION', 'h3Text' => 'COSMICUBE jest pod AMONG US VR, ale żadna z tych komórek nie jest w rogu.'],
        'input5' => ['expected' => 'DOORLOG', 'h1Text' => 'DOORLOG', 'h3Text' => 'Każde z THE SKELD, FREEPLAY i TOWN OF US zdecydowanie dzieli kolumnę lub rząd z tą komórką.'],
        'input7' => ['expected' => 'THE SKELD', 'h1Text' => 'THE SKELD', 'h3Text' => ''],
        'input8' => ['expected' => 'AMONG US VR', 'h1Text' => 'AMONG US VR', 'h3Text' => 'TASK BAR sąsiaduje z THE SKELD i POLUS.'],
        'input9' => ['expected' => 'FREEPLAY', 'h1Text' => 'FREEPLAY', 'h3Text' => ''],
        'input10' => ['expected' => 'TASK BAR', 'h1Text' => 'TASK BAR', 'h3Text' => 'EMERGENCY MEETING i MIRA HQ są w tym samym rzędzie. Gdzie ukrył się DOORLOG?'],
        'input11' => ['expected' => 'POLUS', 'h1Text' => 'POLUS', 'h3Text' => ''],
        'input12' => ['expected' => 'COSMICUBE', 'h1Text' => 'COSMICUBE', 'h3Text' => 'TOWN OF US i THE OTHER ROLES znajdują się na dwóch końcach najdłuższej przekątnej.'],
        'input13' => ['expected' => 'VOTING TIME', 'h1Text' => 'VOTING TIME', 'h3Text' => 'EJECTION jest gdzieś po przekątnej.'],
        'input14' => ['expected' => 'DOUBLE KILL', 'h1Text' => 'DOUBLE KILL', 'h3Text' => ''],
        'input15' => ['expected' => 'THE FUNGLE', 'h1Text' => 'THE FUNGLE', 'h3Text' => 'VOTING TIME ma tylko trzech sąsiadów: DOUBLE KILL, FREEPLAY i TASK BAR. Ostatni z nich na pewno nie znajduje się w tym rzędzie.'],
        'input16' => ['expected' => 'THE OTHER ROLES', 'h1Text' => 'THE OTHER ROLES', 'h3Text' => '']
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

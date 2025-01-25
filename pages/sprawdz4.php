<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputValue = $_POST['inputValue'];
    $inputId = $_POST['inputId'];


    $expectedValues = [
        'input1' => ['expected' => 'BLACK', 'h1Text' => 'BLACK', 'h3Text' => 'W tym rzędzie pozostały jeszcze tylko: PLUM, PINK i YELLOW.'],
        'input2' => ['expected' => 'PLUM', 'h1Text' => 'PLUM', 'h3Text' => ''],
        'input3' => ['expected' => 'PINK', 'h1Text' => 'PINK', 'h3Text' => 'TURQUOISE i CYAN szukaj po przekątnej.'],
        'input4' => ['expected' => 'OLIVE', 'h1Text' => 'OLIVE', 'h3Text' => 'BANANA jest w prawym dolnym rogu tej planszy.'],
        'input5' => ['expected' => 'YELLOW', 'h1Text' => 'YELLOW', 'h3Text' => 'ROSE i GREEN kryją się w kolumnie E. ORANGE jest gdzieś w lewej części planszy.'],
        'input6' => ['expected' => 'MINT', 'h1Text' => 'MINT', 'h3Text' => ''],
        'input7' => ['expected' => 'MAROON', 'h1Text' => 'MAROON', 'h3Text' => 'BLUE, BEIGE i PLUM są w kolumnie B.'],
        'input8' => ['expected' => 'PURPLE', 'h1Text' => 'PURPLE', 'h3Text' => 'MAROON przylega do tej komórki z lewej strony.'],
        'input9' => ['expected' => 'TURQUOISE', 'h1Text' => 'TURQUOISE', 'h3Text' => ''],
        'input10' => ['expected' => 'ROSE', 'h1Text' => 'ROSE', 'h3Text' => ''],
        'input11' => ['expected' => 'CORAL', 'h1Text' => 'CORAL', 'h3Text' => ''],
        'input13' => ['expected' => 'LIME', 'h1Text' => 'LIME', 'h3Text' => 'YELLOW, BEIGE i WATERMELON znajdują się po przekątnych.'],
        'input14' => ['expected' => 'RED', 'h1Text' => 'RED', 'h3Text' => ''],
        'input15' => ['expected' => 'CYAN', 'h1Text' => 'CYAN', 'h3Text' => ''],
        'input16' => ['expected' => 'ORANGE', 'h1Text' => 'ORANGE', 'h3Text' => ''],
        'input17' => ['expected' => 'BEIGE', 'h1Text' => 'BEIGE', 'h3Text' => 'BROWN, GREEN i ORANGE są w tym rzędzie.'],
        'input18' => ['expected' => 'TAN', 'h1Text' => 'TAN', 'h3Text' => 'LIME mieści się pomiędzy PURPLE a TAN w kolumnie C.'],
        'input19' => ['expected' => 'BROWN', 'h1Text' => 'BROWN', 'h3Text' => 'WHITE znajduje się pomiędzy C5 a E5.'],
        'input20' => ['expected' => 'GREEN', 'h1Text' => 'GREEN', 'h3Text' => ''],
        'input21' => ['expected' => 'WATERMELON', 'h1Text' => 'WATERMELON', 'h3Text' => ''],
        'input22' => ['expected' => 'BLUE', 'h1Text' => 'BLUE', 'h3Text' => 'MINT znajdziesz pomiędzy BLACK a CORAL.'],
        'input23' => ['expected' => 'GRAY', 'h1Text' => 'GRAY', 'h3Text' => ''],
        'input24' => ['expected' => 'WHITE', 'h1Text' => 'WHITE', 'h3Text' => 'RED jest gdzieś w trzecim rzędzie i obok CYAN.'],
        'input25' => ['expected' => 'BANANA', 'h1Text' => 'BANANA', 'h3Text' => 'W C4 jest TAN, a w A1 - BLACK.']
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
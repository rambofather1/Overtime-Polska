<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputValue = $_POST['inputValue'];
    $inputId = $_POST['inputId'];

    $expectedValues = [
        'input1' => ['expected' => 'SKIN', 'h1Text' => 'SKIN', 'h3Text' => 'Producent gry Among Us znajduje się w tym samym rzędzie co HAT.'],
        'input2' => ['expected' => 'KILL', 'h1Text' => 'KILL', 'h3Text' => 'IMPOSTOR znajduje się gdzieś po przekątnej od tej komórki.'],
        'input3' => ['expected' => 'BEANS', 'h1Text' => 'BEANS', 'h3Text' => 'SKIN jest w tym rzędzie.'],
        'input4' => ['expected' => 'VOTING TIME', 'h1Text' => 'VOTING TIME', 'h3Text' => ''],
        'input5' => ['expected' => 'PET', 'h1Text' => 'PET', 'h3Text' => 'Pomiędzy PET a GHOST znajduje się urządzenie, na którym można sprawdzić czy ktoś umarł.'],
        'input6' => ['expected' => 'HAT', 'h1Text' => 'HAT', 'h3Text' => 'Umiejętności, których używać mogą tylko Impostorzy znajdują się w kolumnie B.'],
        'input7' => ['expected' => 'VANISH', 'h1Text' => 'VANISH', 'h3Text' => 'SKIN i GUARDIAN ANGEL są gdzies w kolumnie A. Trzeci rząd zawiera tylko nazwy ról z gry.'],
        'input8' => ['expected' => 'SKIP', 'h1Text' => 'SKIP', 'h3Text' => 'VISION i TASK są gdzieś w kolumnie E.'],
        'input9' => ['expected' => 'INNERSLOTH', 'h1Text' => 'INNERSLOTH', 'h3Text' => 'SABOTAGE oraz PROTECT znajdują się w czwartym rzędzie. PROTECT nie ma w kolumnie A.'],
        'input10' => ['expected' => 'VITALS', 'h1Text' => 'VITALS', 'h3Text' => 'REPORT i SHIFT są w kolumnie A i B. BEANS nie znajduje się w żadnej z nich.'],
        'input11' => ['expected' => 'GUARDIAN ANGEL', 'h1Text' => 'GUARDIAN ANGEL', 'h3Text' => 'C1 i E3 zawierają GHOST oraz walutę wykorzystywaną w grze.'],
        'input12' => ['expected' => 'SCIENTIST', 'h1Text' => 'SCIENTIST', 'h3Text' => ''],
        'input13' => ['expected' => 'CREWMATE', 'h1Text' => 'CREWMATE', 'h3Text' => 'USE i TASK znajdują się po przekątnej od tej komórki.'],
        'input14' => ['expected' => 'IMPOSTOR', 'h1Text' => 'IMPOSTOR', 'h3Text' => 'SCIENTIST i PET są w B3 oraz E1.'],
        'input15' => ['expected' => 'GHOST', 'h1Text' => 'GHOST', 'h3Text' => 'Nakrycie głowy znajdziesz pomiędzy SKIN a GUARDIAN ANGEL.'],
        'input16' => ['expected' => 'HAUNT', 'h1Text' => 'HAUNT', 'h3Text' => ''],
        'input17' => ['expected' => 'SABOTAGE', 'h1Text' => 'SABOTAGE', 'h3Text' => 'Umiejętności używane przez rolę duchów mieszczą się po lewej i prawej stronie tej komórki. HAUNT jest w komórce zewnętrznej.'],
        'input18' => ['expected' => 'PROTECT', 'h1Text' => 'PROTECT', 'h3Text' => 'KILL i VOTING TIME przylegają do BEANS z lewej i prawej strony.'],
        'input19' => ['expected' => 'USE', 'h1Text' => 'USE', 'h3Text' => ''],
        'input20' => ['expected' => 'VISION', 'h1Text' => 'VISION', 'h3Text' => ''],
        'input21' => ['expected' => 'REPORT', 'h1Text' => 'REPORT', 'h3Text' => 'W C3 znajdziesz CREWMATE.'],
        'input22' => ['expected' => 'SHIFT', 'h1Text' => 'SHIFT', 'h3Text' => 'W kolumnie C znajduję się SKIP.'],
        'input23' => ['expected' => 'ADMIN', 'h1Text' => 'ADMIN', 'h3Text' => 'Upper Engine jest w D2. Wszystkie pomieszczenia z dwuczłonową nazwą znajdują się w drugim rzędznie'],
        'input24' => ['expected' => 'CHAT', 'h1Text' => 'CHAT', 'h3Text' => ''],
        'input25' => ['expected' => 'TASK', 'h1Text' => 'TASK', 'h3Text' => 'CHAT znajduje się gdzieś obok tej komórki.']
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

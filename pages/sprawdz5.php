<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputValue = $_POST['inputValue'];
    $inputId = $_POST['inputId'];


    $expectedValues = [
        'input1' => ['expected' => 'VETERAN', 'h1Text' => 'VETERAN', 'h3Text' => ''],
        'input2' => ['expected' => 'VIGILANTE', 'h1Text' => 'VIGILANTE', 'h3Text' => 'VENERER chowa się w lewym dolnym rogu.'],
        'input3' => ['expected' => 'SHERIFF', 'h1Text' => 'SHERIFF', 'h3Text' => ''],
        'input4' => ['expected' => 'DETECTIVE', 'h1Text' => 'DETECTIVE', 'h3Text' => 'W trzecim rzędzie chowa się Impostor, który sprząta ciała.'],
        'input5' => ['expected' => 'ARSONIST', 'h1Text' => 'ARSONIST', 'h3Text' => 'W B2 jest Crewmate, kóry może dać komuś tarczę. Na planszy znajdują się na pewno VETERAN, MINER i MORPHLING - z czego MORPHLING jest najbliżej tej komórki.'], 
        'input6' => ['expected' => 'MINER', 'h1Text' => 'MINER', 'h3Text' => ''],
        'input7' => ['expected' => 'MEDIC', 'h1Text' => 'MEDIC', 'h3Text' => ''],
        'input8' => ['expected' => 'WEREWOLF', 'h1Text' => 'WEREWOLF', 'h3Text' => 'BOMBER znajduje się na samym dole tej kolumny.'],
        'input9' => ['expected' => 'MORPHLING', 'h1Text' => 'MORPHLING', 'h3Text' => ''],
        'input10' => ['expected' => 'BLACKMAILER', 'h1Text' => 'BLACKMAILER', 'h3Text' => 'TRACKER i THE GLITCH są w tej samej przekątnej względem tej komórki. MEDIUM jest komórką zewnętrzną.'],
        'input11' => ['expected' => 'ENGINEER', 'h1Text' => 'ENGINEER', 'h3Text' => 'ARSONIST nie jest w drugim rzędzie.'],
        'input12' => ['expected' => 'JANITOR', 'h1Text' => 'JANITOR', 'h3Text' => ''],
        'input13' => ['expected' => 'SWOOPER', 'h1Text' => 'SWOOPER', 'h3Text' => ''],
        'input15' => ['expected' => 'MAYOR', 'h1Text' => 'MAYOR', 'h3Text' => 'INVESTIGATOR jest w rzędzie czwartym. SWOOPER chowa się gdzieś w kolumnie C, ale zdecydowanie nie w czwartym rzędzie.'],
        'input16' => ['expected' => 'MEDIUM', 'h1Text' => 'MEDIUM', 'h3Text' => 'Crewmate, który może korzystać z ventów znajduje się w komórce wyżej.'],
        'input17' => ['expected' => 'INVESTIGATOR', 'h1Text' => 'INVESTIGATOR', 'h3Text' => ''],
        'input18' => ['expected' => 'THE GLITCH', 'h1Text' => 'THE GLITCH', 'h3Text' => ''],
        'input19' => ['expected' => 'SPY', 'h1Text' => 'SPY', 'h3Text' => 'SURVIVOR znajduje się po prawej stronie tej komórki. MEDIUM i THE GLITCH leżą gdzieś po lewej stronie w tym rzędzie.'],
        'input20' => ['expected' => 'SURVIVOR', 'h1Text' => 'SURVIVOR', 'h3Text' => 'SEER, WEREWOLF I VIGILANTE znajdziesz w dwóch przekątnych od tej komórki'],
        'input21' => ['expected' => 'VENERER', 'h1Text' => 'VENERER', 'h3Text' => 'Rola, która koniecznie chce zostać wygłosowana znajduje się po przeciwnej stronie tego rzędu.'],
        'input22' => ['expected' => 'TRACKER', 'h1Text' => 'TRACKER', 'h3Text' => 'WEREWOLF graniczy z SHERIFF. ENGINEER i JANITOR są gdzieś na planszy.'],
        'input23' => ['expected' => 'BOMBER', 'h1Text' => 'BOMBER', 'h3Text' => 'DETECTIVE jest w D1. MEDIC jest gdzieś w drugim rzędzie.'],
        'input24' => ['expected' => 'SEER', 'h1Text' => 'SEER', 'h3Text' => ''],
        'input25' => ['expected' => 'JESTER', 'h1Text' => 'JESTER', 'h3Text' => 'MAYOR znajduje się wpomiędzy SURVIVOR a BLACKMAILER.']
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
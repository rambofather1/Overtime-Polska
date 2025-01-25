<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputValue = $_POST['inputValue'];
    $inputId = $_POST['inputId'];


    $expectedValues = [ 
        'input1' => ['expected' => 'ALIGN TELESCOPE', 'h1Text' => 'ALIGN TELESCOPE', 'h3Text' => 'POLISH RUBY i EMPTY GARBAGE znajdują się w kolumnie A.'],
        'input2' => ['expected' => 'FIX SHOWER', 'h1Text' => 'FIX SHOWER', 'h3Text' => ''],
        'input3' => ['expected' => 'REBOOT WIFI', 'h1Text' => 'REBOOT WIFI', 'h3Text' => 'SORT SAMPLES znajduje się pomiędzy SWIPE CARD a SUBMIT SCAN.'],
        'input4' => ['expected' => 'INSPECT SAMPLES', 'h1Text' => 'INSPECT SAMPLES', 'h3Text' => ''],
        'input5' => ['expected' => 'MAKE BURGER', 'h1Text' => 'MAKE BURGER', 'h3Text' => 'ALIGN TELESCOPE jest po drugiej stronie tego rzędu.'],
        'input6' => ['expected' => 'POLISH RUBY', 'h1Text' => 'POLISH RUBY', 'h3Text' => 'REWIND TAPES znajduje się pomiędzy RECORD TEMPERATURE i REPAIR DRILL.'],
        'input7' => ['expected' => 'REPAIR DRILL', 'h1Text' => 'REPAIR DRILL', 'h3Text' => ''],
        'input8' => ['expected' => 'REWIND TAPES', 'h1Text' => 'REWIND TAPES', 'h3Text' => 'W C1 i D1 znajdują się dwuczęściowe taski, przy których robieniu trzeba odczekać całą minutę. Pierwszy z nich znajduję się Communications na Polusie.'],
        'input9' => ['expected' => 'RECORD TEMPERATURE', 'h1Text' => 'RECORD TEMPERATURE', 'h3Text' => ''],
        'input11' => ['expected' => 'UPLOAD DATA', 'h1Text' => 'UPLOAD DATA', 'h3Text' => 'B3, A5 i C4 zawierają nazwy tasków, które mogą być wizualnymi taskami. Szukaj FIX WIRING w D5.'],
        'input12' => ['expected' => 'SUBMIT SCAN', 'h1Text' => 'SUBMIT SCAN', 'h3Text' => 'CHART COURSE mieści się w trzecim rzędzie.'],
        'input13' => ['expected' => 'MEASURE WEATHER', 'h1Text' => 'MEASURE WEATHER', 'h3Text' => 'SUBMIT SCAN i ASSEMBLE ARTIFACT znajdziesz po lewej i prawej stronie tej komórki.'],
        'input14' => ['expected' => 'ASSEMBLE ARTIFACT', 'h1Text' => 'ASSEMBLE ARTIFACT', 'h3Text' => 'Kolumna D zawiera RECORD TEMPERATURE, SCAN BOARDING PASS oraz INSPECT SAMPLES.'],
        'input15' => ['expected' => 'CHART COURSE', 'h1Text' => 'CHART COURSE', 'h3Text' => 'W C5 jest UNLOCK SAFE. PRIME SHIELDS jest gdzieś w czwartym rzędzie.'],
        'input16' => ['expected' => 'DIVERT POWER', 'h1Text' => 'DIVERT POWER', 'h3Text' => 'CLEAN VENT znajdziesz w E5.'],
        'input17' => ['expected' => 'SORT SAMPLES', 'h1Text' => 'SORT SAMPLES', 'h3Text' => 'MONITOR TREE i FIX SHOWER znajdują się w tej samej kolumnie i w tym samym rzędzie co ta komórka. W rzędzie z FIX SHOWER nie ma taska z kartą.'],
        'input18' => ['expected' => 'PRIME SHIELDS', 'h1Text' => 'PRIME SHIELDS', 'h3Text' => 'DIVERT POWER znajduje się dwie komórki od tej, ale nie po prawej stronie'],
        'input19' => ['expected' => 'SCAN BOARDING PASS', 'h1Text' => 'SCAN BOARDING PASS', 'h3Text' => ''],
        'input20' => ['expected' => 'MONITOR TREE', 'h1Text' => 'MONITOR TREE', 'h3Text' => ''],
        'input21' => ['expected' => 'EMPTY GARBAGE', 'h1Text' => 'EMPTY GARBAGE', 'h3Text' => 'MEASURE WEATHER jest na samym środku tej planszy.'],
        'input22' => ['expected' => 'SWIPE CARD', 'h1Text' => 'SWIPE CARD', 'h3Text' => ''],
        'input23' => ['expected' => 'UNLOCK SAFE', 'h1Text' => 'UNLOCK SAFE', 'h3Text' => 'REBOOT WIFI i REWIND TAPES są dzieś w tej kolumnie. DIVERT POWER i PRIME SHIELDS znajdują się w tym samym rzędzie.'],
        'input24' => ['expected' => 'FIX WIRING', 'h1Text' => 'FIX WIRING', 'h3Text' => 'W E1 znajdziesz task związany z jedzeniem.'],
        'input25' => ['expected' => 'CLEAN VENT', 'h1Text' => 'CLEAN VENT', 'h3Text' => 'REPAIR DRILL jest gdzieś po ukosie.']
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
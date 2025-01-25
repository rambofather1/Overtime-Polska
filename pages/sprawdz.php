<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputValue = $_POST['inputValue'];
    $inputId = $_POST['inputId'];

    $expectedValues = [
        'input1' => ['expected' => 'LAUNCHPAD', 'h1Text' => 'LAUNCHPAD', 'h3Text' => 'VAULT, SPECIMEN ROOM i ELECTRICAL są położone gdzieś po przekątnej od tej komórki.'],
        'input2' => ['expected' => 'ARMORY', 'h1Text' => 'ARMORY', 'h3Text' => 'W A2 jest LOWER ENGINE, a w E2 - BOILER ROOM.'],
        'input3' => ['expected' => 'O2', 'h1Text' => 'O2', 'h3Text' => ''],
        'input4' => ['expected' => 'KITCHEN', 'h1Text' => 'KITCHEN', 'h3Text' => 'COCKPIT mieści się w kolumnie D.'],
        'input5' => ['expected' => 'COMMUNICATIONS', 'h1Text' => 'COMMUNICATIONS', 'h3Text' => ''],
        'input6' => ['expected' => 'LOWER ENGINE', 'h1Text' => 'LOWER ENGINE', 'h3Text' => ''],
        'input7' => ['expected' => 'SPECIMEN ROOM', 'h1Text' => 'SPECIMEN ROOM', 'h3Text' => 'LABORATORY jest w A5.'],
        'input8' => ['expected' => 'LOCKER ROOM', 'h1Text' => 'LOCKER ROOM', 'h3Text' => ''],
        'input9' => ['expected' => 'UPPER ENGINE', 'h1Text' => 'UPPER ENGINE', 'h3Text' => 'KITCHEN i VAULT są nad i pod tą komórką.'],
        'input10' => ['expected' => 'BOILER ROOM', 'h1Text' => 'BOILER ROOM', 'h3Text' => ''],
        'input11' => ['expected' => 'HALLWAY', 'h1Text' => 'Hallway', 'h3Text' => 'C5-D5 to pomieszczenia, w których znajdziesz kamery i Admin Panel w kolejności alfabetycznej.'],
        'input12' => ['expected' => 'WEAPONS', 'h1Text' => 'WEAPONS', 'h3Text' => ''],
        'input13' => ['expected' => 'GREENHOUSE', 'h1Text' => 'GREENHOUSE', 'h3Text' => ''],
        'input14' => ['expected' => 'COCKPIT', 'h1Text' => 'COCKPIT', 'h3Text' => 'WEAPONS oraz REACTOR znajdziesz w tym rzędzie.'],
        'input15' => ['expected' => 'REACTOR', 'h1Text' => 'REACTOR', 'h3Text' => 'LOCKER ROOM, O2 i SHIELDS znajdują się w kolumnie C. SHIELDS nie ma w C1.'],
        'input16' => ['expected' => 'DROPSHIP', 'h1Text' => 'DROPSHIP', 'h3Text' => ''],
        'input17' => ['expected' => 'DECONTAMINATION', 'h1Text' => 'DECONTAMINATION', 'h3Text' => 'WEAPONS i CAFETERIA znajdują się powyżej i poniżej tej komórki.'],
        'input18' => ['expected' => 'SHIELDS', 'h1Text' => 'SHIELDS', 'h3Text' => 'W A4 i B1 znajdziesz ARMORY oraz DROPSHIP.'],
        'input19' => ['expected' => 'VAULT', 'h1Text' => 'VAULT', 'h3Text' => ''],
        'input21' => ['expected' => 'LABORATORY', 'h1Text' => 'LABORATORY', 'h3Text' => 'COMMUNICATIONS oraz DECONTAMINATION są położone po przekątnej od tej komórki.'],
        'input22' => ['expected' => 'CAFETERIA', 'h1Text' => 'CAFETERIA', 'h3Text' => ''],
        'input23' => ['expected' => 'ADMIN', 'h1Text' => 'ADMIN', 'h3Text' => 'UPPER ENGINE jest w D2. Wszystkie pomieszczenia z dwuczłonową nazwą znajdują się w drugim rzędzie.'],
        'input24' => ['expected' => 'SECURITY', 'h1Text' => 'SECURITY', 'h3Text' => 'D1, C3 oraz A1 zawierają LAUNCHPAD, KITCHEN i GREENHOUSE. LAUNCHPAD ani KITCHEN nie są w C3.'],
        'input25' => ['expected' => 'ELECTRICAL', 'h1Text' => 'ELECTRICAL', 'h3Text' => 'W A4 i B4 znajdują się pomieszczenia/lokacje zaczynającę się na literę D.']
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

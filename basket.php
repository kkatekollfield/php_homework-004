<?php
declare(strict_types=1);

const OPERATION_EXIT = 0;
const OPERATION_ADD = 1;
const OPERATION_DELETE = 2;
const OPERATION_PRINT = 3;
const OPERATION_EDIT = 4;

$operations = [
    OPERATION_EXIT => OPERATION_EXIT . '. Завершить программу.',
    OPERATION_ADD => OPERATION_ADD . '. Добавить товар в список покупок.',
    OPERATION_DELETE => OPERATION_DELETE . '. Удалить товар из списка покупок.',
    OPERATION_PRINT => OPERATION_PRINT . '. Отобразить список покупок.',
    OPERATION_EDIT => OPERATION_EDIT . '. Редактировать количество товара.',
];

$items = [
    ['name' => 'молоко', 'qty' => 2],
    ['name' => 'хлеб', 'qty' => 1],
];

function clearScreen(): void {
    if (PHP_OS_FAMILY === 'Windows') {
        system('cls');
    } else {
        system('clear');
    }
}

function printItems(array $items): void {
    if (count($items)) {
        echo 'Ваш список покупок: ' . PHP_EOL;
        echo implode("\n", array_map(fn($item) => $item['name'] . ' - ' . $item['qty'], $items)) . PHP_EOL;
    } else {
        echo 'Ваш список покупок пуст.' . PHP_EOL;
    }
}
function printOperations(array $operations): void {
    echo 'Выберите операцию для выполнения: ' . PHP_EOL;
    echo implode(PHP_EOL, $operations) . PHP_EOL . '> ';
}

function getOperation(array $items, array $operations): int {
    
    do {
        clearScreen();
        printItems($items);
        printOperations($operations);
        $operationNumber = trim(fgets(STDIN));

        $isValid = array_key_exists($operationNumber, $operations);

        if (!$isValid) {
            clearScreen();
            echo '!!! Неизвестный номер операции, повторите попытку.' . PHP_EOL;
        }
    } while (!$isValid);
    
    return(int) $operationNumber;
}

function addItem(array &$items): void {
    echo "Введите название товара: \n> ";
    $itemName = trim(fgets(STDIN));

    $existing = array_search($itemName, array_column($items, 'name'), true);
    if ($existing !== false) {
        echo 'Товар уже есть в списке!' . PHP_EOL;
        return;
    }

    echo "Введите количество: \n> ";  
    $itemQty = (int) trim(fgets(STDIN));

    if ($itemQty <= 0) {
        echo 'Количество должно быть больше 0!' . PHP_EOL;
        return;
    }

    $items[] = ['name' => $itemName, 'qty' => $itemQty];    

    echo "Товар \"$itemName\" в количестве $itemQty добавлен в список покупок." . PHP_EOL;      
}

function deleteItem(array &$items): void {
    printItems($items);
    echo 'Введите название товара для удаления из списка:' . PHP_EOL . '> ';
    $itemName = trim(fgets(STDIN));
    $index = array_search($itemName, array_column($items, 'name'), true);

    if ($index !== false) {
        unset($items[$index]);
    } else {
        echo '!!! Товар "' . $itemName . '" не найден в списке покупок.' . PHP_EOL;
    }

    $items = array_values($items);
}

function printBasketWithCount(array $items): void {
    printItems($items);

    if (count($items)) {
        echo 'Всего ' . count($items) . ' позиций. '. PHP_EOL;
    }

    echo 'Нажмите enter для продолжения';
    fgets(STDIN);
}

function editItem(array &$items): void {
    printItems($items);

    echo 'Введите название товара для редактирования:' . PHP_EOL . '> ';
    $itemName = trim(fgets(STDIN));

    $itemIndex = array_search($itemName, array_column($items, 'name'), true);

    if ($itemIndex !== false) {
        echo 'Введите новое название (или Enter, чтобы не менять):' . PHP_EOL . '> ';
        $newName = trim(fgets(STDIN));

        echo 'Введите новое количество (или Enter, чтобы не менять):' . PHP_EOL . '> ';
        $newQty = trim(fgets(STDIN));

        if ($newName !== '') {
            $items[$itemIndex]['name'] = $newName;
        }
        if ($newQty !== '') {
            $items[$itemIndex]['qty'] = (int) $newQty;
        }
    } else {
        echo '!!! Товар "' . $itemName . '" не найден.' . PHP_EOL;
    }
}


do {

    $operationNumber = getOperation($items, $operations);
    echo 'Выбрана операция: ' . $operations[$operationNumber] . PHP_EOL;

    switch ($operationNumber) {
        case OPERATION_ADD:
            addItem($items);
            break;

        case OPERATION_DELETE:
            deleteItem($items);
            break;

        case OPERATION_PRINT:
            printBasketWithCount($items);
            break;

        case OPERATION_EDIT:
            editItem($items);
            break;
    }

    echo "\n ----- \n";
} while ($operationNumber > 0);

echo 'Программа завершена' . PHP_EOL;

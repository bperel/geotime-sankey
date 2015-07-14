<?php
$input = json_decode(file_get_contents('territories.json'));
$territories = $input->results->bindings;

$nodes = array();
$links = array();

$territoryNames = array();
foreach($territories as $territory) {
    $territoryNames[]=$territory->name->value;
    $currentTerritoryIndex = count($territoryNames) - 1;

    $previousTerritories = explode('|', $territory->previous->value);
    foreach($previousTerritories as $previousTerritory) {
        if (!empty($previousTerritory)) {
            $previousTerritoryIndex = array_search($previousTerritory, $territoryNames);
            if ($previousTerritoryIndex === false) {
                $territoryNames[] = $previousTerritory;
                $previousTerritoryIndex = count($territoryNames) - 1;
            }
            $links[] = array('source' => $previousTerritoryIndex, 'target' => $currentTerritoryIndex, 'value' => 1);
        }
    }

    $nextTerritories = explode('|', $territory->next->value);
    foreach($nextTerritories as $nextTerritory) {
        if (!empty($nextTerritory)) {
            $nextTerritoryIndex = array_search($nextTerritory, $territoryNames);
            if ($nextTerritoryIndex === false) {
                $territoryNames[] = $nextTerritory;
                $nextTerritoryIndex = count($territoryNames) - 1;
            }
            $links[] = array('source' => $currentTerritoryIndex, 'target' => $nextTerritoryIndex, 'value' => 1);
        }
    }
}

$nodes = array_map(function($territoryName) {
    return array('name' => $territoryName);
}, $territoryNames);

file_put_contents('territoriesSankey.json', json_encode(array('nodes' => $nodes, 'links' => $links), JSON_PRETTY_PRINT));

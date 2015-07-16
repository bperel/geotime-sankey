<?php
$input = json_decode(file_get_contents('territories.json'));
$territories = $input->results->bindings;

$nodes = array();
$links = array();

$territoryNames = array();
$territoryLinks = array();

foreach($territories as $territory) {
    $territoryName = $territory->name->value;
    $dates = implode(' to ', array($territory->date1->value, $territory->date2->value));
    $territoryNames[$territoryName]=$dates;

    $previousTerritories = explode('|', $territory->previous->value);
    foreach($previousTerritories as $previousTerritory) {
        if (!empty($previousTerritory)) {
            if (!array_key_exists($previousTerritory, $territoryNames)) {
                $territoryNames[$previousTerritory] = 'unknown';
            }
            $territoryLinks[] = array($previousTerritory, $territoryName);
        }
    }

    $nextTerritories = explode('|', $territory->next->value);
    foreach($nextTerritories as $nextTerritory) {
        if (!empty($nextTerritory)) {
            $nextTerritoryIndex = array_key_exists($nextTerritory, $territoryNames);
            if (!array_key_exists($nextTerritory, $territoryNames)) {
                $territoryNames[$nextTerritory] = 'unknown';
            }
            $territoryLinks[] = array($territoryName, $nextTerritory);
        }
    }
}

foreach($territoryNames as $territoryName => $dates) {
    $nodes[] = array('name' => $territoryName, 'dates' => $dates);
}

foreach($territoryLinks as $territoryLink) {
    $source = null;
    $target = null;

    foreach($nodes as $i=>$node) {
        if ($node['name'] === $territoryLink[0]) {
            $source = $i;
        }
        else if ($node['name'] === $territoryLink[1]) {
            $target = $i;
        }
        if (!is_null($source) && !is_null($target)) {
            $links[] = array('source' => $source, 'target' => $target, 'value' => 1);
            break;
        }
    }
}

file_put_contents('territoriesSankey.json', json_encode(array('nodes' => $nodes, 'links' => $links), JSON_PRETTY_PRINT));

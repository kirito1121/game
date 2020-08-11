<?php

return [
    "entityType" => [
        "Snow" => 1, // target
        "Grass" => 2, // target
        "Helipad" => 3, // target
        "Highway" => 4,
        "SpaceshipPad" => 5, // target
        "Deployer" => 21,
        "Redirecter" => 22,
        "SleeperSpawner" => 23,
        "LockerSpawner" => 24,
        "Bollard" => 25, // target
        "EmptyObject" => 39,
        "Block" => 41,
        "Car" => 42,
        "Joker" => 43,
        "Clown" => 44,
        "PoliceCar" => 45,
        "Ambulance" => 46, // target
        "Helicopter" => 47, // target
        "Train" => 48, // target
        "TrafficCone" => 49, // target
        "Wreck" => 50, // target
        "Smog" => 51,
        "Barrier" => 52,
        "RoadRoller" => 53,
        "StreetLight" => 54,
        "Garbage" => 55,
        "TrafficLight" => 56,
        "Tram" => 57, // target
        "GasPump" => 58, // target
        "CarryTruck" => 59, // target
        "PhoneBox" => 60, // target
        "FireTruck" => 61, // target
        "RadarTruck" => 62, // target
        "Fountain" => 63, // target
        "Construction" => 64,
        "Sleeper" => 81,
        "Locker" => 82,
        "Container" => 83,
        "Tunnel" => 84,
        "Spaceship" => 65, // target
        "Virus" => 85,
        "FoldingBarrier" => 65,
    ],

    "entityColor" => [
        "None" => -1,
        "Red" => 1,
        "Green" => 2,
        "Blue" => 3,
        "Yellow" => 4,
        "Purple" => 5,
    ],

    "direction" => [
        "None" => -1,
        "Left" => 1,
        "Up" => 2,
        "Right" => 3,
        "Down" => 4,
    ],
    "levels" => [
        "Lv1" => 1,
        "Lv2" => 2,
        "Lv3" => 3,
    ],
    "bollard" => [
        "On" => 1,
        "Off" => 2,
    ],
    "targetType" => [
        "ClearTheBoard" => -1, // Clear the Board
        "FreeAmbulance" => 0,
        "ClearHelipad" => 1,
        "FreeTrain" => 2,
        "ClearSnow" => 3,
        "RepairWreck" => 4,
        "PlantGrass" => 5,
        "CleanUpGarbage" => 6,
        "FreeTram" => 7,
        "CollectGasPump" => 8,
        "CollectCarryTruck" => 9,
        "CollectPhoneBox" => 10, // Red target 1 hit
        "FreeFireTruck" => 11,
        "FreeRadarTruck" => 12,
        "CollectFountain" => 13,
        "CollectConstruction" => 14,
        "ClearSpaceshipPad" => 15,
    ],
    "levelType" => [
        "Saga" => 0, // Clear the Board
        "Rush" => 1,
        "EventX" => 2,
    ],
    "obsType" => [
        "Highway" => 4,
        "Redirecter" => 22,
        "SleeperSpawner" => 23,
        "LockerSpawner" => 24,
        "EmptyObject" => 39,
        "Block" => 41,
        "Car" => 42,
        "PoliceCar" => 45,
        "Smog" => 51,
        "Barrier" => 52,
        "RoadRoller" => 53,
        "StreetLight" => 54,
        "Garbage" => 55,
        "TrafficLight" => 56,
        "Construction" => 64,
        "Sleeper" => 81,
        "Locker" => 82,
        "Container" => 83,
        "Tunnel" => 84,
        "TrafficCone" => 49,
        "Virus" => 85,
    ],
    "target" => [
        "Snow",
        "Grass",
        "Helipad",
        "SpaceshipPad",
        "Deployer",
        "Bollard",
        "Ambulance",
        "Helicopter",
        "Train",
        "TrafficCone",
        "Wreck",
        "Tram",
        "GasPump",
        "CarryTruck",
        "PhoneBox",
        "FireTruck",
        "RadarTruck",
        "Fountain",
    ],
];

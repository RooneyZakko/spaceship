<?php
session_start();

include_once 'space/Spaceship.php';
include_once 'space/Fighter.php';
include_once 'space/Cargoship.php';
include_once 'space/Fleet.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spaceships</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<p style="font-family: Cambria; font-size: 20px;">
<?php
$numberOfShips = 3;
$fleet = [];

// Maak een vloot van ruimteschepen
for ($i = 0; $i < $numberOfShips; $i++) {
    $randomShip = rand(1, 2); // Kies willekeurig tussen Fighter (1) en Cargoship (2)
    $ammo = rand(10, 100);
    $fuel = rand(10, 100);
    $hitPoints = rand(10, 100);
    
    if ($randomShip === 1) {
        $fleet[$i] = new space\Fighter($fuel, $hitPoints, $ammo);
    } else {
        $cargoSpace = rand(10, 100);
        $fleet[$i] = new space\Cargoship($fuel, $hitPoints, $cargoSpace);
    }
}

// Sla één schip op in de sessie
$fleet[0]->save('ship_1');

// Laad het opgeslagen schip uit de sessie
$savedShip = space\Spaceship::load('ship_1');
if ($savedShip) {
    echo "Loaded ship has " . $savedShip->getAmmo() . " ammo.<br>";
    echo "Loaded ship has " . $savedShip->getFuel() . " fuel.<br>";
    echo "Loaded ship has " . $savedShip->getHitPoints() . " HP.<br><br>";
} else {
    echo "Failed to load ship.<br>";
}

// Maak vloten en voeg schepen toe aan de vloten
$myFleet = new space\Fleet();
$enemyFleet = new space\Fleet();

foreach ($fleet as $ship) {
    $myFleet->addShip($ship);
    // Voeg dezelfde schepen toe aan de vijandelijke vloot voor de demonstratie
    $enemyFleet->addShip($ship);
}

// Loop door de vloot en toon informatie over elk schip
for ($i = 0; $i < $numberOfShips; $i++) {
    $ship = $fleet[$i];
    echo "Ship " . ($i + 1) . " has " . $ship->getAmmo() . " ammo.<br>";
    echo "Ship " . ($i + 1) . " has " . $ship->getFuel() . " fuel left.<br>";
    echo "Ship " . ($i + 1) . " has " . $ship->getHitPoints() . " HP left.<br>";

    if ($ship instanceof space\Fighter) {
        $damage = $ship->shoot();
        echo "Fighter " . ($i + 1) . " shoots and does " . $damage . " damage.<br>";
    } elseif ($ship instanceof space\Cargoship) {
        $cargo = rand(10, 100);
        $ship->loadCargo($cargo);
        echo "Cargoship " . ($i + 1) . " loads " . $cargo . " cargo.<br>";
    }

    $status = $ship->isAlive() ? "Alive" : "Destroyed";
    echo "Ship " . ($i + 1) . " is " . $status . "<br><br>";
}

// Doe nu een gevecht tussen de vloten
$battleResult = $myFleet->battle($enemyFleet);

// Bereken de ranking op basis van totale schade
$totalDamageMyFleet = $myFleet->calculateTotalDamage();
$totalDamageEnemyFleet = $enemyFleet->calculateTotalDamage();

if ($totalDamageMyFleet + $totalDamageEnemyFleet > 0) {
    $ranking = $totalDamageMyFleet / ($totalDamageMyFleet + $totalDamageEnemyFleet) * 100;
} else {
    $ranking = 0; // Voorkom divisie door nul
}

// Stap 8: HTML-output voor gevechtsresultaat en ranking
echo "Battle result: $battleResult<br>";
echo "Ranking: " . round($ranking) . "%<br>";
?>

</p>

<!-- Voeg een canvas-element toe voor de grafiek -->
<canvas id="myChart" style="max-width: 400px; max-height: 400px"></canvas>

<script>
    //  JavaScript voor het maken van de grafiek met Chart.js
    var shipTypes = ["Ship 1", "Ship 2", "Enemy Ship"];
    var shipCounts = [
        <?php echo $totalDamageMyFleet; ?>,
        <?php echo $totalDamageMyFleet; ?>,
        <?php echo $totalDamageEnemyFleet; ?>
    ];

    var ctx = document.getElementById("myChart").getContext("2d");

    var myChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: shipTypes,
            datasets: [
                {
                    label: "Total Damage",
                    data: shipCounts,
                    backgroundColor: ["rgba(0, 0, 255, 0.2)", "rgba(255, 99, 132, 0.2)"],
                    borderColor: ["rgba(0, 0, 255, 1)", "rgba(255, 99, 132, 1)"],
                    borderWidth: 1,
                },
            ],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
</script>

</body>
</html>

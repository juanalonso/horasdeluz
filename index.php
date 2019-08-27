<?php

    require_once('config/config.php');

    date_default_timezone_set('Europe/Madrid');

    $bgMadDark = "#e03854";
    $bgCphDark = "#3262b5";

    $dateStart = DateTime::createFromFormat (DATE_ATOM, '2019-08-27T00:00:00+02:00');
    $dateEnd = new DateTime();
    $dateEnd->setTimezone(new DateTimeZone('Europe/Madrid'));
    //$dateEnd = $dateEnd->modify('+1 day'); 

    $dateInterval = new DateInterval('P1D');
    $datePeriod = new DatePeriod($dateStart, $dateInterval ,$dateEnd);

    $data['mad']['coords'] = array('lat'=> 40.416775,'lon'=>-3.703790);
    $data['cph']['coords'] = array('lat'=> 55.676098,'lon'=>12.568337);

    $sunByDate = array();
    $totalTime = 0;
    $day = 0;

    foreach($datePeriod as $date){

        $currDay = array();
        
        $day++;
        $currDay['day'] = $day;
        
        $currentDate = $date->format('d/m/Y');
        $currDay['date'] = $currentDate;

        $timestamp = $date->getTimestamp();

        foreach ($data as $key=>$city) {

            $sunInfo = date_sun_info($timestamp,$city['coords']['lat'], $city['coords']['lon']);

            $currDay[$key]['sunrise'] = $sunInfo['sunrise'];
            $dft = dateFromTimestamp($sunInfo['sunrise']);
            $currDay[$key]['sunrise_h'] = $dft["human"];
            $currDay[$key]['sunrise_o'] = $dft["offset"];

            $currDay[$key]['sunset'] = $sunInfo['sunset'];
            $dft = dateFromTimestamp($sunInfo['sunset']);
            $currDay[$key]['sunset_h'] = $dft["human"];
            $currDay[$key]['sunset_o'] = $dft["offset"];

            $currDay[$key]['daylight'] = $sunInfo['sunset'] - $sunInfo['sunrise'];
            $currDay[$key]['daylight_h'] = gmdate('G:i', $currDay[$key]['daylight']);
        }

        $currDay['diff'] = $currDay['cph']['daylight'] - $currDay['mad']['daylight'];
        $currDay['diff_h'] = /*($currDay['diff'] > 0 ? '+':'-') .*/ '+'. gmdate('G:i', abs($currDay['diff']));
        $currDay['diff_c'] = ($currDay['diff'] > 0 ? '+':'-') . gmdate('G:i', abs($currDay['diff']));

        $totalTime += $currDay['diff'];
        $currDay['total'] = $totalTime;
        $currDay['total_h'] = /*($currDay['total'] > 0 ? '+':'-') .*/ 
                                              floor(abs($currDay['total']/3600)). ":" .
                                              sprintf('%02d', floor(abs($currDay['total']%3600)/60));

        $sunByDate[] = $currDay;
    }

    //print_r($sunByDate[count($sunByDate)-1]);
    
    $today = $sunByDate[count($sunByDate)-1];

    function dateFromTimestamp($timestamp) {

        $dt = DateTime::createFromFormat('U', $timestamp);
        $dt->setTimezone(new DateTimeZone('Europe/Madrid'));
        return array("human"=>$dt->format('G:i'),
                     "offset" => $dt->format('G')*3600+$dt->format('i')*60+$dt->format('s'));

    }


?>
<!DOCTYPE html>
<html lang="es">

<head>
     <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?=$googleID?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?=$googleID?>');
    </script>   
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Horas de luz</title>
    <link rel="canonical" href="https://neuronasmuertas.com/horasdeluz" />
    <meta name="description" content="¿Cuántas horas de luz llevo perdidas desde el 27/08/2019?" />
    <meta property="og:title" content="Horas de luz" />
    <meta property="og:locale" content="es" />
    <meta property="og:description" content="¿Cuántas horas de luz llevo perdidas desde el 27/08/2019?" />
    <meta property="og:url" content="https://neuronasmuertas.com/horasdeluz/" />
    <meta property="og:site_name" content="Horas de luz" />
    <meta property="og:image" content="https://neuronasmuertas.com/horasdeluz/img/sol_logo.png" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@kokuma" />
    <meta name="twitter:image" content="https://neuronasmuertas.com/horasdeluz/img/sol_logo.png" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <style>
        .bg-mad-dark {
            background-color:<?=$bgMadDark?>!important
        }     
        .bg-cph-dark {
            background-color:<?=$bgCphDark?>!important
        }
        .btn-link {
            color:#fff;
            background-color:<?=$bgCphDark?>;
            border-color:<?=$bgCphDark?>;
            font-size: 1rem;
            vertical-align: baseline;
            padding: 0rem 0.3rem;
        }
        .btn-link:hover {
            color:#fff;
            background-color:#0069d9;
            border-color:#0069d9;
            text-decoration:none;
        } 
        .nobackground {
            background-color:#ffffff;
        }    
        img.mt-1{
            margin-top: .15rem !important;
        }
        .display-5 {
            font-size:3rem;
            font-weight:300;
            line-height:1.2;
        }
        .lead {
            font-size: 1rem;
            font-weight: 300;
        }   
    </style>
</head>

<body>
    <div class="container">
        <div class="jumbotron pt-1 pb-1">
            <div class="row">
                <div class="col-6"><h4>Horas de luz</h3></div>
                <div class="col-6"> <h4 class="float-right"><?= $today["date"] ?></h3></div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md">
                <div class="bg-cph-dark text-white clearfix pt-3 pb-2 pr-4">
                    <h3 class="float-left pl-4">Copenhague</h3>
                    <?php 
                    if ($today["diff"]>0) {
                    ?>
                    <img src="img/sol.png" class="float-right" width="35rem">
                    <h3 class="float-right pr-2"><?= $today["diff_h"] ?></h3>
                    <?php
                    }
                    ?>
                </div>
                <div class="row mt-3">
                    <div class="col-4">
                        <h6 class="card-subtitle mb-1">Horas de luz</h6>
                        <h3 class="card-subtitle mb-3"><?= $today["cph"]["daylight_h"] ?></h3>
                    </div>
                    <div class="col-4">
                        <h6 class="card-subtitle mb-1">Salida del sol</h6>
                        <h3 class="card-subtitle mb-3"><?= $today["cph"]["sunrise_h"] ?></h3>
                    </div>
                    <div class="col-4">
                        <h6 class="card-subtitle mb-1">Puesta del sol</h6>
                        <h3 class="card-subtitle"><?= $today["cph"]["sunset_h"] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <div class="bg-mad-dark text-white clearfix pt-3 pb-2 pr-4">
                    <h3 class="float-left pl-4">Madrid</h3>
                    <?php 
                    if ($today["diff"]<=0) {
                    ?>
                    <img src="img/sol.png" class="float-right" width="35rem">
                    <h3 class="float-right pr-2"><?= $today["diff_h"] ?></h3>
                    <?php
                    }
                    ?>                    
                </div>
                <div class="row mt-3">
                    <div class="col-4">
                        <h6 class="card-subtitle mb-1">Horas de luz</h6>
                        <h3 class="card-subtitle mb-3"><?= $today["mad"]["daylight_h"] ?></h3>
                    </div>
                    <div class="col-4">
                        <h6 class="card-subtitle mb-1">Salida del sol</h6>
                        <h3 class="card-subtitle mb-3"><?= $today["mad"]["sunrise_h"] ?></h3>
                    </div>
                    <div class="col-4">
                        <h6 class="card-subtitle mb-1">Puesta del sol</h6>
                        <h3 class="card-subtitle"><?= $today["mad"]["sunset_h"] ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5 mb-5">
            <div class="col">
            <h1 class="display-5">En <?= $today["day"] ?> día<?=($today["day"]!=1)?"s":""?> he <?= $today["total"]<0 ? "perdido" : "ganado"?> <?= $today["total_h"] ?> horas de luz</h1>
            </div>
        </div>
        <div class="row">
            <div class="col mt-4">
                <canvas id="myChart"></canvas>
            </div>
        </div>
        <div class="jumbotron mt-3 pt-3 pb-1">
            <p class="lead mb-0">Esta página muestra la diferencia de horas de luz -día a día y acumulada desde el 27/08/19- entre Madrid y Copenhague.</p>
            <p class="lead">Hecha por <a class="btn btn-link" href="https://twitter.com/kokuma">@kokuma</a>. Gracias a <a class="btn btn-link" href="https://twitter.com/daniseuba">@daniseuba</a> por ayudarme a organizar el contenido. Código disponible en <a class="btn btn-link" href="https://github.com/juanalonso/horasdeluz">GitHub</a>.</p>
        </div>
    </div>

    <script src="js/Chart.min.js"></script>
    <script>
        var chartHours = [
            <?php 
                for ($f=0; $f <$day ; $f++) { 
                    echo '"' . $sunByDate[$f]["diff_c"] . '",';
                }
            ?>            
        ];
        var ctx = document.getElementById('myChart').getContext('2d');
        var chart = new Chart(ctx, {

            type: 'line',

            data: {
                labels: [
                    <?php 
                        for ($f=0; $f <$day ; $f++) { 
                            echo "'',";
                        }
                    ?>
                ],
                datasets: [

                {
                    label: 'Luz diurna en Copenhague',
                    fill: '+1', 
                    borderColor: '<?=$bgCphDark?>',
                    backgroundColor: 'rgba(50, 98, 181, 0.3)',
                    data: [
                        <?php 
                            for ($f=0; $f <$day ; $f++) { 
                                echo $sunByDate[$f]["cph"]["sunrise_o"] . ",";
                            }
                        ?>
                ]
                },
                {
                    label: 'Atardecer CPH',
                    fill: false, 
                    borderColor: '<?=$bgCphDark?>',
                    data: [
                    <?php 
                        for ($f=0; $f <$day ; $f++) { 
                            echo $sunByDate[$f]["cph"]["sunset_o"] . ",";
                        }
                    ?>
                ]
                },
                                {
                    label: 'Luz diurna en Madrid',
                    fill: '+1', 
                    borderColor: '<?=$bgMadDark?>',
                    backgroundColor: 'rgba(224, 56, 84, 0.15)',
                    data: [
                        <?php 
                            for ($f=0; $f <$day ; $f++) { 
                                echo $sunByDate[$f]["mad"]["sunrise_o"] . ",";
                            }
                        ?>
                ]
                },
                {
                    label: 'Atardecer MAD',
                    fill: false, 
                    borderColor: '<?=$bgMadDark?>',
                    data: [
                    <?php 
                        for ($f=0; $f <$day ; $f++) { 
                            echo $sunByDate[$f]["mad"]["sunset_o"] . ",";
                        }
                    ?>
                ]
                },
                {
                    label: 'temp',
                    hidden: true,
                    data: [
                    <?php 
                        for ($f=0; $f <$day ; $f++) { 
                            echo $sunByDate[$f]["diff"] . ",";
                        }
                    ?>
                ]
                },
                ]
            },

            // Configuration options go here
            options: {
                animation: {
                    duration: 0 // general animation time
                },
                hover: {
                    animationDuration: 0 // duration of animations when hovering an item
                },
                responsiveAnimationDuration: 0, // animation duration after a resize
                elements: {
                    point:{
                        radius: 0
                    },
                    line: {
                        tension: 0.1
                    },
                },
                scales : {
                    xAxes : [ {
                        gridLines : {
                            display : false
                        }
                    } ],
                    yAxes: [{
                        ticks: {
                            min: 0, //minimum tick
                            max: 24*60*60, //maximum tick
                            stepSize: 3600*2,
                            callback: function(label, index, labels) {
                                switch (index) {
                                    case 12:
                                        return '0:00';
                                    case 11:
                                        return '2:00';
                                    case 10:
                                        return '4:00';
                                    case 9:
                                        return '6:00';
                                    case 8:
                                        return '8:00';
                                    case 7:
                                        return '10:00';
                                    case 6:
                                        return '12:00';
                                    case 5:
                                        return '14:00';
                                    case 4:
                                        return '16:00';
                                    case 3:
                                        return '18:00';
                                    case 2:
                                        return '20:00';
                                    case 1:
                                        return '22:00';
                                    case 0:
                                        return '24:00';
                                }
                            }
                        },
                    }]
                },
                legend: {
                    onClick: (e) => e.stopPropagation(),
                    labels: {
                        filter: function(legendItem, chartData) {
                            return (legendItem.datasetIndex === 0) || (legendItem.datasetIndex === 2);
                        }
                    }
                },
                tooltips: {
                    enabled: true,
                    bodyFontSize: 18,
                    backgroundColor: '<?=$bgCphDark?>',
                    bodyFontColor: '#fff',
                    xPadding: 12,
                    yPadding: 8,
                    displayColors: false,
                    callbacks: {
                        // use label callback to return the desired label
                        label: function(tooltipItem, data) {
                          return chartHours[tooltipItem['index']];
                        },
                    },
                },
            }
        });
    </script>

</body>

</html>
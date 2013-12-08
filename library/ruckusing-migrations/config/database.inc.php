<?php


//----------------------------
// DATABASE CONFIGURATION
//----------------------------

//php main.php db:migrate ENV=production

$ruckusing_db_config = array(
	
    'development' => array(
        'type'      => 'mysql',
        'host'      => 'localhost',
        'port'      => 3306,
        'database'  => 'examen',
        'user'      => 'examen',
        'password'  => 'examen1'
    ),

	'production'		=> array(
			'type' 			=> 'mysql',
			'host' 			=> 'internal-db.s111665.gridserver.com',
			'port'			=> 3306,
			'database' 	=> 'db111665_examen',
			'user' 			=> 'db111665_examen',
			'password' 	=> "M3hhbTNOIW0z",
			'protected' 	=> 1
	),
	'staging'		=> array(
			'type' 			=> 'mysql',
			'host' 			=> 'internal-db.s111665.gridserver.com',
			'port'			=> 3306,
			'database' 	=> 'db111665_staging',
			'user' 			=> 'db111665_examenm',
			'password' 	=> "ZXhhbWVuMTIz",
			'protected' 	=> 1
	)
	
);


?>
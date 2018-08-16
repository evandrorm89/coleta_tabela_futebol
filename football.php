<?php

	setlocale( LC_ALL, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese' );

	$server = "shareddb-h.hosting.stackcp.net";

	$user = "";

	$senha = "";

	$con = mysqli_connect($server, $user, $senha, $user);

	mysqli_set_charset($con,"utf8");

	if (!$con){
		die ("Falha ao conectar no banco de dados: ".mysqli_connect_error());
	} else {

		$url = @get_headers('https://www.over25tips.com/free-football-betting-tips/#');

		if ($url[0] == 'HTTP/1.1 404 Not Found'){

			echo "Não há conexão com o site";
		}

			else {

				$pagina = file_get_contents('https://www.over25tips.com/free-football-betting-tips/#');

				$lista = explode('predictionsTable', $pagina);

				$tabelaHoje = $lista[3];

				$linha = explode('COL-1 hour_start hide-on-small-only" style="">', $tabelaHoje);

				$linhaLength = count($linha);

				$linhaInserida = 0;

				$erroLinha = 0;

				$linhaFaltando = "";

				for($x = 1; $x < $linhaLength; $x++){

					$tempo = strtotime(str_split($linha[$x], 5)[0])-10800;

					$horario = date("H:i", $tempo - 10800);

					$liga = explode('<', explode('span>', $linha[$x])[1])[0];

					$casa = explode('<', explode('COL-3 right-align">', $linha[$x])[1])[0];

					$visitante = explode('<', explode('COL-5 left-align">', $linha[$x])[1])[0];

					$hs = filter_var(explode('<', explode('home-scored tlt', $linha[$x])[1])[0], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					
					$hc = filter_var(explode('<', explode('home-conceded tlt', $linha[$x])[1])[0], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					
					$ass = filter_var(explode('<', explode('away-scored tlt', $linha[$x])[1])[0], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

					$ac = filter_var(explode('<', explode('away-conceded tlt', $linha[$x])[1])[0], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

					$prediction = explode('<', explode('COL-10 hide-on-small-only">', $linha[$x])[1])[0];
					$prediction = str_replace('Under', 'Abaixo de', $prediction);
					$prediction = str_replace('Over', 'Acima de', $prediction);
					$prediction = str_replace('goals', 'gols', $prediction);
					$prediction = str_replace('Home', 'Time da casa', $prediction);
					$prediction = str_replace('Away', 'Time visitante', $prediction);
					$prediction = str_replace('Win', 'ganha', $prediction);
					$prediction = str_replace('and', 'e', $prediction);
					$prediction = str_replace('Btts', 'ambos os times marcam', $prediction);

					$dados = array($horario, $liga, $casa, $visitante, $hs, $hc, $ass, $ac, $prediction);

					//var_dump($dados);

					$sql = "INSERT INTO `football` (`horario`, `liga`, `casa`, `visitante`, `hs`, `hc`, `ass`, `ac`, `prediction`) VALUES('".$horario."', '".mysqli_real_escape_string($con, $liga)."', '".mysqli_real_escape_string($con, $casa)."', '".mysqli_real_escape_string($con, $visitante)."', '".$hs."', '".$hc."', '".$ass."', '".$ac."', '".mysqli_real_escape_string($con, $prediction)."')";

					if (mysqli_query($con, $sql)){
						
						$linhaInserida++;

					} else {
						die(mysqli_error($con));
						$erroLinha++;
						$linhaFaltando.= implode(' ', $dados).'<br>';

					}

				}

				echo "Consulta concluída! Foram inseridas $linhaInserida linhas";


			}
		}


?>
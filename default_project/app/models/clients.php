<?php

class ClientsModel{

	function getAll(){

		$client1 = new stdClass();
		$client1->name = "John Clark";
		$client1->fortune = 12100;

		$client2 = new stdClass();
		$client2->name = "Mike Wong";
		$client2->fortune = 16787.2;

		$client3 = new stdClass();
		$client3->name = "Leonardo Da Vinci";
		$client3->fortune = 768;

		return [$client1, $client2, $client3];


	}

}
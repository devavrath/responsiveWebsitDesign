<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
function notApplicable($node) {
	if ($node)
		return $node;
	else
		return "";
}
function formatValue($num) {
	if ($num) {
		$num = ( int ) $num;
		$num = "$" . number_format ( $num, 2 );
		return $num;
	} else {
		return notApplicable ( $num );
	}
}
test_function();
function test_function() {
	//$return = $_GET;
	//echo "hello";
	$streetAddress = $_GET ['streetInput'];
	$city = $_GET ['cityInput'];
	$state = $_GET ['stateInput'];
	$url = "http://www.zillow.com/webservice/GetDeepSearchResults.htm?zws-id=X1-ZWz1dxyz2kdb0r_7vqv1&address=";
	$url .= urlencode ( $streetAddress ) . "&citystatezip=" . urlencode ( $city ) . "%2C+" . urlencode ( $state ) . "&rentzestimate=true";
	//echo "Success! You entered: " . $url."<br><br>";
	$xml = simplexml_load_file ( $url );
	
	if($xml){
		$codeReturn = $xml->message->code;
		$textReturn = $xml->message->text;
		$limitWarningText = 'limit-warning';
		$limitWarning = $xml->message->$limitWarningText;
	if ($codeReturn == 508) {
		//echo "<div style=\"margin-left: 6%;\"><b>No exact match found--Verify that the given address is correct</b></div>";
		//echo json_encode($return["code"]=($codeReturn));
		$displayError = (object) array('code' => $codeReturn,
		'text' => "Error");
		echo json_encode($displayError);
	
	} else if ($codeReturn == 502) {
		$displayError = (object) array('code' => $codeReturn,
				'text' => "Error");
		echo json_encode($displayError);
		//echo json_encode($return["code"]=($codeReturn));
		//echo "<div style=\"margin-left: 6%;\"><b>No results found--Sorry, the address you provided is not found in Zillow's property database.</b></div>";
	} else if ($codeReturn == 503) {
		$displayError = (object) array('code' => $codeReturn,
				'text' => "Error");
		echo json_encode($displayError);
		//echo json_encode($return["code"]=($codeReturn));
		//echo "<div style=\"margin-left: 6%;\"><b>Failed to resolve city, state or ZIP code--Please check to see if the city/state you entered is valid. If you provided a ZIP code, check to see if it is valid.</b></div>";
	}
	else {
		/*Initialisation of variables */
		$timeAtLA = new DateTimeZone('America/Los_Angeles');
		$resultHeader=$propertyType=$yearBuilt=$lotSizeSqFt=$finishedSqFt=$bathrooms='NA';
		$bedrooms=$taxAssessmentYear=$taxAssessment=$lastSoldPrice=$lastSoldPriceCurrency='NA';
		$lastSoldDate=$zestimateAmount=$lastUpdated='NA';
		$valueChange=$valuationRangeLow=$valuationRangeHigh=$rentzestimateAmount=$rentzestimateLastUpdated='NA';
		$rentzestimateValueChange=$rentzestimateValueChangeLow=$rentzestimateValueChangeHigh='NA';
			
		$resultHeader = $xml->response->results->result->links->homedetails;
		$propertyType = $xml->response->results->result->useCode;
		$yearBuilt = $xml->response->results->result->yearBuilt;
		$lotSizeSqFt = $xml->response->results->result->lotSizeSqFt;
		$finishedSqFt= $xml->response->results->result->finishedSqFt;
		$bathrooms= $xml->response->results->result->bathrooms;
		$bedrooms= $xml->response->results->result->bedrooms;
		$taxAssessmentYear = $xml->response->results->result->taxAssessmentYear;
		$taxAssessmentYear=notApplicable($taxAssessmentYear);
	
		$taxAssessment= $xml->response->results->result->taxAssessment;
		$taxAssessment= formatValue($taxAssessment);
			
		$lastSoldPrice=$xml->response->results->result->lastSoldPrice;
		$lastSoldPrice= formatValue($lastSoldPrice);
			
		if($xml->response->results->result->lastSoldPrice[0])
			$lastSoldPriceCurrency=$xml->response->results->result->lastSoldPrice[0]->attributes();
		else
			$lastSoldPriceCurrency="";
		$lastSoldDate=$xml->response->results->result->lastSoldDate;
		if($lastSoldDate){
			$lastSoldDate=str_replace("/","-", $lastSoldDate);
			$lastSoldDate = date_create_from_format('m-d-Y', $lastSoldDate,$timeAtLA);
			$lastSoldDate=date_format($lastSoldDate, 'd-M-Y');
		}
		else {
			$lastSoldDate=notApplicable($lastSoldDate);
		}
			
		$zestimateAmount=$xml->response->results->result->zestimate->amount;
		$zestimateAmount= formatValue($zestimateAmount);
			
		if($xml->response->results->result->lastSoldPrice[0])
			$zestimateAmountCurrency=$xml->response->results->result->lastSoldPrice[0]->attributes();
		else
			$zestimateAmountCurrency="";
			
		$lastUpdated=$xml->response->results->result->zestimate->{'last-updated'};
		if($lastUpdated){
			$lastUpdated=str_replace("/","-", $lastUpdated);
			$lastUpdated = date_create_from_format('m-d-Y', $lastUpdated,$timeAtLA);
			$lastUpdated=date_format($lastUpdated, 'd-M-Y');
		}
			
		// 			$lastUpdated = new DateTime($lastUpdated);
		// 			$lastUpdated->setTimezone($la_time);
		// 			$lastUpdated=date_format($lastUpdated, 'd-M-Y');
		$valueChange=$xml->response->results->result->zestimate->valueChange;
		// 			$valueChange1=(int)$valueChange;
		// 			$valueChange= formatValue($valueChange);
		// 			echo $valueChange1;
		$valuationRangeLow=$xml->response->results->result->zestimate->valuationRange->low;
		$valuationRangeLow= formatValue($valuationRangeLow);
			
		$valuationRangeHigh=$xml->response->results->result->zestimate->valuationRange->high;
		$valuationRangeHigh=formatValue($valuationRangeHigh);
			
		$rentzestimateAmount = $xml->response->results->result->rentzestimate->amount;
		$rentzestimateAmount=formatValue($rentzestimateAmount);
			
		$rentzestimateLastUpdated=$xml->response->results->result->rentzestimate->{'last-updated'};
		if($rentzestimateLastUpdated){
			$rentzestimateLastUpdated=str_replace("/","-", $rentzestimateLastUpdated);
			$rentzestimateLastUpdated = date_create_from_format('m-d-Y', $rentzestimateLastUpdated,$timeAtLA);
			$rentzestimateLastUpdated=date_format($rentzestimateLastUpdated, 'd-M-Y');
		}
			
		// 			$rentzestimateLastUpdated = new DateTime($rentzestimateLastUpdated);
		// 			$rentzestimateLastUpdated->setTimezone($la_time);
		// 			$rentzestimateLastUpdated=date_format($rentzestimateLastUpdated, 'd-M-Y');
		$rentzestimateValueChange=$xml->response->results->result->rentzestimate->valueChange;
			
		$rentzestimateValueChangeLow=$xml->response->results->result->rentzestimate->valuationRange->low;
		$rentzestimateValueChangeLow=formatValue($rentzestimateValueChangeLow);
		$rentzestimateValueChangeHigh=$xml->response->results->result->rentzestimate->valuationRange->high;
		$rentzestimateValueChangeHigh=formatValue($rentzestimateValueChangeHigh);
		
		$zipcode = $xml->response->results->result->address->zipcode;
		$street = $xml->response->results->result->address->street;
		//$street = $street{0};
		$valueChange=(int)$valueChange;
		if($valueChange < 0) {
			$estimateValueChangeSign = "-";
			$valueChange=(-1)*$valueChange;
			$valueChange=formatValue($valueChange);
		}
		else{
			$estimateValueChangeSign = "+";
			$valueChange=formatValue($valueChange);
		}
		if($rentzestimateValueChange < 0) {
			$rentzestimateValueChangeSign = "-";
			$rentzestimateValueChange=(-1)*$rentzestimateValueChange;
			$rentzestimateValueChange= formatValue($rentzestimateValueChange);
		}
		else {
			$rentzestimateValueChangeSign="+";
			$rentzestimateValueChange= formatValue($rentzestimateValueChange);
		}
		
		$obj = (object) array(
				'homedetails' => $resultHeader,
				'street' => $street, 
				'city' => $city, 
				'state'=> $_GET ['stateInput'],
				'zipcode'=> $zipcode,
				'latitude' => $xml->response->results->result->address->latitude,
				'longitude' => $xml->response->results->result->address->longitude,
				'useCode' => $propertyType,
				'lastSoldPrice' => $lastSoldPrice,
				'yearBuilt' => $yearBuilt,
				'lastSoldDate' => $lastSoldDate,
				'lotSizeSqFt' => $lotSizeSqFt,
				'estimateLastUpdate' => $lastUpdated,
				'estimateAmount' => $zestimateAmount,
				'finishedSqFt' => $finishedSqFt,
				'estimateValueChangeSign' => $estimateValueChangeSign,
				'imgn' => "http://cs-server.usc.edu:45678/hw/hw6/down_r.gif",
				'imgp' => "http://cs-server.usc.edu:45678/hw/hw6/up_g.gif",
				'estimateValueChange' => $valueChange,
				'bathrooms' => $bathrooms,
				'estimateValuationRangeLow' => $valuationRangeLow,
				'estimateValuationRangeHigh' => $valuationRangeHigh,
				'bedrooms' => $bedrooms,
				'restimateLastUpdate' => $rentzestimateLastUpdated,
				'restimateAmount' => $rentzestimateAmount,
				'taxAssessmentYear' => $taxAssessmentYear,
				'restimateValueChangeSign' => $rentzestimateValueChangeSign,
				'restimateValueChange' => $rentzestimateValueChange,
				'taxAssessment' => $taxAssessment,
				'restimateValuationRangeLow' => $rentzestimateValueChangeLow,
				'restimateValuationRangeHigh' => $rentzestimateValueChangeHigh
		);
		$chOneYear="http://www.zillow.com//app?chartDuration=1year&chartType=partner&height=300&page=webservice%2FGetChart&service=chart&showPercent=true&width=600&zpid=".$xml->response->results->result->zpid;
		$chFiveYears="http://www.zillow.com//app?chartDuration=5years&chartType=partner&height=300&page=webservice%2FGetChart&service=chart&showPercent=true&width=600&zpid=".$xml->response->results->result->zpid;
		$chTenYears="http://www.zillow.com//app?chartDuration=10years&chartType=partner&height=300&page=webservice%2FGetChart&service=chart&showPercent=true&width=600&zpid=".$xml->response->results->result->zpid;
		$chart = (object) array('1year' => $chOneYear,
				'5years' => $chFiveYears,
				'10years' => $chTenYears
			);
		$return ["debugURL"]=$url;
		$return ["result"] = ($obj);
		$return ["chart"] = ($chart);
		$return["code"]=($codeReturn);
		echo ( json_encode($return) );
		}
	}
}
?>
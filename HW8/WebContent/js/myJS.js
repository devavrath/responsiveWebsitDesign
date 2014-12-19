$(document).ready(function () {
	$('#myForm').validate({ 
		rules: {
			Address: {
				required: true,
				minlength: 1,
			},
			City: {
				required: true,
				minlength: 1,
			},
			state: {
				required: true,
				minlength: 1,
			}            
		},

		highlight: function(element) {
			$(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		errorElement: 'span',
		errorClass: 'help-block',
		errorPlacement: function(error, element) {
			if(element.parent('.input-group').length) {
				error.insertAfter(element);
			} else {
				error.insertAfter(element);
			}},
			submitHandler: function (form) { 
				var streetAddress = $("#myForm").find("#streetAddress").val();
				var city = $("#myForm").find("#city").val();
				var state = $("#myForm").find("#state").val();

				var data = {
						"action": "test"
				};
				data = $("#myForm").serialize() + "&" + $.param(data);
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "http://devavrathhw8.elasticbeanstalk.com",
					data: data,
					success: function(data) {
						showTableData(data);
					},
					error: function(e){
						$("#errorMessage").toggleClass('row hidden row show help-block');
						$("#errorMessage").append(e.responseText);
					}
				});
				return false;
			},
	});
});

$('#basicInfo').click(function (e) {
	e.preventDefault()
	$(this).tab('show')
});


function showTableData(data){
	data=JSON.parse(data);
	data["result"]=JSON.parse(data["result"]);
	data["chart"]=JSON.parse(data["chart"]);
	result=data.result;
	result.chart=data.chart["1year"];
	$("#errorMessage").toggleClass('row show help-block row hidden');
	$("#tableList").toggleClass('row hidden row show');
	var img1;
	var img2;
	if(result.estimateValueChangeSign == "+"){
		img1=result.imgp;
	}
	else {
		img1=result.imgn;
	}
	if(result.restimateValueChangeSign == "+"){
		img2=result.imgp;
	}
	else {
		img2=result.imgn;
	}
	$("#tableActual").append("<tbody>" +
			"<tr><td colspan=3>See more details for <a href = '"
			+result.homedetails["0"]+ "'>" + result.street["0"] + ", " 
			+ result.city + ", " 
			+ result.state + "-" 
			+result.zipcode["0"]+ "" + "</a>  on Zillow</td>"+"<td><button type=\"button\" class=\"btn btn-primary\" id=\"facebookButton\">Share on Facebook</button></td></tr>"+		
			"<tr><td> Property type: </td><td> " + result.useCode["0"] + "</td><td> Last Sold Price: </td><td> " 
			+result.lastSoldPrice+ "</td></tr>" + "<tr><td> Year Built: </td><td> " + result.yearBuilt["0"] + "</td>"+
			"<td> Last Sold Date: </td><td> " + result.lastSoldDate + "</td></tr>"+
			"<tr><td> Lot Size: </td><td> " + result.lotSizeSqFt["0"] + " sq.ft</td><td>"+
			"Zestimate &reg Property Estimate as of " + result.estimateLastUpdate + "</td>"+
			"<td> " + result.estimateAmount + "</td></tr>"+
			"<tr><td> Finished Area: </td><td> " + result.finishedSqFt["0"] + " sq.ft</td><td>"+
			"30 Days Overall Change: </td><td> <img src=\""+img1+"\"> " +result.estimateValueChange+"</td></tr>"+
			"<tr><td>Bathrooms:</td><td>" +result.bathrooms["0"]+ "</td><td>All Time Property Range :</td>	<td>"
			+result.estimateValuationRangeLow+" - "+result.estimateValuationRangeHigh+"</td></tr>"+
			"<td>Bedrooms:</td><td>" +result.bedrooms["0"]+ "</td><td> Rent Zestimate &reg Property Estimate as of " +
			result.restimateLastUpdate + "</td><td> " + result.restimateAmount + "</td></tr>"+
			"<tr><td> Tax Assesment Year: </td><td> " + result.taxAssessmentYear["0"] + 
			"</td><td> 30 Days Rent Change: </td><td> <img src=\""+img2+"\"> "+result.restimateValueChange+"</td></tr>"+
			"<tr><td> Tax Assesment: </td><td> " + result.taxAssessment + "</td><td>All Time Rent Range :</td><td>"+
			result.restimateValuationRangeLow+" - "+result.restimateValuationRangeHigh+"</td></tr></table>"
	);

	for(var i=0;i<Object.keys(data.chart).length;i++){
		if(i==0){
			$("#carouselActiveObject").append(
					"<img style=\"margin-left: 25%;\" src=\""+data.chart["1year"]+"\">" +
					"<div class=\"carousel-caption\"><h2>Historical Zestimates for the past 1 year</h2><br><h3>" +
					result.street["0"] + ", "+ result.city + ", "+ result.state + "-"+result.zipcode["0"]+
			"</h3></div>");
		}
		else {
			$("#CarouselMain").append(
					"<div class=\"item\"><img style=\"margin-left: 25%;\" src=\""+data.chart[Object.keys(data.chart)[i]]+"\">" +
					"<div class=\"carousel-caption\"><h2>Historical Zestimates for the past "+Object.keys(data.chart)[i]+"</h2><br><h3>" +
					result.street["0"] + ", "+ result.city + ", "+ result.state + "-"+result.zipcode["0"]+
			"</h3></div></div>");
		}
	}
	$('#facebookButton').click(function (e) {

		FB.ui(
				{
					method: 'feed',
					name: result.street["0"]+","+result.city+","+result.state,
					link: 'http://localhost:81/HW8/WebContent/index.html',
					picture: result.chart,
					caption: 'Property Information from Zillow.com: ',
					description: 
						('Last Sold Price:'+result.lastSoldPrice+',30 Days Overall Change:'+result.restimateValueChangeSign+result.restimateValueChange)
				},



				function(response) {
					if (response && response.post_id) {
						alert('Post was published.');
					} else {
						alert('Post was not published.');
					}
				}
		);

	});
}


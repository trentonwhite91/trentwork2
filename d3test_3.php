<!DOCTYPE html>

<?php
$servername = "localhost";
$username = "trentw";
$password = "whitet";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
//	if (mysqli_connect_errno()) {
//    die("Connection failed: " . mysqli_connect_error());
//		} 
//	echo "Connected successfully";

//SQL queries 
$query1 = "SELECT Invoice_Amt, Invoice_Date FROM PROJECT1_DATABASE.Invoices;";
   
$query1_r = mysqli_query($conn, $query1);

//check for successful query
   
//  if ( !$query1_r ) {
//  echo mysql_error();
//  	die;
//  } 

//  echo "Query successful!";




?>

<head>
<!--Get bootstrap-->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>   
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

		
</head>
<style> /* set the CSS */

body { font: 12px Arial;}

path { 
    stroke: steelblue;
    stroke-width: 2;
    fill: none;
}

.axis path,
.axis line {
    fill: none;
    stroke: grey;
    stroke-width: 1;
    shape-rendering: crispEdges;
}

</style>
<body>

<div id="option">
<!--Insert linked date pickers-->
    <div class="container">
    <div class='col-md-5'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker6'>
                <input type='text' class="form-control" id="date1" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    <div class='col-md-5'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker7'>
                <input type='text' class="form-control" id="date2" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="btn">
  <button type="button" class="btn btn-default" onclick="dates()">Update</button>
</div>
<script type="text/javascript">
    $(function () {
        $('#datetimepicker6').datetimepicker({
			format: 'YYYY-MM-DD'
		});
        $('#datetimepicker7').datetimepicker({
			format: 'YYYY-MM-DD',
            useCurrent: false //Important! See issue #1075
        });
        $("#datetimepicker6").on("dp.change", function (e) {
            $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker7").on("dp.change", function (e) {
            $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
        });
    });
</script>
</div>

<!-- load the d3.js library -->    
<script src="https://d3js.org/d3.v4.min.js"></script>

<!--Begin line graph-->

<script>

// Set the dimensions of the canvas / graph
var margin = {top: 30, right: 20, bottom: 100, left: 50},
    width = 600 - margin.left - margin.right,
    height = 400 - margin.top - margin.bottom;

// Parse the date / time
//var parseDate = d3.time.format("%d-%b-%y").parse;

// Parse the date / time
var parseDate = d3.timeParse("%Y-%m-%d");


// Set the ranges
var x = d3.scaleTime().range([0, width]);
var y = d3.scaleLinear().range([height, 0]);

/*// Define the axes
var xAxis = d3.svg.axis().scale(x)
    .orient("bottom").ticks(5);

var yAxis = d3.svg.axis().scale(y)
    .orient("left").ticks(5);*/
	
// Define the axes

var xAxis = d3.axisBottom(x).tickFormat(d3.timeFormat("%m/%d/%Y"));
var yAxis = d3.axisLeft(y);

// Define the line
var valueline = d3.line()
    .x(function(d) { return x(d.Invoice_Date); })
    .y(function(d) { return y(d.Invoice_Amt); });
    
// Adds the svg canvas
var svg = d3.select("body")
    .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
    .append("g")
        .attr("transform", 
              "translate(" + margin.left + "," + margin.top + ")");

// Get the data
d3.json("DB_Connect.php", function(error, data) {
	
  if (error) throw error;
    data.forEach(function(d) {
        d.Invoice_Date = parseDate(d.Invoice_Date);
        d.Invoice_Amt = +d.Invoice_Amt;
    });

    // Scale the range of the data
    x.domain(d3.extent(data, function(d) { return d.Invoice_Date; }));
    y.domain([0, d3.max(data, function(d) { return d.Invoice_Amt; })]);

    // Add the valueline path.
    svg.append("path")
        .attr("class", "line")
        .attr("d", valueline(data));

    // Add the X Axis
    svg.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + height + ")")
        .call(xAxis)
		.selectAll("text")	
        .style("text-anchor", "end")
        .attr("dx", "-.8em")
        .attr("dy", ".15em")
        .attr("transform", "rotate(-65)");

    // Add the Y Axis
    svg.append("g")
        .attr("class", "y axis")
        .call(yAxis);

});

// ** Update data section (Called from the onclick)
function dates() {
	var date1 = document.getElementById('date1').value
	var date2 = document.getElementById('date2').value


	$.ajax({
  type: 'GET',
  url: 'DB_Connect_Update.php',
  dataType: 'json',
  data: {date1: date1, date2: date2},
  error: function (error) {
	alert(error.responseText)},
  success: function(data){
    spanish();
	spanish2();
	newData = data;
	spanish3();
	newGraph();
		}
	});
	
	function spanish(){console.log("uno")}
	function spanish2(){console.log("dos")}
	function spanish3(){console.log(newData)}
	
	function newGraph(newData){
	
    // Get the data again
    d3.json(newData, function(error, root) {
       	root.forEach(function(d) {
	    	d.Invoice_Date = parseDate(d.Invoice_Date);
	    	d.Invoice_Amt = +d.Invoice_Amt;
	    });

    	// Scale the range of the data again 
    	x.domain(d3.extent(data, function(d) { return d.Invoice_Date; }));
	    y.domain([0, d3.max(data, function(d) { return d.Invoice_Amt; })]);

    // Select the section we want to apply our changes to
    var svg = d3.select("body").transition();

    // Make the changes
        svg.select(".line")   // change the line
            .duration(750)
            .attr("d", valueline(data));
        svg.select(".x.axis") // change the x axis
            .duration(750)
            .call(xAxis);
        svg.select(".y.axis") // change the y axis
            .duration(750)
            .call(yAxis);

    });
  }
}

</script>

</body>
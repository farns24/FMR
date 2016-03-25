var map;

var BodyClass = React.createClass({
	getInitalState: function(){
		return {
				data:[]
			};
		
		},
	componentDidMount: function() {
		
		var city =document.getElementById("city").className;
		var county =document.getElementById("county").className;
		var state = document.getElementById("state").className;
		var placeId = city + "%20"+ county+ "%20" + state;//document.getElementById("place").className;
		
		var searchSize = document.getElementById("searchSize").className;
		var startYear = document.getElementById("startYear").className;
		map = new GMap2(document.getElementById("point_map_canvas1"));
		map.setCenter(new GLatLng(37, -96), 4);
		map.addControl(new GSmallMapControl());
		map.addControl(new GMapTypeControl())
		
    $.get("/fmr/FamilySearchAPI/singleGenerationPull.php?placeId=" +placeId +"&searchSize="+ searchSize +"&startYear="+startYear, function(result) {
     
      if (this.isMounted()) {
        this.setState({
          data: JSON.parse(result)
        });
		this.state.data.map(function(dataProps){
			console.log(dataProps);
			var point0 = new GLatLng(55.8333333, -4.4333333);
			var mark0 = new GMarker(point0);
			GEvent.addListener(mark0,'mouseover',function(){
				mark0.openInfoWindowHtml('<Name</b> Grace Dick<br><b>ID </b>9W39-JX9')
				});
					GEvent.addListener(mark0,'click',function(){
					mark0.openInfoWindowHtml('<a href="MapPersonDetails.php?id=9W39-JX9">Click for more details</a>')
				});
				map.addOverlay(mark0);
			
			});
      }
    }.bind(this));
  },
render: function() {
	var divStyles = {'width':'100%', 'height':'100%', 'marginLeft':'auto',  'marginRight':'auto'};
	var bodyStyles = {'height':'100%','margin':'0px'};

	return (
	

<body style={bodyStyles}>
	<script type="text/javascript">
	
	</script>

	<div id="point_map_canvas1" style={divStyles}></div>
</body>
		
	);
}
	
});

var myBody = React.createElement(BodyClass);

ReactDOM.render(myBody,document.getElementById('singleGenBody'));
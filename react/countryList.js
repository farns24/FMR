
var CityList = React.createClass({
  render: function() {
    return (
	<div className="input-group hide_while_creating_projects">
		<span className="input-group-addon ">Country</span>		
		<datalist id="cityList">
			<option value="Ireland" />
			<option value="United States" />
		</datalist>
		<input className="form-control" type="text" list="countryList" name="country" placeholder="*Country" id="autocomplete"/>		
	</div> 
	);
  }
});

var cityList_el = React.createElement(CityList);

ReactDOM.render(
  cityList_el,
  document.getElementById('countryListR')
);
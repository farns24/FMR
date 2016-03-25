var CountyListClass = React.createClass({
	render: function() {
		
		return (
		<div className="input-group hide_while_creating_projects">
			
			<span className="input-group-addon">County</span>
			
				<input className="form-control" type="text" size="12" list="countyList" name="county"  placeholder="*County" id="autocomplete"/>
							<datalist id="countyList">
								<option value="Henry" />
								<option value="Jefferson" />
								<option value="Winston" />
								<option value="Hale" />
								<option value="Shelby" />
								<option value="Windsor"/>
								<option value="Addison"/>
								
								// Vermont
								<option value="Washington" />
								<option value="Chittenden"/>
								<option value="Franklin" />
								<option value="Orleans" />
								<option value="Addison" />
								<option value="Bennington" />
								<option value="Windham" />
								<option value="Caledonia"/>
								<option value="Shelburne" />
								
							
							</datalist>
			</div>
		
		);
	}
	
	
});

var countyList_el = React.createElement(CountyListClass);

ReactDOM.render(
  countyList_el,
  document.getElementById('countyListR')
);
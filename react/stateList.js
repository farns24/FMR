var StateListClass = React.createClass({
	render: function() {
		
		return (
		<div className="input-group hide_while_creating_projects">
			
			<span className="input-group-addon">State</span>
			
				<input className="form-control" type="text" size="12" name="state" list="stateList" placeholder="*State" id="autocomplete"/>
							<datalist id="stateList">
								<option value="Alabama" />
								<option value="Alaska" />
								<option value="Arizona" />
								<option value="Arkansas" />
								<option value="California" />
								<option value="Colorado" />
								<option value="Connecticut" />
								<option value="Delaware" />
								<option value="Florida" />
								<option value="Georgia" />
								<option value="Hawaii" />
								<option value="Idaho" />
								<option value="Illinois" />
								<option value="Indiana" />
								<option value="Iowa" />
								<option value="Kansas" />
								<option value="Kentucky" />
								<option value="Louisiana" />
								<option value="Maine" />
								<option value="Maryland" />
								<option value="Massachusetts" />
								<option value="Michigan" />
								<option value="Minnesota" />
								<option value="Mississippi" />
								<option value="Missouri" />
								<option value="Montana" />
								<option value="Nebraska" />
								<option value="Nevada" />
								<option value="New Hampshire" />
								<option value="New Jersey" />
								<option value="New Mexico" />
								<option value="New York" />
								<option value="North Carolina" />
								<option value="North Dakota" />
								<option value="Ohio" />
								<option value="Oklahoma" />
								<option value="Organ" />
								<option value="Pennsylvania" />
								<option value="Rhode Island" />
								<option value="South Carolina" />
								<option value="South Dakota" />
								<option value="Tennessee" />
								<option value="Texas" />
								<option value="Utah" />
								<option value="Vermont" />
								<option value="Virginia" />
								<option value="Washington" />
								<option value="West Virginia" />
								<option value="Wisconsin" />
								<option value="Wyoming" />
							</datalist>
			</div>
		
		);
	}
	
	
});

var stateList_el = React.createElement(StateListClass);

ReactDOM.render(
  stateList_el,
  document.getElementById('stateListR')
);
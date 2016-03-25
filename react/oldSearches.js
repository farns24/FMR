var OldSearchItem = React.createClass({
	render: function(){
		return(
			<option value={this.props.name}>{this.props.name}</option>
		
		);
		
	}
	
	
});



var OldSearchClass = React.createClass({
  getInitialState: function() {
    return {
      data: []
    };
  },

  componentDidMount: function() {
    $.get("/fmr/FamilySearchAPI/oldProjects.php", function(result) {
      
      if (this.isMounted()) {
		  
        this.setState({
          data: JSON.parse(result)
          
        });
      }
    }.bind(this));
  },

  render: function() {
	  
	  var list = this.state.data.map(function(dataprops){
		  return <OldSearchItem {...dataprops} />
	  });
	  
	  
    return (
      <div>
		<select name="fileName" className="selectpicker col-md-6">
			<option value="">Choose a file</option>
		{list}
		</select>
      </div>
    );
  }
});

								
									

								


var oldSearch = React.createElement(OldSearchClass);


ReactDOM.render(
  oldSearch,
  document.getElementById('oldSearchR')
);
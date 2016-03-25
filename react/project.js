var ProjectFileClass = React.createClass({
	promptSpinner: function(){
		
		//document.getElementById('loadingAnalysis').showModal();
		 
		 return false;
	},
	render: function() {
    return (
	<li>
	<div>
	 <form action="BYUFMR.php" method="post" >	
	 
		<button type="submit" onclick ={this.promptSpinner()} className="btn" name="fileName" value={this.props.filePath}>{this.props.fileName}</button><br />
		<input type="hidden" name="step" value="analyzeProxy" />
		<input type="hidden" name="analysisFile" value="none" />
	</form>
	</div>
	</li> 
    );
  }	
});




var ProjectClass = React.createClass({
	render: function() {
		
		var fileList = this.props.files.map(function(dataProps) {
			
			return <ProjectFileClass {...dataProps} />
		});
    return (
	
	      <div className="btn-group">
	  <button className="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	  {this.props.name }
    <span className="caret"></span>
	</button>
		<ul className="dropdown-menu">
			{fileList}
		</ul>
      </div>
	
    );
  }	
});

var ProjectListClass = React.createClass({
  getInitialState: function() {
    return {
      data:[]
    };
  },

  componentDidMount: function() {
    $.get("/fmr/FamilySearchAPI/projects.php", function(result) {
      console.log(result);
      if (this.isMounted()) {
        this.setState({
          data: JSON.parse(result)
        });
      }
    }.bind(this));
  },

  render: function() {
	  
	    var list = this.state.data.map(function(dataprops){
		  return <ProjectClass {...dataprops} />
		});
		
    return (
      <div>
	  {list}
	  
      </div>
    );
  }
});

var project = React.createElement(ProjectListClass);


ReactDOM.render(
  project,
  document.getElementById('projectsR')
);
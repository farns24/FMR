var myFile = document.getElementById('myDebuggerDiv');
var query = myFile.classList.length;
var fileName = "";
for (var i = 0; i<query;i++)
{
	fileName += myFile.classList.item(i);
	fileName +="%20";
}
fileName = fileName.slice(0, -3);


var FileDebugClass = React.createClass({
	 getInitialState: function() {
    return {
      data:{}
    };
  },

  componentDidMount: function() {
	  
	  
    $.get("/fmr/FamilySearchAPI/fileReader.php?fileName="+fileName, function(result) {
      console.log(result);
      if (this.isMounted()) {
        this.setState({
          data: JSON.parse(result)
        });
      }
    }.bind(this));
  },

  render: function() {
	  
		
    return (
      <div>
		<a>Data Debugger</a>
      </div>
    );
  }

});


var debugInstance = React.createElement(FileDebugClass);

ReactDOM.render(
  debugInstance,
  document.getElementById('myDebuggerDiv')
);